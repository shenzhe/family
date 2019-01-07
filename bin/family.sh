#! /bin/sh
### BEGIN INIT INFO
# Provides:          family application server
# Required-Start:    $remote_fs $network
# Required-Stop:     $remote_fs $network
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: starts family server
# Description:       starts the Family Application daemon
### END INIT INFO


#php路径，如不知道在哪，可以用whereis php尝试
PHP_BIN=`which php`
#bin目录
BIN_PATH=`pwd`
#入口文件
SERVER_PATH=$BIN_PATH/..
#脚本执行地址, 可修改为你的php运行脚本#########
APPLICATION_FILE=$SERVER_PATH/application/index.php

#获取主进程id
getMasterPid()
{
    if [ ! -f "$BIN_PATH/master.pid" ];then
        echo ''
    else
        PID=`cat $BIN_PATH/master.pid`
        echo $PID
    fi
}

#获取管理进程id
getManagerPid()
{
    if [ ! -f "$BIN_PATH/manager.pid" ];then
        echo ''
    else
        MID=`cat $BIN_PATH/manager.pid`
        echo $MID
    fi
}

case "$1" in
        #启动服务
        start)
                PID=`getMasterPid`
                if [ -n "$PID" ]; then
                    echo "server is running"
                    exit 1
                fi
                echo "Starting server "
                $PHP_BIN $APPLICATION_FILE
                echo " done"
        ;;
        #停止服务
        stop)
                PID=`getMasterPid`
                if [ -z "$PID" ]; then
                    echo "server is not running"
                    exit 1
                fi
                echo "Gracefully shutting down server "
                kill $PID
                sleep 1
                if [ -n "$PID" ]; then
                    unlink $BIN_PATH/master.pid
                    unlink $BIN_PATH/manager.pid
                fi
                echo " done"
        ;;
        #查看状态
        status)
                PID=`getMasterPid`
                if [ -n "$PID" ]; then
                    echo "server is running"
                else
                    echo "server is not running"
                fi
        ;;
        #退出
        force-quit)
                $0 stop
        ;;
        #重启
        restart)
                $0 stop
                $0 start

        ;;
        #reload
        reload)
                MID=`getManagerPid`
                if [ -z "$MID" ]; then
                    echo "server is not running"
                    exit 1
                fi
                echo "Reload server ing... $MID"
                kill -USR1 $MID
                echo " done"
        ;;
        #reload task进程
        reloadtask)

                MID=`getManagerPid`

                if [ -z "$MID" ]; then
                    echo "server is not running"
                    exit 1
                fi
                echo "Reload task ing..."
                kill -USR2 $MID
                echo " done"
        ;;
        #提示
        *)
                echo "Usage: $0 {start|stop|force-quit|restart|reload|status}"
                exit 1
        ;;
esac