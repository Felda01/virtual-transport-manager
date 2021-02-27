<?php

namespace App\Jobs;

use App\Events\ProcessTransaction;
use App\Events\RefreshQuery;
use App\Models\BankLoan;
use App\Models\Company;
use App\Utilities\BroadcastUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\TransactionUtility;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayBankLoans implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $single;

    /**
     * Create a new job instance.
     *
     * @param bool $single
     */
    public function __construct($single = false)
    {
        $this->single = $single;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $companies = Company::whereHas('bankLoans', function($query) {
            $query->where('done', false);
        })->get();

        $companies->each(function ($item, $key) {
            $result = DB::transaction(function () use ($item) {
                $sum = DB::table('bank_loans')->where('company_id', $item->id)
                    ->leftJoin('bank_loan_types', 'bank_loans.bank_loan_type_id', '=', 'bank_loan_types.id')
                    ->where('done', false)
                    ->sum('bank_loan_types.payment');

                $updated = DB::table('bank_loans')->leftJoin('bank_loan_types', 'bank_loans.bank_loan_type_id', '=', 'bank_loan_types.id')
                    ->where('bank_loans.company_id', $item->id)
                    ->where('bank_loan_types.period', '>', DB::raw('bank_loans.paid + 1'))
                    ->where('done', false)
                    ->increment('bank_loans.paid');

                $updatedDone = DB::table('bank_loans')->leftJoin('bank_loan_types', 'bank_loans.bank_loan_type_id', '=', 'bank_loan_types.id')
                    ->where('bank_loans.company_id', $item->id)
                    ->where('bank_loan_types.period', '=', DB::raw('bank_loans.paid + 1'))
                    ->update([
                        'done' => true,
                    ]);

                $transaction = TransactionUtility::create($item, null, -1 * $sum, 'bank_loan_payment');

                $oldMoney = $item->money;

                $item->decrement('money', $sum);
                $item->save();

                if ($item->money !== ($oldMoney - $sum) || (!$updated && !$updatedDone) || !$transaction) {
                    throw new Exception(trans('validation.general_exception'));
                }
            });

            BroadcastUtility::broadcast(new ProcessTransaction($item));
            BroadcastUtility::broadcast(new RefreshQuery($item, 'BankLoan'));
        });

        $now = Carbon::parse(GameTimeUtility::gameTime(Carbon::now('Europe/Bratislava')));
        $nextMonth = $now->copy()->addMonths();
        $diffMinutes = $nextMonth->diffInRealMinutes($now);

        if (!$this->single) {
            QueueJobUtility::dispatch(new PayBankLoans(), Carbon::parse(GameTimeUtility::addTimeToRealTime($diffMinutes), 'Europe/Bratislava'));
        }
    }
}
