<?php

include '../includes.php';

//修改某个语言的tagtou

$key = $_POST['key'];

$tags = trim($_POST['tags']);
if(empty($tags)){
	die(json_encode(array('result'=>true)));
}

$tags = explode(',', $tags);

if(!empty($tags)){
	MongoHelper::getInstance()->update(Otable::DB_LANG, Otable::TABLE_ITEMS,
		array('key'=>$key),
		array('$set'=>array('tags'=>$tags))
		);
}

echo json_encode(array('result'=>true));