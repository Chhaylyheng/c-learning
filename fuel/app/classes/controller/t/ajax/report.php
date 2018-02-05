<?php
class Controller_T_Ajax_Report extends Controller_T_Ajax
{
	public function post_Sort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = \Model_Report::getReportBase(array(array('rb.rbID','=',$par['rb'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aReport = $result->current();
			$result = Model_Report::getReportBase(array(array('rb.ctID','=',$par['ct'])),null,array('rb.rbSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aReport['rbSort'] == $iMax && $par['m'] == 'up') || ($aReport['rbSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のレポートテーマのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Report::sortReport($aReport,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_ShareOpen()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$par['rb'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			if ($par['m'] == 'share1')
			{
				$iShare = 1;
				$sText = __('共有中');
				$sClass = 'font-green';
				$iAnony = 1;
			}
			elseif ($par['m'] == 'share2')
			{
				$iShare = 2;
				$sText = __('共有中（相互評価）');
				$sClass = 'font-blue';
				$iAnony = 1;
			}
			elseif ($par['m'] == 'share0')
			{
				$iShare = 0;
				$sText = __('共有なし');
				$sClass = 'font-default';
				$iAnony = 0;
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('共有情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$aUpdate = array(
				'rbShare' => $iShare,
			);

			$result = Model_Report::updateReport($aUpdate,array(array('rbID','=',$aReport['rbID'])));

			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText,'anony'=>$iAnony),'msg'=>__('共有設定を変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_ShareAnony()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$par['rb'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			if ($par['m'] == 'anony1')
			{
				$iShare = 1;
				$sText = __('先生のみ記名');
				$sClass = 'font-green';
			}
			elseif ($par['m'] == 'anony2')
			{
				$iShare = 2;
				$sText = __('記名');
				$sClass = 'font-blue';
			}
			elseif ($par['m'] == 'anony0')
			{
				$iShare = 0;
				$sText = __('匿名');
				$sClass = 'font-default';
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('匿名情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$aUpdate = array(
				'rbAnonymous' => $iShare,
			);

			$result = Model_Report::updateReport($aUpdate,array(array('rbID','=',$aReport['rbID'])));

			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>__('匿名設定を変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_ResultPublic()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$par['rb'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			if ($par['m'] == 'public')
			{
				$iPub = 1;
				$sText = __('公開中');
				$sClass = 'font-blue';
			}
			elseif ($par['m'] == 'private')
			{
				$iPub = 0;
				$sText = __('非公開');
				$sClass = 'font-default';
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('公開情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$aUpdate = array(
				'rbRatePublic' => $iPub,
			);

			$result = Model_Report::updateReport($aUpdate,array(array('rbID','=',$aReport['rbID'])));

			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>__('評価の公開情報を変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_Public()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$par['rb'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			$aUpdate = array();
			$sDate = date('YmdHis');
			$sTimer = null;
			if ($par['m'] == 'public')
			{
				$iPub = 1;
				$sText = __('公開中');
				$sClass = 'font-blue';
				$aUpdate['rbAutoPublicDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['rbPublicDate'] = $sDate;
				if ($aReport['rbAutoCloseDate'] != CL_DATETIME_DEFAULT)
				{
					$sTimer = '～ '.ClFunc_Tz::tz('n/j H:i',$this->tz,$aReport['rbAutoCloseDate']);
				}
			}
			elseif ($par['m'] == 'close')
			{
				$iPub = 2;
				$sText = __('締切');
				$aUpdate['rbAutoCloseDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['rbCloseDate'] = $sDate;
				$sClass = 'font-red';
			}
			elseif ($par['m'] == 'private')
			{
				$iPub = 0;
				$sText = __('非公開');
				$sClass = 'font-default';
				$aUpdate['rbPublicDate'] = CL_DATETIME_DEFAULT;
				$aUpdate['rbCloseDate'] = CL_DATETIME_DEFAULT;
				if ($aReport['rbAutoPublicDate'] != CL_DATETIME_DEFAULT)
				{
					$sTimer = ClFunc_Tz::tz('n/j H:i',$this->tz,$aReport['rbAutoPublicDate']).' ～';
				}
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('公開情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$aUpdate['rbPublic'] = $iPub;
			$result = Model_Report::updateReport($aUpdate,array(array('rbID','=',$aReport['rbID'])));
			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText,'timer'=>$sTimer),'msg'=>__('公開情報を変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_ArchiveDownloadBtn()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$par['rb'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			$result = null;
			switch ($aReport['zipProgress'])
			{
				case 0:
					$result['status'] = 0;
					$result['href'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aReport['zipFID'],'mode'=>'e'));
					$result['text'] = __('アーカイブファイルのダウンロード').' ('.ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$aReport['zipFDate']).' '.\Clfunc_Common::FilesizeFormat($aReport['zipFSize'],1).')';
				break;
				case 1:
					$result['status'] = 1;
				break;
				case 2:
					$result['status'] = 2;
					$result['text'] = __('アーカイブファイルの作成失敗');
				break;
			}
			$res = array('err'=>0, 'res'=>$result, 'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_ReportCommentRes()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$sRbID = $par['rb'];
			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$sRbID)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			$sStID = $par['st'];
			$result = Model_Report::getReportPut(array(array('rp.rbID','=',$sRbID),array('rp.stID','=',$sStID)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('レポート提出情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aPut = $result->current();

			$iNO = $par['no'];
			if ($iNO != 0)
			{
				$result = Model_Report::getReportComment(array(array('rc.no','=',$iNO),array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID)));
				if (!count($result))
				{
					$res = array('err'=>-2,'res'=>'','msg'=>__('記事情報が見つかりません。'));
					$this->response($res);
					return;
				}
				$aParent = $result->current();
			}

			$sCID = (!is_null($this->aAssistant))? $this->aAssistant['atID']:$this->aTeacher['ttID'];

			$sResMsg = __('登録');
			$sDate = date('Y-m-d H:i:s');

			switch ($par['m'])
			{
				case 'cstart':
				case 'input':
					$sResMsg = __('コメントを登録しました。');
					try
					{
						$aInsert = array(
							'rbID'      => $sRbID,
							'stID'      => $sStID,
							'rcComment' => $par['c_text'],
							'rcID'      => $sCID,
							'rcDate'    => $sDate,
							'rcTeach'   => 1,
							'rcBranch'  => $iNO,
						);
						$iNO = \Model_Report::insertReportComment($aInsert);
					}
					catch (Exception $e)
					{
						\Clfunc_Common::LogOut($e,__CLASS__);
						$res = array('err'=>-2,'res'=>'','msg'=>__('コメントの登録に失敗しました。').$e->getMessage());
						$this->response($res);
						return;
					}
				break;
				case 'edit':
					if ($aParent['rcID'] != $this->aTeacher['ttID'])
					{
						$res = array('err'=>-2,'res'=>'','msg'=>__('記事情報が見つかりません。'));
						$this->response($res);
						return;
					}

					$sResMsg = __('コメントを更新しました。');
					try
					{
						$aUpdate = array(
							'rcComment' => $par['c_text'],
							'rcDate'    => $sDate,
						);
						$aWhere = array(array('no','=',$iNO));
						$result = \Model_Report::updateReportComment($aUpdate,$aWhere);
					}
					catch (Exception $e)
					{
						\Clfunc_Common::LogOut($e,__CLASS__);
						$res = array('err'=>-2,'res'=>'','msg'=>__('コメントの更新に失敗しました。').$e->getMessage());
						$this->response($res);
						return;
					}
				break;
			}
		}

		$res['err'] = 0;
		$res['res'] = array(
			'no'        => $iNO,
			'rcName'    => '',
			'rcBranch'  => 0,
			'rcDate'    => ClFunc_Tz::tz('Y/m/d H:i',$this->tz,$sDate),
			'rcComment' => nl2br(\Clfunc_Common::url2link($par['c_text'],480)),
		);

		$result = \Model_Report::getReportComment(array(array('no','=',$iNO)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>__('記事情報が見つかりません。'));
			$this->response($res);
			return;
		}
		$aActive = $result->current();

		$res['res']['rcBranch'] = (int)$aActive['rcBranch'];
		$res['res']['rcName'] = (($aActive['rcTeach'])? (($aActive['atName'])? $aActive['atName']:$aActive['ttName']):$aActive['stName']);
		$res['msg'] = $sResMsg;
		$this->response($res);
		return;
	}

	public function post_ReportCommentDelete()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$sRbID = $par['rb'];
			$sStID = $par['st'];
			$iNO = $par['no'];

			$result = Model_Report::getReportBase(array(array('rb.rbID','=',$sRbID)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定のレポートテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aReport = $result->current();
			$result = Model_Report::getReportPut(array(array('rp.rbID','=',$sRbID),array('rp.stID','=',$sStID)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('レポート提出情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aPut = $result->current();

			$result = Model_Report::getReportComment(array(array('rc.no','=',$iNO),array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID)));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('記事情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$aCom = $result->current();

			try
			{
				$result = Model_Report::deleteReportComment($aCom);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>'処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>array('childNum'=>$result, 'parentNo'=>array($aCom['rcBranch'])));
		}
		$this->response($res);
		return;
	}


}
