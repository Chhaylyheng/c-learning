<?php
class Controller_S_Ajax extends Controller_Restbase
{
	public $aStudent = null;
	public $sAwsSavePath = null;
	public $sTempFilePath = null;

	public function before()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'ログインされていないため処理できません。');
		parent::before();

		$sHash = Cookie::get('CL_SL_HASH',false);
		if (!$sHash)
		{
			$sHash = Session::get('CL_SL_HASH',false);
			if (!$sHash)
			{
				$this->response($res);
				return;
			}
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Student::getStudentFromHash($aLogin['hash']);
		if (!count($result))
		{
			$res['msg'] = 'ログイン情報が取得できないため、処理を続行することはできません。';
			$this->response($res);
			return;
		}

		$this->aStudent = $result->current();
		$this->tz = $this->aStudent['stTimeZone'];
		$this->sAwsSavePath = 'student'.DS.$this->aStudent['stID'];
		$this->sTempFilePath = CL_UPPATH.DS.'temp';
	}
}

