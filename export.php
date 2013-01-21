<?php

//将指定tag下面所有语言导出成指定代码格式文件，并打包下载

include  __DIR__. '/includes.php';

$all_tags = MongoHelper::getInstance()->find(Otable::DB_LANG, Otable::TABLE_TAGS, array());
foreach ($all_tags as $key => $value) {
	$tags[$value['key']] = $value['value'];
}

include __DIR__.'/templates/export.html';