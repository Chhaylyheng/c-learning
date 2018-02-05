<?php

class Strfunc
{
	public static function vdumpStr($obj)
	{
		ob_start();
		var_dump($obj);
		$dump = ob_get_contents();
		ob_end_clean();
		return $dump;
	}
	public static function randStr($length = 8)
	{
		static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
		$str = '';
		for ($i = 0; $i < $length; ++$i) {
			$str .= $chars[mt_rand(0, 61)];
		}
		return $str;
	}
}