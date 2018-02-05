<?php
class Helper_CustomValidation
{
	/**
	 * URLのチェック
	 */
	public static function _validation_url_opt($val, $options=null)
	{
		if (empty($val))
		{
			return true;
		}

		if (substr($val, 0, 2) == '//')
		{
			$val = 'http:'.$val;
		}

		return filter_var($val, FILTER_VALIDATE_URL);
	}

	/**
	 * 先生メールアドレス重複チェック
	 */
	public static function _validation_tmail_chk($val,$sTtID=null,$option = null)
	{
		$aWhere = array(array('ttMail','=',$val));
		if (!is_null($sTtID)) {
			$aWhere[] = array('ttID','!=',$sTtID);
		}

		$result = Model_Teacher::getTeacher($aWhere);
		$row = $result->as_array();
		if (empty($row))
		{
			return true;
		}
		return false;
	}

	/**
	 * 先生ログインID重複チェック
	 */
	public static function _validation_tuid_chk($val,$sGtID=null,$sTtID=null,$option = null)
	{
		if (is_null($sGtID))
		{
			return false;
		}

		$aWhere = array(
			array('tv.ttLoginID','=',$val),
			array('gtp.gtID','=',$sGtID),
		);
		if (!is_null($sTtID)) {
			$aWhere[] = array('gtp.ttID','!=',$sTtID);
		}

		$result = Model_Group::getGroupTeachers($aWhere);
		$row = $result->as_array();
		if (empty($row))
		{
			return true;
		}
		return false;
	}

	/**
	 * 副担当メールアドレス重複チェック
	 */
	public static function _validation_amail_chk($val,$sAtID=null,$option = null)
	{
		$aWhere = array(array('atMail','=',$val));
		if (!is_null($sAtID)) {
			$aWhere[] = array('atID','!=',$sAtID);
		}

		$result = Model_Assistant::getAssistant($aWhere);
		$row = $result->as_array();
		if (empty($row))
		{
			return true;
		}
		return false;
	}

	/**
	 * 学生ログインID重複チェック
	 */
	public static function _validation_slogin_chk($val,$sStID=null,$option = null)
	{
		$aWhere = array(array('stLogin','=',$val));
		if (!is_null($sStID)) {
			$aWhere[] = array('stID','!=',$sStID);
		}

		$result = Model_Student::getStudent($aWhere);
		$row = $result->as_array();
		if (empty($row))
		{
			return true;
		}
		return false;
	}

	/**
	 * 学生メールアドレス重複チェック
	 */
	public static function _validation_smail_chk($val,$sStID=null,$option = null)
	{
		if ($val == '') {
			return true;
		}
		$aWhere = array(array('stMail','=',$val));
		if (!is_null($sStID)) {
			$aWhere[] = array('stID','!=',$sStID);
		}

		$result = Model_Student::getStudent($aWhere);
		$row = $result->as_array();
		if (empty($row))
		{
			return true;
		}
		return false;
	}

	/**
	 * 全角カタカナのみかどうかのバリデーション
	 */
	public static function _validation_katakana($val, $options=null)
	{
		mb_regex_encoding('UTF-8');
		return preg_match('/^[ァ-ヶー\s]+$/u', $val) === 1;
	}

	/**
	 * パスワード検証（2種類以上の文字列混在構成か？）
	 */
	public static function _validation_passwd_char($val,$options=null)
	{
		$iLen = strlen($val);

		// 存在チェック
		if (!$iLen) return false;

		// 使用可能文字チェック
		if(!preg_match('/^[a-zA-Z0-9\._\/-]+$/',$val)) return false;

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
			else if(preg_match("/[\._\/-]/",$sChar))
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

		return true;
	}

	/**
	 * パスワード検証（現在のパスワードと同じか？）
	 */
	public static function _validation_passwd_true($val,$chk,$option=null)
	{
		if (sha1($val) == $chk)
		{
			return true;
		}
		return false;
	}

	/**
	 * パスワード検証（現在のパスワードと異なるか？）
	 */
	public static function _validation_passwd_false($val,$chk,$option=null)
	{
		if (sha1($val) != $chk)
		{
			return true;
		}
		return false;
	}

	/**
	 * 大学名チェック
	 */
	public static function _validation_college_name($val,$option=null)
	{
		if ($val == '')
		{
			return true;
		}
		$result = Model_College::getCollegeFromName($val);
		$row = $result->as_array();
		if (empty($row))
		{
			return false;
		}
		return true;
	}
	/**
	 * 講義コード重複チェック
	 */
	public static function _validation_class_code($val,$option = null)
	{
		if ($val == $option[0]) return true;

		if (isset($option[1]))
		{
			$val = \Clfunc_Common::setCode($val,$option[1]);
		}
		$result = Model_Class::getClassFromCode($val);
		$row = $result->as_array();
		if (empty($row))
		{
			return true;
		}
		return false;
	}

	/**
	 * 簡易日本語日付(YYYY年MM月DD日)のチェック
	 */
	public static function _validation_date_ja($val,$options = null)
	{
		if (!preg_match("/^([0-9]{4})年(1[0-2]|0?[1-9])月(3[0-1]|[1-2][0-9]|0?[1-9])日$/",$val,$matches)) return false;
		if (!checkdate((int)$matches[2],(int)$matches[3],(int)$matches[1])) return false;
		return true;
	}
	/**
	 * 日付(YYYY/MM/DD)のチェック
	 */
	public static function _validation_date($val,$options = null)
	{
		if (!preg_match("/^([0-9]{4})\/(1[0-2]|0?[1-9])\/(3[0-1]|[1-2][0-9]|0?[1-9])$/",$val,$matches))
		{
			\Log::error($val.' - preg_match');
			return false;
		}
		if (!checkdate((int)$matches[2],(int)$matches[3],(int)$matches[1]))
		{
			\Log::error(print_r($matches,true).' - checkdate');
			return false;
		}
		return true;
	}
	/**
	 * 日付(YYYY/MM/DD)の最大値チェック
	 */
	public static function _validation_max_date($val,$options = null)
	{
		$iVal = strtotime($val);
		$iReg = strtotime($options[0]);
		if ($iVal > $iReg) return false;
		return true;
	}
	/**
	 * 日付(YYYY/MM/DD)の最小値チェック
	 */
	public static function _validation_min_date($val,$options = null)
	{
		$iVal = strtotime($val);
		$iReg = strtotime($options[0]);
		if ($iVal < $iReg) return false;
		return true;
	}
	/**
	 * 時間(HH:MM)のフォーマットチェック
	 */
	public static function _validation_time($val,$options = null)
	{
		if (!preg_match("/^([0-9]|[0-1][0-9]|2[0-3]):([0-5][0-9])$/",$val)) return false;
		return true;
	}
	/**
	 * 時間(HH:MM)の最大値チェック
	 */
	public static function _validation_max_time($val,$basic,$date = null)
	{
		if (!is_null($date))
		{
			$iVal = strtotime($date.' '.$val.':00');
		}
		else
		{
			$iVal = strtotime(date('Y/m/d '.$val.':00'));
		}
		$iReg = strtotime($basic);
		if ($iVal > $iReg) return false;
		return true;
	}
	/**
	 * 時間(HH:MM)の最小値チェック
	 */
	public static function _validation_min_time($val,$basic,$date = null)
	{
		if (!is_null($date))
		{
			$iVal = strtotime($date.' '.$val.':00');
		}
		else
		{
			$iVal = strtotime(date('Y/m/d '.$val.':00'));
		}
		$iReg = strtotime($basic);
		if ($iVal < $iReg) return false;
		return true;
	}
	/**
	 * 出席受付中が存在するかチェック
	 */
	public static function _validation_now_attend($val,$options = null)
	{
		$result = Model_Attend::getAttendCalendarActive($options[0]);
		if (count($result)) return false;
		return true;
	}
	/**
	 * 出席予約が存在するかチェック
	 */
	public static function _validation_exists_attend($val,$options = null)
	{
		$sDate  = date('Y-m-d',strtotime($options[1]));
		$sStart = date('Y-m-d H:i:00',strtotime($sDate.' '.$options[2]));
		$sEnd   = date('Y-m-d H:i:00',strtotime($sDate.' '.$val));

		$aWhere = null;
		$aWhere[] = array('ac.abDate','=',$sDate);
		$aWhere[] = array('ac.acAEnd','>=',$sStart);
		$aWhere[] = array('ac.acAStart','<=',$sEnd);
		if (isset($options[3]))
		{
			$aWhere[] = array('ac.no','!=',$options[3]);
		}

		$result = Model_Attend::getAttendCalendarFromClass($options[0],$aWhere);
		if (count($result)) return false;
		return true;
	}
}

