<?php
class Controller_T_Baseclass extends Controller_T_Base
{
	public function before()
	{
		parent::before();

		$sCtID = Cookie::get('CL_T_CLASS_ID',false);
		if ($sCtID)
		{
			$result = Model_Class::getClassFromTeacher($this->aTeacher['ttID'],null,array(array('tp.ctID','=',$sCtID)));
			if (count($result)) {
				$this->aClass = $result->current();
			}
		}
		if (is_null($this->aClass))
		{
			Session::set('SES_T_ERROR_MSG',__('講義情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		# 基本のパンくずリスト
		if ($this->aClass['ctStatus'] == 0)
		{
			$this->aBread[] = array('link'=>'/index/close','name'=>__('終了した講義'));
		}
		$this->aBread[] = array('link'=>'/class/index/'.$this->aClass['ctID'],'name'=>$this->aClass['ctName']);

		$this->template->set_global('aClass',$this->aClass);

/*
		if (!$this->aTeacher['gtID'] && $this->aTeacher['coTermDate'] < date('Y-m-d'))
		{
			Session::set('SES_T_ERROR_MSG',__('現在、契約がありません。:siteを利用するにはプランを選択の上、購入・契約を行ってください。',array('site'=>CL_SITENAME)));
			Response::redirect('/t/payment/product');
		}
*/
	}
}

