<?php

namespace thinkPlus\exception;

use Exception;
use think\exception\Handle as ThinkHandle;
use Throwable;

class Handle extends ThinkHandle
{
    /**
     * Report or log an exception.
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {

        if ($this->isIgnoreReport($exception)) {
            return;
        }

        // 收集异常数据
        if ($this->app->isDebug()) {
            $data = [
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'message' => $this->getMessage($exception),
                'code'    => $this->getCode($exception),
            ];
        } else {
            $data = [
                'code'    => $this->getCode($exception),
                'message' => $this->getMessage($exception),
            ];
        }

        $log = $data;


        if ($this->app->config->get('log.record_trace')) {
            $log['trace'] = $exception->getTrace();
        }

        try {
            $this->app->log->record($log, 'error');
        } catch (Exception $e) {
        }
    }
}