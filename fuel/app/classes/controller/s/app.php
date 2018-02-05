<?php
class Controller_S_App extends Controller_Rest
{
	public $aStudent = null;
	public $aClass = null;
	public $aQuest = null;
	public $aQQuery = null;
	public $aQAnswer = null;
	public $aTest = null;
	public $aTQuery = null;
	public $aTAnswer = null;
	public $aMCate = null;
	public $aMaterial = null;
	public $aWeekday = array(
		0=>'指定なし',
		1=>'月曜',
		2=>'火曜',
		3=>'水曜',
		4=>'木曜',
		5=>'金曜',
		6=>'土曜',
		7=>'日曜',
	);
	public $aPeriod = array(
		0=>'指定なし',
		1=>'前期',
		2=>'後期',
		3=>'通期',
	);
	public $aHour = array(
		0=>'指定なし',
		1=>'1限',
		2=>'2限',
		3=>'3限',
		4=>'4限',
		5=>'5限',
		6=>'6限',
		7=>'7限',
	);

	/**
	 * ログイン判定
	 * @param string $sHash ログインハッシュ
	 */
	private function loginChk($sHash = null)
	{
		$sRedirect = 's/login/index/1';
		$res = array('err'=>-3,'res'=>'','msg'=>'ログインハッシュが取得できません');

		if (is_null($sHash))
		{
			$sIP = \Input::real_ip();
			$sUA = \Input::user_agent();
			\Log::warning("No Hash\n".$sIP."\n".$sUA);
			$this->response($res);
			exit();
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		if (!isset($aLogin['hash']))
		{
			$sIP = \Input::real_ip();
			$sUA = \Input::user_agent();
			\Log::warning("Hash Ditect Error\n".$sIP."\n".$sUA);
			$this->response($res);
			exit();
		}

		$result = Model_Student::getStudentFromHash($aLogin['hash']);
		if (!count($result))
		{
			$sIP = \Input::real_ip();
			$sUA = \Input::user_agent();
			\Log::warning($aLogin['hash']."\n".$sIP."\n".$sUA);
			$res['err'] = -2;
			$res['msg'] = 'ログイン情報がハッシュから特定できないため、処理を続行することはできません';
			$this->response($res);
			exit();
		}
		$this->aStudent = $result->current();
		return;
	}

	/**
	 * 講義履修判定
	 * @param string $sCtID 講義ID
	 */
	private function classChk($sCtID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'講義IDが取得できません');

		if (is_null($sCtID))
		{
			$this->response($res);
			exit();
		}

		$result = Model_Class::getClassFromStudent($this->aStudent["stID"],1,$sCtID);
		if (!count($result)) {
			$res['err'] = -2;
			$res['msg'] = '講義情報が講義IDから特定できないため、処理を続行することはできません';
			$this->response($res);
			exit();
		}
		$this->aClass = $result->current();
		return;
	}

	/**
	 * アンケートチェック
	 * @param string $sQbID
	 */
	private function questChk($sQbID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'必要な情報が取得できません');

		if (is_null($sQbID))
		{
			$this->response($res);
			exit();
		}

		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbID','=',$sQbID),array('qb.qbPublic','>',0)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'回答可能なアンケート情報が見つかりません');
			$this->response($res);
			exit();
		}
		$this->aQuest = $result->current();
		$this->aQuest['QPut'] = '';

		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$this->aQuest['qbID']),array('qp.stID','=',$this->aStudent['stID'])));
		if ($result)
		{
			$aPut = $result->current();
			$this->aQuest['QPut'] = array(
				'qpComment' => $aPut['qpComment'],
				'qpComDate' => $aPut['qpComDate'],
				'qpDate' => $aPut['qpDate'],
			);
		}
		return;
	}


	/**
	 * アンケート設問チェック
	 * @param string $sQbID
	 */
	private function questQueryChk($sQbID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'必要な情報が取得できません');

		if (is_null($sQbID))
		{
			$this->response($res);
			exit();
		}
		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sQbID)),null,array('qqSort'=>'asc'));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'指定のアンケートには設問がありません');
			$this->response($res);
			exit();
		}
		$this->aQQuery = $result->as_array();
		$sSavePath = CL_UPPATH.DS.$sQbID.DS;
		foreach ($this->aQQuery as $i => $aQQ)
		{
			if ($aQQ['qqImage'])
			{
				$this->aQQuery[$i]['qqImage'] = base64_encode(File::read($sSavePath.$aQQ['qqNO'].DS.$aQQ['qqImage'],true));
			}
			for ($j = 1; $j <= 50; $j++)
			{
				if ($aQQ['qqChoiceImg'.$j])
				{
					$this->aQQuery[$i]['qqChoiceImg'.$j] = base64_encode(File::read($sSavePath.$aQQ['qqNO'].DS.$aQQ['qqChoiceImg'.$j],true));
				}
			}
		}
		return;
	}

	/**
	 * アンケート回答チェック
	 * @param string $sQbID
	 * @param string $sStID
	 */
	private function questAnsChk($sQbID = null,$sStID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'必要な情報が取得できません');

		if (is_null($sQbID))
		{
			$this->response($res);
			exit();
		}

		$sID = (is_null($sStID))? $this->aStudent['stID']:$sStID;

		$result = Model_Quest::getQuestQuery(array(array('qbID','=',$sQbID)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'アンケートの設問が見つかりません');
			$this->response($res);
			exit();
		}
		$aQuery = $result->as_array();

		$result = Model_Quest::getQuestAns(array(array('qa.qbID','=',$sQbID),array('qa.stID','=',$sID)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'指定の学生は未回答です');
			$this->response($res);
			exit();
		}
		$aAns = $result->as_array('qqNO');

		foreach ($aQuery as $aQ)
		{
			$aA = $aAns[$aQ['qqNO']];
			if ($aQ['qqStyle'] == 2)
			{
				$this->aQAnswer[$aA['qqNO']] = array('qqNO'=>$aA['qqNO'],'text'=>$aA['qaText'],'qaPick'=>(int)$aA['qaPick']);
			}
			else
			{
				$aSel = array();
				for ($i = 1; $i <= $aQ['qqChoiceNum']; $i++)
				{
					if ($aA['qaChoice'.$i])
					{
						$aSel[] = $i;
					}
					$sSel = implode('|',$aSel);
					$this->aQAnswer[$aA['qqNO']] = array('qqNO'=>$aA['qqNO'],'select'=>$sSel,'qaPick'=>'');
				}
			}
		}
		return;
	}

	/**
	 * 小テストチェック
	 * @param string $sTbID
	 */
	private function testChk($sTbID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'必要な情報が取得できません');

		if (is_null($sTbID))
		{
			$this->response($res);
			exit();
		}

		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],array(array('tb.tbID','=',$sTbID),array('tb.tbPublic','>',0)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'回答可能な小テスト情報が見つかりません');
			$this->response($res);
			exit();
		}
		$this->aTest = $result->current();
		$sSavePath = CL_UPPATH.DS.$sTbID.DS.'base'.DS;
		if ($this->aTest['tbExplainImage'])
		{
			$this->aTest['tbExplainImage'] = base64_encode(File::read($sSavePath.$this->aTest['tbExplainImage'],true));
		}
		$this->aTest['TPut'] = '';

		$result = Model_Test::getTestPut(array(array('tp.tbID','=',$this->aTest['tbID']),array('tp.stID','=',$this->aStudent['stID'])));
		if ($result)
		{
			$aPut = $result->current();
			$this->aTest['TPut'] = array(
				'tpScore' => $aPut['tpScore'],
				'tpTime' => $aPut['tpTime'],
				'tpDate' => $aPut['tpDate'],
			);
		}
		return;
	}


	/**
	 * 小テスト設問チェック
	 * @param string $sTbID
	 */
	private function testQueryChk($sTbID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'必要な情報が取得できません');

		if (is_null($sTbID))
		{
			$this->response($res);
			exit();
		}
		$result = Model_Test::getTestQuery(array(array('tbID','=',$sTbID)),null,array('tqSort'=>'asc'));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'指定の小テストには設問がありません');
			$this->response($res);
			exit();
		}
		$this->aTQuery = $result->as_array();
		$sSavePath = CL_UPPATH.DS.$sTbID.DS;
		foreach ($this->aTQuery as $i => $aTQ)
		{
			if ($aTQ['tqImage'])
			{
				$this->aTQuery[$i]['tqImage'] = base64_encode(File::read($sSavePath.$aTQ['tqNO'].DS.$aTQ['tqImage'],true));
			}
			if ($aTQ['tqExplainImage'])
			{
				$this->aTQuery[$i]['tqExplainImage'] = base64_encode(File::read($sSavePath.$aTQ['tqNO'].DS.$aTQ['tqExplainImage'],true));
			}
			for ($j = 1; $j <= 50; $j++)
			{
				if ($aTQ['tqChoiceImg'.$j])
				{
					$this->aTQuery[$i]['tqChoiceImg'.$j] = base64_encode(File::read($sSavePath.$aTQ['tqNO'].DS.$aTQ['tqChoiceImg'.$j],true));
				}
			}
		}
		return;
	}

	/**
	 * 小テスト回答チェック
	 * @param string $sTbID
	 * @param string $sStID
	 */
	private function testAnsChk($sTbID = null,$sStID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'必要な情報が取得できません');

		if (is_null($sTbID))
		{
			$this->response($res);
			exit();
		}
		$sID = (is_null($sStID))? $this->aStudent['stID']:$sStID;

		$result = Model_Test::getTestAns(array(array('ta.tbID','=',$sTbID),array('ta.stID','=',$sID)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'指定の学生は未回答です');
			$this->response($res);
			exit();
		}
		$aAns = $result->as_array();
		foreach ($aAns as $aA)
		{
			if ($aA['tqStyle'] == 2)
			{
				$this->aTAnswer[$aA['tqNO']] = array('tqNO'=>$aA['tqNO'],'text'=>$aA['taText'],'right'=>$aA['taRight']);
			}
			else
			{
				$aSel = array();
				for ($i = 1; $i <= $aA['tqChoiceNum']; $i++)
				{
					if ($aA['taChoice'.$i])
					{
						$aSel[] = $i;
					}
					$sSel = implode('|',$aSel);
					$this->aTAnswer[$aA['tqNO']] = array('tqNO'=>$aA['tqNO'],'select'=>$sSel,'right'=>$aA['taRight']);
				}
			}
		}
		return;
	}


	/**
	 * 教材倉庫カテゴリチェック
	 * @param string $sMcID
	 */
	private function mcChk($sMcID = null)
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'必要な情報が取得できません');

		if (is_null($sMcID))
		{
			$this->response($res);
			exit();
		}

		$result = Model_Material::getMaterialCategoryFromClass($this->aClass['ctID'],array(array('mcID','=',$sMcID)));
		if (!count($result))
		{
			$res = array('err'=>-2,'res'=>'','msg'=>'教材倉庫カテゴリ情報が見つかりません');
			$this->response($res);
			exit();
		}
		$this->aMCate = $result->current();

		return;
	}

	public function post_pl()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$val = Validation::forge();
		$val->add_field('login', 'ログインIDまたはメールアドレス', 'required|max_length[200]');
		$val->add_field('pass', 'パスワード', 'required|max_length[200]');
		if (!$val->run())
		{
			$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}

		$result = Model_Student::getStudentFromPostLogin($par['login'],$par['pass']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}
		$aR = $result->current();
		$result = Model_Student::setLoginUpdate($aR);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}
		$aR = $result->current();

		$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$aR['stID'])));
		if (count($result))
		{
			$aGS = $result->current();
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGS['gtID'])));
			if (count($result))
			{
				$aGroup = $result->current();
				if ($aGroup['gtLDAP'])
				{
					$res['err'] = -2;
					$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
					$this->response($res);
					return;
				}
			}
		}

		$sHash = Crypt::encode(serialize(array('hash'=>sha1($aR['stLogin'].$aR['stPass']))));

		$aQuest = null;
		$result = Model_Class::getClassFromStudent($aR['stID'],1);
		if (count($result))
		{
			foreach ($result as $aC)
			{
				$qbres = Model_Quest::getQuestBaseFromClass($aC['ctID'], array(array('qb.qbPublic','=',1),array('qb.qbQuickMode','>',20)));
				if (count($qbres))
				{
					foreach ($qbres as $aQ)
					{
						$qpres = Model_Quest::getQuestPut(array(array('qp.qbID','=',$aQ['qbID']),array('qp.stID','=',$aR['stID'])));
						if (count($qpres))
						{
							continue;
						}

						$aQuest[] = array(
							'ctid' => $aC['ctID'],
							'qbid' => $aQ['qbID'],
							'qbTitle' => $aQ['qbTitle'],
							'qbType' => (int)($aQ['qbQuickMode']/10),
						);
					}
				}
			}
		}

		$res = array('err'=>0,'res'=>array('hash'=>$sHash, 'quest'=>$aQuest));
		$this->response($res);
		return;

	}

	/**
	 * ログイン処理
	 */
	public function post_adlogin()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		if (!isset($par['gcode']) || !$par['gcode'])
		{
			$res['msg'] = '団体コードが指定されていません。';
			$this->response($res);
			return;
		}
		$result = Model_Group::getGroup(array(array('gb.gtPrefix','=',$par['gcode']),array('gb.gtLDAP','=',1)));
		if (!count($result))
		{
			$res['msg'] = '団体コードが正しくありません。';
			$this->response($res);
			return;
		}
		$aGroup = $result->current();

		try
		{
			\Clfunc_Common::LDAPAuthCommand($aGroup,$par['login'],$par['pass']);
		}
		catch (\Exception $e)
		{
			$res['err'] = -1;
			switch ($e->getCode())
			{
				case 255:
					$res['msg'] = 'Can\'t connect to the LDAP server.';
				break;
				case 49:
					$res['msg'] = 'Invalid credentials';
				break;
				default:
					$res['msg'] = $e->getMessage();
				break;
			}
			$this->response($res);
			return;
		}

		$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$aGroup['gtID']),array('st.stLogin','=',$par['login'])));
		if (!count($result))
		{
			$res['msg'] = 'ログインIDが存在しません。';
			$this->response($res);
			return;
		}

		$result = Model_Student::getStudentFromLoginID($par['login']);
		if (!count($result))
		{
			$res['msg'] = 'ログインに失敗しました。再度実行してみてください。';
			$this->response($res);
			return;
		}
		$aResult = $result->current();
		$result = Model_Student::setLoginUpdate($aResult);
		if (!count($result))
		{
			$res['msg'] = 'ログインに失敗しました。再度実行してみてください。';
			$this->response($res);
			return;
		}
		$aResult = $result->current();

		if (isset($par['os']) && isset($par['token']))
		{
			try
			{
				$aUpdate = array(
					'stApp' => (($par['os'] == 'iOS')? 1:(($par['os'] == 'Android')? 2:0)),
					'stDeviceToken' => $par['token'],
					'stDeviceID' => ((isset($par['did']))? $par['did']:''),
				);
				$result = Model_Student::updateStudent($aResult['stID'], $aUpdate);
			}
			catch (Exception $e)
			{
				$res['err'] = -1;
				$res['msg'] = $e->getMessage();
				$this->response($res);
				return;
			}
		}

		$sHash = Crypt::encode(serialize(array('hash'=>sha1($aResult['stLogin'].$aResult['stPass']),'ip'=>Input::real_ip())));
		$res = array('err'=>0,'res'=>array('hash'=>$sHash,'Student'=>$aResult),'Group'=>array('gtCode'=>$aGroup['gtPrefix'], 'gtName'=>$aGroup['gtName']));
		$this->response($res);
		return;
	}

	/**
	 * ログイン処理
	 */
	public function post_login()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$val = Validation::forge();
		$val->add_field('login', 'ログインIDまたはメールアドレス', 'required|max_length[200]');
		$val->add_field('pass', 'パスワード', 'required|max_length[200]');
		if (!$val->run())
		{
			$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}

		$result = Model_Student::getStudentFromPostLogin($par['login'],$par['pass']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}
		$aResult = $result->current();
		$result = Model_Student::setLoginUpdate($aResult);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
			$this->response($res);
			return;
		}
		$aResult = $result->current();

		$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$aResult['stID'])));
		if (count($result))
		{
			$aGS = $result->current();
			$result = Model_Group::getGroup(array(array('gb.gtID','=',$aGS['gtID'])));
			if (count($result))
			{
				$aGroup = $result->current();
				if ($aGroup['gtLDAP'])
				{
					$res['err'] = -2;
					$res['msg'] = '入力されたログインIDまたはメールアドレスが存在しないか、パスワードが異なるためログインできません';
					$this->response($res);
					return;
				}
			}
		}

		if (isset($par['os']) && isset($par['token']))
		{
			try
			{
				$aUpdate = array(
					'stApp' => (($par['os'] == 'iOS')? 1:(($par['os'] == 'Android')? 2:0)),
					'stDeviceToken' => $par['token'],
					'stDeviceID' => ((isset($par['did']))? $par['did']:''),
				);
				$result = Model_Student::updateStudent($aResult['stID'], $aUpdate);
			}
			catch (Exception $e)
			{
				$res['err'] = -1;
				$res['msg'] = $e->getMessage();
				$this->response($res);
				return;
			}
		}

		$sHash = Crypt::encode(serialize(array('hash'=>sha1($aResult['stLogin'].$aResult['stPass']),'ip'=>Input::real_ip())));
		$res = array('err'=>0,'res'=>array('hash'=>$sHash,'Student'=>$aResult));
		$this->response($res);
		return;
	}

	/**
	 * ログアウト処理
	 */
	public function post_logout()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);

		try
		{
			$aUpdate = array(
				'stApp' => 0,
				'stDeviceToken' => '',
				'stDeviceID' => '',
			);
			$result = Model_Student::updateStudent($this->aStudent['stID'], $aUpdate);
		}
		catch (Exception $e)
		{
			$res['err'] = -1;
			$res['msg'] = $e->getMessage();
			$this->response($res);
			return;
		}

		$res = array('err'=>0,'res'=>1);
		$this->response($res);
		return;
	}


	/**
	 * IDからハッシュを取得
	 */
	public function post_gethash()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$result = Model_Student::getStudentFromID($par['sid']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '入力されたIDは存在しません。';
			$this->response($res);
			return;
		}
		$aResult = $result->current();

		$sHash = Crypt::encode(serialize(array('hash'=>sha1($aResult['stLogin'].$aResult['stPass']),'ip'=>Input::real_ip())));
		$res = array('err'=>0,'res'=>array('hash'=>$sHash));
		$this->response($res);
		return;
	}

	/**
	 * デバイストークン登録
	 */
	public function post_tokenadd()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);

		if (isset($par['os']) && isset($par['token']))
		{
			try
			{
				$aUpdate = array(
					'stApp' => (($par['os'] == 'iOS')? 1:(($par['os'] == 'Android')? 2:0)),
					'stDeviceToken' => $par['token'],
					'stDeviceID' => ((isset($par['did']))? $par['did']:''),
				);
				$result = Model_Student::updateStudent($this->aStudent['stID'], $aUpdate);
			}
			catch (Exception $e)
			{
				$res['err'] = -1;
				$res['msg'] = $e->getMessage();
				$this->response($res);
				return;
			}
		}

		$res = array('err'=>0,'res'=>1);
		$this->response($res);
		return;
	}

	/**
	 * 学生情報取得
	 */
	public function post_mine()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);

		$res = array('err'=>0,'res'=>$this->aStudent);
		$this->response($res);
		return;
	}

	/**
	 * 講義一覧取得
	 */
	public function post_classlist() {
		$res = array('err'=>-3,'res'=>'');

		$par = Input::post();
		self::loginChk($par['hash']);

		$aActClass = null;
		$result = Model_Class::getClassFromStudent($this->aStudent['stID'],1);
		if (count($result)) {
			$aTempClass = $result->as_array();
			foreach ($aTempClass as $i => $aC) {
				unset($aC['ctLatLon']);
				$aActClass[$i] = $aC;
				$aActClass[$i]['ctPeriod'] = $this->aPeriod[$aC['dpNO']];
				$aActClass[$i]['ctWeekDay'] = $this->aWeekday[$aC['ctWeekDay']];
				$aActClass[$i]['ctHour'] = $this->aHour[$aC['dhNO']];
				$resT = Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$aC['ctID']),array('tp.tpMaster','=',1)));
				if (count($resT))
				{
					$aTeach = $resT->current();
					$aActClass[$i]['ttName'] = $aTeach['ttName'];
				}
				$aActClass[$i]['AttendNO'] = 0;
				$aActClass[$i]['AttendActive'] = 0;
				$aActClass[$i]['AttendClose'] = 0;
				$aActClass[$i]['AttendAlready'] = 0;
				$resA = Model_Attend::getAttendCalendarActive($aC['ctID']);
				if (count($resA))
				{
					$aAtd = $resA->current();
					$aWhere = array(array('ctID','=',$aC['ctID']),array('abDate','=',$aAtd['abDate']),array('acNO','=',$aAtd['acNO']),array('amAttendState','>',0));
					$resBS = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],$aWhere);
					if (count($resBS))
					{
						$aActClass[$i]['AttendAlready'] = 1;
					}
					$aActClass[$i]['AttendActive'] = 1;
					$aActClass[$i]['AttendClose'] = $aAtd['acAEnd'];
					$aActClass[$i]['AttendNO'] = $aAtd['no'];
				}
				$resAC = Model_Attend::getAttendCalendarFromClass($aC['ctID'],array(array('ac.acAStart','=',CL_DATETIME_DEFAULT)));
				$aActClass[$i]['AttendDays'] = count($resAC);
				$resAB = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],array(array('ctID','=',$aC['ctID']),array('amAbsence','=',0)));
				$aActClass[$i]['AttendNums'] = count($resAB);
				$resAA = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],array(array('ctID','=',$aC['ctID']),array('amAbsence','=',1)));
				$aActClass[$i]['AttendNonNums'] = count($resAA);
			}
		}
		$res = array(
			'err'=>0,
			'res'=>((is_null($aActClass))? '':$aActClass),
		);
		$this->response($res);
		return;
	}

	/**
	 * 新規登録
	 */
	public function post_entry()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('name', '氏名')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('max_length', 50);

		$val->add('mail', 'メールアドレス')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('valid_email')
			->add_rule('max_length', 200)
			->add_rule('smail_chk');

		$val->add('pass', 'パスワード')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('min_length', 8)
			->add_rule('max_length', 32)
			->add_rule('passwd_char');

		$val->add('passchk', 'パスワード（確認）')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('match_field', 'pass');

		if (!$val->run())
		{
			foreach ($val->error() as $field => $error) {
				$res['msg'][$field] = $error->get_message();
			}
			$this->response($res);
			return;
		}

		// ログインID自動生成
		while(true)
		{
			$sLogin = strtolower(Str::random('distinct', 8));
			$result = Model_Student::getStudentFromLogin($sLogin);
			if (count($result))
			{
				continue;
			}
			break;
		}

		// 登録データ生成
		$aInsert = array(
			'stID'            => null,
			'stMail'          => trim($par['mail']),
			'stPass'          => sha1(trim($par['pass'])),
			'stName'          => $par['name'],
			'stLogin'         => $sLogin,
			'stLoginNum'      => 0,
			'stLastLoginDate' => '00000000000000',
			'stLoginDate'     => '00000000000000',
			'stPassDate'      => date('Ymd'),
			'stPassMiss'      => 0,
			'stUA'            => 'APP',
			'stHash'          => sha1($sLogin.sha1(trim($par['pass']))),
			'stStatus'        => 1,
			'stMailAuth'      => 0,
			'stDate'          => date('YmdHis'),
		);

		try
		{
			$sStID = Model_Student::insertStudent($aInsert);
		}
		catch (Exception $e)
		{
			$res['err'] = -1;
			$res['msg'] = $e->getMessage();
			$this->response($res);
			return;
		}

		// 登録完了メール送信
		if ($aInsert['stMail']) {
			$email = \Email::forge();
			$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
			$email->to($aInsert['stMail']);
			$email->subject(CL_SITENAME.' 学生アカウント登録手続き完了のお知らせ');
			$body = View::forge('email/s_fin', $aInsert);
			$email->body($body);
			try
			{
				$email->send();
			}
			catch (\EmailValidationFailedException $e)
			{
				Log::warning('StudentRegistFinishMail - ' . $e->getMessage());
			}
			catch (\EmailSendingFailedException $e)
			{
				Log::warning('StudentRegistFinishMail - ' . $e->getMessage());
			}
		}

		$res = array('err'=>0,'res'=>1);
		$this->response($res);
		return;
	}

	/**
	 * パスワードリセット
	 */
	public function post_reset()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('mail', 'メールアドレス')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('valid_email')
			->add_rule('max_length', 200);

		if (!$val->run())
		{
			foreach ($val->error() as $field => $error) {
				$res['msg'] = $error->get_message();
			}
			$this->response($res);
			return;
		}

		$result = Model_Student::getStudentFromMail($par['mail']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '指定されたメールアドレスは'.CL_SITENAME.'に登録されていません';
			$this->response($res);
			return;
		}
		$aResult = $result->current();

		// 再設定URL生成
		$aMD['reset_hash'] = Crypt::encode($aResult['stID'].CL_SEP.strtotime('+24Hours'));

		// 登録完了メール送信
		$email = \Email::forge();
		$email->from(CL_MAIL_FROM, CL_MAIL_SENDER);
		$email->to($aResult['stMail']);
		$email->subject('[CL]学生パスワードの再設定');
		$body = View::forge('email/s_reset', $aMD);
		$email->body($body);

		try
		{
			$email->send();
		}
		catch (\EmailValidationFailedException $e)
		{
			Log::warning('StudentPasswordReminderMail - ' . $e->getMessage());
		}
		catch (\EmailSendingFailedException $e)
		{
			Log::warning('StudentPasswordReminderMail - ' . $e->getMessage());
		}

		$res = array('err'=>0,'res'=>1);
		$this->response($res);
		return;
	}

	/**
	 * 講義履修
	 */
	public function post_classentry()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('code', '講義コード')
			->add_rule('required')
			->add_rule('trim');

		if (!$val->run())
		{
			foreach ($val->error() as $field => $error) {
				$res['msg'] = $error->get_message();
			}
			$this->response($res);
			return;
		}

		$result = Model_Class::getClassFromCode($par['code'],1);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '指定の講義コードに該当する講義は存在しません';
			$this->response($res);
			return;
		}
		$aClass = $result->current();
		unset($aClass['ctLatLon']);

		$result = Model_Class::getClassFromStudent($this->aStudent['stID'],null,$aClass['ctID']);
		if (count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '指定の講義には既に履修しています';
			$this->response($res);
			return;
		}

		if (!isset($par['check'])) {
			$aClass['ctPeriod']  = $this->aPeriod[$aClass['dpNO']];
			$aClass['ctWeekDay'] = $this->aWeekday[$aClass['ctWeekDay']];
			$aClass['ctHour']    = $this->aHour[$aClass['dhNO']];

			unset($aClass['ctLat']);
			unset($aClass['ctLon']);
			unset($aClass['ctLatLon']);
			$res = array('err'=>0,'res'=>$aClass);
			$this->response($res);
			return;
		}

		try
		{
			$result = Model_Class::entryClass($aClass['ctID'],$this->aStudent['stID']);
		}
		catch (Exception $e)
		{
			$res['err'] = -1;
			$res['msg'] = $e->getMessage();
			$this->response($res);
			return;
		}

		$res = array('err'=>0,'res'=>1);
		$this->response($res);
		return;
	}

	/**
	 * アカウント設定
	 */
	public function post_profile()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		switch ($par['mode'])
		{
			case 'profile':
				$val->add_callable('Helper_CustomValidation');
				$val->add('name', '氏名')
					->add_rule('required')
					->add_rule('max_length',50);
				$val->add('no', '学籍番号')
					->add_rule('trim')
					->add_rule('max_length', 20)
					->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));
			break;
			case 'mail':
				$val->add_callable('Helper_CustomValidation');
				$val->add('mail', '新しいメールアドレス')
					->add_rule('required')
					->add_rule('valid_email')
					->add_rule('max_length',200)
					->add_rule('smail_chk',$this->aStudent['stID']);
				$val->add('mail_chk', '新しいメールアドレス（確認）')
					->add_rule('required')
					->add_rule('match_field','mail');
			break;
			case 'pass':
				$val->add_callable('Helper_CustomValidation');
				$val->add('pass_now', '現在のパスワード')
					->add_rule('required')
					->add_rule('passwd_true',$this->aStudent['stPass']);
				$val->add('pass_edit', '新しいパスワード')
					->add_rule('required')
					->add_rule('min_length',8)
					->add_rule('max_length',32)
					->add_rule('passwd_char')
					->add_rule('passwd_false',$this->aStudent['stPass']);
				$val->add('pass_chk', '新しいパスワード（確認）')
					->add_rule('required')
					->add_rule('match_field','pass_edit');
			break;
		}

		if (!$val->run())
		{
			foreach ($val->error() as $field => $error) {
				$res['msg'][$field] = $error->get_message();
			}
			$this->response($res);
			return;
		}

		switch ($par['mode'])
		{
			case 'profile':
				$aUpdate = array(
					'stName' => $par['name'],
					'stNO'   => $par['no'],
				);
			break;
			case 'mail':
				$aUpdate = array(
					'stMail' => $par['mail'],
				);
			break;
			case 'pass':
				$aUpdate = array(
					'stFirst' => '',
					'stPass' => sha1($par['pass_edit']),
					'stPassDate' => date('Ymd'),
					'stHash' => sha1($this->aStudent['stLogin'].sha1($par['pass_edit'])),
				);
			break;
		}
		try
		{
			$result = Model_Student::updateStudent($this->aStudent['stID'],$aUpdate);
		}
		catch (Exception $e)
		{
			$res['err'] = -1;
			$res['msg'] = $e->getMessage();
			$this->response($res);
			return;
		}

		$sHash = Crypt::encode(serialize(array('hash'=>$this->aStudent['stHash'],'ip'=>Input::real_ip())));
		if (isset($aUpdate['stHash']))
		{
			$sHash = Crypt::encode(serialize(array('hash'=>$aUpdate['stHash'],'ip'=>Input::real_ip())));
		}

		$res = array('err'=>0,'res'=>$sHash);
		$this->response($res);
		return;
	}

	/**
	 * 出席情報マスタの取得
	 */
	public function post_attendmaster()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);

		# 出席マスター取得
		$result = Model_Attend::getAttendMasterFromClass($par['ctid']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '指定の講義の出席項目が取得できませんでした';
			$this->response($res);
			return;
		}
		$aRes = $result->as_array();

		$res = array('err'=>0, 'res'=>$aRes);
		$this->response($res);
		return;
	}



	/**
	 * 現在の出席受付情報取得
	 */
	public function post_currentattend()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);

		$aAttend = null;
		$result = Model_Attend::getAttendCalendarActive($this->aClass['ctID']);
		if (count($result))
		{
			$aAttend = $result->current();
			if ($aAttend['agLatLon'])
			{
				unset($aAttend['agLatLon']);
			}
			$aAttend['already'] = 0;
			$aWhere = array(array('ctID','=',$this->aClass['ctID']),array('abDate','=',$aAttend['abDate']),array('acNO','=',$aAttend['acNO']),array('amAttendState','>',0));
			$result = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],$aWhere);
			if (count($result))
			{
				$aRes = $result->current();
				$aAttend['already'] = 1;
				$aAttend['abData'] = array(
					'AttendTime' => date('H:i',strtotime($aRes['abAttendDate'])),
					'amName'     => $aRes['amName'],
					'amAbsence'  => $aRes['amAbsence'],
					'amTime'     => $aRes['amTime'],
				);
				$aAttend['agLng'] = $aAttend['agLon'];
				unset($aAttend['agLon']);
			}
		}

		$res = array('err'=>0, 'res'=>is_null($aAttend)? '':$aAttend);
		$this->response($res);
		return;
	}

	/**
	 * 出席履歴取得
	 */
	public function post_attendhistory()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);

		$aAttend = null;
		$aWhere = array(array('abDate','<=',date('Y-m-d')));
		$aSort  = array(array('abDate','desc'),array('ctCode','asc'),array('acNO','desc'));
		$result = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],$aWhere,$aSort);
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $i => $aC)
			{
				$aAttend[$i] = $aC;
				$aAttend[$i]['ctPeriod'] = $this->aPeriod[$aC['dpNO']];
				$aAttend[$i]['ctWeekDay'] = $this->aWeekday[$aC['ctWeekDay']];
				$aAttend[$i]['ctHour'] = $this->aHour[$aC['dhNO']];
				$aAttend[$i]['agLng'] = $aAttend[$i]['agLon'];
				unset($aAttend[$i]['agLon']);
			}
		}

		$res = array('err'=>0, 'res'=>is_null($aAttend)? '':$aAttend);
		$this->response($res);
		return;
	}

	/**
	 * 出席提出
	 */
	public function post_attendrequest()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);

		$aAttend = null;
		$result = Model_Attend::getAttendCalendarActive($this->aClass['ctID']);
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '現在、出席は受け付けていません';
			$this->response($res);
			return;
		}
		$aAttend = $result->current();

		if ($par['no'] != $aAttend['no'])
		{
			$res['msg'] = '対象の出席情報が見つかりませんでした';
			$this->response($res);
			return;
		}

		if ($aAttend['acKey'] != '')
		{
			if ($par['key'] != $aAttend['acKey'])
			{
				$res['msg'] = '確認キーが異なるため、出席できません';
				$this->response($res);
				return;
			}
		}

		$aWhere = array(array('ctID','=',$this->aClass['ctID']),array('abDate','=',$aAttend['abDate']),array('acNO','=',$aAttend['acNO']));
		$bAlready = false;
		$result = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],$aWhere);
		if (count($result))
		{
			$aActive = $result->current();
			$bAlready = true;
		}

		# 出席マスター取得
		$result = Model_Attend::getAttendMasterFromClass($this->aClass['ctID'],null,array('amDefault'=>'DESC','amTime'=>'DESC','amAttendState'=>'ASC'));
		if (!count($result))
		{
			$res['err'] = -2;
			$res['msg'] = '出席マスタが登録されていません';
			$this->response($res);
			return;
		}
		$aRes = $result->as_array();

		# 経過時間でステータス変更
		$iDefault = null;
		$iState = null;
		foreach ($aRes as $aA)
		{
			if ($aA['amDefault'] == 1)
			{
				$iDefault = $aA['amAttendState'];
			}
			if ((int)$aA['amTime'] > 0)
			{
				$iChkTime = time() - strtotime($aAttend['acStart']);
				if ($iChkTime >= ((int)$aA['amTime']*60))
				{
					$iState = $aA['amAttendState'];
					break;
				}
			}
		}
		if (is_null($iState))
		{
			$iState = $iDefault;
		}

		$aGeo = null;
		if (!is_null($par['lat']) && !is_null($par['lng']) && $par['lat'] != "" && $par['lng'] != "")
		{
			$aGeo = array(
				'lat'=>$par['lat'],
				'lon'=>$par['lng'],
				'agLat'=>$aAttend['agLat'],
				'agLon'=>$aAttend['agLon'],
			);
			if ($bAlready && $aActive['agNO'] != 0)
			{
				$aGeo['agNO'] = $aActive['agNO'];
			}
		}

		try
		{
			if (!$bAlready)
			{
				# 出席情報生成
				$aInput = array(
				'ctID'          => $this->aClass['ctID'],
				'abDate'        => $aAttend['abDate'],
				'acNO'          => $aAttend['acNO'],
				'stID'          => $this->aStudent['stID'],
				'amAttendState' => $iState,
				'agNO'          => 0,
				'abAttendMemo'  => '',
				'abAttendDate'  => date('YmdHis'),
				'abStName'      => $this->aStudent['stName'],
				'abStNO'        => $this->aStudent['stNO'],
				);
				Model_Attend::insertAttendBook($aInput,$aGeo);
			}
			else
			{
				$aWhere[] = array('stID','=',$this->aStudent['stID']);
				$aInput = array(
						'amAttendState' => $iState,
						'agNO'          => 0,
						'abAttendDate'  => date('YmdHis'),
						'abStName'      => $this->aStudent['stName'],
						'abStNO'        => $this->aStudent['stNO'],
				);
				Model_Attend::updateAttendBook($aWhere,$aInput,$aGeo);
			}
		}
		catch (Exception $e)
		{
			$res['err'] = -1;
			$res['msg'] = $e->getMessage();
			$this->response($res);
			return;
		}

		$aWhere = array(array('ctID','=',$this->aClass['ctID']),array('abDate','=',$aAttend['abDate']),array('acNO','=',$aAttend['acNO']));
		$result = Model_Attend::getAttendBookFromStudent($this->aStudent['stID'],$aWhere);
		if (count($result))
		{
			$aActive = $result->current();
			$aActive['agLng'] = $aActive['agLon'];
			unset($aActive['agLon']);
		}

		$res = array('err'=>0,'res'=>$aActive);
		$this->response($res);
		return;
	}

	/**
	 * アンケート一覧取得
	 */
	public function post_questlist() {
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);

		$aQuest = null;
		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbPublic','>',0)),null,array('qb.qbSort'=>'desc'));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aQ)
			{
				$aQuest[$aQ['qbID']] = $aQ;
				$aQuest[$aQ['qbID']]['QPut'] = array('qpComment'=>'', 'qpDate'=>'');
			}
		}
		if (!is_null($aQuest))
		{
			$aPut = null;
			$result = Model_Quest::getQuestPut(array(array('qb.ctID','=',$this->aClass['ctID']),array('qp.stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aPut = $result->as_array();
				foreach ($aPut as $aP)
				{
					if (array_key_exists($aP['qbID'],$aQuest))
					{
						$aQuest[$aP['qbID']]['QPut']['qpComment'] = $aP['qpComment'];
						$aQuest[$aP['qbID']]['QPut']['qpDate'] = $aP['qpDate'];
					}
				}
			}
		}

		$res = array('err'=>0,'res'=>is_null($aQuest)? '':$aQuest);
		$this->response($res);
		return;
	}

	/**
	 * アンケート基本情報取得
	 */
	public function post_questbase() {
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::questChk($par['qbid']);

		$res = array('err'=>0,'res'=>$this->aQuest);
		$this->response($res);
		return;
	}



	/**
	 * アンケート設問取得
	 */
	public function post_questquery() {
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::questQueryChk($par['qbid']);

		$res = array('err'=>0,'res'=>$this->aQQuery);
		$this->response($res);
		return;
	}

	/**
	 * アンケート回答取得
	 */
	public function post_questans() {
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::questAnsChk($par['qbid']);

		$res = array('err'=>0,'res'=>$this->aQAnswer);
		$this->response($res);
		return;
	}


	/**
	 * アンケート提出
	 */
	public function post_questsubmit()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::questChk($par['qbid']);
		self::questQueryChk($par['qbid']);

		if ($this->aQuest['qbPublic'] != 1)
		{
			$res['err'] = -2;
			$res['msg'] = 'アンケートは締め切られているため、提出できません';
			$this->response($res);
			return;
		}

		if ($this->aQuest['qbReAnswer'] == 0 && isset($this->aQuest['QPut']['qpDate']))
		{
			$res['err'] = -2;
			$res['msg'] = '既に回答済みです';
			$this->response($res);
			return;
		}

		$aAns = json_decode($par['answer'], true);

		try
		{
			$bUpdate = isset($this->aQuest['QPut']['qpDate']);
			Model_Quest::setQuestPut($this->aQuest,$this->aQQuery,$this->aStudent,$aAns,$bUpdate);
		}
		catch (Exception $e)
		{
			$res['err'] = -1;
			$res['msg'] = $e->getMessage();
			$this->response($res);
			return;
		}

		$res = array('err'=>0,'res'=>'');
		$this->response($res);
		return;
	}

	/**
	 * アンケート集計取得
	 */
	public function post_questbent()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::questChk($par['qbid']);
		self::questQueryChk($par['qbid']);

		if (!$this->aQuest['qbBentPublic'])
		{
			$res['err'] = -2;
			$res['msg'] = '指定のアンケートの集計は学生に公開されていません';
			$this->response($res);
			return;
		}

		$aBent = null;
		$result = Model_Quest::getQuestBent(array(array('qb.qbID','=',$this->aQuest['qbID']),array('qb.qbMode','=','ALL'),array('qb.qbDate','>=',date('YmdHis',strtotime('-2min')))));
		if (!count($result))
		{
			try
			{
				$result = Model_Quest::setQuestBent($this->aQuest);
			}
			catch (Exception $e)
			{
				$res['err'] = -1;
				$res['msg'] = $e->getMessage();
				$this->response($res);
				return;
			}
			$result = Model_Quest::getQuestBent(array(array('qb.qbID','=',$this->aQuest['qbID']),array('qb.qbMode','=','ALL')));
		}

		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aBent[$aR['qqNO']][$aR['qbNO']] = $aR;
				if (is_null($aR['qaPick']))
				{
					$aBent[$aR['qqNO']][$aR['qbNO']]['qaPick'] = '';
				}
			}
		}

		foreach ($this->aQQuery as $aQQ)
		{
			if (!isset($aBent[$aQQ['qqNO']]))
			{
				$aBent[$aQQ['qqNO']]["1"] = array(
					"qqNO"=>$aQQ['qqNO'],
					"qbNO"=>"1",
					"qbText"=>"",
					"qbNum"=>"0",
					"qbAll"=>"0",
					"qbTotal"=>"0",
					"qaPick"=>"0"
				);
			}
		}

		$res = array('err'=>0,'res'=>$aBent);
		$this->response($res);
		return;
	}

	/**
	 * アンケート集計取得（クイック）
	 */
	public function post_questbentquick()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::questChk($par['qbid']);
		self::questQueryChk($par['qbid']);

		if (!$this->aQuest['qbBentPublic'])
		{
			$res['err'] = -2;
			$res['msg'] = '指定のアンケートの集計は学生に公開されていません';
			$this->response($res);
			return;
		}

		$aBent = null;
		$result = Model_Quest::getQuestBent(array(array('qb.qbID','=',$this->aQuest['qbID']),array('qb.qbMode','=','ALL'),array('qb.qbDate','>=',date('YmdHis',strtotime('-2min')))));
		if (!count($result))
		{
			try
			{
				$result = Model_Quest::setQuestBent($this->aQuest);
			}
			catch (Exception $e)
			{
				$res['err'] = -1;
				$res['msg'] = $e->getMessage();
				$this->response($res);
				return;
			}
			$result = Model_Quest::getQuestBent(array(array('qb.qbID','=',$this->aQuest['qbID']),array('qb.qbMode','=','ALL')));
		}

		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				$aBent[$aR['qqNO']][$aR['qbNO']] = $aR;
				if (is_null($aR['qaPick']))
				{
					$aBent[$aR['qqNO']][$aR['qbNO']]['qaPick'] = '';
				}
			}
		}

		$aComment = null;
		$iQbNO = 1;
		if ($this->aQuest['qbQuickMode'] && isset($this->aQQuery[1]))
		{
			$selResult = Model_Quest::getQuestAns(array(array('qa.qbID','=',$par['qbid']),array('qq.qqSort','=',1)),null,array('qa.qaDate'=>'DESC'));
			$txtResult = Model_Quest::getQuestBent(array(array('qb.qbID','=',$par['qbid']),array('qb.qqNO','=',2),array('qb.qbMode','=','ALL')),null,array('qa.qaDate'=>'DESC'));
			if (count($selResult) && count($txtResult))
			{
				$aSel = $selResult->as_array('stID');
				$aTxt = $txtResult->as_array();
				foreach ($aTxt as $aT)
				{
					if (!$aT['qbText'])
					{
						continue;
					}
					$sStID = $aT['stID'];
					$aS = $aSel[$sStID];
					$sChoice = null;
					$iNO = null;
					for ($i = 1; $i <= $this->aQQuery[0]['qqChoiceNum']; $i++)
					{
						if ($aS['qaChoice'.$i] == 1)
						{
							$sChoice = $this->aQQuery[0]['qqChoice'.$i];
							$iNO = $i;
							break;
						}
					}

					$aComment['2'][$iQbNO] = array(
						"qqNO"=>2,
						"qbNO"=>$iQbNO,
						"qbText"=>$aT['qbText'],
						"qaPick"=>$aT['qaPick'],
						'qcText'=> $sChoice,
						'qcNO'=> $iNO,
					);
					$iQbNO++;
				}
				$aBent['2'] = $aComment['2'];
			}
		}




		foreach ($this->aQQuery as $aQQ)
		{
			if (!isset($aBent[$aQQ['qqNO']]))
			{
				$aBent[$aQQ['qqNO']]["1"] = array(
					"qqNO"=>$aQQ['qqNO'],
					"qbNO"=>"1",
					"qbText"=>"",
					"qbNum"=>"0",
					"qbAll"=>"0",
					"qbTotal"=>"0",
					"qaPick"=>"0"
				);
			}
		}

		$res = array('err'=>0,'res'=>$aBent);
		$this->response($res);
		return;
	}


	/**
	 * アンケート提出情報取得
	 */
	public function post_questput()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::questChk($par['qbid']);

		if (!$this->aQuest['qbAnsPublic'])
		{
			$res['err'] = -2;
			$res['msg'] = '他の人の回答内容は公開されていません';
			$this->response($res);
			return;
		}

		$aStu = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],null,null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if ($aR['stID'] == $this->aStudent['stID'])
				{
					continue;
				}
				$aStu[$aR['stID']]['stKey'] = Crypt::encode($aR['stID']);
				$aStu[$aR['stID']]['stName'] = ($this->aQuest['qbAnsPublic'] == 1)? '＜匿名＞':$aR['stName'];
				$aStu[$aR['stID']]['put'] = 0;
			}
		}
		$result = Model_Quest::getQuestPut(array(array('qp.qbID','=',$this->aQuest['qbID'])));
		if (count($result))
		{
			$aRes = $result->as_array();
			foreach ($aRes as $aR)
			{
				if ($aR['stID'] == $this->aStudent['stID'])
				{
					continue;
				}
				if (isset($aStu[$aR['stID']]))
				{
					$aStu[$aR['stID']]['put'] = 1;
				}
			}
		}

		if ($this->aQuest['qbAnsPublic'] == 1)
		{
			shuffle($aStu);
		}

		$res = array('err'=>0,'res'=>$aStu);
		$this->response($res);
		return;
	}


	/**
	 * アンケート回答情報取得
	 */
	public function post_questansdetail()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::questChk($par['qbid']);

		$sStID = \Crypt::decode($par['stkey']);

		if (!$sStID)
		{
			$res['msg'] = '指定された学生が特定できません';
			$this->response($res);
			return;
		}

		self::questAnsChk($par['qbid'],$sStID);

		if (!$this->aQuest['qbAnsPublic'])
		{
			$res['err'] = -2;
			$res['msg'] = '他の人の回答内容は公開されていません';
			$this->response($res);
			return;
		}

		$res = array('err'=>0,'res'=>$this->aQAnswer);
		$this->response($res);
		return;
	}

	/**
	 * 小テスト一覧取得
	 */
	public function post_testlist() {
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);

		$aTest = null;
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID'],array(array('tb.tbPublic','>',0)),null,array('tb.tbSort'=>'desc'));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aQ)
			{
				$aTest[$aQ['tbID']] = $aQ;
				unset($aTest[$aQ['tbID']]['tbExplain']);
				unset($aTest[$aQ['tbID']]['tbExplainImage']);
				unset($aTest[$aQ['tbID']]['tpScore']);
				unset($aTest[$aQ['tbID']]['tpNum']);
				unset($aTest[$aQ['tbID']]['tpQualify']);
				$aTest[$aQ['tbID']]['TPut'] = array('tpScore'=>'','tpTime'=>'','tpDate'=>'');
			}
		}
		if (!is_null($aTest))
		{
			$aPut = null;
			$result = Model_Test::getTestPut(array(array('tb.ctID','=',$this->aClass['ctID']),array('tp.stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aPut = $result->as_array();
				foreach ($aPut as $aP)
				{
					if (array_key_exists($aP['tbID'],$aTest))
					{
						$aTest[$aP['tbID']]['TPut']['tpScore'] = $aP['tpScore'];
						$aTest[$aP['tbID']]['TPut']['tpTime'] = $aP['tpTime'];
						$aTest[$aP['tbID']]['TPut']['tpDate'] = $aP['tpDate'];
					}
				}
			}
		}

		$res = array('err'=>0,'res'=>is_null($aTest)? '':$aTest);
		$this->response($res);
		return;
	}

	/**
	 * 小テスト基本情報取得
	 */
	public function post_testbase() {
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::testChk($par['tbid']);

		$res = array('err'=>0,'res'=>$this->aTest);
		$this->response($res);
		return;
	}



	/**
	 * 小テスト設問取得
	 */
	public function post_testquery() {
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::testQueryChk($par['tbid']);

		$res = array('err'=>0,'res'=>$this->aTQuery);
		$this->response($res);
		return;
	}

	/**
	 * 小テスト回答取得
	 */
	public function post_testans() {
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::testAnsChk($par['tbid']);

		$res = array('err'=>0,'res'=>$this->aTAnswer);
		$this->response($res);
		return;
	}

	/**
	 * 小テスト提出
	 */
	public function post_testsubmit()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::testChk($par['tbid']);
		self::testQueryChk($par['tbid']);

		if ($this->aTest['tbPublic'] != 1)
		{
			$res['err'] = -2;
			$res['msg'] = '小テストは締め切られているため、提出できません';
			$this->response($res);
			return;
		}

		if (isset($this->aTest['TPut']['tpDate']))
		{
			$res['err'] = -2;
			$res['msg'] = '既に回答済みです';
			$this->response($res);
			return;
		}

		$aAns = json_decode($par['answer'], true);

		try
		{
			$iTime = $par['time'];
			$bUpdate = isset($this->aTest['TPut']['tpDate']);
			Model_Test::setTestPut($this->aTest,$this->aTQuery,$this->aStudent,$aAns,$iTime,$bUpdate);
		}
		catch (Exception $e)
		{
			$res['err'] = -1;
			$res['msg'] = $e->getMessage();
			$this->response($res);
			return;
		}

		$res = array('err'=>0,'res'=>'');
		$this->response($res);
		return;
	}

	/**
	 * 教材倉庫カテゴリ一覧
	 */
	public function post_mcategorylist()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);

		$aMCate = null;
		$result = Model_Material::getMaterialCategoryFromClass($this->aClass['ctID'],null,null,array('mcSort'=>'desc'));
		if (count($result))
		{
			$aMCate = $result->as_array('mcID');
		}

		$aCnt = null;
		try
		{
			$result = Model_Material::getMaterialAlreadyCountFromStudent($this->aStudent['stID']);
			if (count($result))
			{
				$aCnt = $result->as_array('mcID');
			}
		}
		catch (Exception $e)
		{
			\Log::error($e->getMessage());
		}

		if (!is_null($aMCate))
		{
			foreach ($aMCate as $sID => $aMC)
			{
				$aMR = str_replace(null, '', $aMC);
				$aMCate[$sID] = $aMR;
				$aMCate[$sID]['already'] = 0;
				if (isset($aCnt[$sID]))
				{
					$aMCate[$sID]['already'] = (int)$aCnt[$sID]['aCnt'];
				}
			}
		}

		$res = array('err'=>0,'res'=>is_null($aMCate)? '':$aMCate);
		$this->response($res);
		return;
	}

	/**
	 * 教材倉庫一覧
	 */
	public function post_materiallist()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		self::loginChk($par['hash']);
		self::classChk($par['ctid']);
		self::mcChk($par['mcid']);

		$aMaterial = null;
		$result = Model_Material::getMaterial(array(array('mt.mcID','=',$this->aMCate['mcID']),array('mt.mPublic','=',1)),null,array('mt.mSort'=>'desc'));
		if (count($result))
		{
			$aMaterial = $result->as_array();
		}

		if (!is_null($aMaterial))
		{
			foreach ($aMaterial as $i => $aM)
			{
				$iNO = $aM['mNO'];
				$aMaterial[$i]['already'] = 1;

				$aMaterial[$i]['fileURL'] = '';
				$aMaterial[$i]['externalURL'] = '';
				$aMaterial[$i]['thumbURL'] = '';

				if ($aM['fID'] != '')
				{
					$aMaterial[$i]['fileURL'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aM['fID'],'mode'=>'e'));
					if ($aM['fFileType'] == 2)
					{
						$aMaterial[$i]['thumbURL'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aM['fID'],'mode'=>'t'));
					}
				}
				if ($aM['mURL'] != '')
				{
					$urls = explode("\n", $aM['mURL']);
					if (is_array($urls))
					{
						foreach ($urls as $iENO => $v)
						{
							if (!$v) continue;
							$aMaterial[$i]['externalURL'][] = \Uri::create('getfile/externallink/:mno/:eno',array('mno'=>$aM['mNO'],'eno'=>$iENO));
						}
					}
				}
			}
		}

		if (!is_null($aMaterial))
		{
			foreach ($aMaterial as $i => $aM)
			{
				foreach ($aM as $sKey => $sV)
				{
					if (is_null($sV))
					{
						$aMaterial[$i][$sKey] = '';
					}
				}
			}
		}

		$res = array('err'=>0,'res'=>is_null($aMaterial)? '':$aMaterial);
		$this->response($res);
		return;
	}

	/**
	 * グループ判定
	 */
	public function post_groupchk()
	{
		$res = array('err'=>-3,'res'=>'');
		$par = Input::post();

		if (!isset($par['gcode']) || !$par['gcode'])
		{
			$res['msg'] = '団体コードが指定されていません。';
			$this->response($res);
			return;
		}
		$result = Model_Group::getGroup(array(array('gb.gtPrefix','=',$par['gcode'])));
		if (!count($result))
		{
			$res['msg'] = '団体コードが正しくありません。';
			$this->response($res);
			return;
		}
		$aGroup = $result->current();

		$res = array('err'=>0,'res'=>array('gtCode'=>$aGroup['gtPrefix'],'gtName'=>$aGroup['gtName'],'gtLDAP'=>(int)$aGroup['gtLDAP']));
		$this->response($res);
		return;
	}


}

