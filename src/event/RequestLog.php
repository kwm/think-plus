<?php

namespace thinkPlus\event;

use \think\fluent\event\RequestLog as RequestLogBase;
use thinkPlus\Response;

class RequestLog extends RequestLogBase
{
    /**
     * @author YangQi
     * @param Response $response
     * @return array|string|void
     */
    protected function getResponseBody($response)
    {
        $body = $response->getContent();

        return method_exists($response, 'getType') && $response->getType() == 'json' ? json_decode($body, true) : $body;
    }
}