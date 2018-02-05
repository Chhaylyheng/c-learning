<?php
class Controller_Adm_Ajax extends Controller_Restbase
{
	public $aAdmin = null;

	public function before()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'ログインされていないため処理できません。');
		parent::before();

		$sHash = Cookie::get('CL_ADM_HASH',false);
		if (!$sHash)
		{
			$this->response($res);
			return;
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Admin::getAdminFromHash($aLogin['hash']);
		if (!count($result))
		{
			$res['msg'] = 'ログイン情報が取得できないため、処理を続行することはできません。';
			$this->response($res);
			return;
		}
		$this->aAdmin = $result->current();
		$this->tz = $this->aAdmin['adTimeZone'];
	}

	public function post_KReportPutReset()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'レポート情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$result = Model_KReport::getKReportBase(array(array('krYear','=',$par['iy']),array('krPeriod','=',$par['ip'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'指定のレポート情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aReport = $result->current();

			$result = Model_KReport::deleteKReportPut($par['iy'],$par['ip']);
			$res = array('err'=>0,'res'=>'','msg'=>'提出状況をリセットしました。');
		}
		$this->response($res);
		return;
	}

	public function post_GroupAdminPassReset()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'パスワードリセット情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$result = Model_Group::getGroupAdmins(array(array('gaID','=',$par['ga'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の管理者情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aAdmin = $result->current();

			$sFirst = strtolower(Str::random('distinct', 8));
			$sPass = sha1($sFirst);
			$sHash = sha1($aAdmin['gaLogin'].$sPass);

			$aInsert = array(
				'gaPass'     => $sPass,
				'gaFirst'    => $sFirst,
				'gaPassDate' => '00000000',
				'gaPassMiss' => 0,
				'gaHash'     => $sHash,
			);

			try
			{
				$sGaID = Model_Group::updateGroupAdmin($aInsert,array(array('gaID','=',$par['ga'])));
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>'処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>array('pw'=>$sFirst,'msg'=>''));
		}
		$this->response($res);
		return;
	}























}