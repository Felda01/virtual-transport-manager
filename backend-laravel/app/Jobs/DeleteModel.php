<?php

namespace App\Jobs;

use App\Events\RefreshMarketQuery;
use App\Events\RefreshQuery;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Market;
use App\Utilities\BroadcastUtility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteModel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $model;

    /**
     * Create a new job instance.
     *
     * @param $model
     */
    public function __construct(Model $model)
    {
        /** @var Model model */
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        if ($this->model instanceof Driver && $this->model->company_id !== null) {
            return;
        }
        if ($this->model instanceof Driver && $this->model->company_id === null) {
            $this->model->delete();
            return;
        }

        $company = Company::find($this->model->company_id);
        BroadcastUtility::broadcast(new RefreshQuery($company, class_basename($this->model), $this->model->id));

        $this->model->delete();
    }
}
