<?php
//framework/Family/family.php
namespace Family;

use Family\Core\Config;
use Family\Core\Route;
use Swoole;


class Family
{

    final public static function run()
    {
        //先注册自动加载
        \spl_autoload_register(__CLASS__ . '::autoLoader');
        //加载配置
        Config::load();

        //通过读取配置获得ip、端口等
        $http = new Swoole\Http\Server(Config::get('host'), Config::get('port'));
        $http->set([
            "worker_num" => Config::get('worker_num'),
        ]);
        $http->on('request', function ($request, $response) {
            //自动路由
            $result = Route::dispatch($request->server['path_info']);
            $response->end($result);
        });
        $http->start();
    }


    /**
     * @param $class
     * @desc 自动加载类
     */
    final public static function autoLoader($class)
    {
        //定义rootPath
        $rootPath = dirname(dirname(__DIR__));

        //把类转为目录，eg \a\b\c => /a/b/c.php
        $classPath = \str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

        //约定框架类都在framework目录下, 业务类都在application下
        $findPath = [
            $rootPath . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR,
            $rootPath . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR,
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
