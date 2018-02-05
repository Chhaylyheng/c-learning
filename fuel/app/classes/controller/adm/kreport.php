<?php
class Controller_Adm_Kreport extends Controller_Adm_Base
{
	private $aSearchCol = array(
		'ttMail','ttName','ttDept','ttSubject','cmName'
	);

	public function action_index()
	{
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'ケータイ研レポート')));
		# ページタイトル生成
		$this->template->set_global('pagetitle','ケータイ研レポート');

		$aReport = null;
		$result = Model_KReport::getKReportBase(null,null,array('no'=>'desc'));
		if (count($result))
		{
			$aReport = $result->as_array();
		}

		$aSes = Session::get(null,false);
		$this->template->content = View::forge('adm/kreport/index');
		$this->template->content->set('ses',$aSes);
		$this->template->content->set_safe('error_msg',(isset($aSes['SES_ADM_ERROR_MSG']))? $aSes['SES_ADM_ERROR_MSG']:null);
		$this->template->content->set('aReport',$aReport);
		$this->template->javascript = array('cl.adm.kreport.js');
		return $this->template;
	}

	public function action_create($iY = null, $iP = null)
	{
		$aReport = null;
		$result = Model_KReport::getKReportBase(array(array('krYear','=',$iY),array('krPeriod','=',$iP)));
		if (count($result))
		{
			Session::set('SES_ADM_NOTICE_MSG','既に作成済みです');
			Response::redirect('/adm/kreport');
		}

		$aInput = array('krYear'=>$iY,'krPeriod'=>$iP);
		# 開始日と終了日を生成
		$aInput['krAutoPublicDate'] = date('Ymd000000',strtotime(($iP == 1)? $iY.'-09-01':($iY+1).'-03-01'));
		$aInput['krAutoCloseDate']  = date('Ymd235959',strtotime(($iP == 1)? $iY.'-10-31':($iY+1).'-04-30'));

		$aQInsert = array(
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 1,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'大学名'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 2,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'学部、学科、コース等'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 3,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'教員名'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 4,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'科目名、講義名'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 5,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'実施日時（曜日、時限等）'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 6,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'学年、対象クラス'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 7,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'場所'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 8,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'受講人数'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=> 9,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'公開シラバスURL'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>10,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'講義のねらい、目的'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>11,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'ICT活用により期待できる効果、ICT活用のねらい'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>12,'krStyle'=>1,'krDate'=>date('YmdHis'),'krText'=>'利用機能',
				'krChoiceNum'=>13,
				'krChoice1'=>'出席',
				'krChoice2'=>'アンケート',
				'krChoice3'=>'小テスト',
				'krChoice4'=>'ドリル',
				'krChoice5'=>'協働板',
				'krChoice6'=>'教材倉庫',
				'krChoice7'=>'連絡・相談',
				'krChoice8'=>'レポート',
				'krChoice9'=>'ニュース',
				'krChoice10'=>'利用なし',
			),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>13,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'その他ICT利用機能'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>14,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'講義の展開・デザイン（全体の流れとICT運用の流れ、消費時間数、道具）'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>15,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'学習成果、考察、所感'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>16,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'ICT活用の更なる発展への提言'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>17,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'見学可能講義情報（日時、講義名、場所など）'),
			array('krYear'=>$iY,'krPeriod'=>$iP,'krNO'=>18,'krStyle'=>2,'krDate'=>date('YmdHis'),'krText'=>'問い合わせ先（氏名、メールアドレス、関連WEBサイトなど）'),
		);

		$aInput['krNum'] = count($aQInsert);
		$aInput['krDate'] = date('YmdHis');

		try
		{
			$result = Model_KReport::insertKReport($aInput,$aQInsert);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','レポート受付情報を作成しました。');
		Response::redirect('/adm/kreport');
	}

	public function action_target($iNO = null)
	{
		$aReport = null;
		$aTeachers = null;
		$aTarget = null;

		$aChk = self::ReportChecker($iNO,$aReport);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sName = $aReport['krYear'].'年度 '.(($aReport['krPeriod'] == 1)? '4～9月期':'10～3月期');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'ケータイ研レポート','link'=>'/kreport'),array('name'=>$sName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle','回答対象者の変更（'.$sName.'）');

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
		$aSearchForm = array(
				'url' => '/adm/kreport/target/'.$iNO,
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);
		$this->template->javascript = array('cl.adm.kreport.js');

		$result = Model_KReport::getKReportTarget(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aT) {
				$aTarget[$aT['ttID']] = true;
			}
		}

		if (!Input::post(null,false))
		{
			$aSes = Session::get(null,false);
			$this->template->content = View::forge('adm/kreport/target');
			$this->template->content->set('ses',$aSes);
			$this->template->content->set_safe('error_msg',(isset($aSes['SES_ADM_ERROR_MSG']))? $aSes['SES_ADM_ERROR_MSG']:null);
			$this->template->content->set('aReport',$aReport);
			$this->template->content->set('aTeachers',$aTeachers);
			$this->template->content->set('aTarget',$aTarget);
			return $this->template;
		}

		$aInput = Input::post();
		$aInsert = null;
		if (count($aInput['chkT']))
		{
			foreach ($aInput['chkT'] as $sTtID)
			{
				$aInsert[] = array(
					'krYear'=>$aReport['krYear'],
					'krPeriod'=>$aReport['krPeriod'],
					'ttID'=>$sTtID,
					'krDate'=>date('YmdHis'),
				);
			}
		}
		else
		{
			Session::set('SES_ADM_NOTICE_MSG','回答対象者が選択されていません。');
			Response::redirect('/adm/kreport/targert/'.$iNO);
		}

		$aReport['krSetNum'] = count($aInsert);

		try
		{
			$result = Model_KReport::updateKReportTarget($aInsert,$aReport);
		}
		catch (Exception $e)
		{
			\Clfunc_Common::LogOut($e,__CLASS__);
			Session::set('SES_ADM_ERROR_MSG',$e->getMessage());
			Response::redirect($this->eRedirect);
		}

		Session::set('SES_ADM_NOTICE_MSG','回答対象者を変更しました。');
		Response::redirect('/adm/kreport');
	}

	public function action_preview($iNO = null)
	{
		$aReport = null;
		$aQuery = null;
		$aChk = self::QueryChecker($iNO,$aReport,$aQuery);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$iYear = $aReport['krYear'];
		$iPeriod = $aReport['krPeriod'];

		$sKrID = $iYear.'-'.$iPeriod;

		$sRName = 'ケータイ研レポート（'.$iYear.'年度 '.(($iPeriod == 1)? '4～9月期':'10～3月期').'）';
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'ケータイ研レポート','link'=>'/kreport'),array('name'=>$sRName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sRName);

		$aMsg = null;
		if (Input::post(null,false))
		{
			$aMsg['error'] = 'プレビューのため回答はできません。';
		}

		$this->template->content = View::forge('adm/kreport/preview');
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aMsg',$aMsg);
		$this->template->javascript = array('cl.t.kreport.js');
		return $this->template;
	}

	public function action_put($iNO = null)
	{
		$aReport = null;
		$aTeachers = null;

		$aChk = self::ReportChecker($iNO,$aReport);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$sName = $aReport['krYear'].'年度 '.(($aReport['krPeriod'] == 1)? '4～9月期':'10～3月期');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'ケータイ研レポート','link'=>'/kreport'),array('name'=>$sName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle','提出状況（'.$sName.'）');

		$result = Model_KReport::getKReportTarget(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])),null,array('ttName'=>'asc'));
		if (count($result))
		{
			$aTs = $result->as_array('ttID');
		}

		$result = Model_KReport::getKReportPut(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])),null,array('krDate'=>'desc'));
		if (count($result))
		{
			foreach ($result->as_array() as $put)
			{
				if (isset($aTs[$put['ttID']]))
				{
					$aTeachers[$put['ttID']]['put'][] = $put;
					$aTeachers[$put['ttID']]['teach'] = $aTs[$put['ttID']];
					unset($aTs[$put['ttID']]);
				}
				else if (isset($aTeachers[$put['ttID']]))
				{
					$aTeachers[$put['ttID']]['put'][] = $put;
				}
			}
		}
		if (count($aTs))
		{
			foreach ($aTs as $sTtID => $aT)
			{
				$aTeachers[$sTtID]['put'] = null;
				$aTeachers[$sTtID]['teach'] = $aT;
			}
		}

		$this->template->content = View::forge('adm/kreport/put');
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aTeachers',$aTeachers);
		$this->template->javascript = array('cl.t.kreport.js');
		return $this->template;
	}


	public function action_ansdetail($iNO = null, $sTtID = null, $iSub = null)
	{
		$back = '/adm/kreport/put/'.$iNO;

		$aReport = null;
		$aQuery = null;
		$aPTeacher = null;
		$aAns = null;

		$aChk = self::QueryChecker($iNO,$aReport,$aQuery);
		if (is_array($aChk))
		{
			Session::set('SES_ADM_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$result = Model_Teacher::getTeacherFromID($sTtID);
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','指定の先生が見つかりませんでした。');
			Response::redirect($back);
		}
		$aPTeacher = $result->current();

		$result = Model_KReport::getKReportPut(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('ttID','=',$sTtID),array('krSub','=',$iSub)));
		if (!count($result))
		{
			Session::set('SES_T_ERROR_MSG','指定の先生は未回答です。');
			Response::redirect($back);
		}
		$aPut = $result->current();

		$result = Model_KReport::getKReportAns(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod']),array('ttID','=',$sTtID),array('krSub','=',$iSub)));
		if (!count($result))
		{
			Session::set('SES_ADM_ERROR_MSG','指定の先生は未回答です。');
			Response::redirect($back);
		}
		$aTemp = $result->as_array();
		foreach ($aTemp as $aA)
		{
			$aAns[$aA['krNO']] = $aA;
		}

		$sTName = (($aPTeacher['ttName'])? $aPTeacher['ttName']:$aPTeacher['ttMail']).'さんのレポート'.$iSub;
		$sName = $aReport['krYear'].'年度 '.(($aReport['krPeriod'] == 1)? '4～9月期':'10～3月期');
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'ケータイ研レポート','link'=>'/kreport'),array('name'=>$sName,'link'=>'/kreport/put/'.$iNO),array('name'=>$sTName)));
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTName.'（'.date('Y/m/d H:i',strtotime($aAns[1]['krDate'])).'提出）');

		$aSes = Session::get(null,false);
		$this->template->content = View::forge('adm/kreport/ansdetail');
		$this->template->content->set('aReport',$aReport);
		$this->template->content->set('aQuery',$aQuery);
		$this->template->content->set('aPTeacher',$aPTeacher);
		$this->template->content->set('aPut',$aPut);
		$this->template->content->set('aAns',$aAns);
		$this->template->javascript = array('cl.adm.kreport.js');
		$this->template->javascript = array('cl.t.kreport.js');
		return $this->template;
	}

	private function ReportChecker($iNO = null,&$aReport = null)
	{
		$back = '/adm/kreport';
		if (is_null($iNO))
		{
			return array('msg'=>'レポート受付情報が送信されていません。','url'=>$back);
		}
		$result = Model_KReport::getKReportBase(array(array('no','=',$iNO)));
		if (!count($result))
		{
			return array('msg'=>'指定されたレポート受付情報が見つかりません。','url'=>$back);
		}
		$aReport = $result->current();
		return true;
	}

	private function QueryChecker($iNO = null,&$aReport = null,&$aQuery)
	{
		$back = '/adm/kreport';
		if (is_null($iNO))
		{
			return array('msg'=>'レポート受付情報が送信されていません。','url'=>$back);
		}
		$result = Model_KReport::getKReportBase(array(array('no','=',$iNO)));
		if (!count($result))
		{
			return array('msg'=>'指定されたレポート受付情報が見つかりません。','url'=>$back);
		}
		$aReport = $result->current();

		$result = Model_KReport::getKReportQuery(array(array('krYear','=',$aReport['krYear']),array('krPeriod','=',$aReport['krPeriod'])));
		if (!count($result))
		{
			return array('msg'=>'指定されたレポートの設問が見つかりません。','url'=>$back);
		}
		$aQuery = $result->as_array();

		return true;
	}













}