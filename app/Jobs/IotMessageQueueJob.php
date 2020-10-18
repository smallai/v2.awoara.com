<?php

namespace App\Jobs;

use Aliyun\MNS\Client;
use function GuzzleHttp\Promise\is_settled;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Lokielse\LaravelMNS\Adaptors\MNSAdapter;
use Lokielse\LaravelMNS\MNSQueue;

class IotMessageQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    protected $queue = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $config = [
//            'driver'       => 'mns',
//            'key'          => config('aliyun_iot.access_key_id'),
//            'secret'       => config('aliyun_iot.access_secret'),
//            'endpoint'     => config('aliyun_iot.endpoint'),
//            'queue'        => config('aliyun_iot.queue'),
//            'wait_seconds' => 30,
//        ];
//        $client = new Client($config['endpoint'], $config['key'], $config['secret']);
//        $adapter = new MNSAdapter($client);
//        $this->queue = new MNSQueue($adapter, $config['queue'], $config['wait_seconds']);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    }
}
