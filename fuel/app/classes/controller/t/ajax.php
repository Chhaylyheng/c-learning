<?php
class Controller_T_Ajax extends Controller_Restbase
{
	public $aAssistant = null;
	public $aTeacher = null;
	public $sAwsSavePath = null;
	public $sTempFilePath = null;
	public $sCurrentID = null;

	public function before()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('ログインされていないため処理できません。'));
		parent::before();

		if (Session::get('CL_AL_LOGIN', false))
		{
			$this->bT = false;
			$this->sDir = 'a';
			$sHash = Cookie::get('CL_AL_HASH',false);
			if (!$sHash)
			{
				Response::redirect($this->sDir.'/login/index/1');
			}
			$aLogin = unserialize(Crypt::decode($sHash));

			$result = Model_Assistant::getAssistantFromHash($aLogin['hash']);
			if (!count($result))
			{
				Response::redirect($this->sDir.'/login/index/1');
			}
			$this->aAssistant = $result->current();

			$this->sAwsSavePath = 'assistant'.DS.$this->aAssistant['atID'];
			$this->sTempFilePath = CL_UPPATH.DS.'temp';

			$result = Model_Teacher::getTeacherFromID($this->aAssistant['ttID']);
			if (!count($result))
			{
				Response::redirect($this->sDir.'/login/index/1');
			}
			$this->aTeacher = $result->current();
			$this->aTeacher['ttTimeZone'] = $this->aAssistant['atTimeZone'];
			$this->sCurrentID = $this->aAssistant['atID'];
		}
		else
		{
			$sHash = Cookie::get('CL_TL_HASH',false);
			if (!$sHash)
			{
				$this->response($res);
				return;
			}
			$aLogin = unserialize(Crypt::decode($sHash));

			$result = Model_Teacher::getTeacherFromHash($aLogin['hash']);
			if (!count($result))
			{
				$res['msg'] = __('ログイン情報が取得できないため、処理を続行することはできません。');
				$this->response($res);
				return;
			}
			$this->aTeacher = $result->current();
			$this->sAwsSavePath = 'teacher'.DS.$this->aTeacher['ttID'];
			$this->sTempFilePath = CL_UPPATH.DS.'temp';
			$this->sCurrentID = $this->aTeacher['ttID'];
		}
		$this->tz = $this->aTeacher['ttTimeZone'];
	}

	public function post_schoolname()
	{
		$res = array();
		$par = Input::post('param1');
		if ($par)
		{
			$result = Model_College::getCollegeLikeName($par);
			foreach ($result as $r)
			{
				$res[] = $r['cmName'];
			}
		}
		$this->response($res);
		return;
	}

	public function post_deptlist()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post('college');
		if ($par)
		{
			$result = Model_College::getCollegeFromName($par);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'');
			}
			else
			{
				$aRes = $result->as_array();
				$sKCode = $aRes[0]['cmKCode'];
				$result = Model_College::getDeptListFromKCode($sKCode);
				if (!count($result)) {
					$res = array('err'=>-1,'res'=>'');
				}
				else
				{
					$res['err'] = 0;
					foreach ($result as $r)
					{
						$res['res'][]['dmName'] = $r['dmName'];
					}
				}
			}
		}
		$this->response($res);
		return;
	}

	public function post_deptformlist()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post('college');
		if ($par)
		{
			$result = Model_College::getCollegeFromName($par);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'');
			}
			else
			{
				$aRes = $result->as_array();
				$sKCode = $aRes[0]['cmKCode'];
				$result = Model_College::getDeptListFromKCode($sKCode);
				if (!count($result)) {
					$res['err'] = 0;
					$res['res']['dept'][0]['dmNO'] = 0;
					$res['res']['dept'][0]['dmName'] = __('学部指定なし');
				}
				else
				{
					$res['err'] = 0;
					$i = 0;
					foreach ($result as $r)
					{
						$res['res']['dept'][$i]['dmNO'] = $r['dmNO'];
						$res['res']['dept'][$i]['dmName'] = $r['dmName'];
						$i++;
					}
				}
				$aInput = array('cmKCode'=>$sKCode,'dmNO'=>$res['res']['dept'][0]['dmNO']);
				$result = Model_College::getPeriod($aInput);
				$res['res']['period'][0]['dpNO'] = 0;
				$res['res']['period'][0]['dpText'] = __('指定なし');
				if (count($result))
				{
					$i = 1;
					foreach ($result as $r)
					{
						$res['res']['period'][$i]['dpNO'] = $r['dpNO'];
						$res['res']['period'][$i]['dpText'] = $r['dpName'].'（'.date('n/j',strtotime(date('Y').'-'.$r['dpStartDate'])).'～'.date('n/j',strtotime(date('Y').'-'.$r['dpEndDate'])).'）';
						$i++;
					}
				}
				$result = Model_College::getHour($aInput);
				$res['res']['hour'][0]['dhNO'] = 0;
				$res['res']['hour'][0]['dhText'] = __('指定なし');
				if (count($result))
				{
					$i = 1;
					foreach ($result as $r)
					{
						$res['res']['hour'][$i]['dhNO'] = $r['dhNO'];
						$res['res']['hour'][$i]['dhText'] = $r['dhName'].'（'.date('H:i',strtotime($r['dhStartTime'])).'～'.date('H:i',strtotime($r['dhEndTime'])).'）';
						$i++;
					}
				}
			}
		}
		$this->response($res);
		return;
	}

	public function post_periodlist()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();
		if ($par)
		{
			$aInput = array('cmName'=>$par['college'],'dmName'=>$par['dept']);
			$res['err'] = 0;

			$result = Model_College::getPeriod($aInput);
			$res['res']['period'][0]['dpNO'] = 0;
			$res['res']['period'][0]['dpText'] = __('指定なし');
			if (count($result))
			{
				$i = 1;
				foreach ($result as $r)
				{
					$res['res']['period'][$i]['dpNO'] = $r['dpNO'];
					$res['res']['period'][$i]['dpText'] = $r['dpName'].'（'.date('n/j',strtotime(date('Y').'-'.$r['dpStartDate'])).'～'.date('n/j',strtotime(date('Y').'-'.$r['dpEndDate'])).'）';
					$i++;
				}
			}
			$result = Model_College::getHour($aInput);
			$res['res']['hour'][0]['dhNO'] = 0;
			$res['res']['hour'][0]['dhText'] = __('指定なし');
			if (count($result))
			{
				$i = 1;
				foreach ($result as $r)
				{
					$res['res']['hour'][$i]['dhNO'] = $r['dhNO'];
					$res['res']['hour'][$i]['dhText'] = $r['dhName'].'（'.date('H:i',strtotime($r['dhStartTime'])).'～'.date('H:i',strtotime($r['dhEndTime'])).'）';
					$i++;
				}
			}
		}
		$this->response($res);
		return;
	}

	public function post_SwitchAttendState()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post(null,false);

		try
		{
			if (!$par)
			{
				throw new Exception('e');
			}

			$result = Model_Attend::getAttendCalendarFromClass($par['ct'],array(array('ac.abDate','=',$par['date']),array('ac.acNO','=',$par['no'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'');
				throw new Exception('e');
			}
			$aCal = $result->current();

			$result = Model_Attend::getAttendMasterFromClass($par['ct'],array(array('amAttendState','=',$par['state'])));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'');
				throw new Exception('e');
			}
			$aMas = $result->current();

			$sDate = date('Y-m-d H:i:s');
			$result = Model_Attend::getAttendBookFromClass($par['ct'],array(array('abDate','=',$par['date']),array('acNO','=',$par['no']),array('stID','=',$par['st'])));
			if (!count($result))
			{
				$aInsert = array(
					'ctID'=>$par['ct'],
					'abDate'=>$par['date'],
					'acNO'=>$par['no'],
					'stID'=>$par['st'],
					'amAttendState'=>$par['state'],
					'abModifyDate'=>$sDate,
				);
				$result = Model_Attend::insertAttendBook($aInsert);
			}
			else
			{
				$aBook = $result->current();
				$aInsert = array(
					'amAttendState'=>$par['state'],
					'abAttendDate'=>CL_DATETIME_DEFAULT,
					'abModifyDate'=>$sDate,
				);
				$result = Model_Attend::updateAttendBook(array(array('no','=',$aBook['no'])),$aInsert);
			}
			$result = Model_Student::getStudentFromClass($par['ct'],array(array('sp.stID','=',$par['st'])));
			$aSt = $result->current();
			$result = Model_Attend::getAttendCalendarFromNO($aCal['no']);
			$aCal = $result->current();
			$sStyle = ($aMas['amAbsence'])? 'font-red':(($aMas['amTime'])? 'font-green':'font-blue');

			$res = array(
				'err'=>0,
				'res'=>array(
					'stID'=>$aSt['stID'],
					'abDate'=>$aCal['abDate'],
					'acNO'=>$aCal['acNO'],
					'stAbNum'=>$aSt['abNum'],
					'AbNum'=>$aCal['abNum'],
					'amShort'=>$aMas['amShort'],
					'amName'=>$aMas['amName'],
					'amClass'=>$sStyle,
					'abModifyDate'=>ClFunc_Tz::tz('Y/m/d G:i',$this->tz,$sDate),
				),
			);
		}
		catch (Exception $e)
		{
			$this->response($res);
			return;
		}
		$this->response($res);
		return;
	}

	public function post_KReportLikeUP()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'レポート情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$result = Model_Teacher::getTeacher(array(array('ttID','=',$par['ta']),array('ttStatus','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の先生情報が見つかりません。'.$par['ta']);
				$this->response($res);
				return;
			}
			$aMine = $result->current();

			$result = Model_KReport::getKReportPut(array(array('krYear','=',$par['y']),array('krPeriod','=',$par['p']),array('ttID','=',$par['tt']),array('krSub','=',$par['sub'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'指定のレポートの提出状況が見つかりません。');
				$this->response($res);
				return;
			}
			$aPut = $result->current();

			$bUpdate = false;
			$result = Model_KReport::getKReportAlready(array(array('krYear','=',$par['y']),array('krPeriod','=',$par['p']),array('ttID','=',$par['tt']),array('krSub','=',$par['sub']),array('kaID','=',$par['ta'])));
			if (count($result))
			{
				$bUpdate = true;
				$aAlready = $result->current();
				if ($aAlready['kaLike'] == 1)
				{
					$res = array('err'=>0,'res'=>array('num'=>$aPut['krLike']),'msg'=>'');
					$this->response($res);
					return;
				}
			}

			try
			{
				$result = Model_KReport::setKReportAlready('like',$aMine,$aPut,$bUpdate);
			}
			catch (Exception $e)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}

			$res = array('err'=>0,'res'=>array('num'=>$aPut['krLike'] + 1),'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_KReportCommentSet()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'コメント情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$result = Model_KReport::getKReportBase(array(array('krYear','=',$par['y']),array('krPeriod','=',$par['p'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象のレポート情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			$result = Model_Teacher::getTeacher(array(array('ttID','=',$par['tt']),array('ttStatus','=',1)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の先生情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aTeacher = $result->current();

			$aPut['ttID'] = $par['put'];
			if ($par['put'] != 'ALL')
			{
				$result = Model_Teacher::getTeacher(array(array('ttID','=',$par['put']),array('ttStatus','=',1)));
				if (!count($result))
				{
					$res = array('err'=>-2,'res'=>'','msg'=>'レポート提出の先生情報が見つかりません。');
					$this->response($res);
					return;
				}
				$aPut = $result->current();
			}

			if (!trim($par['txt']))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'コメントが入力されていません。');
				$this->response($res);
				return;
			}

			$aInput = array(
				'krYear' => $aReport['krYear'],
				'krPeriod' => $aReport['krPeriod'],
				'putID' => $aPut['ttID'],
				'ttID' => $aTeacher['ttID'],
				'krSub' => (isset($par['sub']))? $par['sub']:1,
				'kcComment' => $par['txt'],
				'kcDate' => date('YmdHis'),
			);

			try
			{
				$result = Model_KReport::setKReportComment($aInput);
			}
			catch (Exception $e)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}

			if ($aPut['ttID'] != $aTeacher['ttID'] && $aPut['ttID'] != 'ALL')
			{
				$sName = 'ケータイ研レポート（'.$aReport['krYear'].'年度 '.(($aReport['krPeriod'] == 1)? '4～9月期':'10～3月期').'）';
				# 購入完了メール
				$email = \Email::forge();
				$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
				$email->to($aPut['ttMail']);
				$email->subject('[CL]ケータイ研レポート：コメント投稿のお知らせ');
				$body = View::forge('email/t_kreport_com', array('aP'=>$aPut,'aT'=>$aTeacher,'sName'=>$sName,'sCom'=>$par['txt']));
				$email->body($body);

				try
				{
					$email->send();
				}
				catch (\EmailValidationFailedException $e)
				{
					Log::warning('TeacherKReportCommentMail - ' . $e->getMessage());
				}
				catch (\EmailSendingFailedException $e)
				{
					Log::warning('TeacherKReportCommentMail - ' . $e->getMessage());
				}
			}

			$res = array('err'=>0,'res'=>'','msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_KReportCommentGet()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'コメント取得情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$aLists = (isset($par['lists']))? $par['lists']:array(0);

			$aAndWhere = array(array('krYear','=',$par['y']),array('krPeriod','=',$par['p']));

			$result = Model_KReport::getKReportBase($aAndWhere);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象のレポート情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			$aTeacher['ttID'] = 'Admin';
			if ($par['tt'] != 'Admin')
			{
				$result = Model_Teacher::getTeacher(array(array('ttID','=',$par['tt']),array('ttStatus','=',1)));
				if (!count($result))
				{
					$res = array('err'=>-2,'res'=>'','msg'=>'対象の先生情報が見つかりません。');
					$this->response($res);
					return;
				}
				$aTeacher = $result->current();
			}

			$aData = null;
			$resData = array();
			$iLimit = (int)$par['limit'];
			$bSort = true;
			$aAndWhereC = $aAndWhere;
			if ($par['put'] != 'ALL')
			{
				$aAndWhereC[] = array('putID','=',$par['put']);
				$aAndWhereC[] = array('krSub','=',$par['sub']);
			}
			if ($par['s'] > 0)
			{
				$aAndWhereC[] = array('no','<',(int)$par['s']);
				$bSort = false;
			}

			$iS = 0;
			$result = Model_KReport::getKReportComment($aAndWhereC,null,array('no'=>'desc'),array($iLimit));
			if (count($result))
			{
				$aData = $result->as_array('no');
				ksort($aData);
				$iS = key($aData);
				if (!$bSort)
				{
					krsort($aData);
				}
				$i = 1;
				foreach ($aData as $no => $aD)
				{
					$resData[$i] = array(
						'no'        => $no,
						'ttName'    => (($aD['ttName'])? $aD['ttName']:$aD['ttMail']),
						'cmName'    => ((isset($aD['cmName']))? $aD['cmName']:''),
						'kcComment' => nl2br(htmlspecialchars($aD['kcComment'])),
						'ttImage'   => (($aD['ttImage'])? '/upload/profile/t/'.$aD['ttImage'].'?'.mt_rand():'/'.Asset::find_file('img_no_icon.png','img')),
						'kcDate'    => ClFunc_Tz::tz('n/j<\b\\r>H:i',$this->tz,$aD['kcDate']),
						'kcDel'     => $aD['kcDel'],
						'mine'      => 0,
						'n'         => 0,
						'ptName'    => (($aD['putName'])? $aD['putName']:$aD['putMail']),
						'pcName'    => ((isset($aD['putCmName']))? $aD['putCmName']:''),
					);
					if (array_search($no,$aLists) === false)
					{
						if ($no > $par['s'])
						{
							# 新規追加（後付）
							$resData[$i]['n'] = 1;
						}
						else
						{
							# 新規追加（前付）
							$resData[$i]['n'] = 2;
						}
					}
					if ($aD['ttID'] == $aTeacher['ttID'])
					{
						$resData[$i]['mine'] = 1;
					}
					$i++;
				}
			}
			else
			{
				$resData = 0;
			}
			$sCnt = ($iS < $par['s'] || $par['s'] == 0)? $iS:$par['s'];

			$iMore = 0;
			if ($iS > 0)
			{
				$no = ($iS < $aLists[0] || $aLists[0] == 0)? $iS:$aLists[0];
				$aAndWhereC = $aAndWhere;
				if ($par['put'] != 'ALL')
				{
					$aAndWhereC[] = array('putID','=',$par['put']);
					$aAndWhereC[] = array('krSub','=',$par['sub']);
				}
				$aAndWhereC[] = array('no','<',(int)$no);
				$result = Model_KReport::getKReportComment($aAndWhereC);
				if (count($result))
				{
					$iMore = 1;
				}
			}
			$res = array('err'=>0,'res'=>array('data'=>$resData,'cnt'=>$sCnt,'more'=>$iMore),'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_StudentPassReset()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('パスワードリセット情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($par['tt'],1,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('対象の先生情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aTeacher = $result->current();
			$result = Model_Student::getStudentFromClass($par['ct'],array(array('sp.stID','=',$par['st'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('対象の学生情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aStudent = $result->current();

			$sFirst = strtolower(Str::random('distinct', 8));
			$sPass = sha1($sFirst);
			$sHash = sha1($aStudent['stLogin'].$sPass);

			$aInsert = array(
				'stPass'     => $sPass,
				'stFirst'    => $sFirst,
				'stPassDate' => '00000000',
				'stPassMiss' => 0,
				'stHash'     => $sHash,
			);

			try
			{
				$sStID = Model_Student::updateStudent($aStudent['stID'],$aInsert);
			}
			catch (Exception $e)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').'('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>array('pw'=>$sFirst,'msg'=>''));
		}
		$this->response($res);
		return;
	}

}
