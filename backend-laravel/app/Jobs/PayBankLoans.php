<?php

namespace App\Jobs;

use App\Models\BankLoan;
use App\Models\Company;
use App\Utilities\GameTimeUtility;
use App\Utilities\QueueJobUtility;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        });

        $companies->each(function ($item, $key) {
            $result = DB::transaction(function () use ($item) {
                $sum = BankLoan::where('company_id', $item->id)
                    ->where('done', false)
                    ->sum('payment');

                $updated = DB::table('bank_loans')->leftJoin('bank_loan_types', 'bank_loans.bank_loan_type_id', '=', 'bank_loan_types.id')
                    ->where('bank_loans.company_id', $item->id)
                    ->where('bank_loan_types.period', '>', DB::raw('bank_loans.paid + 1'))
                    ->increment('bank_loans.paid');

                $updatedDone = DB::table('bank_loans')->leftJoin('bank_loan_types', 'bank_loans.bank_loan_type_id', '=', 'bank_loan_types.id')
                    ->where('bank_loans.company_id', $item->id)
                    ->where('bank_loan_types.period', '=', DB::raw('bank_loans.paid + 1'))
                    ->update([
                        'done' => true,
                    ]);

                $oldMoney = $item->money;

                $item->decrement('money', $sum);
                $item->save();

                if ($item->money !== ($oldMoney - $sum) || !$updated || !$updatedDone) {
                    throw new Exception(trans('validation.general_exception'));
                }
            });
        });

        $now = Carbon::parse(GameTimeUtility::gameTime(Carbon::now('Europe/Bratislava')));
        $nextMonth = $now->copy()->addMonths();
        $diffMinutes = $nextMonth->diffInRealMinutes($now);

        if (!$this->single) {
            QueueJobUtility::dispatch(new PayBankLoans(), Carbon::parse(GameTimeUtility::gameTimeToRealTime($diffMinutes), 'Europe/Bratislava'));
        }
    }
}
