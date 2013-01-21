<?php

//添加语言
include  __DIR__. '/includes.php';

$all_tags = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_TAGS, array());
foreach ($all_tags as $key => $value) {
	$tags[$value['key']] = $value['value'];
}

include __DIR__.'/templates/add_lang.html';