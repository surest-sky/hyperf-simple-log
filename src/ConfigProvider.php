<?php
/**
 * Created by PhpStorm.
 * User: surestdeng
 * Date: 2020/3/15
 * Time: 21:34:20
 */
namespace Surest\SimpleLog;

class ConfigProvider
{
    public function __invoke() :array
    {
        return  [
            'enable'     => env('ZLOG_ENABLE', true),
            'moduleName' => env('SERVICE_NAME', 'xiaoe_pe'),
            'path'       => "./data/logs/" . env('SERVICE_NAME'),
            'call'       => [
                'to'         => env('ZLOG_CALL_SINK_TO', 'noop'),
                'url'        => env('ZLOG_CALL_SINK_URL'),
                'maxFiles'   => env('ZLOG_CALL_MAX_FILES', 3),
                'samplingPr' => env('ZLOG_CALL_SAMPLING_PR', 1),
            ],
            'log'        => [
                'maxFiles' => env('ZLOG_MAX_FILES', 3),
                'level'    => env('ZLOG_MIN_LEVEL', 'info'),
            ],
            'request'    => [
                'maxFiles'   => env('ZLOG_REQUEST_MAX_FILES', 3),
                'samplingPr' => env('ZLOG_REQUEST_SAMPLING_PR', 1),
            ],
            'exception'  => [
                'maxFiles' => env('ZLOG_EXCEPTION_MAX_FILES', 3),
            ],
        ];
    }
}
