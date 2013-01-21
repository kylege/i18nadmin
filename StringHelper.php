<?php

/**
 * string
 * @package mxcommon
 */

/**
 * string操作类
 * @author chenming
 * @package mxcommon_lib
 */
class StringHelper {

	/**
	 * 获取字符串长度
	 * @param  string $string  要截取的字符串
	 * @param  string $length  要截取的长度
	 * @param  string $dot     结尾添加修饰
	 * @param  string $charset 编码
	 * @return string          返回截取后的字符串
	 */
	public static function subStr($string, $length, $dot = '', $charset='utf-8') {
		if(strlen($string) <= $length) {
			return $string;
		}

		$pre = chr(1);
		$end = chr(1);
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

		$strcut = '';
		if(strtolower($charset) == 'utf-8') {

			$n = $tn = $noc = 0;
			while($n < strlen($string)) {

				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t <= 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}

				if($noc >= $length) {
					break;
				}

			}
			if($noc > $length) {
				$n -= $tn;
			}

			$strcut = substr($string, 0, $n);

		} else {
			for($i = 0; $i < $length; $i++) {
				$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			}
		}

		$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

		$pos = strrpos($strcut, chr(1));
		if($pos !== false) {
			$strcut = substr($strcut,0,$pos);
		}
		return $strcut.$dot;
	}
}

?>