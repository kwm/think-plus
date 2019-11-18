<?php

namespace thinkPlus;

use think\Container;
use think\Cookie;
use think\facade\Config;
use think\facade\Request;
use think\Response as ThinkResponse;
use ReflectionClass;

class Response extends ThinkResponse
{
    /**
     * @var string 输出类型，为空是自动获取，可指定：json|jsonp|xml
     */
    protected $type = '';

    public function __construct(Cookie $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * 创建Response对象
     * @param string $data 输出数据
     * @param string $type 输出类型
     * @param int    $code 状态码
     * @return $this|object
     */
    public static function create($data = '', string $type = '', int $code = 200): ThinkResponse
    {
        /** @var $this $instance */
        $instance = Container::getInstance()->get('response');

        $instance->data($data)
                 ->type($type ?: $instance->getType())
                 ->code($code ?: $instance->getCode());

        return $instance;
    }

    /**
     * 处理数据
     * @access protected
     * @param mixed $data 要处理的数据
     * @return mixed
     * @throws
     */
    protected function output($data)
    {
        if ($driver = $this->driver()) {
            $driver->options($this->options);
            $data = $driver->output($data);

            $reflect = new ReflectionClass($driver);
            $prop    = $reflect->getProperty('contentType');
            $prop->setAccessible(true);

            $this->contentType($prop->getValue($driver));
        } //没有设置有效的数据处理方式，且数据为数组时，转成字符串
        elseif (is_array($data)) {
            $data = var_export($data, true);
        }

        return $data;
    }

    /**
     * 设置页面返回类型
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = strtolower($type);

        return $this;
    }

    /**
     * 获取页面返回类型
     * @return string
     */
    public function getType()
    {
        return $this->type ?: (Request::isJson() ? 'json' : Config::get('app.default_return_type', ''));
    }

    /**
     * 获取当前contentType
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * 根据页面处理类型调用扩展方法
     * @author YangQi
     * @param string $method    方法名
     * @param array  $arguments 参数
     * @return mixed|$this|ThinkResponse
     */
    public function __call($method, $arguments)
    {
        $driver = $this->driver();

        $res = call_user_func_array([$driver, $method], $arguments);

        return $res instanceof ThinkResponse ? $this : $res;
    }

    /**
     * 获取驱动
     * @author YangQi
     * @return static|object
     */
    protected function driver()
    {
        $type  = $this->getType();
        $class = false !== strpos($type, '\\') ? $type : '\\think\\response\\' . ucfirst(strtolower($type));

        return class_exists($class) ? Container::pull($class) : null;
    }
}