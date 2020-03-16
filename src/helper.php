<?php
/**
 * Created by PhpStorm.
 * User: surestdeng
 * Date: 2020/3/15
 * Time: 15:15:31
 */

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;
use Surest\SimpleLog\Logging;

if (!function_exists('getCurrentRequest')) {
    /**
     * 获取请求
     */
    function getCurrentRequest() {
        $request = ApplicationContext::getContainer()
            ->get(RequestInterface::class);

        return $request;
    }
}

if (!function_exists("getTargetUrl")) {
    /**
     * 获取 target_url 字段
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    function getTargetUrl($request)
    {
        $target_url = getRequestUrl($request);

        // 运行在命令行模式下，则为脚本命令行参数的第一个值，但有可能是 swoole
        if ((0 === strcmp($target_url, "http://:")) && isRunInCliMode()) {
            $target_url = implode(" ", $_SERVER["argv"]);
        }

        return $target_url;
    }
}

if (!function_exists("isRunInCliMode")) {
    /**
     * 是否运行在 CLI 模式
     * @return bool
     */
    function isRunInCliMode()
    {
        return (strpos(php_sapi_name(), 'cli') !== false);
    }
}

if (!function_exists("getApiParams")) {
    /**
     * 获取接口参数及命令行参数
     *
     * @param RequestInterface $request
     *
     * @return array
     */

    function getApiParams($request)
    {
        $params = $request->all();

        // 运行在 CLI 模式下把 $argv 也放进参数中
        if (isRunInCliMode()) {
            $params['argv'] = $_SERVER["argv"];
        }

        return $params;
    }
}

if (!function_exists("getMethod")) {
    /**
     * @param RequestInterface $request
     *
     * @return string
     */
    function getMethod($request)
    {
        $target_url = getRequestUrl($request);

        // 运行在命令行模式下，但有可能是 swoole
        if ((0 === strcmp($target_url, "http://:")) && isRunInCliMode()) {
            return "CLI";
        }

        return $request->getMethod();
    }
}

if (!function_exists("getServerIp")) {
    function getServerIp()
    {
        $default = "127.0.0.1";

        if (!isRunInCliMode()) {
            return isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $default;
        }

        $ip = gethostbyname(gethostname());
        if (false === filter_var($ip, FILTER_FLAG_IPV4)) {
            $ip = $default;
        }

        return $ip;
    }
}

if (!function_exists("getRequestUrl")) {
    /**
     * @param RequestInterface $request
     *
     * @return string
     */
    function getRequestUrl($request)
    {
        /**
         * 由于框架获取 url 的代码可能被瞎改，取了 $_SERVER["SERVER_PORT"]，
         * 但 CLI 模式下这个变量是没有的，会导致异常，这里做下兼容，发生异常的时候返回默认的
         */
        try {
            $url = $request->url();
        } catch (Exception $exception) {
            $url = "http://:";
        }

        return $url;
    }
}

if (!function_exists("getServerPort")) {
    function getServerPort()
    {
        if (!isRunInCliMode()) {
            return isset($_SERVER["SERVER_PORT"]) ? $_SERVER["SERVER_PORT"] : null;
        }

        return null;
    }
}

if (!function_exists("getClientIp")) {
    function getClientIp()
    {
        if (isRunInCliMode()) {
            return "127.0.0.1";
        }

        if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = Request::ip();
        }

        return $ip;
    }
}

if (!function_exists("getResponseContent")) {
    /**
     * @param \Illuminate\Http\Response $response
     *
     * @return string
     */
    function getResponseContent($response)
    {
        if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            $content = "binary: " . $response->getFile()->getRealPath();
        } else if ($response instanceof \Symfony\Component\HttpFoundation\RedirectResponse) {
            $content = "redirect: " . $response->getTargetUrl();
        } else {
            // 截取 1024 的返回
            // $content 有可能是二进制字符串，可能会导致 json_encode 失败，但没有找到靠谱的方法检测是否是二进制字符串，折中检查下是否 utf8 编码
            $content = $response->getContent();
            if (mb_check_encoding($content, "UTF-8")) {
                $content = mb_substr($content, 0, 1024);
            } else {
                $content = "content isn't encode by UTF-8, may be bin string or other thing else";
            }
        }

        return $content;
    }
}


if (!function_exists('requestLogger')) {
    /**
     * 返回请求日志信息
     * @return \Surest\SimpleLog\Logger\RequestLogger
     */
    function requestLogger()
    {
        return (new Logging())->getRequestLogger();
    }
}

if (!function_exists('getZlogConfig')) {
    function getZlogConfig($key = "", $default = null)
    {
        $realkey = "zlog";

        if (!empty($key)) {
            $realkey .= ".{$key}";
        }

        return config($realkey, $default);
    }
}

if (!function_exists("nowToEsTime")) {
    function nowToEsTime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msec = round($msec * 1000, '0');

        return date('Y-m-d\TH:i:s') . '.' . $msec . "Z";
    }
}

if (!function_exists('zlogPath')) {
    function zlogPath()
    {
        return getZlogConfig('path');
    }
}
