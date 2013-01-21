<?php
/**
 * mongo
 * @package mxcommon
 */

/**
 * mongodb操作类
 * @author chenming
 * 调用 MongoHelper::getInstance()->find(...);
 * @package mxcommon_lib
 */
class MongoHelper extends Singleton{
	/**
	 * master
	 * @var MongoClient
	 */
	private $master = NULL;
	/**
	 * slaves
	 * @var MongoClient
	 */
	private $slaves = NULL;
	/**
	 * slaves连接备份，当切换到全用master连接操作，完成后，再切回来
	 * @var MongoClient
	 */
	private $slaves_backup = NULL;
	
	/**
	 * 错误消息
	 * @var string
	 */
	public $error;
	
	/**
	 * 构造，需要用到配置
	 */
	function __construct() {
		if(!class_exists('ConfigMongo')){
			throw new Exception("Can not find class ConfigMongo.");
		}
		if(!class_exists('Mongo')){
			throw new Exception("Can not find class Mongo.");
		}
	}
	/**
	 * 开启所有操作都从master走
	 * 一定要在执行完后运行 disableAllMaster
	 * @return bool
	 */
	public function enableAllMaster(){
		if(!empty($this->slaves_backup)){
			return false;
		}
		$this->initMasterConnection();
		$this->slaves_backup = $this->slaves;
		$this->slaves = $this->master;
		return true;
	}
	/**
	 * 禁用　所有操作都从master走，回到正常的读写分离模式
	 * @return bool
	 */
	public function disableAllMaster(){		
		$this->slaves = $this->slaves_backup;
		$this->slaves_backup = NULL;
	}
	
	/**
	 * 从mongoid字符串中提取时间信息
	 * @param  string $mongo_id mongoid
	 * @return int           返回unix格式时间
	 */
	public static function getUnixtimeFromMongoId($mongo_id){
		if(is_object($mongo_id)){
			$mongo_id = $mongo_id.'';
		}
		if(strlen($mongo_id) < 8){
			return -1;
		}
		$time_str = substr($mongo_id, 0, 8);  //前8位为时间信息
		return hexdec($time_str);
	}
	/**
	 * save方法，保存完整记录 
	 * @param string $dbname   
	 * @param string $table_name
	 * @param array $record    
	 * @param array $options   
	 * @return bool
	 */
	public function save($dbname,$table_name,$record,$options=array()){
		$this->initMasterConnection();
		if($options == array()){
			return $this->master->$dbname->$table_name->save($record);
		}else{
			return $this->master->$dbname->$table_name->save($record, $options);
		}		 
	}
	/**
	 * group
	 * @param string $dbname
	 * @param string $table_name
	 * @param array $keys
	 * @param array $initial
	 * @param array $reduce
	 * @param array $options
	 * @return array
	 */
	public function group($dbname,$table_name,$keys, $initial, $reduce,$options=array()){
		$this->initMasterConnection();
		if($options == array()){
			return $this->master->$dbname->$table_name->group($keys, $initial, $reduce);
		}else{
			return $this->master->$dbname->$table_name->group($keys, $initial, $reduce,$options);
		}
		 
	}
	/**
	 * 获取当前错误信息
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}
	/**
	 * 查找一条记录
	 * @param string $dbname
	 * @param string $table_name 表名
	 * @param array $condition 查找条件
	 * @param array $fields 获取字段   
	 * @return array               
	 */
	public function findOne($dbname,$table_name, $condition, $fields=array()) {
		$this->initSlavesConnection();
		MongoCursor::$slaveOkay = true;
		$cursor = $this->slaves->$dbname->$table_name->find($condition, $fields)->slaveOkay(true);
		$cursor->limit(1);
		$result = $cursor->getNext();
		return $result;
	}
	/**
	 * 查找记录
	 * @param string $dbname
	 * @param string $table_name 表名
	 * @param array $query_condition 字段查找条件
	 * @param array $result_condition 查询结果限制条件-limit/sort等
	 * @param array $fields 获取字段
	 * @return array
	 */
	public function find($dbname, $table_name, $query_condition, $result_condition=array(), $fields=array())
	{
		$this->initSlavesConnection();
		MongoCursor::$slaveOkay = true;  //从查询必备
		$cursor = $this->slaves->$dbname->$table_name->find($query_condition, $fields)->slaveOkay(true);
		if (!empty($result_condition['start'])) {
			$cursor->skip($result_condition['start']);
		}
		if (!empty($result_condition['limit'])) {
			$cursor->limit($result_condition['limit']);
		}
		if (!empty($result_condition['sort'])) {
			$cursor->sort($result_condition['sort']);
		}
		$result = array();	
		try {
			while ($cursor->hasNext()) {
				$result[] = $cursor->getNext();
			}
		}
		catch (MongoConnectionException $e) {
			$this->error = $e->getMessage();
			return false;
		}
		catch (MongoCursorTimeoutException $e) {
			$this->error = $e->getMessage();
			return false;
		}
		return $result;
	}
	/**
	 * 返回结果数量
	 * @param string $dbname
	 * @param string $table_name
	 * @param string $query_condition
	 * @param array $fields
	 * @return int
	 */
	public function findCount($dbname, $table_name, $query_condition, $fields=array())
	{
		$this->initMasterConnection();
		$cursor = $this->master->$dbname->$table_name->find($query_condition, $fields);
		return $cursor->count();
	}
	/**
	 * 删除记录
	 * @param string $dbname
	 * @param string $table_name 表名
	 * @param array $condition 删除条件
	 * @param string $options 删除选择-justOne
	 * @return bool
	 */
	public function remove($dbname, $table_name, $condition, $options=true) {
		$this->initMasterConnection();
		try {
			$this->master->$dbname->$table_name->remove($condition, $options);
			return true;
		}
		catch (MongoCursorException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}
	/**
	 * 更新记录
	 * @param string $dbname
	 * @param string $table_name 表名
	 * @param array $condition 更新条件
	 * @param array $newdata 新的数据记录
	 * @param array $options 更新选择-upsert/multiple
	 * @return bool
	 */
	public function update($dbname, $table_name, $condition, $newdata, $options=array()) {
		$this->initMasterConnection();
		$options['safe'] = 1;
		if (!isset($options['multiple'])) {
			$options['multiple'] = 0;
		}
		try {
			$this->master->$dbname->$table_name->update($condition, $newdata, $options);
			return true;
		}
		catch (MongoCursorException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}
	/**
	 * 查询表的记录数
	 * 返回值：表的记录数
	 * @param string $dbname
	 * @param string $table_name
	 * @param array $condition
	 * @return int
	 */
	public function count($dbname, $table_name, $condition) {
		$this->initMasterConnection();
		return $this->master->$dbname->$table_name->count($condition);
	}
	/**
	 * 插入记录
	 * @param string $dbname
	 * @param string $table_name 表名
	 * @param array $record 记录
	 * @return bool
	 */
	public function insert($dbname, $table_name, $record) {
		$this->initMasterConnection();
		try {
			$this->master->$dbname->$table_name->insert($record, array('safe'=>true));
			return true;
		}
		catch (MongoCursorException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}

	/**
	 * 创建索引：如索引已存在，则返回。
	 * @param  string $dbname      db
	 * @param  string $table_name  表名
	 * @param  string $index       索引-array("id"=>1)-在id字段建立升序索引
	 * @param  array  $index_param 其它条件-是否唯一索引等
	 * @return bool              
	 */
	public function ensureIndex($dbname, $table_name, $index, $index_param=array()) {
		$this->initMasterConnection();
		$index_param['safe'] = 1;
		try {
			$this->master->$dbname->$table_name->ensureIndex($index, $index_param);
			return true;
		}
		catch (MongoCursorException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}
	
	/**
	 * 初始化master连接
	 * @return bool
	 */
	private function initMasterConnection(){
		if(!empty($this->master)){
			return true;
		}
		$master_options = array(
				'connect'=>true,
				'persist'=>'moxian',
				'safe'=>true,
		);
		$rep = ConfigMongo::getReplicaSet();
		if(!empty($rep)){
			$master_options['replicaSet'] = $rep;
		}
		try {
			$this->master = new Mongo($this->getMasterConString(), $master_options);
		}catch (MongoConnectionException $e){
			$this->error = $e->getMessage();
			die('Can not connect the mongo master:'.$this->getMasterConString());
			return false;
		}
		return true;
	}
	/**
	 * 初始化slaves连接
	 * @return bool
	 */
	private function initSlavesConnection(){
		if(!empty($this->slaves)){
			return true;
		}
		$slaves_options = array(
				'connect'=>true,
				'persist'=>'moxian',
				'safe'=>true,
				);
		try {
			$this->slaves = new Mongo($this->getSlavesConString(), $slaves_options);
		}catch (MongoConnectionException $e){
			$this->error = $e->getMessage();
			die('Can not connect the mongo slaves: '.$this->getSlavesConString());
			return false;
		}
		return true;		
	}
	/**
	 * 获取slaves连接字符串
	 * @return string
	 */
	private function getSlavesConString() {
		return "mongodb://". ConfigMongo::getDBSlaves()."/?safe=true";
	}
	/**
	 * 获取master连接字符串
	 * @return string
	 */
	private function getMasterConString() {
		return "mongodb://". ConfigMongo::getDBMaster()."/?safe=true";
	}
}

?>