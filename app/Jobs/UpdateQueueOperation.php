<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UpdateQueueOperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $serialNumberId;

    public function __construct($serialNumberId)
    {
        $this->serialNumberId = $serialNumberId;
    }

    public function handle()
    {
        try {
            // Update the status of queue operations
            DB::table('queuetb_raw_queue_operations')
                ->where('queue_serial_number_id', $this->serialNumberId)
                ->where('status', 5)
                ->update(['status' => 6]);
    
            // Log success message
            Log::info(' UpdateQueueOperation jobs Queue operation updated successfully.', ['serialNumberId' => $this->serialNumberId]);
        } catch (\Exception $e) {
            // Log error message
            Log::error('UpdateQueueOperation jobs Failed to update queue operation.', ['error' => $e->getMessage(), 'serialNumberId' => $this->serialNumberId]);
        }
    }
}
