<?php
/**
 * Created by PhpStorm.
 * User: surestdeng
 * Date: 2020/3/15
 * Time: 15:28:46
 */
namespace Surest\SimpleLog;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Logger as MLogger;
use Monolog\Processor\PsrLogMessageProcessor;
use Surest\SimpleLog\InvalidArgumentException;
use Surest\SimpleLog\Logger\RequestLogger;

class Logging
{
    public function getRequestLogger()
    {
        $logger = new RequestLogger(getZlogConfig());
        return $logger;
    }

    /**
     * 获取写指定路径的 MLogger 对象
     *
     * @param string $filename
     * @param string $dirname
     * @param int    $maxFiles
     * @param string $filenameFormat
     * @param int    $level
     * @param string $dataFormat
     *
     * @return MLogger
     * @throws InvalidArgumentException
     */
    public function getMLogger($filename, $dirname, $maxFiles = 3, $filenameFormat = '{filename}_{date}', $level = Mlogger::INFO, $dataFormat = 'Y-m-d')
    {
        if ((!is_string($filename)) || (strlen($filename) <= 0)) {
            throw new InvalidArgumentException('\$filename cannot be empty');
        }

        // 非绝对路径则是以默认路径为相对路径
        if (empty($dirname) || (0 == strcmp($dirname, "."))) {
            $dirname = zlogPath();
        } else {
            if (!Str::startsWith($dirname, "/")) {
                $dirname = zlogPath() . "/$dirname";
            }
        }

        $realpath = "{$dirname}/{$filename}.log";
        $handler = new RotatingFileHandler($realpath, $maxFiles, $level);
        $handler->setFilenameFormat($filenameFormat, $dataFormat);
        $handler->setFormatter(new JsonFormatter());
        $handler->pushProcessor(new PsrLogMessageProcessor());

        $logger = new Mlogger($filename);
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @param string $name
     *
     * @return ZLogger
     * @throws InvalidArgumentException
     */
    public function getZLogger($name = 'default') :Logger
    {
        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $zlog = $config->get('zlog');
        $logger = $this->getMlogger($name,
            null,
            Arr::get($zlog, 'log.maxFiles'),
            "biz-{filename}_{date}",
            Arr::get($zlog, 'log.maxFiles')
        );
        return $logger;
    }

    /**
     * 日志输出
     * User: surest
     * Date: 2020/3/16
     * @param $messages
     */
    public function exectionReport(\Throwable $throwable)
    {
        $messages = [
            'message' => $throwable->getMessage(),
            'line' => $throwable->getLine(),
            'file' => $throwable->getFile(),
            'trace' => $throwable->getTraceAsString(),
        ];
        $logger = $this->getZLogger('exection');
        $logger->error('error exection', $messages);
    }
}




