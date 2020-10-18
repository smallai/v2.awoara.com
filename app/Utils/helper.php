<?php

//小数转换为证书
use App\Models\Device;
use App\Models\UserVipCard;
use Illuminate\Support\Carbon;

if (!function_exists('to_float'))
{
    function to_float($value, $precision = 2)
    {
        return number_format($value * 1.0 / pow(10, $precision), $precision, '.', '');
    }
}

if (!function_exists('to_int'))
{
    function to_int($money, $precision = 2)
    {
        $value = 0;

        if (is_string($money))
        {
            $value = round( (float)($money), $precision);
        }

        return (int)($value * pow(10, $precision));
    }
}

if (!function_exists('to_time'))
{
    function to_time($value)
    {
        return sprintf("%02d:%02d", $value/60, $value % 60);
    }
}

//abort(403);
if (!function_exists('role'))
{
    function role($role)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $result = isset($user) && $user->hasRole($role);
        Debugbar::info('role#'.$role.$result);
        return $result;
    }
}

if (!function_exists('isAlipay'))
{
    function isAlipay()
    {
        //return true;
		return strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient');
    }
}

if (!function_exists('isWeChat'))
{
    function isWeChat()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger');
    }
}

if (!function_exists('GetDeviceSnapKey'))
{
    function GetDeviceSnapKey($deviceId)
    {
        return 'device_trades_snap_'.$deviceId;
    }
}

/*
 * 获取用户的会员卡信息
 * */
function GetUserVipCard($user_openid)
{
    return UserVipCard::where('user_openid', $user_openid)
        ->orderBy('created_at', 'desc')
        ->first();
}

/*
 * 会员卡可用状态
 * */
function VipCardIsActive($device, $card)
{
    return ($device !== null)
        && ($card !== null)
        && ($card['owner_id'] === $device['owner_id'])
        && ($card['used_count'] < $card['total_count'])
        && ($card->leftTime() > 0);
}

/*
 * 用户有已经过期的会员卡
 * */
function UserHaveExpiredVipCard($device, $card)
{
    return ($device !== null)
        && ($card !== null)
        && ($card['owner_id'] === $device['owner_id'])
        && ($card['used_count'] < $card['total_count'])
        && ($card['expiration'] < (string)Carbon::now());
}

/*
 * 用户有不适用于当前设备的会员卡
 * */
function UserHaveNotMatchVipCard($card, $device)
{
    return ($device !== null)
        && ($card !== null)
        && ($card['owner_id'] !== $device['owner_id'])
        && ($card['used_count'] < $card['total_count'])
        && ($card['expiration'] > (string)Carbon::now());
}
