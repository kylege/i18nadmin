<?php

include '../includes.php';
include '../model/ExportLang.php';

$tag = $_GET['tag'];
$codetype = $_GET['codetype'];

$zip_file_name = __DIR__.'/../data/'.$tag . '.zip';

$all_langs = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_LANGS,
	array()
	);

$all_items = MongoHelper::getInstance()->find( Otable::DB_LANG, Otable::TABLE_ITEMS,
	array('tags'=>array('$in'=>array($tag)) ),
	array('sort'=>array('la_id'=>-1) )
	);

if(empty($all_items)){
	exit('未找到');
}

$zip = new ZipArchive;
$res = $zip->open($zip_file_name, ZipArchive::OVERWRITE);

if( $res !== true){
	die(json_encode(array('reuslt'=>false, 'msg'=>'创建zip文件失败')));
}

$export = new ExportLang();
foreach ($all_langs as $key => $value) {
	$string = $export->export2String($codetype, $all_items, $value['key']);
	$filename = $export->file_name;
	$zip->addFromString($filename, $string);
}

$zip->close();

header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.$tag .'-'.$codetype . '.zip');
header('Content-Length: ' . filesize($zip_file_name));
readfile($zip_file_name);