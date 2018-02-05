<?php
class Controller_T_Ajax_Alog extends Controller_T_Ajax
{
	public function post_ThemeSort()
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
			$result = Model_Alog::getAlogThemeFromClass($par['ct'],null,null,array('altSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するテーマの情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aALTheme['altSort'] == $iMax && $par['m'] == 'up') || ($aALTheme['altSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のテーマのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Alog::sortAlogTheme($aALTheme,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_ThemePublic()
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

			if ($par['m'] == 'public')
			{
				$iPub = 1;
				$sText = __('公開中');
				$sClass = 'font-blue';
			}
			elseif ($par['m'] == 'private')
			{
				$iPub = 0;
				$sText = __('非公開');
				$sClass = 'font-default';
			}
			else
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('公開情報が正しく送信されていません。'));
				$this->response($res);
				return;
			}

			$result = Model_Alog::publicAlogTheme($aALTheme,$iPub);

			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>__('公開ステータスを変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_TeachComment()
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

			$result = Model_Alog::getAlog(array(array('al.altID','=',$aALTheme['altID']),array('al.no','=',$par['no'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定された記録が見つかりません。'));
				$this->response($res);
				return;
			}
			$aALog = $result->current();

			try
			{
				$aUpdate = array(
					'alCom'     => $par['com'],
					'alComDate' =>($par['com'] != '')? date('YmdHis'):CL_DATETIME_DEFAULT,
					'alComID'   =>($par['com'] != '')? $this->aTeacher['ttID']:null,
				);
				$result = Model_Alog::updateAlog($aUpdate,array(array('altID','=',$aALog['altID']),array('no','=',$aALog['no'])));
			}
			catch (Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				$res = array('err'=>-3,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}
			$res = array('err'=>0,'res'=>1,'msg'=>__('コメントの更新が完了しました。'));
		}
		$this->response($res);
		return;
	}

}
