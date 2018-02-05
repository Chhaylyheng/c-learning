<?php
class Controller_Org_Ajax extends Controller_Restbase
{
	public $aAdmin = null;

	public function before()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'ログインされていないため処理できません。');
		parent::before();

		$sHash = Cookie::get('CL_ORG_HASH',false);
		if (!$sHash)
		{
			$this->response($res);
			return;
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Group::getGroupAdminFromHash($aLogin['hash']);
		if (!count($result))
		{
			$res['msg'] = 'ログイン情報が取得できないため、処理を続行することはできません。';
			$this->response($res);
			return;
		}
		$this->aAdmin = $result->current();
		$this->tz = $this->aAdmin['gaTimeZone'];
	}
}
