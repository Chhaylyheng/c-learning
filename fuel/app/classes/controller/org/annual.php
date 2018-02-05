<?php
class Controller_Org_Annual extends Controller_Org_Base
{
	private $bn = 'org/annual';

	public function action_index()
	{
		$sTitle = __('年次更新');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>$sTitle)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		$aYears = null;
		$result = Model_Group::getGroupClasses(array(array('gtID','=',$this->aGroup['gtID'])));
		if (count($result))
		{
			$aTemp = $result->as_array('ctYear');
			$aYears = array_keys($aTemp);
			if (!is_null($aYears))
			{
				sort($aYears);
			}
		}

		$aStuYears = null;
		$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$this->aGroup['gtID'])));
		if (count($result))
		{
			foreach ($result as $aS)
			{
				if ($aS['stYear'])
				{
					if (isset($aStuYears[$aS['stYear']]))
					{
						$aStuYears[$aS['stYear']] += 1;
					}
					else
					{
						$aStuYears[$aS['stYear']] = 1;
					}
				}
			}
			if (!is_null($aStuYears))
			{
				ksort($aStuYears);
			}
		}

		$this->template->content = View::forge($this->bn.DS.'index');
		$this->template->content->set('aYears',$aYears);
		$this->template->content->set('aStuYears',$aStuYears);
		$this->template->javascript = array('cl.org.annual.js');
		return $this->template;
	}

	public function post_classclose()
	{
		$aInput = Input::post(null,false);
		if (!$aInput)
		{
			Session::set('SES_ORG_ERROR_MSG', __('処理に必要な情報がありません'));
			Response::redirect(DS.$this->bn);
		}

		$aClass = null;
		$result = Model_Group::getGroupClasses(array(array('gtID','=',$this->aGroup['gtID']),array('ctYear','=',(int)$aInput['year'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の講義が見つかりません'));
			Response::redirect(DS.$this->bn);
		}
		$aClass = $result->as_array('ctID');

		$sMsg = __(':year年度の講義を終了しました。',array('year'=>(int)$aInput['year'])).'（'.count($aClass).__('講義').'）';
		if (isset($aInput['renew']))
		{
			$sMsg .= "\n".__('また、:year度講義を自動生成しました。',array('year'=>((int)$aInput['year'] + 1)));
		}

		try
		{
			$result = Model_Group::annualClass($aClass, (int)$aInput['renew']);

		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		$result = Model_Group::getGroupTeachers(array(array('gtp.gtID','=',$this->aGroup['gtID'])));
		if (count($result))
		{
			$aTemp = $result->as_array('ttID');
			$aIDs = array_keys($aTemp);
			\Model_Teacher::setTeacherClassNum($aIDs);
		}

		Session::set('SES_ORG_NOTICE_MSG',$sMsg);
		Response::redirect(DS.$this->bn);
	}

	public function post_studentdelete()
	{
		$aInput = Input::post(null,false);
		if (!$aInput)
		{
			Session::set('SES_ORG_ERROR_MSG', __('処理に必要な情報がありません'));
			Response::redirect(DS.$this->bn);
		}

		$aStudents = null;
		$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$this->aGroup['gtID']),array('st.stYear','=',(int)$aInput['year'])));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の学生が見つかりません'));
			Response::redirect(DS.$this->bn);
		}
		$aStudents = $result->as_array('stID');
		$aStIDs = array_keys($aStudents);

		$sMsg = __(':year年の学生を削除しました。',array('year'=>(int)$aInput['year'])).'（'.__(':num名',array('num'=>count($aStudents))).'）';

		try
		{
			$result = Model_Student::deleteGroupStudent($aStIDs);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG',$sMsg);
		Response::redirect(DS.$this->bn);
	}

	public function post_studentyearincrement()
	{
		$aInput = Input::post(null,false);
		if (!$aInput)
		{
			Session::set('SES_ORG_ERROR_MSG', __('処理に必要な情報がありません'));
			Response::redirect(DS.$this->bn);
		}

		$aStudents = null;
		$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$this->aGroup['gtID']),array('st.stYear','!=',0)));
		if (!count($result))
		{
			Session::set('SES_ORG_ERROR_MSG', __('対象の学生が見つかりません'));
			Response::redirect(DS.$this->bn);
		}
		$aStudents = $result->as_array('stID');
		$aStIDs = array_keys($aStudents);

		$sMsg = __('学生の学年を更新しました。').'（'.__(':num名',array('num'=>count($aStudents))).'）';

		try
		{
			$result = Model_Group::annualStudentYearIncrement($aStIDs);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ORG_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ORG_NOTICE_MSG',$sMsg);
		Response::redirect(DS.$this->bn);
	}

}


