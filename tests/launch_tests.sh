#!/bin/sh

if [ -z "$PHP_FOR_TESTS" ]; then
    PHP_FOR_TESTS=php
fi

echo Version de PHP
php --version

MOCKED_ENV=tests/mocked_Jeedom_env
PLUGIN_NAME=ExtraTemplate

mkdir -p $MOCKED_ENV/plugins
rm -fr $MOCKED_ENV/plugins/*
mkdir $MOCKED_ENV/plugins/$PLUGIN_NAME
mkdir $MOCKED_ENV/plugins/$PLUGIN_NAME/tests
cp -fr core $MOCKED_ENV/plugins/$PLUGIN_NAME
cp -fr desktop $MOCKED_ENV/plugins/$PLUGIN_NAME
cp -fr plugin_info $MOCKED_ENV/plugins/$PLUGIN_NAME
cp -fr tests/testsuite/* $MOCKED_ENV/plugins/$PLUGIN_NAME/tests
cp -fr tests/phpunit.xml $MOCKED_ENV/plugins/$PLUGIN_NAME/phpunit.xml
cp -fr vendor $MOCKED_ENV/plugins/$PLUGIN_NAME

cd $MOCKED_ENV/plugins/$PLUGIN_NAME

$PHP_FOR_TESTS ./vendor/phpunit/phpunit/phpunit --configuration phpunit.xml
