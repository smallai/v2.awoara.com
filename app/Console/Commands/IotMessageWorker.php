<?php

namespace App\Console\Commands;

use AliyunMNS\Client;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Requests\SendMessageRequest;
use App\Models\Device;
use \App\Utils\IdGenerator;
use App\Jobs\RefreshIotDeviceStateJob;
use App\Models\Trade;
use Illuminate\Console\Command;

class IotMessageWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mns:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'aliyun iot mns worker';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->index();
    }

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
        $config = $this->getConfig();
        print_r($config);

        //1. 创建客户端
        $this->client = new Client($config['endpoint'], $config['access_key'], $config['access_secret']);

        //2. 获取队列句柄
        $queue = $this->client->getQueueRef($config['queue_name']);

        // 3. 接收消息
        $receiptHandle = NULL;
        try
        {
            // when receiving messages, it's always a good practice to set the waitSeconds to be 30.
            // it means to send one http-long-polling request which lasts 30 seconds at most.
            $res = $queue->receiveMessage($config['wait_seconds']);
            echo "ReceiveMessage Succeed! \n";

            //解码消息
//            [payload] => eyJ0IjoxNTY2NjY2ODk4LCJvcmRlcnMiOlsxOTA4MjQxNzAzMzIyMjAyXSwidG90YWwiOjYwMDAwMCwidXNlZCI6NjAwMDAwLCJmbGFncyI6MywibW90b3JfY291bnQiOjI3MCwibW90b3Jfc2Vjb25kcyI6ODcyNSwiY2xlYW5lcl9jb3VudCI6MTEsImNsZWFuZXJfc2Vjb25kcyI6ODM0LCJkaXNfY291bnQiOjM3LCJkaXNfc2Vjb25kcyI6MTk4OCwid2F0ZXJfY291bnQiOjIyLCJ3YXRlcl9zZWNvbmRzIjoyMzA5NCwidGFwX2NvdW50IjowfQ==
//            [messagetype] => upload
//            [topic] => /b1esXZaKJvO/19022103/put
//            [messageid] => 1165190766729961984
//            [timestamp] => 1566638101
//            [topic] => /as/mqtt/status/b1esXZaKJvO/19032153
            $msg = json_decode($res->getMessageBody(), true);
            print_r($msg);

            //获取设备信息
            $device = $this->getDeviceByTopic($config['product_key'], $msg['topic']);

            //处理消息
            $this->handleMessage($device, $msg);

            //获取消息处理句柄
            $receiptHandle = $res->getReceiptHandle();
        }
        catch (MnsException $e)
        {
            echo "ReceiveMessage Failed: " . $e;
            return;
        }

//        // 4. 删除消息
//        try
//        {
//            if ($receiptHandle) {
//                $res = $queue->deleteMessage($receiptHandle);
//                echo "DeleteMessage Succeed! \n";
//            }
//        }
//        catch (MnsException $e)
//        {
//            echo "DeleteMessage Failed: " . $e;
//        }
    }

    public function getConfig()
    {
        $config = [
            'driver'        => 'mns',
            'access_key'     => config('aliyun_iot.access_key_id'),
            'access_secret' => config('aliyun_iot.access_secret'),
            'endpoint'      => config('aliyun_iot.endpoint'),
            'queue_name'    => config('aliyun_iot.queue_name'),
            'product_key'   => config('aliyun_iot.product_key'),
            'wait_seconds'  => config('aliyun_iot.wait_seconds'),
        ];

        return $config;
    }

//    根据Topic获取设备信息
//    /b1esXZaKJvO/19022103/put
//    /as/mqtt/status/b1esXZaKJvO/19032153
    private function getDeviceByTopic($productKey, $topic)
    {
        $device = null;
        $begin = strstr($topic, $productKey);
        $items = explode('/', $begin);
        $device = $this->getDevice($items[0], $items[1]);
        return $device;
    }

//    获取设备信息
    private function getDevice($productKey, $deviceName)
    {
        $device = Device::where('product_key', $productKey)
            ->where('device_name', $deviceName)->first();
        return $device;
    }

//    刷新设备状态
    private function refreshDeviceNetworkState($device)
    {
        RefreshIotDeviceStateJob::dispatch($device->id)->delay(3);
    }

//    处理物联网设备消息
    public function handleMessage($device, $msg)
    {
        print_r($msg);
        $msgType = $msg['messagetype'];
        $payload = json_decode(base64_decode($msg['payload']), true);

        switch ($msgType) {
            case 'status':
                $this->onStatusMessage($device, $payload);
                break;
            case 'upload':
                $this->onUploadMessage($device, $payload);
                break;
            default:
                print_r("msgType: $msgType");
                break;
        }
    }

//    设备上下线消息
    private function onStatusMessage($device, $payload)
    {
        echo "refresh device status";
        switch ($payload['status']) {
            case 'online':
                $device->is_online = true;
                break;
            case 'offline':
                $device->is_online = false;
                break;
            default:
                break;
        }
        $device->save();

        //由于消息不能保证时序性，所以需要稍后重新查询一次设备的网络状态，否则可能出现显示状态不对。
        $this->refreshDeviceNetworkState($device);
        print_r($payload);
    }

//    上传来的消息
    private function onUploadMessage($device, $payload)
    {
        echo "upload message";
        if (!$payload) {
            return;
        }

        print_r($payload);

        if (array_key_exists('event', $payload))
        {
            switch ($payload['event']) {
                case 'coin_event':
                    $this->onCoinEvent($device, $payload);
                    break;
                case 'card_event':
                    $this->onCardMessage($device, $payload);
                    break;
                case 'banknote_event':
                    $this->onBanknoteEvent($device, $payload);
                    break;
                case 'enter_work_state':
                    $this->onEnterWorkState($device, $payload);
                    break;
                case 'exit_work_state':
                    $this->onExitWorkState($device, $payload);
                    break;
                default:
                    break;
            }
        }
        else {
//    upload messageArray
//    (
//        [t] => 1566668058
//    [orders] => Array
//    (
//        [0] => 1908241715152152
//    )
//    [total] => 600000
//    [used] => 600000
//    [flags] => 3
//    [motor_count] => 49
//    [motor_seconds] => 1487
//    [cleaner_count] => 2
//    [cleaner_seconds] => 152
//    [dis_count] => 6
//    [dis_seconds] => 279
//    [water_count] => 3
//    [water_seconds] => 3619
//    [tap_count] => 0
//    )
            if (array_key_exists('total', $payload) && array_key_exists('used', $payload)) {
                $this->onCarWashFinished($device, $payload);
            }
        }
    }

//    投币事件
    private function onCoinEvent($device, $payload)
    {

    }

//    纸币事件
    private function onBanknoteEvent($device, $payload)
    {

    }

//    进入工作状态
    private function onEnterWorkState($device, $payload)
    {

    }

//    退出工作状态
    private function onExitWorkState($device, $payload)
    {

    }

//    刷卡消息
    private function onCardMessage($device, $payload)
    {

    }

    private function onCarWashFinished($device, $payload)
    {
        $order_id = null;
        if (array_key_exists('order_id', $payload)) {
            $order_id = $payload['order_id'];
        }
        else if (array_key_exists('orders', $payload))
        {
//            print_r($payload['orders']);
            $order_id = $payload['orders'][0];
        }

        if ($order_id) {
            echo "update order info";
            try {
                \DB::enableQueryLog();
                $order = Trade::findOrFail($order_id);
                print_r($order->toArray());
                $order->meta = $payload;
                $order->save();
            } catch (\Exception $exception) {
                $logs = \DB::getQueryLog();
                \DB::disableQueryLog();
                print_r($logs);
                print_r($exception);
            }
        }
        else {
            echo "create order info";
            $trade = new Trade();
            $trade->id = IdGenerator::tradeId();
            $trade->device_id = $device->id;
            $trade->owner_id = $device->owner_id;
            $trade->payment_type = Trade::PaymentType_IcCard;
            $trade->payment_status = Trade::PaymentStatus_Success;
            $trade->confirm_status = Trade::GoodsStatus_Confirmed;
            $trade->refund_status = Trade::RefundStatus_None;
            $trade->withdraw_status = Trade::WithdrawState_Disable;
            $trade->meta = json_encode($payload);
            $trade->saveOrFail();
        }
    }
}
