<?php

namespace App\Http\Controllers\Test;

use Aliyun\MNS\Client;
use Aliyun\MNS\Exception\MnsException;
use Aliyun\MNS\Http\HttpClient;
use Aliyun\MNS\Queue;
use App\Models\Device;
use App\Jobs\RefreshIotDeviceStateJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IotMessageQueueController extends Controller
{
    /**
    array:4 [▼
    "payload" => "eyJsYXN0VGltZSI6IjIwMTgtMDYtMDMgMTI6MDg6MTQuMzYzIiwiY2xpZW50SXAiOiIyMjMuMTA0LjI1NC4yNDMiLCJ0aW1lIjoiMjAxOC0wNi0wMyAxMjowODoxNC4zODIiLCJwcm9kdWN0S2V5IjoiSlgxRGczQmpWbHYiLCJkZXZpY2VOYW1lIjoiMjAxODAzMjYwMDciLCJzdGF0dXMiOiJvbmxpbmUifQ== ◀eyJsYXN0VGltZSI6IjIwMTgtMDYtMDMgMTI6MDg6MTQuMzYzIiwiY2xpZW50SXAiOiIyMjMuMTA0LjI1NC4yNDMiLCJ0aW1lIjoiMjAxOC0wNi0wMyAxMjowODoxNC4zODIiLCJwcm9kdWN0S2V5IjoiSlgxRGcz ▶"
    "messagetype" => "status"
    "messageid" => 1003126185158254592
    "timestamp" => 1527998894
    ]
     *
     * array:6 [▼
    "lastTime" => "2018-06-03 12:08:14.363"
    "clientIp" => "223.104.254.243"
    "time" => "2018-06-03 12:08:14.382"
    "productKey" => "JX1Dg3BjVlv"
    "deviceName" => "20180326007"
    "status" => "online"
    ]

     * array:5 [▼
    "payload" => "ewoJImRldmljZV9pZCI6CTAsCgkidGltZXN0YW1wIjoJMjg3NDU0MDIwLAoJInZhbHVlIjoJMTAwCn0="
    "messagetype" => "upload"
    "topic" => "/JX1Dg3BjVlv/20180326007/put"
    "messageid" => 1002864652352245760
    "timestamp" => 1527936540
    ]
     *
     * array:3 [▼
    "device_id" => 0
    "timestamp" => 287454020
    "value" => 100
    ]

     *
     *
     *
     */
    public function index()
    {
        $config = $this->config();

        //1.
        $client = new Client($config['endpoint'], $config['key'], $config['secret']);

        //2.
        $queue = $client->getQueueRef($config['queue']);

        // 3. receive message
        $receiptHandle = NULL;
//        try
        {
            // when receiving messages, it's always a good practice to set the waitSeconds to be 30.
            // it means to send one http-long-polling request which lasts 30 seconds at most.
            $res = $queue->receiveMessage(30);
            $body = $res->getMessageBody();
            $data = json_decode($body, true);
            $payload = json_decode(base64_decode($data['payload']), true);
//            echo "ReceiveMessage Succeed! \n";
            debugbar()->debug($res);
            debugbar()->debug($body);
            debugbar()->debug($data);
            debugbar()->debug($payload);

            if ($data['messagetype'] == 'status')
            {
                $this->onStatusMessage($payload['productKey'], $payload['deviceName'], $payload);
            }
            else if ($data['messagetype'] === 'upload')
            {
                $topic = explode('/', $data['topic']);
                $this->onUploadMessage($topic[1], $topic[2], $payload);
            }
            else
            {}

//            $receiptHandle = $res->getReceiptHandle();
        }
//        catch (MnsException $e)
//        {
//            echo "ReceiveMessage Failed: " . $e;
//            return 'exception';
//        }

        // 4. delete message
//        try
//        {
//            $res = $queue->deleteMessage($receiptHandle);
//            echo "DeleteMessage Succeed! \n";
//        }
//        catch (MnsException $e)
//        {
//            echo "DeleteMessage Failed: " . $e;
//            return;
//        }

        return 'hello';
    }

    public function config()
    {
        $config = [
            'driver'       => 'mns',
            'key'          => config('Log::access_key_id'),
            'secret'       => config('aliyun_iot.access_secret'),
            'endpoint'     => config('aliyun_iot.endpoint'),
            'queue'        => config('aliyun_iot.queue'),
            'wait_seconds' => 30,
        ];

        return $config;
    }

    private function getDevice($productKey, $deviceName)
    {
        $device = Device::where('product_key', $productKey)
            ->where('device_name', $deviceName)->first();
        return $device;
    }

    private function onStatusMessage($productKey, $deviceName, $payload)
    {
        $device = $this->getDevice($productKey, $deviceName);
        if (isset($device))
        {
            switch ($payload['status'])
            {
                case 'online':
                    $device->status = Device::DeviceStatus_Online;
                    break;

                case 'offline':
                    RefreshIotDeviceStateJob::dispatch($device->id)->delay(30);
                    break;

                default:
                    break;
            }

            debugbar()->debug('device: ' .$device->name .' ' .Device::tr('status', $device->status));
            $device->saveOrFail();
        }
    }

    private function onUploadMessage($productKey, $deviceName, $payload)
    {
        $device = $this->getDevice($productKey, $deviceName);
        if (isset($device))
        {
            debugbar()->debug('message from: ' .$device->name);
        }
    }

    private function onCoinMessage($device, $data)
    {}

    private function onBanknoteMessage($device, $data)
    {}

    private function onWashCarMessage($device, $data)
    {}

    private function onCardMessage($device, $data)
    {}
}
