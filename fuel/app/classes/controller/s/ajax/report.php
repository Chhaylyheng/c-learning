<?php
class Controller_S_Ajax_Report extends Controller_S_Ajax
{
	public function post_ReportCommentRes()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromStudent($this->aStudent['stID'],1,$par['ct']);
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
							'rcID'      => $this->aStudent['stID'],
							'rcDate'    => $sDate,
							'rcTeach'   => 0,
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
					if ($aParent['rcID'] != $this->aStudent['stID'])
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
		$res['res']['rcName'] = (($aActive['rcTeach'])? $aActive['ttName']:$aActive['stName']);
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

			if ($aCom['rcID'] != $this->aStudent['stID'])
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('記入者以外は削除できません。'));
				$this->response($res);
				return;
			}

			$result = Model_Report::getReportComment(array(array('rc.rcBranch','=',$iNO)));
			if (count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('配下にコメントがあるため、削除できません。'));
				$this->response($res);
				return;
			}

			try
			{
				$result = Model_Report::deleteReportComment($aCom);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').'('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>array('childNum'=>$result, 'parentNo'=>array($aCom['rcBranch'])));
		}
		$this->response($res);
		return;
	}

	public function post_ReportRate()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Class::getClassFromStudent($this->aStudent['stID'],1,$par['ct']);
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

			$iScore = $par['r'];
			if ($iScore < 1 || $iScore > 5)
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$result = Model_Report::getReportRate(array(array('rr.rbID','=',$sRbID),array('rr.stID','=',$sStID),array('rr.rrID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aRate = $result->current();
				try
				{
					$aUpdate = array(
						'rrScore' => $iScore,
						'rrDate' => date('YmdHis'),
					);
					$result = \Model_Report::updateReportRate($aUpdate,$aRate);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					$res = array('err'=>-2,'res'=>'','msg'=>__('更新に失敗しました。').$e->getMessage());
					$this->response($res);
					return;
				}
			}
			else
			{
				try
				{
					$aInsert = array(
						'rbID' => $sRbID,
						'stID' => $sStID,
						'rrID' => $this->aStudent['stID'],
						'rrScore' => $iScore,
						'rrDate' => date('YmdHis'),
					);
					$result = \Model_Report::insertReportRate($aInsert);
				}
				catch (Exception $e)
				{
					\Clfunc_Common::LogOut($e,__CLASS__);
					$res = array('err'=>-2,'res'=>'','msg'=>__('更新に失敗しました。').$e->getMessage());
					$this->response($res);
					return;
				}
			}

			$result = Model_Report::getReportCount(array(array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID)));
			if (!count($result))
			{
				$result = Model_Report::setReportCount($sRbID,$sStID);
				$result = Model_Report::getReportCount(array(array('rc.rbID','=',$sRbID),array('rc.stID','=',$sStID)));
			}
			$aCount = $result->current();

			$aRes = array('avg'=>$aCount['rcAvg']);

			$aGR = array();
			$aMem = array('r1'=>'','r2'=>'','r3'=>'','r4'=>'','r5'=>'');
			for($i = 1; $i <= 5; $i++)
			{
				if ($i <= $iScore)
				{
					$aMem['r'.$i] = 'fa mr0 fa-star font-red';
				}
				else
				{
					$aMem['r'.$i] = 'fa mr0 fa-star-o font-gray';
				}
				$aGR['gr'.$i]['num'] = $aCount['rc'.$i];
				$aGR['gr'.$i]['avg'] = round(($aCount['rc'.$i] / $aCount['rcNum']) * 100, 1);
			}

			$aRes['mem'] = $aMem;
			$aRes['gr'] = $aGR;

			$res = array('err'=>0,'res'=>$aRes,'msg'=>'');
		}
		$this->response($res);
		return;
	}


}
