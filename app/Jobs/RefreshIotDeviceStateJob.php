<?php

namespace App\Jobs;

use App\Models\Device;
use App\Http\Controllers\Test\IotMessageQueueController;
use App\Utils\IotDevice;
use App\Scopes\DeviceScope;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class RefreshIotDeviceStateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    private $device_id = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->device_id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->delete();

        try {
            if (\is_array($this->device_id)) {
                foreach ($this->device_id as $id) {
                    $this->process($id);
                }
            } else {
                $this->process($this->device_id);
            }
        } catch (\Exception $exception) {
            \Log::warning('ping device', [
                'exception' => $exception
            ]);
        }
    }

    public function process($id)
    {
        if (\is_int($id) && ($id > 0)) {
            Log::debug('refresh device online state', ['id' => $id]);

            try {
                $device = Device::findOrFail($id);
                $iotDevice = new IotDevice($id);
                $device['is_online'] = $iotDevice->ping();
                $device->save();
            } catch (\Exception $exception) {
                \Log::warning('ping device', [
                    'exception' => $exception,
                    'device_id' => $id
                ]);
            }
        }
    }
}
