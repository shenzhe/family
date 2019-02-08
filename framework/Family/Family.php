<?php
//file: framework/Family/family.php
namespace Family;

use Family\Core\Config;
use Family\Core\Log;
use Family\Core\Route;
use Family\Coroutine\Context;
use Family\Coroutine\Coroutine;
use Family\Helper\Template;
use Swoole;


class Family
{

    /**
     * @var 根目录
     */
    public static $rootPath;
    /**
     * @var 框架目录
     */
    public static $frameworkPath;
    /**
     * @var 程序目录
     */
    public static $applicationPath;

    final public static function run()
    {
        try {
            if (!defined('DS')) {
                define('DS', DIRECTORY_SEPARATOR);
            }
            self::$rootPath = dirname(dirname(__DIR__));
            self::$frameworkPath = self::$rootPath . DS . 'framework';
            self::$applicationPath = self::$rootPath . DS . 'application';

            //先注册自动加载
            \spl_autoload_register(__CLASS__ . '::autoLoader');
            //加载配置
            Config::load();

            $timeZone = Config::get('time_zone', 'Asia/Shanghai');
            \date_default_timezone_set($timeZone);

            //通过读取配置获得ip、端口等
            $http = new Swoole\Http\Server(Config::get('host'), Config::get('port'));
            $http->set(Config::get('swoole_setting'));
            $http->on('start', function (\swoole_server $serv) {
                //服务启动
                //日志初始化
                Log::init();
                file_put_contents(self::$rootPath . DS . 'bin' . DS . 'master.pid', $serv->master_pid);
                file_put_contents(self::$rootPath . DS . 'bin' . DS . 'manager.pid', $serv->manager_pid);
                Log::info("http server start! {host}: {port}, masterId:{masterId}, managerId: {managerId}", [
                    '{host}' => Config::get('host'),
                    '{port}' => Config::get('port'),
                    '{masterId}' => $serv->master_pid,
                    '{managerId}' => $serv->manager_pid,
                ]);
            });

            $http->on('shutdown', function () {
                //服务关闭，删除进程id
                unlink(self::$rootPath . 'DS' . 'bin' . DS . 'master.pid');
                unlink(self::$rootPath . 'DS' . 'bin' . DS . 'manager.pid');
                Log::info("http server shutdown");
            });
            $http->on('workerStart', function (\swoole_http_server $serv, int $worker_id) {
                if (1 == $worker_id) {
                    if (function_exists('opcache_reset')) {
                        //清除opcache 缓存，swoole模式下其实可以关闭opcache
                        \opcache_reset();
                    }
                }
                try {
                    //加载配置，让此处加载的配置可热更新
                    Config::loadLazy();
                    //日志初始化
                    Log::init();
                    $mysqlConfig = Config::get('mysql');
                    if (!empty($mysqlConfig)) {
                        //配置了mysql, 初始化mysql连接池
                        Pool\Mysql::getInstance($mysqlConfig);
                    }

                } catch (\Exception $e) {
                    //初始化异常，关闭服务
                    print_r($e);
                    $serv->shutdown();
                } catch (\Throwable $throwable) {
                    //初始化异常，关闭服务
                    print_r($throwable);
                    $serv->shutdown();
                }
            });
            $http->on('request', function (\swoole_http_request $request, \swoole_http_response $response) {
                //初始化根协程ID
                Coroutine::setBaseId();
                //初始化上下文
                $context = new Context($request, $response);
                //存放容器pool
                Pool\Context::getInstance()->put($context);
                //协程退出，自动清空
                defer(function () {
                    //清空当前pool的上下文，释放资源
                    Pool\Context::getInstance()->release();
                });
                try {
                    //自动路由
                    $result = Route::dispatch();
                    $response->end($result);
                } catch (\Exception $e) { //程序异常
                    Log::exception($e);
                    $context->getResponse()->withStatus(500);
                } catch (\Error $e) { //程序错误，如fatal error
                    Log::exception($e);
                    $context->getResponse()->withStatus(500);
                } catch (\Throwable $e) {  //兜底
                    Log::exception($e);
                    $context->getResponse()->withStatus(500);
                }
            });
            $http->start();
        } catch (\Exception $e) {
            print_r($e);
        } catch (\Throwable $throwable) {
            print_r($throwable);
        }
    }


    /**
     * @param $class
     * @desc 自动加载类
     */
    final public static function autoLoader($class)
    {

        //把类转为目录，eg \a\b\c => /a/b/c.php
        $classPath = \str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

        //约定框架类都在framework目录下, 业务类都在application下
        $findPath = [
            self::$rootPath . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR,
        ];


        //遍历目录，查找文件
        foreach ($findPath as $path) {
            //如果找到文件，则require进来
            $realPath = $path . $classPath;
            if (is_file($realPath)) {
                require "{$realPath}";
                return;
            }
        }
    }
}
