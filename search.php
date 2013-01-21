<?php
include 'includes.php';

$search_key = $_GET['s'];

$all_tags = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_TAGS, array());
foreach ($all_tags as $key => $value) {
	$tags[$value['key']] = $value['value'];
}

$regexObj = new MongoRegex('/'.$search_key.'/');
$find_condition = array('$or'=>
	array(array('key'=>$regexObj), 
	array('cs'=>$regexObj), 
	array('tags'=>array('$in'=>array($search_key))))
);

$langs = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_ITEMS, 
	$find_condition, array( 'sort'=>array('la_id', 1))
	);


include __DIR__.'/templates/index.html';

//搜索，key和value都搜索