#!/bin/bash
DIR=$(cd `dirname $0`; pwd)
checkExt=php
checkTplExt=twig
fswatch $DIR/.. | while read file
do
    filename=$(basename "$file")
    extension="${filename##*.}"
    #php文件改动，则reload
    if [ "$extension" == "$checkExt" ];then
        #reload代码
        $DIR/family.sh reload
    fi

    #模板文件改动，则reload
    if [ "$extension" == "$checkTplExt" ];then
        #reload代码
        $DIR/family.sh reload
    fi
done

