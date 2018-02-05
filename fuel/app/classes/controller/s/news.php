<?php
class Controller_S_News extends Controller_S_Baseclass
{
	private $aNews = null;

	public function action_index()
	{
		Response::redirect('index/404','location',404);
	}

	public function action_detail($no = null)
	{
		$aChk = self::NewsChecker($no,$this->aClass);
		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		# タイトル
		$sTitle = __('講義ニュース');
		$this->template->set_global('pagetitle',$sTitle,false);

		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.'class/news');
		$this->template->content->set('aNews',$this->aNews);
		return $this->template;
	}

	private function NewsChecker($no = null)
	{
		if (is_null($no))
		{
			return array('msg'=>__('ニュース情報が送信されていません。'),'url'=>'/s/class/index/'.$this->aClass['ctID'].$this->sesParam);
		}
		$sNow = date('Y-m-d H:i:s');
		$result = Model_Class::getNews($this->aClass['ctID'],array(array('no','=',$no),array('cnStart','<=',$sNow),array('cnEnd','>=',$sNow)));
		if (!count($result))
		{
			return array('msg'=>__('指定されたニュースが見つかりません。'),'url'=>'/s/class/index/'.$this->aClass['ctID'].$this->sesParam);
		}
		$this->aNews = $result->current();

		$this->aNews['cnChain'] = ($this->aNews['cnURL'])? \Clfunc_Common::ExtUrlDetectForStudent($this->aNews['cnURL'], $this->aStudent['stID']):null;

		return true;
	}
}