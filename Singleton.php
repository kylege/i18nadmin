<?php

/**
 * singleton
 * @package mxcommon
 */

/**
 * 单实例类
 * 
 * 如果想要其它类也为单实例的，则继承此类，然后通过getInstance方法获取实例
 * 
 * 要求php5.3以上
 * 
 * 示例：
 * 
 * 	class Foobar extends Singleton {};
 *	
 *	$foo = Foobar::getInstance();
 * 
 * 注意，在php中应慎用单实例模式
 * @author chenming
 * @package mxcommon_lib
 */

class Singleton {
	/**
	 * instance
	 * @var object
	 */
	protected static $instance = array();
	/**
	 * construct
	 */
	protected function __construct(){
		//Thou shalt not construct that which is unconstructable!
	}
	/**
	 * clone
	 * @return [type] [description]
	 */
	protected function __clone(){
		//Me not like clones! Me smash clones!
	}
	/**
	 * get instance
	 * @return object return instance
	 */
	public static function getInstance(){
		$called_class_name = get_called_class();
		if (!isset($_instance[$called_class_name])){
			$_instance[$called_class_name] = new $called_class_name();
			$_instance[$called_class_name]->init();
			return $_instance[$called_class_name];				
		}
	}
	
	/**
	 * init
	 */
	public function init(){
		
	}	
}

?>