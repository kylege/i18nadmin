<?php

class ExportLang {

	public $code_types = array('php','js');

	public $file_header_prefix = array(
		'php' =>"<?php return array( \r\n",
		'js'  =>"MX_LANG = $.extend(window.MX_LANG || {},{ \r\n",
		);

	public $file_footer_prefix = array(
		'php' =>"\r\n ); ",
		'js'  =>"\r\n }); "
		);

	public $key_prefix = array(
		'php' =>"'",
		'js'  =>"'",
		);

	public $value_prefix = array(
		'php' => "'=>'",
		'js'  => "':'",
		);

	public $line_end = array(
		'php' =>"',",
		'js'  =>"',",
		);

	public $file_name = '';

	/**
	 * 将语言数组导出成相应的代码
	 * @param  string $code_type   代码类型，php或js
	 * @param  array $lang_items   语言包数组
	 * @param  string $lang        要导出哪种语言
	 * @return string              返回组装好的字符串
	 */
	public function export2String($code_type, $lang_items, $lang){
		if(!in_array($code_type, $this->code_types)){
			throw new Exception('wrong code type.', 1);
		}
		$file_string = $this->file_header_prefix[$code_type];
		$all_count = count($lang_items);
		for($i=0; $i<$all_count; $i++){

			$value = $lang_items[$i];
			$item_key = trim($value['key']);
			$item_content = isset($value[$lang]) ? $value[$lang] : '';
			$item_content = trim($item_content);

			$line = $this->key_prefix[$code_type] . $item_key . $this->value_prefix[$code_type] . $item_content . $this->line_end[$code_type];
			
			if($i == $all_count-1){
				$line = rtrim($line, ',');
			}

			$file_string = $file_string ."\t\t". $line. "\r\n";
		}
		$file_string = $file_string . $this->file_footer_prefix[$code_type];

		$this->getFileName($code_type, $lang);

		return $file_string;
	}

	public function getFileName($code_type, $lang){

		if($code_type == 'php'){
			$this->file_name = 'Lang'.strtoupper($lang).'.php';

		}else if($code_type == 'js') {
			$this->file_name = 'lang.'.strtolower($lang).'.js';
		}

		return $this->file_name;
	}
}