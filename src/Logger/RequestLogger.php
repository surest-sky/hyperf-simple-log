<?php
/**
 * Created by PhpStorm.
 * User: surestdeng
 * Date: 2020/3/15
 * Time: 15:33:40
 */
namespace Surest\SimpleLog\Logger;

use Surest\SimpleLog\Constants;
use Surest\SimpleLog\Logging;

class RequestLogger
{
    private $path;

    private $maxFiles;

    private $moduleName;

    private $mlogger;

    private $config;

    /**
     * RequestLogger constructor.
     *
     * @param $config
     *
     * @throws InvalidArgumentException
     */
    public function __construct($config)
    {
        $this->path       = $config['path'];
        $this->maxFiles   = $config['request']['maxFiles'];
        $this->moduleName = $config['moduleName'];
        $this->mlogger    = (new Logging())->getMLogger(Constants::T_REQUEST, 0, $this->maxFiles);
        $this->config = $config;
    }

    /**
     * 上报
     *
     * @param Request   $request
     * @param Response  $response
     * @param XiaoeSpan $rootSpan
     * @param array     $extra
     */
    public function zreport($request, $response, array $extra = [])
    {
        $record              = new \stdClass();
        $record->log_at      = nowToEsTime();
        $record->target_url  = getTargetUrl($request);
        $record->method      = getMethod($request);
        $record->params      = getApiParams($request);
        $record->agent       = $request->header('user-agent');
        $record->module_name = $this->moduleName;
        $record->server_ip   = getServerIp();
        $record->server_port = getServerPort();
        $record->client_ip   = getClientIp();
        $record->extra       = $extra;

        $this->mlogger->info('request-all', (array)$record);
    }
}
