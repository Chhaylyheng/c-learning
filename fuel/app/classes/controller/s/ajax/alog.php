<?php
class Controller_S_Ajax_Alog extends Controller_S_Ajax
{
	public function post_getGoal()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Alog::getAlogThemeFromID($par['alt']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aALTheme = $result->current();

			$sGoal = '';
			$result = Model_Alog::getAlogGoal(array(array('altID','=',$aALTheme['altID']),array('stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aG = $result->current();
				$sGoal = $aG['algText'];
			}
			$res = array('err'=>0,'res'=>
				array(
					'theme'=>$aALTheme['altName'],
					'goal_label'=>$aALTheme['altGoalLabel'],
					'goal_desc'=>$aALTheme['altGoalDescription'],
					'goal'=>$sGoal,
				)
			);
		}
		$this->response($res);
		return;
	}

	public function post_setGoal()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Alog::getAlogThemeFromID($par['alt']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたテーマが見つかりません。'));
				$this->response($res);
				return;
			}
			$aALTheme = $result->current();

			$aWhere = array(
				array('altID','=',$aALTheme['altID']),
				array('stID','=',$this->aStudent['stID']),
			);


			$aG = null;
			$result = Model_Alog::getAlogGoal($aWhere);
			if (count($result))
			{
				$aG = $result->current();
			}

			try
			{
				if (!is_null($aG))
				{
					$aUpdate = array(
						'algText' => $par['goal'],
						'algDate' => date('YmdHis'),
					);
					$result = Model_Alog::updateAlogGoal($aUpdate,$aWhere);
				}
				else
				{
					$aInsert = array(
						'altID'   => $aALTheme['altID'],
						'stID'    => $this->aStudent['stID'],
						'algText' => $par['goal'],
						'algDate' => date('YmdHis'),
					);
					$result = Model_Alog::insertAlogGoal($aInsert);
				}
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-2,'res'=>'','msg'=>__('処理に失敗しました。時間をおいてから、再度実行してください。').'('.$e->getMessage().')');
				$this->response($res);
				return;
			}

			$res = array('err'=>0,'res'=>'','msg'=>__('目標を更新しました。'));
		}
		$this->response($res);
		return;
	}
}

