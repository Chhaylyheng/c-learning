<?php
class Controller_Org_Ajax_Teacher extends Controller_Org_Ajax
{
	public function post_MasterChange()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'共同先生情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$aWhere = array(
				array('gtID','=',$this->aAdmin['gtID']),
				array('ctID','=',$par['ct'])
			);

			$result = Model_Group::getGroupClasses($aWhere);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の講義情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$aWhere[] = array('ttID','=',$par['tt']);

			$result = Model_Group::getGroupTeachersClasses(
				array(
					array('gc.gtID','=',$this->aAdmin['gtID']),
					array('tp.ctID','=',$par['ct']),
					array('tp.ttID','=',$par['tt']),
				)
			);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の先生情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aTeacher = $result->current();

			try
			{
				$result = Model_Group::changeGroupClassMaster($par['ct'],$par['tt']);
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>'処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>'');
		}
		$this->response($res);
		return;
	}


	public function post_ClassAdd()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'登録する先生情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$aGtWhere = array('gtID','=',$this->aAdmin['gtID']);

			$result = Model_Group::getGroupClasses(array(array('ctID','=',$par['ct']),$aGtWhere));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の講義情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$sTeachers = "('".implode("','", $par['tt'])."')";

			$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','IN',DB::expr($sTeachers)),array('gtp.gtID','=',$this->aAdmin['gtID'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の先生情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aTeachers = $result->as_array('ttID');

			$result = Model_Group::getGroupTeachersClasses(
				array(
					array('tp.ttID','IN',DB::expr($sTeachers)),
					array('tp.ctID','=',$par['ct']),array('gc.gtID','=',$this->aAdmin['gtID']))
			);
			if (count($result))
			{
				foreach ($result as $aTC)
				{
					if (isset($aTeachers[$aTC['ttID']]))
					{
						unset($aTeachers[$aTC['ttID']]);
					}
				}
			}

			try
			{
				$iCnt = 0;
				if (count($aTeachers))
				{
					$result = Model_Class::entryClassTeacher($aClass,$aTeachers);
					$iCnt = $result[1];
					$result = Model_Teacher::setTeacherClassNum(array_keys($aTeachers));
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>'処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>$iCnt);
		}
		$this->response($res);
		return;
	}

	public function post_ClassRemove()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'共同先生情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$aGtWhere = array('gtID','=',$this->aAdmin['gtID']);

			$result = Model_Group::getGroupClasses(array(array('ctID','=',$par['ct']),$aGtWhere));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の講義情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$sTeachers = "('".implode("','", $par['tt'])."')";

			$result = Model_Group::getGroupTeachersClasses(
				array(
					array('tp.ttID','IN',DB::expr($sTeachers)),
					array('tp.ctID','=',$par['ct']),
					array('tp.tpMaster','=',0),
					array('gc.gtID','=',$this->aAdmin['gtID'])
				)
			);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の先生情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aTeachers = $result->as_array('ttID');
			try
			{
				$result = Model_Class::removeClassTeacher($aClass,$aTeachers);
				$iCnt = $result;
				$result = Model_Teacher::setTeacherClassNum(array_keys($aTeachers));
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>'処理に失敗しました。時間をおいてから、再度実行してください。('.$e->getMessage().')');
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>$iCnt);
		}
		$this->response($res);
		return;
	}


	public function post_PassReset()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'パスワードリセット情報が正しく送信されていません。');
		$par = Input::post();
		if ($par)
		{
			$result = Model_Group::getGroupTeachers(array(array('gtp.ttID','=',$par['tt'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>'対象の先生情報が見つかりません。');
				$this->response($res);
				return;
			}
			$aTeacher = $result->current();

			$sFirst = strtolower(Str::random('distinct', 8));
			$sPass = sha1($sFirst);
			$sHash = sha1($aTeacher['ttMail'].$sPass);

			$aInsert = array(
				'ttPass'     => $sPass,
				'ttFirst'    => $sFirst,
				'ttPassDate' => '00000000',
				'ttPassMiss' => 0,
				'ttHash'     => $sHash,
			);

			try
			{
				$sStID = Model_Teacher::updateTeacher($aTeacher['ttID'],$aInsert);
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

