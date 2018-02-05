<?php
class Controller_S_Ajax_Test extends Controller_S_Ajax
{

	public function post_LimitTime()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('小テスト情報が正しく送信されていません。'));
		$par = Input::post();

		if ($par)
		{
			$result = Model_Test::getTestBaseFromID($par['tb']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定の小テストが見つかりません。'));
				$this->response($res);
				return;
			}
			$aTest = $result->current();

			$aTimer = Session::get('SES_S_TEST_TIMER_'.$par['tb'],false);
			$aTimer = ($aTimer)? unserialize($aTimer):null;
			if (!isset($aTimer[$par['tb']]))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('小テストの制限時間が取得できません。'));
				$this->response($res);
				return;
			}

			$res = array('err'=>0, 'res'=>array('start'=>($aTimer[$par['tb']]*1000), 'limit'=>($aTest['tbLimitTime']*60*1000), 'server'=>(time()*1000)));
		}
		$this->response($res);
		return;
	}

}
