<?php
class Controller_Adm_Group extends Controller_Adm_Base
{
	private $bn = 'group';

	private $aGroupBase = array(
		'gt_prefix'=>null,
		'gt_name'=>null,
		'gt_ldap'=>0,
		'gt_l_protocol'=>'LDAPS',
		'gt_l_server'=>null,
		'gt_l_port'=>0,
		'gt_l_dn'=>null,
		'gt_l_sb'=>null,
		'gt_l_uid'=>'uid',
	);
	private $aAdminBase = array(
		'ga_name'=>null,
		'ga_login'=>null,
		'ga_pass'=>null,
	);

	private $aGroup = null;
	private $aGAdmin = null;

	public function action_index()
	{
		$sTitle = '団体一覧';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/adm/'.$this->bn.'/create',
				'name' => '団体の新規登録',
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aGroups = null;
		$result = Model_Group::getGroup(null,null,array('gb.gtName'=>'asc'));
		if (count($result))
		{
			$aGroups = $result->as_array();
		}

		$this->template->content = View::forge('adm/group/index');
		$this->template->content->set('aGroups',$aGroups);
		$this->template->javascript = array('cl.adm.group.js');
		return $this->template;
	}

	public function action_create()
	{
		$view = 'adm/'.$this->bn.'/edit';

		# タイトル
		$sTitle = '団体の新規作成';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->bn,'name'=>'団体一覧');
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aGroupBase;
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.adm.group.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('gt_name', '団体名称', 'required|max_length[30]');

		$val->add('gt_prefix', '接頭辞')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('min_length', 3)
			->add_rule('max_length', 3)
			->add_rule('valid_string', array('alpha','numeric','utf8'));

		if (isset($aInput['gt_ldap']) && $aInput['gt_ldap'] > 0)
		{
			$val->add_field('gt_l_server', 'サーバー', 'required|trim|max_length[255]');
			$val->add('gt_l_port', 'ポート番号')
				->add_rule('required')
				->add_rule('trim')
				->add_rule('valid_string',array('numeric'))
				->add_rule('numeric_min', 0)
				->add_rule('numeric_max', 99999)
			;
			$val->add_field('gt_l_dn', 'バインドする識別名 (-D,binddn)', 'required|trim|max_length[255]');
			$val->add_field('gt_l_sb', '検索の開始位置 (-b,searchbase)', 'trim|max_length[255]');
		}

		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.adm.group.js');
			return $this->template;
		}

		$result = Model_Group::getGroup(array(array('gb.gtPrefix','=',$aInput['gt_prefix'])));
		if (count($result))
		{
			$aInput['error'] = array('gt_prefix'=>'指定の接頭辞は利用できません。');
			$this->template->content = View::forge($view,$aInput);
			$this->template->javascript = array('cl.adm.group.js');
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'gtPrefix' => $aInput['gt_prefix'],
			'gtName' => $aInput['gt_name'],
			'gtLDAP' => ((isset($aInput['gt_ldap']))? (int)$aInput['gt_ldap']:0),
			'gtLProtocol' => $aInput['gt_l_protocol'],
			'gtLServer' => $aInput['gt_l_server'],
			'gtLPort' => (int)$aInput['gt_l_port'],
			'gtLBinddn' => $aInput['gt_l_dn'],
			'gtLSearchbase' => $aInput['gt_l_sb'],
			'gtLUID' => $aInput['gt_l_uid'],
			'gtDate' => date('YmdHis'),

			'gtTeacherProfFlag' => 45,
			'gtTeacherAuthFlag' => 81,
			'gtStudentProfFlag' => 255,
			'gtStudentAuthFlag' => 1,
		);

		try
		{
			$result = Model_Group::insertGroup($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','団体を作成しました。');
		Response::redirect('/adm/'.$this->bn);
	}

	public function action_edit($sID = null)
	{
		$view = 'adm/'.$this->bn.'/edit';

		$aChk = self::GroupChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aGroup',$this->aGroup);

		# タイトル
		$sTitle = '団体情報の編集';
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/'.$this->bn,'name'=>'団体一覧');
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		if (!Input::post(null,false))
		{
			$data = $this->aGroupBase;
			$data['gt_name'] = $this->aGroup['gtName'];
			$data['gt_prefix'] = $this->aGroup['gtPrefix'];

			$data['gt_ldap'] = $this->aGroup['gtLDAP'];
			$data['gt_l_protocol'] = $this->aGroup['gtLProtocol'];
			$data['gt_l_server'] = $this->aGroup['gtLServer'];
			$data['gt_l_port'] = $this->aGroup['gtLPort'];
			$data['gt_l_dn'] = $this->aGroup['gtLBinddn'];
			$data['gt_l_sb'] = $this->aGroup['gtLSearchbase'];
			$data['gt_l_uid'] = $this->aGroup['gtLUID'];

			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->content->set('aGroup',$this->aGroup);
			$this->template->javascript = array('cl.adm.group.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add_field('gt_name', '団体名称', 'required|max_length[30]');

		if ($this->aGroup['gtCNum'] <= 0)
		{
			$val->add('gt_prefix', '接頭辞')
				->add_rule('required')
				->add_rule('trim')
				->add_rule('exact_length', 3)
				->add_rule('valid_string', array('alpha','numeric','utf8'));
		}

		if (isset($aInput['gt_ldap']) && $aInput['gt_ldap'] > 0)
		{
			$val->add_field('gt_l_server', 'サーバー', 'required|trim|max_length[255]');
			$val->add('gt_l_port', 'ポート番号')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('valid_string',array('numeric'))
			->add_rule('numeric_min', 0)
			->add_rule('numeric_max', 99999)
			;
			$val->add_field('gt_l_dn', 'バインドする識別名 (-D,binddn)', 'required|trim|max_length[255]');
			$val->add_field('gt_l_sb', '検索の開始位置 (-b,searchbase)', 'trim|max_length[255]');
		}

		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($view,$aInput);
			$this->template->javascript = array('cl.adm.group.js');
			return $this->template;
		}

		if ($this->aGroup['gtCNum'] <= 0)
		{
			$result = Model_Group::getGroup(array(array('gb.gtPrefix','=',$aInput['gt_prefix']),array('gb.gtID','!=',$this->aGroup['gtID'])));
			if (count($result))
			{
				$aInput['error'] = array('gt_prefix'=>'指定の接頭辞は利用できません。');
				$this->template->content = View::forge($view,$aInput);
				$this->template->javascript = array('cl.adm.group.js');
				return $this->template;
			}
		}

		// 更新データ生成
		$aUpdate = array(
			'gtName' => $aInput['gt_name'],
			'gtLDAP' => ((isset($aInput['gt_ldap']))? (int)$aInput['gt_ldap']:0),
			'gtLProtocol' => $aInput['gt_l_protocol'],
			'gtLServer' => $aInput['gt_l_server'],
			'gtLPort' => (int)$aInput['gt_l_port'],
			'gtLBinddn' => $aInput['gt_l_dn'],
			'gtLSearchbase' => $aInput['gt_l_sb'],
			'gtLUID' => $aInput['gt_l_uid'],
		);

		if ($this->aGroup['gtCNum'] <= 0)
		{
			$aUpdate['gtPrefix'] = $aInput['gt_prefix'];
		}

		try
		{
			$result = Model_Group::updateGroup($aUpdate,array(array('gtID','=',$sID)));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','団体情報を更新しました。');
		Response::redirect('/adm/'.$this->bn);

	}

	public function action_admlist($sID = null)
	{
		$aChk = self::GroupChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sTitle = $this->aGroup['gtName'].'管理者一覧';
		# パンくずリスト生成
		$aBreadCrumbs = array(
			array('name'=>'団体一覧','link'=>DS.$this->bn),
			array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/adm/'.$this->bn.'/admcreate/'.$sID,
				'name' => '管理者の新規登録',
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aAdmins = null;
		$result = Model_Group::getGroupAdmins(array(array('gtID','=',$sID)),null,array('gaLogin'=>'asc'));
		if (count($result))
		{
			$aAdmins = $result->as_array();
		}

		$this->template->content = View::forge('adm/group/admlist');
		$this->template->content->set('aGroup',$this->aGroup);
		$this->template->content->set('aAdmins',$aAdmins);
		$this->template->javascript = array('cl.adm.group.js');
		return $this->template;
	}

	public function action_admcreate($sID = null)
	{
		$view = 'adm/'.$this->bn.'/admedit';

		$aChk = self::GroupChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = '管理者の新規登録';
		# パンくずリスト生成
		$aBreadCrumbs = array(
			array('name'=>'団体一覧','link'=>DS.$this->bn),
			array('name'=>$this->aGroup['gtName'].'管理者一覧','link'=>DS.$this->bn.DS.'admlist'.DS.$sID),
			array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		$this->template->set_global('pagetitle',$sTitle);
		$this->template->set_global('aGroup',$this->aGroup);

		if (!Input::post(null,false))
		{
			$data = $this->aAdminBase;

			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('ga_login', 'ログインID')
		->add_rule('required')
		->add_rule('trim')
		->add_rule('min_length', 4)
		->add_rule('max_length', 20)
		->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));

		if (trim($aInput['ga_pass']))
		{
			$val->add('ga_pass', 'パスワード')
			->add_rule('required')
			->add_rule('trim')
			->add_rule('min_length', 8)
			->add_rule('max_length', 32)
			->add_rule('passwd_char');
		}

		$val->add('ga_name', '氏名')
		->add_rule('required')
		->add_rule('trim')
		->add_rule('max_length', 50);

		if (!$val->run())
		{
			$aInput['error'] = $val->error();

			$this->template->content = View::forge($view,$aInput);
			return $this->template;
		}
		$result = Model_Group::getGroupAdmins(array(array('gaLogin','=',$aInput['ga_login'])));
		if (count($result))
		{
			$aInput['error'] = array('ga_login'=>'指定のログインIDは利用できません。');
			$this->template->content = View::forge($view,$aInput);
			return $this->template;
		}

		// 登録データ生成
		$sFirst = null;
		if (trim($aInput['ga_pass']))
		{
			$sFirst = $aInput['ga_pass'];
		}
		else
		{
			$sFirst = strtolower(Str::random('distinct', 8));
		}
		$sPass = sha1($sFirst);
		$sHash = sha1($aInput['ga_login'].$sPass);

		$aInsert = array(
			'gtID'            => $sID,
			'gaLogin'         => $aInput['ga_login'],
			'gaPass'          => $sPass,
			'gaFirst'         => $sFirst,
			'gaName'          => $aInput['ga_name'],
			'gaHash'          => $sHash,
			'gaDate'          => date('YmdHis'),
		);

		try
		{
			$sGaID = Model_Group::insertGroupAdmin($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','管理者「'.$aInput['ga_name'].'」を登録しました。');
		Response::redirect('/adm/group/admlist/'.$sID);
	}

	public function action_admedit($sID = null, $sAID = null)
	{
		$view = 'adm/'.$this->bn.'/admedit';

		$aChk = self::GroupChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::GroupAdminChecker($sAID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = '管理者情報の編集';
		# パンくずリスト生成
		$aBreadCrumbs = array(
			array('name'=>'団体一覧','link'=>DS.$this->bn),
			array('name'=>$this->aGroup['gtName'].'管理者一覧','link'=>DS.$this->bn.DS.'admlist'.DS.$sID),
			array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		$this->template->set_global('pagetitle',$sTitle);
		$this->template->set_global('aGroup',$this->aGroup);
		$this->template->set_global('aGAdmin',$this->aGAdmin);

		if (!Input::post(null,false))
		{
			$data = $this->aAdminBase;
			$data = array(
				'ga_name'  => $this->aGAdmin['gaName'],
				'ga_login' => $this->aGAdmin['gaLogin'],
			);
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			return $this->template;
		}

		$aInput = Input::post();
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('ga_login', 'ログインID')
		->add_rule('required')
		->add_rule('trim')
		->add_rule('min_length', 4)
		->add_rule('max_length', 20)
		->add_rule('valid_string', array('alpha','numeric','dashes','dots','utf8'));

		$val->add('ga_name', '氏名')
		->add_rule('required')
		->add_rule('trim')
		->add_rule('max_length', 50);

		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$this->template->content = View::forge($view,$aInput);
			return $this->template;
		}
		$result = Model_Group::getGroupAdmins(array(array('gaLogin','=',$aInput['ga_login']),array('gaID','!=',$sAID)));
		if (count($result))
		{
			$aInput['error'] = array('ga_login'=>'指定のログインIDは利用できません。');
			$this->template->content = View::forge($view,$aInput);
			return $this->template;
		}

		// 登録データ生成
		$sHash = sha1($aInput['ga_login'].$this->aGAdmin['gaPass']);
		$aInsert = array(
			'gaLogin' => $aInput['ga_login'],
			'gaName'  => $aInput['ga_name'],
			'gaHash'  => $sHash,
			'gaPassMiss' => 0,
		);

		try
		{
			$sGaID = Model_Group::updateGroupAdmin($aInsert,array(array('gaID','=',$sAID)));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','管理者「'.$aInput['ga_name'].'」を更新しました。');
		Response::redirect('/adm/group/admlist/'.$sID);
	}

	public function action_admdelete($sID = null, $sAID = null)
	{
		$aChk = self::GroupChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$aChk = self::GroupAdminChecker($sAID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		try
		{
			$result = Model_Group::deleteGroupAdmin($sAID);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','管理者を削除しました。');
		Response::redirect('/adm/group/admlist/'.$sID);
	}

	public function action_teachlist($sID = null)
	{
		$aChk = self::GroupChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sTitle = $this->aGroup['gtName'].'所属先生一覧';
		# パンくずリスト生成
		$aBreadCrumbs = array(
			array('name'=>'団体一覧','link'=>DS.$this->bn),
			array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/adm/'.$this->bn.'/teachadd/'.$sID,
				'name' => '先生の追加',
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aTeachers = null;
		$result = Model_Group::getGroupTeachers(array(array('gtp.gtID','=',$sID)),null,array('tv.ttName'=>'asc'));
		if (count($result))
		{
			$aTeachers = $result->as_array();
		}

		$this->template->content = View::forge('adm/group/teachlist');
		$this->template->content->set('aGroup',$this->aGroup);
		$this->template->content->set('aTeachers',$aTeachers);
		$this->template->javascript = array('cl.adm.group.js');
		return $this->template;
	}

	public function action_teachadd($sID = null)
	{
		$view = 'adm/'.$this->bn.'/teachadd';

		$aChk = self::GroupChecker($sID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aGroup',$this->aGroup);

		$sTitle = '所属先生の追加';
		# パンくずリスト生成
		$aBreadCrumbs = array(
			array('name'=>'団体一覧','link'=>DS.$this->bn),
			array('name'=>$this->aGroup['gtName'].'所属先生一覧','link'=>DS.$this->bn.DS.'teachlist'.DS.$sID),
			array('name'=>$sTitle),
		);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = array('gtlist' => '');
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			return $this->template;
		}

		$aInput = Input::post(null,false);
		$array = explode("\n", $aInput['gtlist']); // とりあえず行に分割
		$array = array_map('trim', $array); // 各行にtrim()をかける
		$array = array_filter($array, 'strlen'); // 文字数が0の行を取り除く
		$array = array_unique($array);
		$aTIDs = array_values($array); // これはキーを連番に振りなおしてるだけ

		if (!count($aTIDs))
		{
			$aInput['error'] = array('gtlist'=>'追加する先生IDが指定されていません');;
			$this->template->content = View::forge($view,$aInput);
			return $this->template;
		}

		try
		{
			$iTCnt = Model_Group::addGroupTeachers($aTIDs,$sID);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		if ($iTCnt)
		{
			Session::set('SES_ADM_NOTICE_MSG','所属先生を'.$iTCnt.'名追加しました。');
		}
		else
		{
			Session::set('SES_ADM_NOTICE_MSG','追加可能な先生がいませんでした。');
		}
		Response::redirect('/adm/group/teachlist/'.$sID);
	}



	private function GroupChecker($sGtID = null)
	{
		if (is_null($sGtID))
		{
			return array('msg'=>'団体が送信されていません。','url'=>'/adm/'.$this->bn);
		}
		$result = Model_Group::getGroup(array(array('gb.gtID','=',$sGtID)));
		if (!count($result))
		{
			return array('msg'=>'指定された団体が見つかりません。','url'=>'/adm/'.$this->bn);
		}
		$this->aGroup = $result->current();

		return true;
	}

	private function GroupAdminChecker($sGaID = null)
	{
		if (is_null($sGaID))
		{
			return array('msg'=>'管理者が送信されていません。','url'=>'/adm/'.$this->bn);
		}
		$result = Model_Group::getGroupAdmins(array(array('gaID','=',$sGaID)));
		if (!count($result))
		{
			return array('msg'=>'指定された管理者が見つかりません。','url'=>'/adm/'.$this->bn);
		}
		$this->aGAdmin = $result->current();

		return true;
	}

}


