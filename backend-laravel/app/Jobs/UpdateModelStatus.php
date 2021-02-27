<?php

namespace App\Jobs;

use App\Events\RefreshQuery;
use App\Models\Company;
use App\Utilities\BroadcastUtility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateModelStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $model;
    public $status;

    /**
     * Create a new job instance.
     *
     * @param $model
     * @param $status
     */
    public function __construct($model, $status)
    {
        $this->model = $model;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->model->update([
            'status' => $this->status
        ]);

        $company = Company::find($this->model->company_id);
        BroadcastUtility::broadcast(new RefreshQuery($company, class_basename($this->model), $this->model->id));
    }
}
