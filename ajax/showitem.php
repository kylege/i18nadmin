<?php

include '../includes.php';

//显示指定key的语言所有语言版本

$key = $_GET['key'];

$res = MongoHelper::getInstance()->findOne(Otable::DB_LANG, Otable::TABLE_ITEMS,
	array('key'=>$key)
	);
if (empty($res) ) {
	exit(json_encode(array('result'=>false, 'msg'=>'不存在该记录')));
}

echo json_encode($res);