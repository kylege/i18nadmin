<?php
include 'includes.php';

$all_tags = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_TAGS, array());
foreach ($all_tags as $key => $value) {
	$tags[$value['key']] = $value['value'];
}
$langs = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_ITEMS, 
	array('la_id'=>array('$gt'=>0)), array( 'limit'=>100, 'sort'=>array('la_id', 1))
	);

//列出前100条语言
include __DIR__.'/templates/index.html';