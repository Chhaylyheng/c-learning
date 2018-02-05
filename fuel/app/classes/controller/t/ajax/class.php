<?php
class Controller_T_Ajax_Class extends Controller_T_Ajax
{
	public function post_Sort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$iStatus = ($par['status'] == 'active')? 1:0;


			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],$iStatus,array(array('tp.ctID','=',$par['ct'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('講義情報が確認できませんでした。'));
				$this->response($res);
				return;
			}
			$aClass = $result->current();

			$aCls = null;
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],$iStatus,null,null,array('tp.tpSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更する講義情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			$aCls = $result->as_array('ctID');
			$aCtIDs = array_keys($aCls);

			if (($aClass['tpSort'] == $iMax && $par['m'] == 'up') || ($aClass['tpSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}

			$result = Model_Class::sortClass($aClass,$aCtIDs,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}
}
