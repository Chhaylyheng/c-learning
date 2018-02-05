<?php
class Controller_S_Drill extends Controller_S_Baseclass
{
	private $baseName = 'drill';

	private $aDCategory = null;
	private $aDrill = null;
	private $aQuery = null;
	private $sPSep = '?';

	public function before()
	{
		parent::before();

		$this->sPSep = ($this->sesParam)? '&':'?';
	}

	public function action_index()
	{

		$aDCategory = null;
		$result = Model_Drill::getDrillCategoryFromClass($this->aClass['ctID'],array(array('dcPubNum','>',0)),null,array('dcSort'=>'desc'));
		if (count($result))
		{
			$aDCategory = $result->as_array();
		}

		if (count($aDCategory) == 1)
		{
			Response::redirect('/s/'.$this->baseName.'/list/'.$aDCategory[0]['dcID'].$this->sesParam);
		}

		# タイトル
		$sTitle = __('ドリル');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/index');
		$this->template->content->set('aDCategory',$aDCategory);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_list($sID = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aDCategory',$this->aDCategory);

		$result = Model_Drill::getDrill(array(array('dcID','=',$sID),array('dbPublic','!=',0)),null,array('dbSort'=>'desc'));
		if (count($result))
		{
			$this->aDrill = $result->as_array('dbNO');
		}

		if (!is_null($this->aDrill))
		{
			$result = Model_Drill::getDrillPut(array(array('dcID','=',$sID),array('stID','=',$this->aStudent['stID'])),null,array('dbNO'=>'asc','dpDate'=>'asc'));
			if (count($result))
			{
				foreach ($result as $aP)
				{
					$this->aDrill[$aP['dbNO']]['dpDate'] = $aP['dpDate'];
					if (isset($this->aDrill[$aP['dbNO']]['dpNum']))
					{
						$this->aDrill[$aP['dbNO']]['dpNum']++;
						$this->aDrill[$aP['dbNO']]['dpTotal'] += (float)$aP['dpAvg'];
					}
					else
					{
						$this->aDrill[$aP['dbNO']]['dpNum'] = 1;
						$this->aDrill[$aP['dbNO']]['dpTotal'] = (float)$aP['dpAvg'];
					}
				}
			}
		}

		# タイトル
		$sTitle = $this->aDCategory['dcName'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName,'name'=>__('ドリル'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/list');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_ans($sID = null, $iDbNO = null)
	{
		$iQqNO = (int)Input::get('qq') - 1;

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sSesPub = 'DTAKE_'.$sID.'_'.$iDbNO.'_'.$this->aStudent['stID'];
		$sSesRight = 'DANS_'.$sID.'_'.$iDbNO.'_'.$this->aStudent['stID'];

		if ($iQqNO == -1) {
			Session::delete($sSesRight);
			if (Session::get($sSesPub,false)) {
				Session::delete($sSesPub);
			}
			if ($this->aDrill['dbRand'])
			{
				# 設問番号をランダム生成
				$aAll = null;
				for ($i = 1; $i <= $this->aDrill["dbQueryNum"]; $i++) {
					$aAll[$i] = $i;
				}
				$aRand = array_rand($aAll, $this->aDrill["dbPublicNum"]);
				if (!is_array($aRand)) {
					$aRand = array($aRand);
				}
				shuffle($aRand);
			}
			else
			{
				for ($i = 1; $i <= $this->aDrill["dbPublicNum"]; $i++) {
					$aRand[] = $i;
				}
			}
			# セッションに格納
			Session::set($sSesPub, $aRand);
			# 問題へジャンプ
			Response::redirect('/s/'.$this->baseName.'/ans/'.$sID.DS.$iDbNO.DS.$this->sesParam.$this->sPSep.'qq=1');
		}
		else
		{
			if (!Session::get($sSesPub,false)) {
				# 問題列作り直し
				Response::redirect('/s/'.$this->baseName.'/ans/'.$sID.DS.$iDbNO.DS.$this->sesParam);
			}
			$aQPub = Session::get($sSesPub);
			$iDqSort = $aQPub[$iQqNO];

			$aChk = self::QueryChecker($sID,$iDbNO,null,$iDqSort);
			if (is_array($aChk))
			{
				Session::set('SES_S_ERROR_MSG',$aChk['msg']);
				Response::redirect($aChk['url']);
			}
		}
		$aRight = null;
		if (Session::get($sSesRight,false))
		{
			$aRight = Session::get($sSesRight);
			if (isset($aRight[$iQqNO]))
			{
				if (($iQqNO + 2) < $this->aDrill['dbPublicNum'])
				{
					# 問題へジャンプ
					Response::redirect('/s/'.$this->baseName.'/ans/'.$sID.DS.$iDbNO.DS.$this->sesParam.$this->sPSep.'qq='.($iQqNO + 2));
				}
				else
				{
					Response::redirect('/s/'.$this->baseName.'/fin/'.$sID.DS.$iDbNO.DS.$this->sesParam);
				}
			}
		}

		# タイトル
		$sTitle = $this->aDrill['dbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリル'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID, 'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/ans');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->content->set('aQuery',$this->aQuery);
		$this->template->content->set('iQqNO',$iQqNO);
		$this->template->content->set('aDebug',$aRight);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	public function post_anschk($sID = null, $iDbNO = null)
	{
		if (!Input::post(null,false))
		{
			Session::set('SES_S_ERROR_MSG',__('必要な情報が送信されていません。'));
			Response::redirect('/s/'.$this->baseName);
		}

		$sSesPub = 'DTAKE_'.$sID.'_'.$iDbNO.'_'.$this->aStudent['stID'];
		$sSesRight = 'DANS_'.$sID.'_'.$iDbNO.'_'.$this->aStudent['stID'];

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if (!Session::get($sSesPub,false)) {
			Session::set('SES_S_ERROR_MSG',__('必要な情報が送信されていません。'));
			Response::redirect('/s/'.$this->baseName);
		}
		$aQPub = Session::get($sSesPub);

		$aPost = Input::post();

		$iQqNO = (int)$aPost['qq'] - 1;
		$iDqSort = $aQPub[$iQqNO];
		$aChk = self::QueryChecker($sID,$iDbNO,null,$iDqSort);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sAnswer = '';
		switch($this->aQuery['dqStyle'])
		{
			case 0:
				if (isset($aPost['radioSel']))
				{
					$sAnswer = $aPost['radioSel'];
				}
			break;
			case 1:
				if (isset($aPost['checkSel']))
				{
					$sChecks = implode("|",$aPost['checkSel']);
					$sAnswer = $sChecks;
				}
				break;
			case 2:
				if (isset($aPost['textAns']))
				{
					$sTemp = ClFunc_Common::convertKana(preg_replace(CL_WHITE_TRIM_PTN, '$1', $aPost['textAns']),'aqpsu');
					$sTemp = str_replace(array("\r\n","\r"), "\n", $sTemp);
					$sAnswer = trim($sTemp);
				}
			break;
		}
		$iRight = self::RightChecker($this->aQuery,$sAnswer);

		$aRight = Session::get($sSesRight,null);
		$aRight[$iQqNO] = array($this->aQuery['dqNO'],$iRight);
		Session::set($sSesRight,$aRight);

		# タイトル
		$sTitle = $this->aDrill['dbTitle'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリル'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID, 'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/chk');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->content->set('aQuery',$this->aQuery);
		$this->template->content->set('iRight',$iRight);
		$this->template->content->set('sAnswer',$sAnswer);
		$this->template->content->set('iQqNO',$iQqNO);
		$this->template->content->set('aDebug',$aRight);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_fin($sID = null, $iDbNO = null)
	{
		$sSesPub = 'DTAKE_'.$sID.'_'.$iDbNO.'_'.$this->aStudent['stID'];
		$sSesRight = 'DANS_'.$sID.'_'.$iDbNO.'_'.$this->aStudent['stID'];

		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		if (!Session::get($sSesRight,false))
		{
			Session::set('SES_S_ERROR_MSG',__('ドリルの解答情報が見つかりませんでした。'));
			Response::redirect('/s/'.$this->baseName);
		}
		$aRight = Session::get($sSesRight,false);

		$iRight = 0;
		$aAnsInsert = array();
		foreach ($aRight as $aR)
		{
			$aAnsInsert[] = array(
				'dqNO' => $aR[0],
				'daRight' => $aR[1],
			);
			if ($aR[1])
			{
				$iRight++;
			}
		}
		$aPutInsert = array(
			'dpQNum' => (int)$this->aDrill['dbPublicNum'],
			'dpRNum' => (int)$iRight,
			'dpAvg'  => round((((int)$iRight / (int)$this->aDrill['dbPublicNum']) * 100), 1),
		);

		try
		{
			Model_Drill::setDrillPut($this->aDrill,$aPutInsert,$aAnsInsert,$this->aStudent);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_S_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::delete($sSesRight);
		Session::delete($sSesPub);

		Session::set('SES_S_NOTICE_MSG',__(':nameを実施しました。',array('name'=>$this->aDrill['dbTitle'])));
		Response::redirect('/s/drill/list/'.$sID.DS.$this->sesParam);
	}

	public function action_put($sID = null, $iDbNO = null)
	{
		$aChk = self::DrillCategoryChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::DrillChecker($sID,$iDbNO);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aPut = null;
		$result = Model_Drill::getDrillPut(array(array('dcID','=',$sID),array('dbNO','=',$iDbNO),array('stID','=',$this->aStudent['stID'])),null,array('dpDate'=>'desc'));
		if (count($result))
		{
			$aPut = $result->as_array();
		}

		# タイトル
		$sTitle = $this->aDrill['dbTitle'].'｜'.__('実施結果');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->baseName, 'name'=>__('ドリル'));
		$this->aBread[] = array('link'=>'/'.$this->baseName.'/list/'.$sID, 'name'=>$this->aDCategory['dcName']);
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/put');
		$this->template->content->set('aDrill',$this->aDrill);
		$this->template->content->set('aPut',$aPut);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	private function DrillCategoryChecker($sID = null)
	{
		if (is_null($sID))
		{
			return array('msg'=>__('カテゴリ情報が送信されていません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$result = Model_Drill::getDrillCategoryFromID($sID);
		if (!count($result))
		{
			return array('msg'=>__('指定されたカテゴリが見つかりません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$this->aDCategory = $result->current();

		return true;
	}

	private function DrillChecker($sID = null, $iNO = null)
	{
		if (is_null($sID) || is_null($iNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$result = Model_Drill::getDrill(array(array('dcID','=',$sID),array('dbNO','=',$iNO),array('dbPublic','!=',0)));
		if (!count($result))
		{
			return array('msg'=>__('指定されたドリルが見つかりません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$this->aDrill = $result->current();

		return true;
	}

	private function QueryChecker($sDcID = null, $iDbNO = null, $iDqNO = null, $iDqSort = null)
	{
		if (is_null($sDcID) || is_null($iDbNO) || (is_null($iDqNO) && is_null($iDqSort)))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$aQWhere = null;
		if (!is_null($iDqNO))
		{
			$aQWhere = array('dqNO','=',$iDqNO);
		}
		else
		{
			$aQWhere = array('dqSort','=',$iDqSort);
		}
		$result = Model_Drill::getDrillQuery(array(array('dcID','=',$sDcID),array('dbNO','=',$iDbNO),$aQWhere));
		if (!count($result))
		{
			return array('msg'=>__('指定されたドリル問題が見つかりません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}
		$this->aQuery = $result->current();

		return true;
	}

	private function RightChecker($aQuery = null,$sAnswer = null)
	{
		if (is_null($aQuery) || is_null($sAnswer))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/s/'.$this->baseName.$this->sesParam);
		}

		$iRight = 0;
		switch ($aQuery['dqStyle'])
		{
			case 0:
			case 1:
				$aChoice = explode('|', $sAnswer);
				sort($aChoice,SORT_NUMERIC);
				$aRight = explode('|', $aQuery['dqRight1']);
				sort($aRight,SORT_NUMERIC);
				if (implode('|',$aChoice) == implode('|',$aRight)) {
					$iRight = 1;
				}
			break;
			case 2:
				if (
					$sAnswer != '' &&
					(
						$sAnswer == $aQuery['dqRight1'] ||
						$sAnswer == $aQuery['dqRight2'] ||
						$sAnswer == $aQuery['dqRight3'] ||
						$sAnswer == $aQuery['dqRight4'] ||
						$sAnswer == $aQuery['dqRight5']
					)
				)
				{
					$iRight = 1;
				}
			break;
		}
		return $iRight;
	}

}