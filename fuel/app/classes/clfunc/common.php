<?php
class ClFunc_Common
{
	public static function version($type = null)
	{
		if ($type == 'query')
		{
			return '?v='.str_replace('.', '', CL_V);
		}
		return CL_V;
	}

	public static function contentsSum($type = null)
	{
		$sum = self::version($type);
		$path = '/home/cladmin/air_chksum';
		if (is_readable($path))
		{
			$sum = file_get_contents($path);
			$sum = str_replace(array("\r","\n"), '', $sum);
			if ($type == 'query')
			{
				$sum = '?'.$sum;
			}
		}
		return $sum;
	}

	public static function chkDir($dirpath,$create_flg = true)
	{
		$return = false;
		if(is_dir($dirpath))
		{
			$return = true;
		}
		if(!$return)
		{
			if($create_flg)
			{
				self::DirMake($dirpath);
			}
			$return = true;
		}
		return $return;
	}

	public static function strtotime_ja($date)
	{
		$date = preg_replace(array('/年/','/月/','/日/'),array('-','-',''),$date);
		return strtotime($date);
	}

	public static function getCSV($file)
	{
		$data = file_get_contents($file);
		$data = preg_replace("/\r\n|\r|\n/", "\n", $data);
		$data = mb_convert_encoding($data,'UTF-8','sjis-win');
		$temp = tmpfile();
		fwrite($temp,$data);
		rewind($temp);

		$aCSV = array();
		while (($line = fgetcsv($temp, 0)) !== FALSE)
		{
			$aCSV[] = $line;
		}
		fclose($temp);

		return $aCSV;
	}

	public static function stringValidation($val, $require = false, $digit = null, $char = null, $mix = false)
	{
		if ($val == '')
		{
			return ($require)? false:true;
		}

		if (!is_null($digit))
		{
			$iLen = mb_strlen($val,'UTF-8');
			if ($iLen < $digit[0])
			{
				return false;
			}
			if ($iLen > $digit[1])
			{
				return false;
			}
		}

		if (!is_null($char))
		{
			$sPtn = '';
			foreach ($char as $c)
			{
				switch ($c)
				{
					case 'alpha':
						$sPtn .= 'a-zA-Z';
					break;
					case 'uppercase':
						$sPtn .= 'A-Z';
					break;
					case 'lowercase':
						$sPtn .= 'a-z';
					break;
					case 'numeric':
						$sPtn .= '0-9';
					break;
					case 'dots':
						$sPtn .= '\.';
					break;
					case 'commas':
						$sPtn .= '\,';
					break;
					case 'dashes':
						$sPtn .= '\-\_';
					break;
					case 'slashes':
						$sPtn .= '\/';
					break;
					case 'singlequotes':
						$sPtn .= '\\\'';
					break;
					case 'doublequotes':
						$sPtn .= '\"';
					break;
					case 'quotes':
						$sPtn .= '\\\'\"';
					break;
				}
			}
			if ($sPtn != '')
			{
				$sPtn = '/^['.$sPtn.']+$/';

				if (!preg_match($sPtn,$val))
				{
					return false;
				}
			}
			if ($mix)
			{
				// 文字種別不足
				$bHasNum = false;
				$bHasLAlp = false;
				$bHasSAlp = false;
				$bHasMark = false;
				// 文字列を一文字づつ分断
				$aChars = preg_split('//', $val, -1, PREG_SPLIT_NO_EMPTY);

				foreach ($aChars as $sChar) {
					if(is_numeric($sChar))
					{
						$bHasNum = true;
					}
					else if(preg_match("/[A-Z]/",$sChar))
					{
						$bHasLAlp = true;
					}
					else if(preg_match("/[a-z]/",$sChar))
					{
						$bHasSAlp = true;
					}
					else
					{
						$bHasMark = true;
					}
				}
				$iCnt=0;
				if ($bHasNum)  $iCnt++;
				if ($bHasLAlp) $iCnt++;
				if ($bHasSAlp) $iCnt++;
				if ($bHasMark) $iCnt++;
				if ($iCnt < 2) return false;
			}
		}
		return true;
	}

	public static function dateValidation($val, $require = false, $range = null)
	{
		if ($val == '')
		{
			return ($require)? false:true;
		}

		list($y,$m,$d) = explode('/', $val);

		if (!checkdate($m, $d, $y))
		{
			return false;
		}

		if (!is_null($range))
		{
			if (isset($range['min']))
			{
				if (strtotime($val) < strtotime($range['min']))
				{
					return false;
				}
			}
			if (isset($range['max']))
			{
				if (strtotime($val) > strtotime($range['max']))
				{
					return false;
				}
			}
		}

		return true;
	}

	public static function timeValidation($val, $date, $require = false, $range = null)
	{
		if ($val == '')
		{
			return ($require)? false:true;
		}

		list($h,$i,) = explode(':', $val);

		if (!is_null($range))
		{
			if (isset($range['min']))
			{
				if (strtotime($date.' '.$h.':'.$i) < strtotime($range['min']))
				{
					return false;
				}
			}
			if (isset($range['max']))
			{
				if (strtotime($date.' '.$h.':'.$i) > strtotime($range['max']))
				{
					return false;
				}
			}
		}

		return true;
	}

	public static function existsAttend($ctID,$date,$start,$end,$tz = null)
	{
		$tz = ($tz)? $tz:date_default_timezone_get();

		$sDate  = ClFunc_Tz::tz('Y-m-d',null,$date.' '.$start,$tz);
		$sStart = ClFunc_Tz::tz('Y-m-d H:i:00',null,$date.' '.$start,$tz);
		$sEnd   = ClFunc_Tz::tz('Y-m-d H:i:00',null,$date.' '.$end,$tz);

		$aWhere = null;
		$aWhere[] = array('ac.abDate','=',$sDate);
		$aWhere[] = array('ac.acAEnd','>=',$sStart);
		$aWhere[] = array('ac.acAStart','<=',$sEnd);

		$result = Model_Attend::getAttendCalendarFromClass($ctID,$aWhere);
		if (count($result)) return false;
		return true;
	}

	public static function AttendTimeRange($date,$start,$end,$aRange)
	{
		$iS = strtotime($date.' '.$start);
		$iE = strtotime($date.' '.$end);

		if (is_null($aRange))
		{
			return true;
		}

		foreach ($aRange as $aR)
		{
			if ($iS > $aR['start'] && $iS <  $aR['end'])
			{
				return false;
			}
			if ($iE > $aR['start'] && $iE <  $aR['end'])
			{
				return false;
			}
		}
		return true;
	}

	public static function dateDiff($date,$edate = null) {
		# 開始日を数値にする
		if (!is_null($date) && $date != "0000-00-00 00:00:00") {
			$start = strtotime($date);
			$start = mktime(0,0,0,date("n",$start),date("d",$start),date("Y",$start));
		} else {
			return 0;
		}
		# 終了日が無い場合は今日を取得
		if (is_null($edate)) {
			$end = mktime(0,0,0,date("n"),date("d"),date("Y"));
		} else {
			$end = strtotime($edate);
			$end = mktime(0,0,0,date("n",$end),date("d",$end),date("Y",$end));
		}
		# 差を計算
		$diff = floor(($end - $start) / (60*60*24));

		return $diff;
	}

	public static function i5MinFloor($time) {
		if (!$time)
		{
			return '00:00';
		}

		list($h,$m) = explode(':', $time);

		$over = ((int)$m % 5);
		if ($over == 0) {
			return $h.':'.$m;
		}
		return $h.':'.($m - $over);
	}


	public static function endTime($iM = 0)
	{
		$nd = date('j');
		$iET = strtotime('+'.$iM.'minute');
		$ed = date('j',$iET);

		$eH = date('H',$iET);
		$eM = (int)date('i',$iET);

		if ($nd != $ed)
		{
			return false;
		}

		$iOver = ($eM % 5);
		if ($iOver != 0) {
			$eM = $eM - $iOver;
		}

		return $eH.':'.sprintf('%02d',$eM);
	}

	public static function getGeocoding($adrs = '')
	{
		$res = array();
		$req = 'http://maps.google.com/maps/api/geocode/json';
		$adrs = ($adrs)? $adrs:'東京駅';
		$req .= '?address='.urlencode($adrs);
		$req .= '&sensor=false';
		$result = file_get_contents($req);
		# JSON形式から連想配列へ変換
		$aLocation = json_decode($result,true);

		if ($aLocation['status'] == 'OK') {
			$res['lat'] = $aLocation['results']['0']['geometry']['location']['lat'];;
			$res['lon'] = $aLocation['results']['0']['geometry']['location']['lng'];;
		}
		return $res;
	}

	public static function TextToGraphLegend($text = null)
	{
		if (is_null($text))
		{
			return $text;
		}

		$text = str_replace(array("\r","\n"),' ', $text);
		$text = str_replace(array('"'),"\\\"", $text);

		return $text;
	}

	/**
	 * Content-Typeの判別
	 *
	 * @param string $path
	 * @return string
	 */
	public static function GetContentType($path)
	{
		$mime = shell_exec('file -bi '.escapeshellcmd($path));
		$mime = trim($mime);
		$mime = preg_replace('/ [^ ]*/', '', $mime);
		$mime = preg_replace('/\;$/', '', $mime);
		return $mime;
	}

	/**
	 * ファイルの画像判別
	 *
	 * @param unknown $path
	 * @return boolean|string
	 */
	public static function isImg($path)
	{
		if (!file_exists($path))
		{
			return false;
		}
		$type = exif_imagetype($path);
		if (!$type)
		{
			return false;
		}

		switch ($type)
		{
			case IMAGETYPE_GIF:
				return 'gif';
			break;
			case IMAGETYPE_JPEG:
				return 'jpg';
			break;
			case IMAGETYPE_PNG:
				return 'png';
			break;
		}
		return $type;
	}

	public static function OrientationFixedImage($infile, $outfile = null)
	{
		$image = \Image::load($infile);
		$image->config('quality',CL_Q_IMG_QUALITY);

		$exif = @exif_read_data($infile);
		if (isset($exif['Orientation']))
		{
			$orientation = $exif['Orientation'];
			if($image)
			{
				switch ($orientation)
				{
					case 3:
						$image->rotate(180);
					break;
					case 6:
						$image->rotate(90);
					break;
					case 8:
						$image->rotate(-90);
					break;
				}
				$outfile = (!is_null($outfile))? $outfile:$infile;
				$image->save($outfile, 0666);
			}
		}
		return;
	}

	/**
	 * ファイルタイプの判別（S3用）
	 *
	 * @param unknown $path
	 * @return number
	 */
	public static function GetFileType($path)
	{
		$iFileType = 0;
		$sContentType = self::GetContentType($path);
		switch ($sContentType) {
			case 'video/mp4':
			case 'video/webm':
			case 'video/ogg':
			case 'video/quicktime':
			case 'video/mpeg':
			case 'video/3gp':
			case 'video/x-ms-wmv':
			case 'video/x-ms-wvx':
			case 'video/x-ms-wm':
			case 'video/x-ms-wmx':
			case 'video/x-ms-asf':
			case 'video/x-flv':
			case 'video/x-m4v':
				$iFileType = 2;
			break;
			case 'application/octet-stream':
				$sExt = mb_strtolower(pathinfo($path,PATHINFO_EXTENSION));
				if ($sExt == '3gp')
				{
					$iFileType = 2;
				}
			break;
			default:
				if (self::isImg($path))
				{
					$iFileType = 1;
				}
			break;
		}
		return $iFileType;
	}

	/**
	 * ファイルサイズのフォーマット設定
	 *
	 * @param number $size
	 * @param number $dec
	 * @return string
	 */
	public static function FilesizeFormat($size = 0, $dec = 0)
	{
		$units = array('B','KB','MB','GB','TB','PB');
		$i = 0;
		while ($size >= 1024)
		{
			$i++;
			$size = $size / 1024;
		}
		return number_format($size,($i ? $dec : 0)).$units[$i];
	}

	/**
	 * ディレクトリの作成
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public static function DirMake($dir = null)
	{
		if (is_null($dir))
		{
			return false;
		}
		if (is_dir($dir))
		{
			return true;
		}
		$oldmask = umask(0);
		$res = mkdir($dir, 0777, true);
		umask($oldmask);
		return $res;
	}


	/**
	 * ディレクトリの移動
	 *
	 * @param string $dir
	 * @return boolean|string
	 */
	public static function DirMove($dir = null)
	{
		if (is_null($dir))
		{
			return false;
		}
		if (!is_dir($dir))
		{
			return false;
		}
		if (is_dir($dir.'_'))
		{
			self::DirRemove($dir.'_');
		}
		return system('mv '.$dir.' '.$dir.'_');
	}


	/**
	 * ディレクトリの削除
	 *
	 * @param string $dir
	 * @return boolean|string
	 */
	public static function DirRemove($dir = null)
	{
		if (is_null($dir))
		{
			return false;
		}
		if (!is_dir($dir))
		{
			return false;
		}
		return system('rm -rf '.$dir);
	}

	/**
	 * PHPの「mb_convert_kana」風文字列置換え
	 *
	 * 入力文字列の揺らぎの修正を主な目的としていますので「mb_convert_kana」とは
	 * 異なります。
	 *
	 * オプション文字列の先頭から変換関数を順番に実行していきます。
	 *
	 * 再変換時に元の文字列に戻る保障はありません。
	 * 文字数が変わる可能性があります。
	 * 「濁点」「半濁点」の揺らぎの修正をデフォルトで行います。
	 * 「ゕゖ」を「ヵヶ」にデフォルトで変換します。
	 * 「水平タブ(HT)」をスペース4文字に展開します。
	 * 「改行(LF)」以外の制御文字を空文字に変換します。
	 * 半角カタカナは全角カタカナに置き換えられます。
	 *
	 * オプションの相違点
	 * 「h」「H」「K」「k」は存在しません。半角カタカナはデフォルトで
	 * 全角カタカナに変換されます。
	 * 「V」は「う濁」から「は濁」への変換となります。
	 *
	 * ひらがなに無いカタカナは変換しません。
	 * 「ㇰ」「ㇱ」「ㇲ」「ㇳ」「ㇴ」「ㇵ」「ㇶ」「ㇷ」
	 * 「ㇸ」「ㇹ」「ㇺ」「ㇻ」「ㇼ」「ㇽ」「ㇾ」「ㇿ」
	 *
	 * 合成できなかった濁点・半濁点は単独の濁点(U+309B)・半濁点(U+309C)になります。
	 * NFKC正規化では「U+3099（゙）」「U+309A（゚）」ですがフォントによっては
	 * うまく表示されないための対処です。
	 * 「mb_convert_kana」と同じ処理になります。
	 *
	 * http://hydrocul.github.io/wiki/blog/2014/1127-unicode-nfkd-mb-convert-kana.html
	 *
	 * オプションで使用する文字列
	 * r: 「全角」英字を「半角」に変換します。
	 * R: 「半角」英字を「全角」に変換します。
	 * n: 「全角」数字を「半角」に変換します。
	 * N: 「半角」数字を「全角」に変換します。
	 * a: 「全角」英数字記号を「半角」に変換します。
	 * A: 「半角」英数字記号を「全角」に変換します。
	 * s: 「全角」スペースを「半角」に変換します（U+3000 -> U+0020）。
	 * S: 「半角」スペースを「全角」に変換します（U+0020 -> U+3000）。
	 * c: 「全角カタカナ」を「全角ひらがな」に変換します。
	 * C: 「全角ひらがな」を「全角カタカナ」に変換します。
	 * v: 「う濁」を「は濁」に変換します。
	 * V: 「ウ濁」を「ハ濁」に変換します。
	 * Q: 「半角」クォーテーション、「半角」アポストロフィを「全角」に変換します。
	 * q: 「全角」クォーテーション、「全角」アポストロフィを「半角」に変換します。
	 * B: 「半角」バックスラッシュを「全角」に変換します。
	 * b: 「全角」バックスラッシュを「半角」に変換します。
	 * T: 「半角」チルダを「全角」にチルダ変換します。
	 * t: 「全角」チルダを「半角」チルダに変換します。
	 * W: 全角「波ダッシュ」を全角「チルダ」に変換します。
	 * w: 全角「チルダ」を全角「波ダッシュ」に変換します。
	 * P: 「ハイフン、ダッシュ、マイナス」を「全角ハイフンマイナス」に変換します。（U+FF0D）
	 * p: 「ハイフン、ダッシュ、マイナス」を「半角ハイフンマイナス」に変換します。（U+002D）
	 * U: 「U+0021」～「U+007E」以外の「半角」記号を「全角」記号に変換します。
	 * u: 「U+0021」～「U+007E」以外の「全角」記号を「半角」記号に変換します。
	 * M: Aで変換される「半角」記号を「全角」記号に変換します。
	 * m: Aで変換される「全角」記号を「半角」記号に変換します。
	 * X: 「カッコ付き文字」を「半角括弧と中の文字」に展開します。
	 * Y: 集合文字を展開します。（単位文字以外）
	 * Z: 小字形文字を大文字に変換します。（U+FE50～U+FE6B）
	 *
	 * @param String $str 変換する文字列
	 * @param String $opt 変換オプション
	 *
	 * @return String 変換された文字列
	 */
	public static function convertKana($str = '', $opt = '')
	{
		// 変換する文字・オプションが文字列でない場合はそのまま返す
		if (!is_string($str) or ! is_string($opt)) {
			return $str;
		}

		/** ------------------------------------------------------------------------
		 * ここから文字の揺らぎを修正する初期化関数です。
		 * ---------------------------------------------------------------------- */
		$init = function() use(&$str) {
			// 「水平タブ(HT)」をスペース4文字に展開します。
			// 「ゕゖ」を「ヵヶ」に変換します。
			// 「U+3099（゙）」「U+309A（゚）」を単独の濁点
			// 「U+309B（゛）」「U+309C（゜）」に変換します。
			$src = array("\t", '゙', '゚', 'ゕ', 'ゖ');
			$rep = array('    ', '゛', '゜', 'ヵ', 'ヶ');
			$str = str_replace($src, $rep, $str);

			// 半角カタカナを全角カタカナに変換します。
			$str = mb_convert_kana($str, 'KV');

			// 「改行(LF)」以外の制御文字を空文字に変換します。
			$str = preg_replace('/[\x00-\x09\x0b-\x1f\x7f-\x9f]/u', '', $str);
			// unicodoの制御文字を空文字に変換します。
			$decoded = json_decode(
					'["' .
					'\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007' .
					'\u2008\u2009\u200A\u200B\u200C\u200D\u200E\u200F' .
					'\u2028\u2029\u202A\u202B\u202C\u202D\u202E' .
					'\u2060' .
					'\u206A\u206B\u206C\u206D\u206E\u206F' .
					'\uFFF9\uFFFA\uFFFB' .
					'"]', true);
			$decoded = $decoded[0];
			$str = str_replace($decoded, '', $str);

			// 濁点・半濁点付きの文字を一文字に変換します。
			//
			// 「ゔ」は「う゛」に展開されます。
			// 「わ゛」は「う゛ぁ」に変換されます。
			// 「ゐ゛」は「う゛ぃ」に変換されます。
			// 「ゑ゛」は「う゛ぇ」に変換されます。
			// 「を゛」は「う゛ぉ」に変換されます。
			// 「ヷ」「ワ゛」は「ヴァ」に展開されます。
			// 「ヸ」「ヰ゛」は「ヴィ」に展開されます。
			// 「ヹ」「ヱ゛」は「ヴェ」に展開されます。
			// 「ヺ」「ヲ゛」は「ヴォ」に展開されます。
			$multi = array(
					'か゛', 'き゛', 'く゛', 'け゛', 'こ゛',
					'さ゛', 'し゛', 'す゛', 'せ゛', 'そ゛',
					'た゛', 'ち゛', 'つ゛', 'て゛', 'と゛',
					'は゛', 'ひ゛', 'ふ゛', 'へ゛', 'ほ゛',
					'は゜', 'ひ゜', 'ふ゜', 'へ゜', 'ほ゜',
					'ゔ', 'ゝ゛',
					'わ゛', 'ゐ゛', 'ゑ゛', 'を゛',
					'カ゛', 'キ゛', 'ク゛', 'ケ゛', 'コ゛',
					'サ゛', 'シ゛', 'ス゛', 'セ゛', 'ソ゛',
					'タ゛', 'チ゛', 'ツ゛', 'テ゛', 'ト゛',
					'ハ゛', 'ヒ゛', 'フ゛', 'ヘ゛', 'ホ゛',
					'ハ゜', 'ヒ゜', 'フ゜', 'ヘ゜', 'ホ゜',
					'ウ゛', 'ヽ゛',
					'ワ゛', 'ヰ゛', 'ヱ゛', 'ヲ゛',
					'ヷ', 'ヸ', 'ヹ', 'ヺ'
			);
			$single = array(
					'が', 'ぎ', 'ぐ', 'げ', 'ご',
					'ざ', 'じ', 'ず', 'ぜ', 'ぞ',
					'だ', 'ぢ', 'づ', 'で', 'ど',
					'ば', 'び', 'ぶ', 'べ', 'ぼ',
					'ぱ', 'ぴ', 'ぷ', 'ぺ', 'ぽ',
					'う゛', 'ゞ',
					'う゛ぁ', 'う゛ぃ', 'う゛ぇ', 'う゛ぉ',
					'ガ', 'ギ', 'グ', 'ゲ', 'ゴ',
					'ザ', 'ジ', 'ズ', 'ゼ', 'ゾ',
					'ダ', 'ヂ', 'ヅ', 'デ', 'ド',
					'バ', 'ビ', 'ブ', 'ベ', 'ボ',
					'パ', 'ピ', 'プ', 'ペ', 'ポ',
					'ヴ', 'ヾ',
					'ヴァ', 'ヴィ', 'ヴェ', 'ヴォ',
					'ヴァ', 'ヴィ', 'ヴェ', 'ヴォ'
			);

			$str = str_replace($multi, $single, $str);
		};

		/** ------------------------------------------------------------------------
		 * ここからオプションの文字により変換を行う関数です。
		 * ---------------------------------------------------------------------- */
		$convert = function($s) use(&$str) {
			switch ($s) {
				// r: 「全角」英字を「半角」に変換します。
				case 'r':
					$str = mb_convert_kana($str, 'r');
					break;

					// R: 「半角」英字を「全角」に変換します。
				case 'R':
					$str = mb_convert_kana($str, 'R');
					break;

					// n: 「全角」数字を「半角」に変換します。
				case 'n':
					$str = mb_convert_kana($str, 'n');
					break;

					// N: 「半角」数字を「全角」に変換します。
				case 'N':
					$str = mb_convert_kana($str, 'N');
					break;

					// a: 「全角」英数字記号を「半角」に変換します。
					//
					// "a", "A" オプションに含まれる文字は、
					// U+0022, U+0027, U+005C, U+007Eを除く（" ' \ ~ ）
					// U+0021 - U+007E の範囲です。
				case 'a':
					$str = mb_convert_kana($str, 'a');
					break;

					// A: 「半角」英数字記号を「全角」に変換します 。
					//
					// "a", "A" オプションに含まれる文字は、
					// U+0022, U+0027, U+005C, U+007Eを除く（" ' \ ~ ）
					// U+0021 - U+007E の範囲です。
				case 'A':
					$str = mb_convert_kana($str, 'A');
					break;

					// s: 「全角」スペースを「半角」に変換します（U+3000 -> U+0020）。
				case 's':
					$str = mb_convert_kana($str, 's');
					break;

					// S: 「半角」スペースを「全角」に変換します（U+0020 -> U+3000）。
				case 'S':
					$str = mb_convert_kana($str, 'S');
					break;

					// c: 「全角カタカナ」を「全角ひらがな」に変換します。
					//
					// 「ヽヾ」は「ゝゞ」に変換されます。
					// 「ヴ」は「う゛」に展開されます。
					// 「ヶ」は変換されません。（変換先が「か」「が」「こ」の複数あるため）
					// 「ヵ」は「か」に変換されます。
					// http://www.wikiwand.com/ja/%E6%8D%A8%E3%81%A6%E4%BB%AE%E5%90%8D
				case 'c':
					$str = mb_convert_kana($str, 'c');
					$kana = array('ヴ', 'ヵ', 'ヽ', 'ヾ');
					$hira = array('う゛', 'か', 'ゝ', 'ゞ');
					$str = str_replace($kana, $hira, $str);
					break;

					// C: 「全角ひらがな」を「全角カタカナ」に変換します。
					//
					// 「ゝゞ」は「ヽヾ」に変換されます。
					// 「う゛」は「ヴ」に結合されます。
				case 'C':
					$str = mb_convert_kana($str, 'C');
					$hira = array('ウ゛', 'ゝ', 'ゞ');
					$kana = array('ヴ', 'ヽ', 'ヾ');
					$str = str_replace($hira, $kana, $str);
					break;

					// v: 「う濁」を「は濁」に変換します。
					//
					// 「う゛ぁ」「う゛ぃ」「う゛」「う゛ぇ」「う゛ぉ」を
					// 「ば」「び」「ぶ」「べ」「ぼ」に変換します。
				case 'v':
					$udaku = array(
					'う゛ぁ', 'う゛ぃ', 'う゛ぇ', 'う゛ぉ', 'う゛',
					'ゔぁ', 'ゔぃ', 'ゔぇ', 'ゔぉ', 'ゔ'
							);
							$hadaku = array(
									'ば', 'び', 'べ', 'ぼ', 'ぶ',
									'ば', 'び', 'べ', 'ぼ', 'ぶ'
							);
							$str = str_replace($udaku, $hadaku, $str);
							break;

							// V: 「ウ濁」を「ハ濁」に変換します。
							//
							// 「ヴァ」「ヴィ」「ヴ」「ヴェ」「ヴォ」を
							// 「バ」「ビ」「ブ」「ベ」「ボ」に変換します。
				case 'V':
					$udaku = array(
					'ウ゛ァ', 'ウ゛ィ', 'ウ゛ェ', 'ウ゛ォ', 'ウ゛',
					'ヴァ', 'ヴィ', 'ヴェ', 'ヴォ', 'ヴ'
							);
							$hadaku = array(
									'バ', 'ビ', 'ベ', 'ボ', 'ブ',
									'バ', 'ビ', 'ベ', 'ボ', 'ブ'
							);
							$str = str_replace($udaku, $hadaku, $str);
							break;

							// Q: 半角クォーテーション、半角アポストロフィを全角に変換します。
				case 'Q':
					$han = array('"', "'");
					$zen = array('＂', '＇');
					$str = str_replace($han, $zen, $str);
					break;

					// q: 全角クォーテーション、全角アポストロフィを半角に変換します。
				case 'q':
					$han = array('"', "'");
					$zen = array('＂', '＇');
					$str = str_replace($zen, $han, $str);
					break;

					// B: 半角バックスラッシュを全角に変換します。
				case 'B':
					$han = "\\";
					$zen = '＼';
					$str = str_replace($han, $zen, $str);
					break;

					// b: 全角バックスラッシュを半角に変換します。
				case 'b':
					$han = "\\";
					$zen = '＼';
					$str = str_replace($zen, $han, $str);
					break;

					// T: 半角チルダを全角にチルダ変換します。
				case 'T':
					$han = '~';
					$zen = '～';
					$str = str_replace($han, $zen, $str);
					break;

					// t: 全角チルダを半角チルダに変換します。
				case 't':
					$han = '~';
					$zen = '～';
					$str = str_replace($zen, $han, $str);
					break;

					// W: 全角波ダッシュを全角チルダに変換します。
				case 'W':
					$nami = '〜';
					$tilde = '～';
					$str = str_replace($nami, $tilde, $str);
					break;

					// w: 全角チルダを全角波ダッシュに変換します。
				case 'w':
					$nami = '〜';
					$tilde = '～';
					$str = str_replace($tilde, $nami, $str);
					break;

					// P: ハイフン、ダッシュ、マイナスを全角ハイフンマイナスに変換します。（U+FF0D）
					//    英数記号の後ろにある全角・半角長音符も含む
					//
					// http://hydrocul.github.io/wiki/blog/2014/1101-hyphen-minus-wave-tilde.html
					//    「U+002D」半角ハイフンマイナス
					//    「U+FE63」小さいハイフンマイナス。NFKD/NFKC正規化で U+002D
					//    「U+FF0D」全角ハイフンマイナス
					//    「U+2212」「U+207B」「U+208B」マイナス
					//    「U+2010」「U+2011」ハイフン
					//    「U+2012」～「U+2015」「U+FE58」ダッシュ
				case 'P':
					$phyhen = array(
					'-', '﹣', '－', '−', '⁻', '₋',
					'‐', '‑', '‒', '–', '—', '―', '﹘'
							);
					$change = '－';
					$str = str_replace($phyhen, $change, $str);
					$str = preg_replace('/([!-~！-～])(ー|ｰ)/u', '$1' . $change, $str);
					break;

					// p: ハイフン、ダッシュ、マイナスを半角ハイフンマイナスに変換します。（U+002D）
					//    英数記号の後ろにある全角・半角長音符も含む
					//
					// http://hydrocul.github.io/wiki/blog/2014/1101-hyphen-minus-wave-tilde.html
					//    「U+002D」半角ハイフンマイナス
					//    「U+FE63」小さいハイフンマイナス。NFKD/NFKC正規化で U+002D
					//    「U+FF0D」全角ハイフンマイナス
					//    「U+2212」「U+207B」「U+208B」マイナス
					//    「U+2010」「U+2011」ハイフン
					//    「U+2012」～「U+2015」「U+FE58」ダッシュ
				case 'p':
					$phyhen = array(
					'-', '﹣', '－', '−', '⁻', '₋',
					'‐', '‑', '‒', '–', '—', '―', '﹘'
							);
					$change = '-';
					$str = str_replace($phyhen, $change, $str);
					$str = preg_replace('/([!-~！-～])(ー|ｰ)/u', '$1' . $change, $str);
					break;

					// U: 「U+0021」～「U+007E」以外の「半角」記号を「全角」記号に変換します。
					//
					// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/unicode/uff00.html
				case 'U':
					$han = array(
					'⦅', '⦆', '¢', '£', '¬', '¯', '¦', '\\',
					'₩', '￨', '￩', '￪', '￫', '￬', '￭', '￮'
							);
					$zen = array(
							'｟', '｠', '￠', '￡', '￢', '￣', '￤', '￥',
							'￦', '│', '←', '↑', '→', '↓', '■', '○'
					);
					$str = str_replace($han, $zen, $str);
					break;

					// u: 「U+0021」～「U+007E」以外の「全角」記号を「半角」記号に変換します。
					//
					// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/unicode/uff00.html
				case 'u':
					$han = array(
					'⦅', '⦆', '¢', '£', '¬', '¯', '¦', '\\',
					'₩', '￨', '￩', '￪', '￫', '￬', '￭', '￮'
							);
					$zen = array(
							'｟', '｠', '￠', '￡', '￢', '￣', '￤', '￥',
							'￦', '│', '←', '↑', '→', '↓', '■', '○'
					);
					$str = str_replace($zen, $han, $str);
					break;

				case 'M':
					$han = array(
					'!','#','$','%','&','(',')','*','+',',',
					'-','.','/',':',';','<','=','>','?','@',
					'[',']','^','_','`','{','|','}'
							);
							$zen = array(
									'！','＃','＄','％','＆','（','）','＊','＋','，',
									'－','．','／','：','；','＜','＝','＞','？','＠',
									'［','］','＾','＿','｀','｛','｜','｝'
							);
							$str = str_replace($han, $zen, $str);
							break;
				case 'm':
					$han = array(
					'!','#','$','%','&','(',')','*','+',',',
					'-','.','/',':',';','<','=','>','?','@',
					'[',']','^','_','`','{','|','}'
							);
							$zen = array(
									'！','＃','＄','％','＆','（','）','＊','＋','，',
									'－','．','／','：','；','＜','＝','＞','？','＠',
									'［','］','＾','＿','｀','｛','｜','｝'
							);
							$str = str_replace($zen, $han, $str);
							break;
							// X: カッコ付き文字を半角括弧と中の文字に展開します。
							//
							// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/unicode/u2460.html
							// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/unicode/u3200.html
				case 'X':
					$single = array(
					'⑴', '⑵', '⑶', '⑷', '⑸',
					'⑹', '⑺', '⑻', '⑼', '⑽',
					'⑾', '⑿', '⒀', '⒁', '⒂',
					'⒃', '⒄', '⒅', '⒆', '⒇',
					'⒜', '⒝', '⒞', '⒟', '⒠', '⒡', '⒢', '⒣',
					'⒤', '⒥', '⒦', '⒧', '⒨', '⒩', '⒪', '⒫',
					'⒬', '⒭', '⒮', '⒯', '⒰', '⒱', '⒲', '⒳',
					'⒴', '⒵',
					'㈠', '㈡', '㈢', '㈣', '㈤',
					'㈥', '㈦', '㈧', '㈨', '㈩',
					'㈪', '㈫', '㈬', '㈭', '㈮', '㈯', '㈰',
					'㈱', '㈲', '㈳', '㈴', '㈵', '㈶', '㈷',
					'㈸', '㈹', '㈺', '㈻', '㈼', '㈽', '㈾',
					'㈿', '㉀', '㉁', '㉂', '㉃'
							);
					$multi = array(
							'(1)', '(2)', '(3)', '(4)', '(5)',
							'(6)', '(7)', '(8)', '(9)', '(10)',
							'(11)', '(12)', '(13)', '(14)', '(15)',
							'(16)', '(17)', '(18)', '(19)', '(20)',
							'(a)', '(b)', '(c)', '(d)', '(e)', '(f)', '(g)', '(h)',
							'(i)', '(j)', '(k)', '(l)', '(m)', '(n)', '(o)', '(p)',
							'(q)', '(r)', '(s)', '(t)', '(u)', '(v)', '(w)', '(x)',
							'(y)', '(z)',
							'(一)', '(二)', '(三)', '(四)', '(五)',
							'(六)', '(七)', '(八)', '(九)', '(十)',
							'(月)', '(火)', '(水)', '(木)', '(金)', '(土)', '(日)',
							'(株)', '(有)', '(社)', '(名)', '(特)', '(財)', '(祝)',
							'(労)', '(代)', '(呼)', '(学)', '(監)', '(企)', '(資)',
							'(協)', '(祭)', '(休)', '(自)', '(至)'
					);
					$str = str_replace($single, $multi, $str);
					break;

					// Y: 集合文字を展開します。（単位文字以外）
					//
					// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/unicode/u2460.html
					// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/unicode/u3200.html
					// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/unicode/u3300.html
				case 'Y':
					$single = array(
					'㌀', '㌁', '㌂', '㌃', '㌄', '㌅',
					'㌆', '㌇', '㌈', '㌉', '㌊', '㌋',
					'㌌', '㌍', '㌎', '㌏', '㌐', '㌑', '㌒',
					'㌓', '㌔', '㌕', '㌖', '㌗', '㌘',
					'㌙', '㌚', '㌛', '㌜', '㌝', '㌞',
					'㌟', '㌠', '㌡', '㌢', '㌣', '㌤',
					'㌥', '㌦', '㌧', '㌨', '㌩', '㌪', '㌫',
					'㌬', '㌭', '㌮', '㌯', '㌰', '㌱', '㌲',
					'㌳', '㌴', '㌵', '㌶', '㌷', '㌸',
					'㌹', '㌺', '㌻', '㌼', '㌽', '㌾', '㌿',
					'㍀', '㍁', '㍂', '㍃', '㍄', '㍅', '㍆',
					'㍇', '㍈', '㍉', '㍊', '㍋', '㍌',
					'㍍', '㍎', '㍏', '㍐', '㍑', '㍒', '㍓',
					'㍔', '㍕', '㍖', '㍗',
					'㍿', '㍻', '㍼', '㍽', '㍾',
					'㋀', '㋁', '㋂', '㋃', '㋄', '㋅',
					'㋆', '㋇', '㋈', '㋉', '㋊', '㋋',
					'㏠', '㏡', '㏢', '㏣', '㏤',
					'㏥', '㏦', '㏧', '㏨', '㏩',
					'㏪', '㏫', '㏬', '㏭', '㏮',
					'㏯', '㏰', '㏱', '㏲', '㏳',
					'㏴', '㏵', '㏶', '㏷', '㏸',
					'㏹', '㏺', '㏻', '㏼', '㏽', '㏾',
					'㍘', '㍙', '㍚', '㍛', '㍜', '㍝',
					'㍞', '㍟', '㍠', '㍡', '㍢',
					'㍣', '㍤', '㍥', '㍦', '㍧',
					'㍨', '㍩', '㍪', '㍫', '㍬',
					'㍭', '㍮', '㍯', '㍰',
					'⒈', '⒉', '⒊', '⒋', '⒌', '⒍', '⒎', '⒏', '⒐', '⒑',
					'⒒', '⒓', '⒔', '⒕', '⒖', '⒗', '⒘', '⒙', '⒚', '⒛',
					'№', '℡', '㏍', '㏇', '㏂', '㏘'
							);
					$multi = array(
							'アパート', 'アルファ', 'アンペア', 'アール', 'イニング', 'インチ',
							'ウォン', 'エスクード', 'エーカー', 'オンス', 'オーム', 'カイリ',
							'カラット', 'カロリー', 'ガロン', 'ガンマ', 'ギガ', 'ギニー', 'キュリー',
							'ギルダー', 'キロ', 'キログラム', 'キロメートル', 'キロワット', 'グラム',
							'グラムトン', 'クルゼイロ', 'クローネ', 'ケース', 'コルナ', 'コーポ',
							'サイクル', 'サンチーム', 'シリング', 'センチ', 'セント', 'ダース',
							'デシ', 'ドル', 'トン', 'ナノ', 'ノット', 'ハイツ', 'パーセント',
							'パーツ', 'バーレル', 'ピアストル', 'ピクル', 'ピコ', 'ビル', 'ファラッド',
							'フィート', 'ブッシェル', 'フラン', 'ヘクタール', 'ペソ', 'ペニヒ',
							'ヘルツ', 'ペンス', 'ページ', 'ベータ', 'ポイント', 'ボルト', 'ホン',
							'ポンド', 'ホール', 'ホーン', 'マイクロ', 'マイル', 'マッハ', 'マルク',
							'マンション', 'ミクロン', 'ミリ', 'ミリバール', 'メガ', 'メガトン',
							'メートル', 'ヤード', 'ヤール', 'ユアン', 'リットル', 'リラ', 'ルピー',
							'ルーブル', 'レム', 'レントゲン', 'ワット',
							'株式会社', '平成', '昭和', '大正', '明治',
							'1月', '2月', '3月', '4月', '5月', '6月',
							'7月', '8月', '9月', '10月', '11月', '12月',
							'1日', '2日', '3日', '4日', '5日',
							'6日', '7日', '8日', '9日', '10日',
							'11日', '12日', '13日', '14日', '15日',
							'16日', '17日', '18日', '19日', '20日',
							'21日', '22日', '23日', '24日', '25日',
							'26日', '27日', '28日', '29日', '30日', '31日',
							'0点', '1点', '2点', '3点', '4点', '5点',
							'6点', '7点', '8点', '9点', '10点',
							'11点', '12点', '13点', '14点', '15点',
							'16点', '17点', '18点', '19点', '20点',
							'21点', '22点', '23点', '24点',
							'1.', '2.', '3.', '4.', '5.', '6.', '7.', '8.', '9.', '10.',
							'11.', '12.', '13.', '14.', '15.', '16.', '17.', '18.', '19.',
							'20.',
							'No.', 'TEL', 'K.K.', 'Co.', 'a.m.', 'p.m.'
					);
					$str = str_replace($single, $multi, $str);
					break;

					// Z: 小字形文字を大文字に変換します。（U+FE50～U+FE6B）
					// 「﹐﹑﹒﹔﹕﹖﹗﹘﹙﹚﹛﹜﹝﹞﹟﹠﹡﹢﹣﹤﹥﹦﹨﹩﹪﹫」
					//
					// 「U+FF58」は「U+2014」へマッピングされていますが、揺らぎの訂正のため
					// 「U+002D（半角ハイフンマイナス）」に変換します。
					//
					// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/unicode/ufe50.html
				case 'Z':
					$small = array(
					'﹐', '﹑', '﹒', '﹔', '﹕', '﹖', '﹗', '﹘', '﹙', '﹚',
					'﹛', '﹜', '﹝', '﹞', '﹟', '﹠', '﹡', '﹢', '﹣',
					'﹤', '﹥', '﹦', '﹨', '﹩', '﹪', '﹫'
							);
					$big = array(
							',', '、', '.', ';', ':', '?', '!', '-', '(', ')',
							'{', '}', '〔', '〕', '#', '&', '*', '+', '-',
							'<', '>', '=', "\\", '$', '%', '@'
					);
					$str = str_replace($small, $big, $str);
					break;
				default :
					break;
			}
		};

		// 文字列の初期化（揺らぎの訂正）を行ないます
		$init();
		// オプション文字列を分解して一文字ごとに$convertを実行します
		array_map($convert, str_split($opt));

		return $str;
	}

	/**
	 * Dropbox連携用
	 */
	public static function DropboxChooseBtn() {
?>
		<script type="text/javascript" src="<?php echo CL_DROPBOX_URL; ?>" id="dropboxjs" data-app-key="<?php echo CL_DROPBOX_APPKEY; ?>"></script>
		<script type="text/javascript">
			var button = Dropbox.createChooseButton({
				linkType: "preview",
				multiselect: false,
				success: function(files) {
					var link = files[0].link;
					link = link.replace(/dl=0$/,"dl=1");
					if ($("#dburl").get(0)) {
						$("#dburl").val(link);
						$("#material-chain-0").text('');
					} else if ($("textarea[name=c_text]").get(0)) {
						var text = $("textarea[name=c_text]").val();
						text += "\n"+link;
						$("textarea[name=c_text]").val(text);
					}
				},
			});
			$("#dbbtn").append(button);
		</script>
<?php
	}


	/***********************************************************
	 * youtubeのURLから埋め込みタグを生成する
	 *
	 * @param   string $url youtubeのURL
	 * @param   integer $width iframeの横幅
	 * @return  string        埋め込みタグ
	 **********************************************************/
	public static function createYoutubeTag($url,$width = 600)
	{
		//とりあえずURLがyoutubeのURLであるかをチェック
		if(preg_match('#https?://www.youtube.com/.*#i',$url,$matches)){
			//parse_urlでhttps://www.youtube.com/watch以下のパラメータを取得
			$parse_url = parse_url($url);
			// 動画IDを取得
			if (preg_match('#v=([-\w]{11})#i', $parse_url['query'], $v_matches)) {
				$video_id = $v_matches[1];
			} else {
				// 万が一動画のIDの存在しないURLだった場合は埋め込みコードを生成しない。
				return false;
			}
			$v_param = '';
			// パラメータにt=XXmXXsがあった時の埋め込みコード用パラメータ設定
			// t=〜〜の部分を抽出する正規表現は記述を誤るとlist=〜〜の部分を抽出してしまうので注意
			if (preg_match('#t=([0-9ms]+)#i', $parse_url['query'], $t_maches)) {
				$time = 0;
				if (preg_match('#(\d+)m#i', $t_maches[1], $minute)) {
					// iframeでは正の整数のみ有効なのでt=XXmXXsとなっている場合は整形する必要がある。
					$time = $minute[1]*60;
				}
				if (preg_match('#(\d+)s#i', $t_maches[1], $second)) {
					$time = $time+$second[1];
				}
				if (!preg_match('#(\d+)m#i', $t_maches[1]) && !preg_match('#(\d+)s#i', $t_maches[1])) {
					// t=(整数)の場合はそのままの値をセット ※秒数になる
					$time = $t_maches[1];
				}
				$v_param .= '?start=' . $time;
			}
			// パラメータにlist=XXXXがあった時の埋め込みコード用パラメータ設定
			if (preg_match('#list=([-\w]+)#i', $parse_url['query'], $l_maches)) {
				if (!empty($v_param)) {
					// $v_paramが既にセットされていたらそれに続ける
					$v_param .= '&list=' . $l_maches[1];
				} else {
					// $v_paramが既にセットされていなかったら先頭のパラメータとしてセット
					$v_param .= '?list=' . $l_maches[1];
				}
			}

			// iframeの高さを求める
			$height = ceil($width * 0.5633333);
			// 埋め込みコードを返す
			return '<iframe width="'.$width.'" height="'.$height.'" src="https://www.youtube.com/embed/' . $video_id . $v_param . '" frameborder="0" allowfullscreen></iframe>';
		}
		// パラメータが不正(youtubeのURLではない)ときは埋め込みコードを生成しない。
		return false;
	}

	/*********************************************************************
	 * コンテンツ連携URLを解析して、該当コンテンツのタイトルを取得
	 *
	 * @param string $sURL 解析対象URL
	 * @return array/bool コンテンツのタイトル、URL、種別/失敗時はfalse
	 ********************************************************************/
	public static function ExtUrlDetect($sURL = null)
	{
		if (is_null($sURL) || !$sURL)
		{
			return false;
		}

		$aRes = false;

		$sQPth = '/'.preg_quote(CL_DOMAIN.'/s/quest/ans/','/').'([^\/]+)/';
		$sTPth = '/'.preg_quote(CL_DOMAIN.'/s/test/ans/','/').'([^\/]+)/';
		$sMPth = '/'.preg_quote(CL_DOMAIN.'/s/material/list/','/').'([^\/]+)\/\#m(\d+)/';
		$sCPth = '/'.preg_quote(CL_DOMAIN.'/s/coop/thread/','/').'([^\/]+)\/(\d+)/';
		$sRPth = '/'.preg_quote(CL_DOMAIN.'/s/report/put/','/').'([^\/]+)/';

		if (preg_match($sQPth, $sURL, $aMatches))
		{
			$result = Model_Quest::getQuestBaseFromID($aMatches[1]);
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('アンケート').'「'.(($aA['qbQuickMode'])? '[Q]':'').$aA['qbTitle'].'」';
				$aRes['url'] = preg_replace('/\/s\/quest\/ans\//', '/t/quest/put/', $sURL);
				$aRes['target'] = false;
			}
		}
		elseif (preg_match($sTPth, $sURL, $aMatches))
		{
			$result = Model_Test::getTestBaseFromID($aMatches[1]);
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('小テスト').'「'.$aA['tbTitle'].'」';
				$aRes['url'] = preg_replace('/\/s\/test\/ans\//', '/t/test/put/', $sURL);
				$aRes['target'] = false;
			}
		}
		elseif (preg_match($sMPth, $sURL, $aMatches))
		{
			$result = Model_Material::getMaterial(array(array('mt.mcID','=',$aMatches[1]),array('mt.mNO','=',$aMatches[2])));
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('教材倉庫').'「'.$aA['mTitle'].'」';
				$aRes['url'] = preg_replace('/\/s\//', '/t/', $sURL);
				$aRes['target'] = false;
			}
		}
		elseif (preg_match($sCPth, $sURL, $aMatches))
		{
			$result = Model_Coop::getCoop(array(array('ci.ccID','=',$aMatches[1]),array('ci.cNO','=',$aMatches[2])));
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('協働板').'「'.$aA['cTitle'].'」';
				$aRes['url'] = preg_replace('/\/s\//', '/t/', $sURL);
				$aRes['target'] = false;
			}
		}
		elseif (preg_match($sRPth, $sURL, $aMatches))
		{
			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$aMatches[1])));
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('レポート').'「'.$aA['rbTitle'].'」';
				$aRes['url'] = preg_replace('/\/s\//', '/t/', $sURL);
				$aRes['target'] = false;
			}
		}
		return $aRes;
	}

	/*********************************************************************
	 * コンテンツ連携URLを解析して、該当コンテンツのタイトルを取得（学生用に提出済みかどうか判断）
	 *
	 * @param string $sURL 解析対象URL
	 * @param string $sStID 提出判断用の学生ID
	 * @return array/bool コンテンツのタイトル、URL、提出状況/失敗時はfalse
	 ********************************************************************/
	public static function ExtUrlDetectForStudent($sURL = null, $sStID = null)
	{
		if (is_null($sURL) || !$sURL || is_null($sStID) || !$sStID)
		{
			return false;
		}

		$aRes = false;

		$sQPth = '/'.preg_quote(CL_DOMAIN.'/s/quest/ans/','/').'([^\/]+)/';
		$sTPth = '/'.preg_quote(CL_DOMAIN.'/s/test/ans/','/').'([^\/]+)/';
		$sMPth = '/'.preg_quote(CL_DOMAIN.'/s/material/list/','/').'([^\/]+)\/\#m(\d+)/';
		$sCPth = '/'.preg_quote(CL_DOMAIN.'/s/coop/thread/','/').'([^\/]+)\/(\d+)/';
		$sRPth = '/'.preg_quote(CL_DOMAIN.'/s/report/put/','/').'([^\/]+)/';

		if (preg_match($sQPth, $sURL, $aMatches))
		{
			$result = Model_Quest::getQuestBaseFromID($aMatches[1],array(array('qb.qbPublic','>',0)));
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('アンケート').'「'.(($aA['qbQuickMode'])? '[Q]':'').$aA['qbTitle'].'」';
				$aRes['public'] = $aA['qbPublic'];
				$aRes['put'] = false;
				$aRes['url'] = $sURL;

				$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$aA['qbID']),array('qp.stID','=',$sStID)));
				if (count($result))
				{
					$aRes['put'] = true;
					$aRes['url'] = preg_replace('/\/ans\//', '/result/', $sURL);
				}
			}
		}
		elseif (preg_match($sTPth, $sURL, $aMatches))
		{
			$result = Model_Test::getTestBaseFromID($aMatches[1],array(array('tb.tbPublic','>',0)));
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('小テスト').'「'.$aA['tbTitle'].'」';
				$aRes['public'] = $aA['tbPublic'];
				$aRes['put'] = false;
				$aRes['url'] = $sURL;

				$result = Model_Test::getTestPut(array(array('tp.tbID','=',$aA['tbID']),array('tp.stID','=',$sStID)));
				if (count($result))
				{
					$aRes['put'] = true;
					$aRes['url'] = preg_replace('/\/ans\//', '/result/', $sURL);
				}
			}
		}
		elseif (preg_match($sMPth, $sURL, $aMatches))
		{
			$result = Model_Material::getMaterial(array(array('mt.mcID','=',$aMatches[1]),array('mt.mNO','=',$aMatches[2])));
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('教材倉庫').'「'.$aA['mTitle'].'」';
				$aRes['public'] = $aA['mPublic'];
				$aRes['put'] = false;
				$aRes['url'] = $sURL;
			}
		}
		elseif (preg_match($sCPth, $sURL, $aMatches))
		{
			$result = Model_Coop::getCoop(array(array('ci.ccID','=',$aMatches[1]),array('ci.cNO','=',$aMatches[2])));
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('協働板').'「'.$aA['cTitle'].'」';
				$aRes['public'] = true;
				$aRes['put'] = false;
				$aRes['url'] = $sURL;
			}
		}
		elseif (preg_match($sRPth, $sURL, $aMatches))
		{
			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$aMatches[1]),array('rb.rbPublic','>',0)));
			if (count($result))
			{
				$aA = $result->current();
				$aRes['title'] = __('レポート').'「'.$aA['rbTitle'].'」';
				$aRes['public'] = $aA['rbPublic'];
				$aRes['put'] = false;
				$aRes['url'] = $sURL;

				$result = Model_Report::getReportPut(array(array('rp.rbID','=',$aA['rbID']),array('rp.stID','=',$sStID)));
				if (count($result))
				{
					$aRes['put'] = true;
				}
			}
		}

		return $aRes;
	}

	/*********************************************************************
	 * コンテンツ連携URLを解析して、コピー先のURLに変更
	 *
	 * @param string $sURL 解析対象URL
	 * @param string $sCtID 講義ID
	 * @param array  $aQbIDs アンケートIDのペア
	 * @param array  $aTbIDs 小テストIDのペア
	 * @return string/bool URL/失敗時はfalse
	 ********************************************************************/
	public static function ExtUrlDetectForCopy($sURL = null, $sCtID =null, $aQbIDs = null, $aTbIDs = null)
	{
		if (is_null($sURL) || !$sURL)
		{
			return false;
		}

		$sRes = $sURL;

		$sQPth = '/'.preg_quote(CL_DOMAIN.'/s/quest/ans/','/').'([^\/]+)/';
		$sTPth = '/'.preg_quote(CL_DOMAIN.'/s/test/ans/','/').'([^\/]+)/';
#		$sMPth = '/'.preg_quote(CL_DOMAIN.'/s/material/list/','/').'([^\/]+)\/\#m(\d+)/';
#		$sCPth = '/'.preg_quote(CL_DOMAIN.'/s/coop/thread/','/').'([^\/]+)\/(\d+)/';

		if (preg_match($sQPth, $sURL, $aMatches))
		{
			if (isset($aQbIDs[$sCtID][$aMatches[1]]))
			{
				$sRes = preg_replace('/'.$aMatches[1].'/', $aQbIDs[$sCtID][$aMatches[1]], $sURL);
			}
		}
		elseif (preg_match($sTPth, $sURL, $aMatches))
		{
			if (isset($aTbIDs[$sCtID][$aMatches[1]]))
			{
				$sRes = preg_replace('/'.$aMatches[1].'/', $aTbIDs[$sCtID][$aMatches[1]], $sURL);
			}
		}

		return $sRes;
	}

	/*********************************************************************
	 * キーを維持したままランダムに配列を並び替える
	 *
	 * @param array $array 並び替える配列
	 * @return array 並び替え後の配列
	 ********************************************************************/
	public static function array_shuffle($array){
		$keys = array_keys($array);
		shuffle($keys);
		foreach($keys as $key){
			$result[$key] = $array[$key];
		}
		return $result;
	}

	/**********************************************************************
	 * 秒を分に変換する
	 *
	 * @param integer $iSec 秒数
	 * @param string $sMode 出力モード
	 * @return string
	 *********************************************************************/
	public static function Sec2Min($iSec = 0,$sMode = '分')
	{
		$sMin = __('分');
		$sSec = __('秒');

		if ($sMode != '分')
		{
			$sMin = ':';
			$sSec = '';
		}


		$times = 24 * 60 * 60;
		$sTime  = floor($iSec % $times / 60).$sMin;
		$sTime .= (floor($iSec % $times) % 60 % 60).$sSec;

		return $sTime;
	}

	/**********************************************************************
	 * 開始日と契約月数を渡すと契約終了日を返す
	 *
	 * @param strgin  $sS     開始日（Y-m-d）
	 * @param integer $iRange 契約月数
	 * @return string
	 *********************************************************************/
	public static function contractEnd($sS = null, $iRange = 0)
	{
		if (is_null($sS))
		{
			$sS = date('Y-m-d');
		}
		$iS = strtotime($sS);
		if ($iRange < 1)
		{
			return false;
		}


		if ((int)date('d',$iS) <= 20)
		{
			$iRange -= 1;
		}

		$sE = date('Y-m-t',strtotime('+'.$iRange.' months', $iS));
		return $sE;
	}

	/**********************************************************************
	 * 契約終了日から、残りの月数を返す
	 *
	 * @param strgin  $sE     終了日（Y-m-d）
	 * @param strgin  $sS     開始日（Y-m-d）
	 * @return integer
	 *********************************************************************/
	public static function contractMonths($sE = null, $sS = null)
	{
		if (is_null($sE))
		{
			return false;
		}
		$iE = strtotime($sE);
		if (!is_null($sS))
		{
			$iS = strtotime($sS);
		}
		else
		{
			$iS = time();
		}

		$iEM = (date('Y', $iE) * 12) + date('n', $iE);
		$iSM = (date('Y', $iS) * 12) + date('n', $iS);

		$iM = $iEM - $iSM;

		if ((int)date('j', $iS) <= 20)
		{
			$iM += 1;
		}

		return $iM;
	}



	public static function vdumpStr($obj)
	{
		ob_start();
		var_dump($obj);
		$dump = ob_get_contents();
		ob_end_clean();
		return $dump;
	}

	/**********************************************************************
	 * テキスト内のURLをリンクにする
	 *
	 * @param string $body 処理対象のテキスト
	 * @param integer $width Youtubeのiframeの横幅
	 * @return string
	 *********************************************************************/
	public static function url2link($body, $width = 600)
	{
		$pattern = '/(?<!href=")https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+/';
		$body = preg_replace_callback($pattern, function($matches) use ($width)
		{
			$link_title = $matches[0];
			if ($width == 0)
			{
				return '<a href="'.$matches[0].'" target="_blank">'.$link_title.'</a>';
			}
			$sYoutube = \Clfunc_Common::createYoutubeTag($matches[0],$width);
			if ($sYoutube)
			{
				return $sYoutube;
			}
			else
			{
				return '<a href="'.$matches[0].'" target="_blank">'.$link_title.'</a>';
			}
		}, $body);
		return $body;
	}

	/*********************************************************************
	 * ログ出力
	 *
	 * @param Exception $e
	 * @param string $sClass
	 ********************************************************************/
	public static function LogOut($e = null, $sClass = null)
	{
		if (!is_null($e))
		{
			\Log::error($e->getMessage()."\n".$e->getFile().'('.$e->getLine().")\n".$e->getTraceAsString(),$sClass);
		}
		return;
	}

	/*********************************************************************
	 * 協働板ファイル保存
	 *
	 * @param string $aInput
	 * @param string $sID
	 * @param string $sTempPath
	 * @param string $sAwsPath
	 * @throws Exception
	 * @return multitype
	 *********************************************************************/
	public static function CoopFileSave($aInput = null, $sID = null, $sTempPath = null, $sAwsPath = null)
	{
		if (is_null($aInput) || is_null($sID) || is_null($sTempPath) || is_null($sAwsPath))
		{
			throw new Exception('指定したファイルが保存できませんでした。'.$e->getMessage());
		}
		$afID = null;
		for ($i = 1; $i <= 3; $i++)
		{
			$aInput['fileinfo'][$i] = null;
			if ($aInput['c_file'.$i] != '')
			{
				if (@unserialize($aInput['c_file'.$i]))
				{
					$aInput['fileinfo'][$i] = unserialize($aInput['c_file'.$i]);
				}
				else
				{
					$aInput['fileinfo'][$i] = $aInput['c_file'.$i];
				}
			}
			$afID[$i] = array(
				'id'=>'',
				'file'=>'',
				'sourcefile'=>'',
				'thumbfile'=>'',
				'name'=>'',
				'size'=>'',
			);
			if (!is_null($aInput['fileinfo'][$i]))
			{
				if (!is_array($aInput['fileinfo'][$i]))
				{
					$afID[$i]['id'] = $aInput['fileinfo'][$i];
					$result = Model_File::getFileFromID($aInput['fileinfo'][$i]);
					if (count($result))
					{
						$f = $result->current();
						$afID[$i] = array(
							'id'=>$f['fID'],
							'file'=>'',
							'sourcefile'=>'',
							'thumbfile'=>'',
							'name'=>$f['fName'],
							'size'=>$f['fSize'],
						);
					}
					else
					{
						$afID[$i]['id'] = '';
					}
				}
				else if (isset($aInput['fileinfo'][$i]['file']))
				{
					$sSourseFile = $sTempPath.DS.$aInput['fileinfo'][$i]['file'];
					$sThumbFile = $sTempPath.DS.CL_PREFIX_THUMBNAIL.$aInput['fileinfo'][$i]['file'];
					$sContentType = \Clfunc_Common::GetContentType($sSourseFile);
					$sExt = pathinfo($sSourseFile,PATHINFO_EXTENSION);
					$iFileType = \Clfunc_Common::GetFileType($sSourseFile);
					$sfID = \Model_File::getFileID();
					$sFile = $sfID.'.'.$sExt;

					# 登録情報作成
					$aInsert = array(
						'fID'          => $sfID,
						'fName'        => $aInput['fileinfo'][$i]['name'],
						'fSize'        => $aInput['fileinfo'][$i]['size'],
						'fExt'         => $sExt,
						'fContentType' => $sContentType,
						'fFileType'    => $iFileType,
						'fPath'        => $sAwsPath,
						'fUserType'    => (substr($sID,0,1) == 't')? 0:1,
						'fUser'        => $sID,
						'fDate'        => date('YmdHis'),
					);

					try
					{
						$result = \Clfunc_Aws::putFile($sAwsPath, $sFile, $sSourseFile, $sContentType);
						if ($iFileType == 1 && file_exists($sThumbFile))
						{
							$result = \Clfunc_Aws::putFile($sAwsPath, CL_PREFIX_THUMBNAIL.$sFile, $sThumbFile, $sContentType);
						}
						if ($iFileType == 2)
						{
							$result = \Clfunc_Aws::encodeMovie($sAwsPath, $sfID, $sExt);
						}
						$result = \Model_File::insertFile($aInsert);
						$afID[$i] = array(
							'id'=>$sfID,
							'file'=>$sFile,
							'sourcefile'=>$sSourseFile,
							'thumbfile'=>$sThumbFile,
							'name'=>$aInput['fileinfo'][$i]['name'],
							'size'=>$aInput['fileinfo'][$i]['size'],
						);
					}
					catch (Exception $e)
					{
						\Clfunc_Aws::deleteFile($sAwsPath,$sFile);
						if ($iFileType == 1)
						{
							\Clfunc_Aws::deleteFile($sAwsPath, CL_PREFIX_THUMBNAIL.$sFile);
						}
						if ($iFileType == 2)
						{
							\Clfunc_Aws::deleteFile($sAwsPath,CL_PREFIX_ENCODE.$sfID.CL_AWS_ENCEXT);
							\Clfunc_Aws::deleteFile($sAwsPath,CL_PREFIX_THUMBNAIL.$sfID.'-00001.png');
						}
						throw new Exception('指定したファイルが保存できませんでした。'.$e->getMessage());
					}
				}
			}
		}
		return $afID;
	}

	public static function YearList($iStart = null)
	{
		if (is_null($iStart))
		{
			$iStart = (int)date('Y');
		}
		for ($i = $iStart; $i <= (int)date('Y',strtotime('+1 year')); $i++)
		{
		$aYear[$i] = __(':year年度',array('year'=>$i));
		}
		return $aYear;
	}

	public static function getCode($sCode = null)
	{
		if (is_null($sCode))
		{
			return $sCode;
		}

		$aCode = explode('@',$sCode);

		if (isset($aCode[1]))
		{
			return $aCode[1];
		}

		return $sCode;
	}

	public static function setCode($sCode = null,$sGPrefix = null)
	{
		if (is_null($sCode))
		{
			return $sCode;
		}

		if (!is_null($sGPrefix))
		{
			return $sGPrefix.'@'.$sCode;
		}

		return $sCode;
	}

	/**
	 * 整数値をビットに分解し、それぞれの整数を配列で返す
	 *
	 * @param  integer $iDec
	 * @return multitype:number
	 */
	public static function dec2Bits($iDec)
	{
		if (!is_int($iDec))
		{
			return false;
		}
		elseif ($iDec < 0)
		{
			return false;
		}
		elseif ($iDec == 0)
		{
			return array(0 => 0);
		}

		$aBits = array();
		for ($i = 0; $iDec > 0; $iDec >>= 1, $i += 1)
		{
			if ($iDec & 0x01)
			{
				$aBits[] = pow(2, $i);
			}
		}
		return $aBits;
	}

	/**
	 * 検索語句を空白で区切る
	 *
	 * @param string $sW
	 * @return multitype:
	 */
	public static function getSearchWords($sW = null)
	{
		if (is_null($sW))
		{
			return array();
		}
		$sW = trim(mb_convert_kana($sW, "s", 'UTF-8'));
		$sW = preg_replace("/\s+/"," ",$sW);
		if ($sW != "") {
			$aW = explode(" ",$sW);
		} else {
			return array();
		}
		return $aW;
	}

	public static function getSearchWhere($aW = null, $aCol = null)
	{
		if (is_null($aW) || is_null($aCol))
		{
			return null;
		}

		$aWords = null;
		foreach ($aW as $sW)
		{
			foreach ($aCol as $sC)
			{
				$aWords[] = array($sC,'LIKE','%'.$sW.'%');
			}
		}
		return $aWords;
	}

	public static function SearchWordsReplace($sQuery, $sText) {
		$sQuery  = str_replace('　', ' ', $sQuery);
		$q = preg_split("'[\\s,]+'", $sQuery, -1, PREG_SPLIT_NO_EMPTY);
		$qq = array();
		foreach ($q as $val) {
			$qq[] = "'(".preg_quote($val).")'i";
		}
		return preg_replace($qq, "<strong>$1</strong>", $sText);
	}

	public static function getChartColors($i = 0)
	{
		switch ($i)
		{
			case 2:
				$aColors = array(
					0 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
					1 => array(
						'rgb' => '0,128,255',
						'code' => '#0080FF',
					),
					2 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
				);
			break;
			case 3:
				$aColors = array(
					0 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
					1 => array(
						'rgb' => '0,128,255',
						'code' => '#0080FF',
					),
					2 => array(
						'rgb' => '64,255,0',
						'code' => '#40FF00',
					),
					3 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
				);
			break;
			case 4:
				$aColors = array(
					0 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
					1 => array(
						'rgb' => '0,128,255',
						'code' => '#0080FF',
					),
					2 => array(
						'rgb' => '0,255,64',
						'code' => '#00FF40',
					),
					3 => array(
						'rgb' => '255,255,0',
						'code' => '#FFFF00',
					),
					4 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
				);
			break;
			case 5:
				$aColors = array(
					0 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
					1 => array(
						'rgb' => '0,128,255',
						'code' => '#0080FF',
					),
					2 => array(
						'rgb' => '0,255,191',
						'code' => '#00FFBF',
					),
					3 => array(
						'rgb' => '0,255,64',
						'code' => '#00FF40',
					),
					4 => array(
						'rgb' => '255,255,0',
						'code' => '#FFFF00',
					),
					5 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
				);
			break;
			default:
				$aColors = array(
					0 => array(
						'rgb' => '170,170,170',
						'code' => '#AAAAAA',
					),
					1 => array(
						'rgb' => '255,0,0',
						'code' => '#FF0000',
					),
					2 => array(
						'rgb' => '255,64,0',
						'code' => '#FF4000',
					),
					3 => array(
						'rgb' => '255,128,0',
						'code' => '#FF8000',
					),
					4 => array(
						'rgb' => '255,191,0',
						'code' => '#FFBF00',
					),
					5 => array(
						'rgb' => '255,255,0',
						'code' => '#FFFF00',
					),
					6 => array(
						'rgb' => '191,255,0',
						'code' => '#BFFF00',
					),
					7 => array(
						'rgb' => '128,255,0',
						'code' => '#80FF00',
					),
					8 => array(
						'rgb' => '64,255,0',
						'code' => '#40FF00',
					),
					9 => array(
						'rgb' => '0,255,0',
						'code' => '#00FF00',
					),
					10 => array(
						'rgb' => '0,255,64',
						'code' => '#00FF40',
					),
					11 => array(
						'rgb' => '0,255,128',
						'code' => '#00FF80',
					),
					12 => array(
						'rgb' => '0,255,191',
						'code' => '#00FFBF',
					),
					13 => array(
						'rgb' => '0,255,255',
						'code' => '#00FFFF',
					),
					14 => array(
						'rgb' => '0,191,255',
						'code' => '#00BFFF',
					),
					15 => array(
						'rgb' => '0,128,255',
						'code' => '#0080FF',
					),
					16 => array(
						'rgb' => '0,64,255',
						'code' => '#0040FF',
					),
					17 => array(
						'rgb' => '0,0,255',
						'code' => '#0000FF',
					),
					18 => array(
						'rgb' => '64,0,255',
						'code' => '#4000FF',
					),
					19 => array(
						'rgb' => '128,0,255',
						'code' => '#8000FF',
					),
					20 => array(
						'rgb' => '191,0,255',
						'code' => '#BF00FF',
					),
					21 => array(
						'rgb' => '255,0,255',
						'code' => '#FF00FF',
					),
					22 => array(
						'rgb' => '255,0,191',
						'code' => '#FF00BF',
					),
					23 => array(
						'rgb' => '255,0,128',
						'code' => '#FF0080',
					),
					24 => array(
						'rgb' => '255,0,64',
						'code' => '#FF0040',
					),
				);
			break;
		}
		return $aColors;
	}


	/**
	 * LDAPAuthCommand
	 *
	 * @param string $aGroup
	 * @param string $sLogin
	 * @param string $sPass
	 */
	public static function LDAPAuthCommand($aGroup = null, $sLogin = null,$sPass = null)
	{
		try
		{
			if (is_null($aGroup) || is_null($sLogin) || is_null($sPass) || !$aGroup['gtLDAP'])
			{
				throw new Exception(__('処理に必要な情報がありません'),-1);
			}

			$sLogPrefix = ' Group:'.$aGroup['gtName'].' LoginID:'.$sLogin;

			\Log::write('LDAP','['.date("Y-m-d H:i:s.").self::msec().'] ldapsearch Start.'.$sLogPrefix);

			$sCom = 'ldapsearch -x -LLL';
			$sCom .= ' -H "'.$aGroup['gtLProtocol'].'://'.$aGroup['gtLServer'].(($aGroup['gtLPort'] > 0)? ':'.$aGroup['gtLPort']:'').'/"';
			$sCom .= ' -D "'.str_replace('[USER]', $sLogin, $aGroup['gtLBinddn']).'"';
			$sCom .= ' -w '.escapeshellarg($sPass);
			$sCom .= ' -b "'.$aGroup['gtLSearchbase'].'"';
			$sCom .= ' "'.$aGroup['gtLUID'].'='.$sLogin.'"';

			\Log::write('LDAP','['.date("Y-m-d H:i:s.").self::msec().'] Command. '.$sCom);

			exec($sCom." 2>&1", $aOutput, $iReturn);

			if ($iReturn == 255)
			{
				throw new Exception(__('認証サーバーへの接続に失敗しました。').'|'.$aOutput[0].$sLogPrefix,$iReturn);
			}
			elseif ($iReturn == 49)
			{
				throw new Exception(__('ログインIDまたはパスワードが間違っているため、ログインできません。').'|'.$aOutput[0].$sLogPrefix,$iReturn);
			}
			elseif ($iReturn == 0 || $iReturn == 32)
			{
				\Log::write('LDAP','['.date('Y-m-d H:i:s.').self::msec().'] ldapsearch Success!'.$sLogPrefix);
				return true;
			}
			else
			{
				throw new Exception($aOutput[0].$sLogPrefix,$iReturn);
			}
		}
		catch (\Exception $e)
		{
			\Log::error('['.date('Y-m-d H:i:s.').self::msec().'] '.$e->getMessage().'['.$e->getCode().']');
			throw $e;
		}
	}

	public static function msec()
	{
		list($micro, $Unixtime) = explode(" ", microtime());
		return sprintf("%03d",round(($micro) * 1000));
	}

	public static function ContractDetect($aTeacher, $class = null)
	{
		if (!$aTeacher['gtID'] && $aTeacher['coTermDate'] < date('Y-m-d'))
		{
			$sAnchor = null;
			if ($class)
			{
				$aC = explode('_', $class);
				$sAnchor = '#'.strtolower($aC[2]);
			}

			# Session::set('SES_T_ERROR_MSG',__('現在、無料版（クイックアンケート）をご利用中です。指定の機能を利用するにはプランを選択の上、購入・契約を行ってください。'));
			Response::redirect('/t/payment/note'.$sAnchor);
		}
	}

	public static function getStudentProfileRead($aStu)
	{
		$iP = 0;

		$aF = Clfunc_Flag::getStuGetFlag();

		foreach ($aF as $i => $sC)
		{
			if ($aStu[$sC])
			{
				$iP += $i;
			}
		}

		return $iP;
	}



	public static function mathPoint($iPt=null,$iPr=null)
	{
		$result = Model_Payment::getPointRate(array(array('no','=',1)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','システムに不備データがあります。申し訳ありませんが、サポートセンターへご連絡おねがいします。（PR-1001001）');
			Response::redirect('index/error/t');
		}
		$aPR = $result->current();
		$fRate = 1 + ($aPR['prRate']/100);

		if (!is_null($iPt))
		{
			$iPr = ceil($iPt / $fRate);
		}
		else
		{
			$iPt = floor($iPr * $fRate);
		}
		return array('pt'=>$iPt,'pr'=>$iPr);
	}
}