<?php
class Controller_T_News extends Controller_T_Baseclass
{
	private $bn = 't/news';
	private $aNewsBase = array(
		's_date'=>null,
		's_time'=>null,
		'e_date'=>null,
		'e_time'=>null,
		'n_body'=>null,
		'n_url'=>null,
		'n_url_title'=>null,
		'n_send'=>0,
	);
	private $aNums = null;

	public function before()
	{
		parent::before();
		\Clfunc_Common::ContractDetect($this->aTeacher, __CLASS__);

		$this->template->javascript = array('jquery.timepicker.js','cl.t.news.js');

		$this->aNewsBase['s_date'] = ClFunc_Tz::tz('Y/m/d',$this->tz);
		$this->aNewsBase['s_time'] = ClFunc_Tz::tz('H:i',$this->tz);
		$this->aNewsBase['e_date'] = ClFunc_Tz::tz('Y/m/d',$this->tz,date('Y/m/d H:i:s',strtotime('+1 week')));
		$this->aNewsBase['e_time'] = ClFunc_Tz::tz('H:i',$this->tz);
	}

	public function action_index()
	{
		$aNews = null;
		$result = Model_Class::getNews($this->aClass['ctID'],null,null,array('no' => 'desc'));
		if (count($result))
		{
			foreach ($result as $i => $aN)
			{
				$aNews[$i] = $aN;
				$aNews[$i]['cnChain'] = ($aN['cnURL'])? \Clfunc_Common::ExtUrlDetect($aN['cnURL']):null;
			}
		}

		# タイトル
		$sTitle = __('ニュース');
		$this->template->set_global('pagetitle',$sTitle);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomBtn = array(
			array(
				'url'  => DS.$this->bn.DS.'add/',
				'name' => __('ニュースの追加'),
				'show' => 1,
			),
		);
		if (CL_ENV == 'DEVELOPMENT')
		{
			$aCustomBtn[] = array(
				'url'  => DS.$this->bn.DS.'pushtest/',
				'name' => 'DevPushTest',
				'show' => 1,
			);
		}
		$this->template->set_global('aCustomBtn',$aCustomBtn);

		$this->template->content = View::forge($this->bn.DS.'index');
		$this->template->content->set('aNews',$aNews);
		return $this->template;
	}


	public function action_add()
	{
		if (!$this->aClass['ctStatus'])
		{
			Session::set('SES_T_ERROR_MSG', __('終了した講義にニュースを追加することはできません。'));
			Response::redirect(DS.$this->bn);
		}

		self::getContentsNums();
		$this->template->set_global('aNums',$this->aNums);

		# タイトル
		$sTitle = __('ニュースの追加');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/news','name'=>__('ニュース'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$data = $this->aNewsBase;
			$data['no'] = 0;
			$data['error'] = null;

			$this->template->content = View::forge($this->bn.DS.'edit',$data);
			return $this->template;
		}

		$aInput = Input::post();
		$aInput['n_send'] = (isset($aInput['n_send']))? (int)$aInput['n_send']:0;
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('s_date', __('掲載日時'))
			->add_rule('required')
			->add_rule('date')
			->add_rule('min_date',array(ClFunc_Tz::tz('Y/m/d',$this->tz)));
		$val->add('s_time', __('掲載日時'))
			->add_rule('required')
			->add_rule('time')
			->add_rule('min_time',ClFunc_Tz::tz('Y/m/d H:i',$this->tz),$aInput['s_date']);
		$val->add('e_date', __('終了日時'))
			->add_rule('required')
			->add_rule('date')
			->add_rule('min_date',array($aInput['s_date']));
		$val->add('e_time', __('終了日時'))
			->add_rule('required')
			->add_rule('time')
			->add_rule('min_time',$aInput['s_date'].' '.$aInput['s_time'],$aInput['e_date']);
		$val->add('n_body', __('ニュース内容'))
			->add_rule('required')
			->add_rule('trim');

		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$aInput['no'] = 0;
			$aN = \Clfunc_Common::ExtUrlDetect($aInput['n_url']);
			$aInput['n_url_title'] = $aN['title'];

			$this->template->content = View::forge($this->bn.DS.'edit',$aInput);
			return $this->template;
		}

		// 登録データ生成
		$aInsert = array(
			'ctID'       => $this->aClass['ctID'],
			'cnBody'     => $aInput['n_body'],
			'cnURL'      => $aInput['n_url'],
			'cnStart'    => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aInput['s_date'].' '.$aInput['s_time'].':00',$this->tz),
			'cnEnd'      => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aInput['e_date'].' '.$aInput['e_time'].':00',$this->tz),
			'cnSend'     => $aInput['n_send'],
			'cnDate'     => date('YmdHis'),
		);

		try
		{
			$result = Model_Class::insertNews($aInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('ニュースを登録しました。'));
		Response::redirect(DS.$this->bn);
	}

	public function action_edit($no = null)
	{
		try
		{
			if (is_null($no))
			{
				throw new Exception(__('ニュースが指定されていません。'));
			}
			$this->template->set_global('no',$no);

			$result = Model_Class::getNews($this->aClass['ctID'],array(array('no','=',$no)));
			if (!count($result))
			{
				throw new Exception(__('指定されたニュースが見つかりません。'));
			}
			$aNews = $result->current();
		}
		catch (Exception $e)
		{
			Session::set('SES_T_ERROR_MSG', $e->getMessage());
			Response::redirect(DS.$this->bn);
		}

		self::getContentsNums();
		$this->template->set_global('aNums',$this->aNums);

		# タイトル
		$sTitle = __('ニュースの追加');
		# パンくずリスト生成
		$this->aBread[] = array('link'=>'/news','name'=>__('ニュース'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);
		$this->template->set_global('pagetitle',$sTitle);

		if (!Input::post(null,false))
		{
			$aN = \Clfunc_Common::ExtUrlDetect($aNews['cnURL']);
			$data = array(
				's_date' => ClFunc_Tz::tz('Y/m/d',$this->tz,$aNews['cnStart']),
				's_time' => ClFunc_Tz::tz('H:i',$this->tz,$aNews['cnStart']),
				'e_date' => ClFunc_Tz::tz('Y/m/d',$this->tz,$aNews['cnEnd']),
				'e_time' => ClFunc_Tz::tz('H:i',$this->tz,$aNews['cnEnd']),
				'n_body' => $aNews['cnBody'],
				'n_send' => $aNews['cnSend'],
				'n_url'  => $aNews['cnURL'],
				'n_url_title' => $aN['title'],
				'error' => null,
			);
			$this->template->content = View::forge($this->bn.DS.'edit',$data);
			return $this->template;
		}

		$aInput = Input::post();
		$aInput['n_send'] = (isset($aInput['n_send']))? (int)$aInput['n_send']:0;
		$val = Validation::forge();
		$val->add_callable('Helper_CustomValidation');

		$val->add('s_date', __('掲載日時'))
			->add_rule('required')
			->add_rule('date');
		$val->add('s_time', __('掲載日時'))
			->add_rule('required')
			->add_rule('time');
		$val->add('e_date', __('終了日時'))
			->add_rule('required')
			->add_rule('date')
			->add_rule('min_date',array($aInput['s_date']));
		$val->add('e_time', __('終了日時'))
			->add_rule('required')
			->add_rule('time')
			->add_rule('min_time',$aInput['s_date'].' '.$aInput['s_time'],$aInput['e_date']);
		$val->add('n_body', __('ニュース内容'))
		->add_rule('required')
		->add_rule('trim');

		if (!$val->run())
		{
			$aInput['error'] = $val->error();
			$aN = \Clfunc_Common::ExtUrlDetect($aInput['n_url']);
			$aInput['n_url_title'] = $aN['title'];
			$this->template->content = View::forge($this->bn.DS.'edit',$aInput);
			return $this->template;
		}

		// 登録データ生成
		$aUpdate = array(
			'cnBody'     => $aInput['n_body'],
			'cnURL'      => $aInput['n_url'],
			'cnStart'    => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aInput['s_date'].' '.$aInput['s_time'].':00',$this->tz),
			'cnEnd'      => ClFunc_Tz::tz('Y-m-d H:i:00',null,$aInput['e_date'].' '.$aInput['e_time'].':00',$this->tz),
			'cnSend'     => $aInput['n_send'],
		);

		try
		{
			$result = Model_Class::updateNews($aUpdate,$no);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('ニュースを登録しました。'));
		Response::redirect(DS.$this->bn);
	}

	public function action_delete($no = null)
	{
		try
		{
			if (is_null($no))
			{
				throw new Exception(__('ニュースが指定されていません。'));
			}
			$this->template->set_global('no',$no);

			$result = Model_Class::getNews($this->aClass['ctID'],array(array('no','=',$no)));
			if (!count($result))
			{
				throw new Exception(__('指定されたニュースが見つかりません。'));
			}
			$aNews = $result->current();
		}
		catch (Exception $e)
		{
			Session::set('SES_T_ERROR_MSG', $e->getMessage());
			Response::redirect(DS.$this->bn);
		}

		try
		{
			$result = Model_Class::deleteNews($no);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('ニュースを削除しました。'));
		Response::redirect(DS.$this->bn);
	}

	public function action_finish($no = null)
	{
		try
		{
			if (is_null($no))
			{
				throw new Exception(__('ニュースが指定されていません。'));
			}
			$this->template->set_global('no',$no);

			$result = Model_Class::getNews($this->aClass['ctID'],array(array('no','=',$no)));
			if (!count($result))
			{
				throw new Exception(__('指定されたニュースが見つかりません。'));
			}
			$aNews = $result->current();
		}
		catch (Exception $e)
		{
			Session::set('SES_T_ERROR_MSG', $e->getMessage());
			Response::redirect(DS.$this->bn);
		}

		try
		{
			$time = Clfunc_Common::i5MinFloor(date('H:i'));
			$aUpdate = array(
				'cnEnd' => date('Y-m-d '.$time.':00'),
			);
			$result = Model_Class::updateNews($aUpdate,$no);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_T_NOTICE_MSG',__('ニュースを終了しました。'));
		Response::redirect(DS.$this->bn);
	}

	public function action_pushtest()
	{
		$aDTs = array('Android'=>null, 'Apple'=>null);
		$result = \Model_Student::getStudentFromClass($this->aClass['ctID'], array(array('st.stApp','>',0)));

#		print_r($result->as_array());
#		exit();

		if (count($result))
		{
			foreach ($result as $aS)
			{
				$oUC = new ClFunc_UnreadCount();
				$oUC->setStudent($aS);
				if ($aS['stApp'] == 1 && $aS['stDeviceToken'] != '')
				{
					$aDTs['Apple'][] = array(
						'id' => $aS['stDeviceToken'],
						'badge' => $oUC->getUserCount(),
					);
					usleep(10000);
				}
				else if ($aS['stApp'] == 2 && $aS['stDeviceToken'] != '')
				{
					$aDTs['Android'][] = $aS['stDeviceToken'];
				}
			}
		}

		try
		{
			$res_and = null;
			$res_app = null;
			$res_app2 = null;

			$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/s/class/index/'.$this->aClass['ctID']);
			$sMsg = '【学生側】プッシュ通知テストです。プッシュ通知テストです。
プッシュ通知テストです。プッシュ通知テストです。
プッシュ通知テストです。プッシュ通知テストです。';
			if (!is_null($aDTs['Apple']))
			{
				$res_app = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$this->aClass['ctName'].'] '.$sMsg, $aCustom, 'L');
				$res_app2 = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$this->aClass['ctName'].'] '.$sMsg, $aCustom);
			}
			if (!is_null($aDTs['Android']))
			{
				$res_and = \ClFunc_Apppush::AndroidPush($aDTs['Android'], '['.$this->aClass['ctName'].'] '.$sMsg, $aCustom);
			}
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('Push Fail Student - '.$dump);

			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect(DS.$this->bn);
		}

		$sResS = 'Push Finish Student - RES:APPLE1 = '.print_r($res_app,true).' RES:APPLE2 = '.print_r($res_app2,true).' RES:ANDROID = '.print_r($res_and,true);

		$aDTs = array('Android'=>null, 'Apple'=>null);
		$result = \Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$this->aClass['ctID']),array('tt.ttApp','>',0)));

		if (count($result))
		{
			foreach ($result as $aS)
			{
				if ($aS['ttApp'] == 1 && $aS['ttDeviceToken'] != '')
				{
					$aDTs['Apple'][] = array(
						'id' => $aS['ttDeviceToken'],
						'badge' => 0
					);
				}
				else if ($aS['ttApp'] == 2 && $aS['ttDeviceToken'] != '')
				{
					$aDTs['Android'][] = $aS['ttDeviceToken'];
				}
			}
		}

		try
		{
			$res_and = null;
			$res_app = null;
			$res_app2 = null;

			$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/t/class/index/'.$this->aClass['ctID']);
			$sMsg = '【先生側】プッシュ通知テストです。プッシュ通知テストです。
プッシュ通知テストです。プッシュ通知テストです。
プッシュ通知テストです。プッシュ通知テストです。';
			if (!is_null($aDTs['Apple']))
			{
				$res_app = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$this->aClass['ctName'].'] '.'Enterprise/'.$sMsg, $aCustom, 'T');
				$res_app2 = \ClFunc_Apppush::ApplePush(DOCROOT.'assets/docs/', $aDTs['Apple'], '['.$this->aClass['ctName'].'] '.'Distribution/'.$sMsg, $aCustom, 'TT');
			}
			if (!is_null($aDTs['Android']))
			{
				$res_and = \ClFunc_Apppush::AndroidPush($aDTs['Android'], '['.$this->aClass['ctName'].'] '.$sMsg, $aCustom, 'T');
			}
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('Push Fail Teacher - '.$dump);

			Session::set('SES_T_ERROR_MSG',$e->getMessage());
			Response::redirect(DS.$this->bn);
		}

		$sResT = 'Push Finish Teacher - RES:APPLE1 = '.print_r($res_app,true).' RES:APPLE2 = '.print_r($res_app2,true).' RES:ANDROID = '.print_r($res_and,true);

		Session::set('SES_T_NOTICE_MSG',$sResS."\n".$sResT);
		Response::redirect(DS.$this->bn);
	}

	private function getContentsNums()
	{
		$this->aNums = array('Quest'=>0,'Test'=>0,'Mat'=>0,'Coop'=>0,'Report'=>0);

		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID']);
		$this->aNums['Quest'] = count($result);
		$result = Model_Test::getTestBaseFromClass($this->aClass['ctID']);
		$this->aNums['Test'] = count($result);
		$result = Model_Material::getMaterialCategoryFromClass($this->aClass['ctID'],array(array('mcNum','>',0)));
		$this->aNums['Mat'] = count($result);
		$result = Model_Coop::getCoopCategoryFromClass($this->aClass['ctID'],array(array('ccItemNum','>',0)));
		$this->aNums['Coop'] = count($result);
		$result = Model_Report::getReportBase(array(array('rb.ctID','=',$this->aClass['ctID'])));
		$this->aNums['Report'] = count($result);

		return;
	}


}