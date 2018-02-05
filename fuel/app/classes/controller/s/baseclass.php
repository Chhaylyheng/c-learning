<?php
class Controller_S_Baseclass extends Controller_S_Base
{
	public function before()
	{
		parent::before();

		if (is_null($this->aClass))
		{
			Session::set('SES_S_ERROR_MSG',__('講義情報が確認できませんでした。'));
			Response::redirect('/s/index'.$this->sesParam);
		}
		$aC = explode('_', get_class($this));
		$sC = strtolower($aC[2]);
		if ($this->bQuickTeacher && $sC != 'quest')
		{
			Response::redirect('/s/index'.$this->sesParam);
		}

		# 基本のパンくずリスト
		$this->aBread[] = array('link'=>'/class/index/'.$this->aClass['ctID'],'name'=>$this->aClass['ctName']);
	}
}

