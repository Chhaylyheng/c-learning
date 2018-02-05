<?php
class Controller_Adm_Teacher extends Controller_Adm_Base
{
	private $bn = 'teacher';
	private $aTeacher = null;

	private $aContractBase = array(
		'ptID'=>null,
		'coTermDate'=>null,
		'coClassNum'=>0,
		'coStuNum'=>0,
		'coCapacity'=>0,
	);
	private $aSearchCol = array(
		'ttMail','ttName','ttDept','ttSubject','cmName'
	);


	public function action_index()
	{
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'先生一覧')));
		# ページタイトル生成
		$this->template->set_global('pagetitle','先生一覧');

		$aWords = null;
		$sWords = null;
		$sW = Input::get('w',false);
		if ($sW)
		{
			$aW = \Clfunc_Common::getSearchWords($sW);
			$aWords = \Clfunc_Common::getSearchWhere($aW,$this->aSearchCol);
			$sWords = implode(' ', $aW);
		}

		$aTeachers = null;
		$result = Model_Teacher::getTeacher(null,null,array('cmKCode'=>'asc','ttMail'=>'asc'),$aWords);
		if (count($result))
		{
			$aTeachers = $result->as_array();
		}

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => '/adm/output/teacherlist.csv',
				'name' => '一覧をCSVでダウンロード',
				'icon' => 'fa-download',
			),
		);
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$aSearchForm = array(
			'url' => '/adm/teacher',
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge('adm/teacher/index');
		$this->template->content->set('aTeachers',$aTeachers);
		$this->template->javascript = array('cl.adm.teacher.js');
		return $this->template;
	}


	public function action_contract($sTtID = null)
	{
		$view = 'adm/'.$this->bn.'/contract';

		$aChk = self::TeacherChecker($sTtID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}
		$this->template->set_global('aTeacher',$this->aTeacher);

		$aContract = null;
		$result = Model_Contract::getContract(array(array('ttID','=',$sTtID)),null,array('coNO'=>'DESC'));
		if (count($result))
		{
			$aContract = $result->as_array();
		}
		$this->template->set_global('aContract',$aContract);

		$sTitle = (($this->aTeacher['ttName'])? $this->aTeacher['ttName']:$this->aTeacher['ttMail']).'さんの契約情報';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(
			array('name'=>'先生一覧','link'=>DS.$this->bn),
			array('name'=>$sTitle)
		));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		if ($this->aTeacher['ptID'] != 99)
		{
			if ($this->aTeacher['ttStatus'] == 0)
			{
				$sIcon = 'fa-unlock';
				$sAct = '稼働';
			}
			else
			{
				$sIcon = 'fa-lock';
				$sAct = '停止';
			}

			$aCustomBtn = array(
				array(
					'url'  => '#',
					'name' => '先生を'.$sAct,
					'icon' => $sIcon,
					'option' => array(
						'data' => $sTtID,
					),
					'class' => array(
						'TeacherLock'
					),
				),
				array(
					'url'  => '#',
					'name' => '先生を削除',
					'icon' => 'fa-trash',
					'option' => array(
						'data' => $sTtID,
					),
					'class' => array(
						'TeacherDelete'
					),
				),
			);
			$this->template->set_global('aCustomBtn',$aCustomBtn);
		}

		$result = Model_Payment::getPlan(array(array('ptID','!=',99)));
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','プランが設定されていません。');
			Response::redirect('/adm'.DS.$this->bn);
		}
		$aPlan = $result->as_array('ptID');
		$this->template->set_global('aPlan',$aPlan);

		if (!Input::post(null,false))
		{
			$data = $this->aContractBase;
			if ($this->aTeacher['coNO'])
			{
				$data['ptID'] = $this->aTeacher['ptID'];
				$data['coTermDate'] = $this->aTeacher['coTermDate'];
				$data['coClassNum'] = $this->aTeacher['coClassNum'];
				$data['coStuNum'] = $this->aTeacher['coStuNum'];
				$data['coCapacity'] = $this->aTeacher['coCapacity'];
			}
			else
			{
				$data['coTermDate'] = date('Y-m-d', strtotime('+1month'));
			}
			$data['error'] = null;
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.adm.teacher.js');
			return $this->template;
		}

		$aInput = Input::post();

		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');
		$val->add('coTermDate', '契約満了日')
			->add_rule('required')
			->add_rule('date')
		;

		if (!$val->run())
		{
			$data = $aInput;
			$data['error'] = $val->error();
			$this->template->content = View::forge($view,$data);
			$this->template->javascript = array('cl.adm.teacher.js');
			return $this->template;
		}

		unset($aInput['sub_state']);

		try
		{
			if ($this->aTeacher['coNO'])
			{
				$result = Model_Contract::updateContract($aInput,array(array('ttID','=',$sTtID),array('coNO','=',$this->aTeacher['coNO'])));
			}
			else
			{
				$aInput['ttID'] = $sTtID;
				$aInput['coNO'] = (isset($aContract[0]))? ($aContract[0]['coNO'] + 1):1;
				$aInput['coStartDate'] = date('Y-m-d');
				$result = Model_Contract::insertContract($aInput);
			}
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','契約情報を更新しました。【'.$this->aTeacher['ttName'].'】');
		Response::redirect('/adm/'.$this->bn);
	}

	public function action_delete($sTtID = null)
	{
		$sBack = '/adm/'.$this->bn.'/contract/'.$sTtID;

		$aChk = self::TeacherChecker($sTtID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$result = Model_Contract::getContract(array(array('ttID','=',$sTtID),array('coNO','!=',99)));
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','団体所属先生の削除はできません。');
			Response::redirect($sBack);
		}

		$aClasses = null;
		$result = Model_Class::getClassFromTeacher($sTtID,null,array(array('tp.tpMaster','=',1)));
		if (count($result))
		{
			$aClasses = $result->as_array();
		}

		try
		{
			$result = Model_Teacher::deleteTeacher($this->aTeacher, $aClasses);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','先生を削除しました。【'.$this->aTeacher['ttName'].'】');
		Response::redirect('/adm/'.$this->bn);
	}

	public function action_lock($sTtID = null)
	{
		$sBack = '/adm/'.$this->bn.'/contract/'.$sTtID;

		$aChk = self::TeacherChecker($sTtID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$result = Model_Contract::getContract(array(array('ttID','=',$sTtID),array('coNO','!=',99)));
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','団体所属先生の停止処理はできません。');
			Response::redirect($sBack);
		}

		if ($this->aTeacher['ttStatus'] == 0)
		{
			$aUpdate = array('ttStatus' => 1);
			$sAct = '稼働';
		}
		else
		{
			$aUpdate = array('ttStatus' => 0);
			$sAct = '停止';
		}


		try
		{
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'],$aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','先生を'.$sAct.'しました。【'.$this->aTeacher['ttName'].'】');
		Response::redirect('/adm/'.$this->bn);
	}

	public function action_sLogin($sTtID = null)
	{
		$sBack = '/adm/'.$this->bn.'/teacher/';

		$aChk = self::TeacherChecker($sTtID);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		Cookie::set("CL_TL_HASH",Crypt::encode(serialize(array('hash'=>sha1($this->aTeacher['ttMail'].$this->aTeacher['ttPass']),'ip'=>Input::real_ip()))));
		Response::redirect('t/index');
	}


	private function TeacherChecker($sTtID = null)
	{
		$back = '/adm/'.$this->bn;
		if (is_null($sTtID))
		{
			return array('msg'=>'先生情報が送信されていません。','url'=>$back);
		}
		$result = Model_Teacher::getTeacherFromID($sTtID);
		if (!count($result))
		{
			return array('msg'=>'指定された先生情報が見つかりません。','url'=>$back);
		}
		$this->aTeacher = $result->current();

		return true;
	}

}