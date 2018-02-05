<?php
class Controller_Org_Ajax_Student extends Controller_Org_Ajax
{
	public function post_PassReset()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'パスワードリセット情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$result = Model_Group::getGroupStudents(array(array('gsp.stID','=',$par['st']),array('gsp.gtID','=',$this->aAdmin['gtID'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の学生情報が見つかりません。');
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
