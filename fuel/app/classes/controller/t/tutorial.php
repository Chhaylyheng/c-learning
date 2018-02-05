<?php
class Controller_T_Tutorial extends Controller_T_Baseclass
{
	public function before() {
		parent::before();

		$this->template->set_global('pagetitle',__('クイックツアー'));
	}

	public function action_suspend()
	{
		try
		{
			$aUpdate = array(
				'ttStatus' => 1,
			);
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Response::redirect('/t/class/index/'.$this->aClass['ctID']);
	}

	public function action_Finish()
	{
		if (Input::post('back'))
		{
			Response::redirect('/t/tutorial/questbent');
		}

		try
		{
			$aUpdate = array(
				'ttStatus' => 1,
				'ttProgress' => 4,
			);
			$result = Model_Teacher::updateTeacher($this->aTeacher['ttID'], $aUpdate);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}
		Response::redirect('/t/index');
	}

	public function action_questbent()
	{
		global $gaQuickTitle;

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
		$this->template->set_global('aClass',$this->aClass);

		$iMode = 23;
		$sDate = date('Y-m-d H:i:s');

		$aQuest = array(
			'qbID' => 'QuickDEMO',
			'ctID' => $sCtID,
			'qbTitle' => __($gaQuickTitle[$iMode]),
			'qbQuickMode' => $iMode,
			'qbPublic' => 1,
			'qbPublicDate' => $sDate,
			'qbQueryStyle' => 2,
			'qbNum' => 2,
			'qpNum' => 3,
			'scNum' => 0,
			'qpGNum' => 0,
			'qpTNum' => 0,
			'qbOpen' => 0,
			'qbComment' => '',
		);
		$aQuery = array(
			'qq1' => array(
				'qqNO' => 1,
				'qqSort' => 1,
				'qqChoiceNum' => 2,
				'qqDate' => $sDate,
				'qqText' => __('質問に対しての回答を選択してください。'),
				'qqChoice1' => __('はい'),
				'qqChoice2' => __('いいえ'),
			),
			'qq2' => array(
				'qqNO' => 2,
				'qqSort' => 2,
				'qqStyle' => 2,
				'qqChoiceNum' => 0,
				'qqDate' => $sDate,
				'qqText' => __('選択した理由を記入してください。'),
			),
		);

		$aBent = array(
			'ALL' => array(
				'qq1' => array(
					'1' => array(
						'qbNum' => 0,
						'qbAll' => 0,
					),
					'2' => array(
						'qbNum' => 0,
						'qbAll' => 0,
					),
				),
			),
		);
		$aComment = array(
			'ALL' => array(),
		);

		$iEn = ($this->sLang == 'en')? 1:0;
		$aNames = array(
			array('CL 花子','CL demo user1'),
			array('CL 太郎','CL demo user2'),
			array('CL 次郎','CL demo user3'),
		);
		$aTexts = array(
			array('私は○○の理由から違うと思います。','I think that it is different from the reason of *****.'),
			array('共感できたので賛成です。','I agreed because I was able to sympathize.'),
			array('悩みましたがやっぱり違うと思いました。','I was worried but I thought it was different.'),
		);

		for ($i = 0; $i < 3; $i++)
		{
			$iRnd = 2 - ($i % 2);

			$aBent['ALL']['qq1'][$iRnd]['qbNum']++;

			$aComment['ALL']['D'.$i] = array(
				'text'    => $aTexts[$i][$iEn],
				'cName'   => ($iRnd == 1)? __('はい'):__('いいえ'),
				'cNO'     => $iRnd,
				'cPick'   => 0,
				'cPosted' => $aNames[$i][$iEn],
			);

		}

		$aBent['ALL']['qq1'][1]['qbAll'] = 3;
		$aBent['ALL']['qq1'][2]['qbAll'] = 3;

		# タイトル
		$sTitle = '[Q]'.$aQuest['qbTitle'];

		$view = View::forge('template');
		$view->content = View::forge('t/tutorial/bent');
		$view->content->set('sTitle',$sTitle);
		$view->content->set('aQuest',$aQuest);
		$view->content->set('aQuery',$aQuery);
		$view->content->set('aBent',$aBent);
		$view->content->set('aComment',$aComment);
		$view->javascript = array('Chart.js','cl.t.quest.js','cl.t.kreport.js','cl.tutorial.js');
		$view->footer = View::forge('t/footer');
		return $view;
	}

	public function action_start()
	{
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
		$this->template->set_global('aClass',$this->aClass);

		$view = View::forge('template');
		$view->content = View::forge('t/tutorial/start');
		$view->footer = View::forge('t/footer');
		return $view;
	}


}
