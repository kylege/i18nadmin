<?php


include 'includes.php';


$all_langs = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_LANGS, 
	array()
	);

$lang_arr = array();
foreach ($all_langs as $key => $value) {
	$lang_arr[$value['key']] = $value['value'];
}

$cur_lang = 'cs';
$cur_lang_str = $lang_arr[$cur_lang];

if(isset($_GET['lang'])){
	$cur_lang = $_GET['lang'];
	$cur_lang_str = $lang_arr[$cur_lang];
}

if(isset($_GET['key'])){
	$need_translate_item = MongoHelper::getInstance()->findOne(Otable::DB_LANG, Otable::TABLE_ITEMS, 
	array('key'=>$_GET['key'])
	);
}else{
	$find_condition = array( 
		'$or'=>array(
			array($cur_lang=>array('$exists'=>false)), 
			array($cur_lang=>'')
			)
		);
	$need_translate_item = MongoHelper::getInstance()->findOne(Otable::DB_LANG, Otable::TABLE_ITEMS, 
		$find_condition
		);	
}



include 'templates/translate.html';