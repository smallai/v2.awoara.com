<?php
/**
 * Created by PhpStorm.
 * User: hongjie
 * Date: 18-5-8
 * Time: 下午3:53
 */

namespace App\Utils;


use Illuminate\Support\Facades\Log;

trait ModelFiledTranslator
{
    static public function tr($field, $filed_value = null)
    {
        try {
            if (property_exists(static::class, 'filed_text') && array_key_exists($field, static::$filed_text)) {
                if (is_null($filed_value))
                    return static::$filed_text[$field];
                else if (property_exists(static::class, 'filed_value_text')
                    && array_key_exists($field, static::$filed_value_text)
                    && array_key_exists($filed_value, static::$filed_value_text[$field]))
                    return static::$filed_value_text[$field][$filed_value];
            }
        } catch (\Exception $exception) {
            Log::error('exception', ['exception' => $exception]);
        }


        return '未知('.$field.'|'.$filed_value.')';
    }
}