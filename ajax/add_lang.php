<?php

//添加语言
include './../includes.php';
header ( "Content-type:text/html;charset=utf-8" );

//post参数

$cs = isset($_POST['cs'])?$_POST['cs']:"";
$tags = isset($_POST['tags'])?$_POST['tags']:"";

if(empty($cs) || empty($tags))
{
	$res = array('result'=>false,'msg'=>'cs and tags must post.');
	echo json_encode($res);
	exit;
}

$tags_arr = explode(",",$tags);

//获取最大的la_id
$res_la = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_ITEMS, array('la_id'=>array('$exists'=>true)),array('sort'=>array('la_id'=>-1),'limit'=>1));

$last_id = $res_la[0]['la_id']+1;
$key = 'la_' . $last_id;

$record = array(
	'la_id' => (int)$last_id,
	'cs'    => $cs,
	'key'   =>$key,
	'tags'  =>$tags_arr,
	);

MongoHelper::getInstance()->insert(Otable::DB_LANG, Otable::TABLE_ITEMS, $record);

echo json_encode(array('result'=>true));