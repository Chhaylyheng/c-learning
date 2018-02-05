<?php
class Controller_Org_Ajax_Class extends Controller_Org_Ajax
{
	public function post_Public()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>'講義情報が正しく送信されていません。');
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

			if ($par['m'] == 'public')
			{
				$iStatus = 1;
				$sText = '実施';
				$sClass = 'font-red';
			}
			elseif ($par['m'] == 'private')
			{
				$iStatus = 0;
				$sText = '終了';
				$sClass = 'font-silver';
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>'実施情報が正しく送信されていません。');
				$this->response($res);
				return;
			}

			$aUpdate = array(
				'ctStatus'=>$iStatus,
			);
			$result = Model_Class::updateClass($aUpdate,$aClass);
			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>'実施情報を変更しました。');
		}
		$this->response($res);
		return;
	}
}
