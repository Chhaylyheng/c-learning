<?php
class Controller_Org_Flag extends Controller_Org_Base
{
	private $bn = 'org/flag';

	private $aOrgFlag = null;

	public function before()
	{
		parent::before();

		$this->aOrgFlag = \Clfunc_Flag::getOrgFlag();
		$this->template->set_global('aOrgFlag', $this->aOrgFlag);
	}

	public function action_index()
	{
		$sView = $this->bn;

		$sTitle = __('先生と学生の利用設定');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$data = array();
		$data['T_PROF'] = \Clfunc_Common::dec2Bits((int)$this->aGroup['gtTeacherProfFlag']);
		$data['T_AUTH'] = \Clfunc_Common::dec2Bits((int)$this->aGroup['gtTeacherAuthFlag']);
		$data['S_PROF'] = \Clfunc_Common::dec2Bits((int)$this->aGroup['gtStudentProfFlag']);
		$data['S_AUTH'] = \Clfunc_Common::dec2Bits((int)$this->aGroup['gtStudentAuthFlag']);
		$data['S_GET']  = \Clfunc_Common::dec2Bits((int)$this->aGroup['gtStudentGetFlag']);

		if (!Input::post(null,false))
		{
			$data['error'] = null;
			$this->template->content = View::forge($sView,$data);
			$this->template->javascript = array('cl.org.flag.js');
			return $this->template;
		}

		$aInput = Input::post();

		$iTProf = 0;
		if (isset($aInput['T_PROF']))
		{
			foreach ($aInput['T_PROF'] as $iV)
			{
				$iTProf = ($iTProf | (int)$iV);
			}
		}
		$iTAuth = 0;
		if (isset($aInput['T_AUTH']))
		{
			foreach ($aInput['T_AUTH'] as $iV)
			{
				$iTAuth = ($iTAuth | (int)$iV);
			}
		}
		$iSProf = 0;
		if (isset($aInput['S_PROF']))
		{
			foreach ($aInput['S_PROF'] as $iV)
			{
				$iSProf = ($iSProf | (int)$iV);
			}
		}
		$iSAuth = 0;
		if (isset($aInput['S_AUTH']))
		{
			foreach ($aInput['S_AUTH'] as $iV)
			{
				$iSAuth = ($iSAuth | (int)$iV);
			}
		}
		$iSGet = 0;
		if (isset($aInput['S_GET']))
		{
			foreach ($aInput['S_GET'] as $iV)
			{
				$iSGet = ($iSGet | (int)$iV);
			}
		}

		$aInsert = array(
			'gtTeacherProfFlag' => $iTProf,
			'gtTeacherAuthFlag' => $iTAuth,
			'gtStudentProfFlag' => $iSProf,
			'gtStudentAuthFlag' => $iSAuth,
			'gtStudentGetFlag'  => $iSGet,
		);

		try
		{
			$sStID = Model_Group::updateGroup($aInsert,array(array('gtID','=',$this->aGroup['gtID'])));
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG', __('利用設定が完了しました。'));
		Response::redirect(DS.$this->bn);
	}
}