## SimpleLog

这是一个运行在 hyperf框架上的 的日志输出包，可支简单支持配置日志信息等等

## 安装

    composer require 'surest/hyperf-simple-log' -vvv
    
## 启动

    php bin/hyperf.php start

## 目前支持

> 默认使用 JsonFormatter 输出

1、自定义日志配置

2、文件流输出(目前只支持)

3、请求日志输出


## 新增配置项目

备注：暂定

项目新增env配置

    # 项目信息配置
    SERVICE_NAME=blog
    # ZLOG 日志配置
    ZLOG_ENABLE=true                 # 是否开启 zlog：true、false
    ZLOG_MAX_FILES=3                 # 日志文件按天轮转，指定保留多少天的业务日志
    ZLOG_MIN_LEVEL=info              # 日志级别：debug、info、warn、error
    ZLOG_REQUEST_ON=enable         # 是否打开请求日志输出：enable（启用）、disable（禁用）
    
发布配置文件

    php bin/hyperf.php vendor:publish surest/hyperf-simple-log

发布失败情况下

    cp vendor/surest/hyperf-simple-log/src/zlog.php ./config/autoload/zlog.php
    
## 新增全局中间件

1、进入`config/autoload/middlewares.php`, 新增全局路由

    Surest\SimpleLog\Middlewares\SimpleMiddleware::class
    
    example：
    
        use Surest\SimpleLog\Middlewares\SimpleMiddleware;
        
        return [
            'http' => [
                SimpleMiddleware::class
            ],
        ];

## 使用例

1、默认记录所有请求日志
    
    # 关闭请求日志输出
    ZLOG_REQUEST_ON=disable
    
2、日志输出

    $msg = 'dcf';
    $path = __DIR__;
    $logger = Logging::getZLogger('path-info');
    $logger->info('path', compact('path', 'msg'));
    
输出结果:
    
    > request_2020-03-15.log
    {"message":"request-all","context":{"log_at":"2020-03-15T22:11:09.576Z","target_url":"http://blog/test","method":"GET","params":{"a":"1","argv":["bin/hyperf.php","start"]},"agent":"PostmanRuntime/7.23.0","module_name":"blog","server_ip":"127.0.0.1","server_port":null,"client_ip":"127.0.0.1","extra":[]},"level":200,"level_name":"INFO","channel":"request","datetime":{"date":"2020-03-15 22:11:09.576636","timezone_type":3,"timezone":"Asia/Shanghai"},"extra":[]}
    
    > biz-path-info_2020-03-15.log
    {"message":"path","context":{"path":"/var/www/html/xiaoe/hyperf-skeleton/app/Controller","msg":"dcf"},"level":200,"level_name":"INFO","channel":"path-info","datetime":{"date":"2020-03-15 22:11:09.665802","timezone_type":3,"timezone":"Asia/Shanghai"},"extra":[]}
    
## 备注

目前没有深入研究，只是简单的使用`monlog`来输出一些日志 --

～～～





