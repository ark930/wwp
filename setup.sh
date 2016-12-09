#!/usr/bin/env bash

# 设置图片软链接
rm $PWD/public/img
ln -s $PWD/storage/app/img $PWD/public/img
