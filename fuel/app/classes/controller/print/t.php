<?php
class Controller_Print_T extends Controller_T_Base
{
	public function before()
	{
		$this->template = 'print/template';

		parent::before();

		$this->template->set_global('title', $this->sSiteTitle);
	}

	public function action_index()
	{
		Response::redirect('index/404','location',404);
	}

	public function action_StuIdList($sCtID = null, $sStID = null)
	{
		if (!is_null($sCtID))
		{
			$result = Model_Class::getClassFromID($sCtID);
			if (count($result)) {
				$this->aClass = $result->current();
				Cookie::set('CL_T_CLASS_ID',$sCtID);
				$this->template->set_global('aClass',$this->aClass);
			}
			else
			{
				Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
				Response::redirect('/t/index');
			}
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('講義情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		$aAndWhere = null;
		if (!is_null($sStID))
		{
			$aAndWhere = array(array('sp.stID','=',$sStID));
		}

		$aStudent = null;
		$result = Model_Student::getStudentFromClass($this->aClass['ctID'],$aAndWhere,null,array('st.stNO'=>'asc','st.stName'=>'acs','st.stLogin'=>'acs'));
		if (count($result))
		{
			$aStudent = $result->as_array('stID');
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('対象の学生がいません。'));
			Response::redirect('/t/student');
		}

		$this->template->content = View::forge('print/student_id_list');
		$this->template->content->set('aStudent',$aStudent);
		$this->template->javascript = array('cl.t.student.js');
		return $this->template;
	}

	public function action_StuLogin($sCtID = null)
	{
		if (!is_null($sCtID))
		{
			$result = Model_Class::getClassFromID($sCtID);
			if (count($result)) {
				$this->aClass = $result->current();
				Cookie::set('CL_T_CLASS_ID',$sCtID);
				$this->template->set_global('aClass',$this->aClass);
			}
			else
			{
				Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
				Response::redirect('/t/index');
			}
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('講義情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		$this->template->content = View::forge('print/student_login');
		return $this->template;
	}

	public function action_GuestLogin($sCtID = null)
	{
		if (!is_null($sCtID))
		{
			$result = Model_Class::getClassFromID($sCtID,1);
			if (count($result)) {
				$this->aClass = $result->current();
				Cookie::set('CL_T_CLASS_ID',$sCtID);
				$this->template->set_global('aClass',$this->aClass);
			}
			else
			{
				Session::set('SES_T_ERROR_MSG',__('指定された講義が見つかりません。'));
				Response::redirect('/t/index');
			}
		}
		else
		{
			Session::set('SES_T_ERROR_MSG',__('講義情報が確認できませんでした。'));
			Response::redirect('/t/index');
		}

		$this->template->content = View::forge('print/guest_login');
		return $this->template;
	}

	public function action_LoginQR($sDir = null)
	{
		\Package::load('qrcode');
		exit(QRCode::png(CL_MBURL.DS.$sDir));
	}

	public function action_LDAPLoginQR($sDir = null)
	{
		\Package::load('qrcode');
		exit(QRCode::png(CL_PROTOCOL.'://'.CL_DOMAIN.DS.$this->aGroup['gtPrefix'].DS.$sDir));
	}
}

