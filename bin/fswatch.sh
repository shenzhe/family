#!/bin/bash
DIR=`pwd`
checkExt=php
fswatch $DIR/.. | while read file
do
    filename=$(basename "$file")
    extension="${filename##*.}"
    #php文件改动，则reload
    if [ "$extension" == "$checkExt" ];then
        #reload代码
        $DIR/family.sh reload
    fi
done

