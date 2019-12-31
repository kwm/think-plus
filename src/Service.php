<?php

namespace thinkPlus;

class Service
{
    public $bind = [
        'response'                      => Response::class,
        'think\fluent\event\RequestLog' => event\RequestLog::class,
        'think\exception\Handle'        => exception\Handle::class,
    ];
}
