<?php

namespace App\Utils;

use App\Models\Device;
use App\Scopes\DeviceScope;
use Illuminate\Support\Facades\Log;
use Mrgoon\AliIot\AliIot;

class IotDevice
{
    private $device = null;
    private $iot_service = null;
    private $request = null;
    private $response = null;
    private $responsePayload = null;

    public function __construct($device_id)
    {
        $this->device = Device::findOrFail($device_id);
        $accessKey = config('aliyun_iot.access_key_id');
        $accessSecret = config('aliyun_iot.access_secret');
        Log::debug("iot@ $accessKey $accessSecret");
        $this->iot_service = new AliIot($accessKey, $accessSecret);
    }

    /*
     * 读取配置项
     * */
    public function readConfig()
    {
        $response = $this->callMethod('read_config', []);
        return $this->responsePayload;
    }

    /*
     * 写入配置项
     * */
    public function writeConfig(array $config)
    {
        $response = $this->callMethod('write_config', $config);
        return ($response) && (0 === $this->responsePayload['status']);
    }

    /*
    {
        "RequestId": "364734D3-E7E9-47CF-BB55-62E06C769416",
        "DeviceInfo": {
            "DeviceId": "AA80i0MXZRQkVxhBegfU",
            "DeviceName": "20180326007",
            "ProductKey": "JX1Dg3BjVlv",
            "DeviceSecret": "Al5LOIy7jUyf9nbCnH9qZwJIrNgw96lG",
            "GmtCreate": "2018-03-26 22:23:15",
            "GmtModified": "2018-04-29 21:50:38",
            "DeviceStatus": "ONLINE"
        },
        "Success": true
    }
    */
    public function isOnline()
    {
		//debugbar()->debug($this->device);
		//debugbar()->debug($this->iot_service);
        $response = $this->iot_service->queryDeviceByName($this->device['product_key'], $this->device['device_name']);
        Log::debug('isOnline', ['device' => $this->device, 'response' => $response]);
        return (isset($response) && isset($response->DeviceInfo) && ('ONLINE' == $response->DeviceInfo->DeviceStatus));
    }

    public function netOrder($orderId, $seconds)
    {
         $response = $this->callMethod('net_order', [
            'order_id' => ''.$orderId,
            'device_used_seconds' => $seconds,
         ]);
//         return $response;
         Log::debug('push order', ['orderId' => $orderId, 'seconds' => $seconds]);
         return ($response) && (0 === $this->responsePayload['status']);
    }

    public function orderFinish()
    {
        $response = $this->callMethod('finish_order', [
        ]);
//         return $response;
        return ($response) && (0 === $this->responsePayload['status']);
    }

    public function ping()
    {
        $response = $this->callMethod('ping', [
        ]);
        \Log::debug('response', ['response' => $response]);
        return ($response) && (0 === $this->responsePayload['status']);
    }

    /*
    {
        "MessageId": 990786283523571700,
        "RequestId": "BBA62381-D615-4A17-86F8-B04E717FE030",
        "PayloadBase64Byte": "ewoJInN0YXR1cyI6CTAsCgkicGF5bG9hZCI6CXsKCX0KfQ==",
        "Success": true,
        "RrpcCode": "SUCCESS"
    }
     +"ErrorMessage": "HSFServiceAddressNotFoundException-"
  +"RequestId": "E5B8443A-E2C8-458B-8567-784D63F94979"
  +"Success": false
    */
    private function callMethod($method, $payload = null)
    {
        if ($payload === null) {
            $payload = [];
        }

        $this->request = base64_encode( json_encode([
            'method' => $method,
            'payload' => $payload,
        ]) );
        $this->response= $this->iot_service->rRpc($this->device['product_key'], $this->device['device_name'], $this->request, 5000);
        $success = false;

        Log::debug('rrpc', [
            'device' => $this->device,
            'request' => $this->request,
            'response' => $this->response
        ]);

        if (isset($this->response) && $this->response->Success)
        {
            $success = ($this->response->RrpcCode === 'SUCCESS');
            if ($success)
                $this->responsePayload = json_decode( base64_decode($this->response->PayloadBase64Byte), true);
            else
                $this->responsePayload = null;
        }

        return $success;
    }
}
