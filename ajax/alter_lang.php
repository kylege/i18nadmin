<?php
include '../includes.php';
/**
 * 修改语言内容
 */

$key = $_POST['key'];
$lang = $_POST['lang'];
$content = $_POST['content'];

if(empty($key) || empty($lang) || empty($content)){
	die(json_encode(array('result'=>false, 'msg'=>'提交参数不全')));
}


MongoHelper::getInstance()->update(Otable::DB_LANG, Otable::TABLE_ITEMS, 
	array('key'=>$key),
	array('$set'=>array($lang=>$content))
	);

echo json_encode(array('result'=>true));
