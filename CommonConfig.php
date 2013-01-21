<?php

/**
 * 所有模块通用的一些配置
 * 类名必须以Config开头
 * @package mxcommon
 */
/**
 * 哪个环境，所有环境配置写在一起，到时候改这个变量就好了
 * @var string
 * z yx rl gz sh hk sz　几个环境
 * @package mxcommon_config
 */
        const CONFIG_ENV = 'z';
/**
 * 上传文件所在地的uploadid
 * z yx rl gz sh sz hk ...
 * @var string
 * @package mxcommon_config
 */
        const UPLOAD_ID = 'z';

/**
 * mongodb配置
 * @author chenming
 * @package mxcommon_config
 */
class ConfigMongo {

    /**
     * dbmaster
     * @var array
     */
    private static $dbmaster = array(
        'z' => '10.8.8.11:27017',
    );

    /**
     * dbslaves
     * @var array
     */
    private static $dbslaves = array(
        'z' => '10.8.8.11:27017',
    );

    /**
     * replicaset
     * @var array
     */
    private static $replica_set = array(
        'gz' => 'shard1',
    );

    /**
     * get master
     * @return [type] [description]
     */
    public static function getDBMaster() {
        return self::$dbmaster[CONFIG_ENV];
    }

    /**
     * get slaves
     * @return [type] [description]
     */
    public static function getDBSlaves() {
        return self::$dbslaves[CONFIG_ENV];
    }

    /**
     * get replicaset
     * @return [type] [description]
     */
    public static function getReplicaSet() {
        if (isset(self::$replica_set[CONFIG_ENV])) {
            return self::$replica_set[CONFIG_ENV];
        } else {
            return null;
        }
    }

}

?>