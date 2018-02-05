<?php
class Controller_T_Ajax_Drill extends Controller_T_Ajax
{
	public function post_CateSort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillCategoryFromID($par['dc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDCategory = $result->current();
			$result = Model_Drill::getDrillCategoryFromClass($par['ct'],null,null,array('dcSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するカテゴリの情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aDCategory['dcSort'] == $iMax && $par['m'] == 'up') || ($aDCategory['dcSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のカテゴリのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Drill::sortDrillCategory($aDCategory,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_GroupUpdate()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillCategoryFromID($par['dc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDCategory = $result->current();

			$aWhere = array(array('dcID','=',$par['dc']),array('dgNO','=',$par['dg']));

			$aDQGroup = null;
			$bUp = false;
			$result = Model_Drill::getDrillQueryGroup($aWhere);
			if (count($result))
			{
				$bUp = true;
				$aDQGroup = $result->current();
			}

			try
			{
				if ($bUp)
				{
					$aUpdate = array(
						'dgName' => $par['dgname'],
					);
					$result = Model_Drill::updateDrillQueryGroup($aUpdate,$aWhere);
					$aResult = array('obj'=>$par['dc'].'_'.$par['dg'], 'no'=> $par['dc'], 'insert'=>0);
				}
				else
				{
					$aInsert = array(
						'dcID' => $par['dc'],
						'dgNO' => $par['dg'],
						'dgName' => $par['dgname'],
					);
					$result = Model_Drill::insertDrillQueryGroup($aInsert);
					$aResult = array('obj'=>$par['dc'].'_'.$result, 'no'=> $result, 'insert'=>1);
				}
			}
			catch (Exception $e)
			{
				$res = array('err'=>-1,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}

			$res = array('err'=>0,'res'=>$aResult, 'msg'=>__('グループ名を更新しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_GroupDelete()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillCategoryFromID($par['dc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDCategory = $result->current();

			$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$par['dc']),array('dgNO','=',$par['dg'])));
			if (!count($result))
			{
				$res = array('err'=>0, 'res'=>'','msg'=>__('グループを削除しました。'));
				$this->response($res);
				return;
			}
			$aDQGroup = $result->current();

			if ($aDQGroup['dgNO'] == 0)
			{
				$res = array('err'=>-3,'res'=>'','msg'=>__('先頭のグループは削除できません。'));
				$this->response($res);
				return;
			}
			if ($aDQGroup['dgQNum'] > 0)
			{
				$res = array('err'=>-3,'res'=>'','msg'=>__('問題が存在するグループは削除できません。'));
				$this->response($res);
				return;
			}

			try
			{
				$result = Model_Drill::deleteDrillQueryGroup($aDQGroup);
			}
			catch (Exception $e)
			{
				$res = array('err'=>-1,'res'=>'','msg'=>$e->getMessage());
				$this->response($res);
				return;
			}

			$res = array('err'=>0,'res'=>'','msg'=>__('グループを削除しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_GroupSort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$par['dc']),array('dgNO','=',$par['dg'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたグループが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDQGroup = $result->current();

			$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$par['dc'])),null,array('dgSort'=>'asc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するグループの情報が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result) - 1;
			if (($aDQGroup['dgSort'] == 1 && $par['m'] == 'up') || ($aDQGroup['dgSort'] == $iMax && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のグループのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Drill::sortDrillQueryGroup($aDQGroup,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_Sort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = \Model_Drill::getDrill(array(array('dcID','=',$par['dc']),array('dbNO','=',$par['db'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたドリルが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDrill = $result->current();
			$result = Model_Drill::getDrill(array(array('dcID','=',$par['dc'])),null,array('dbSort'=>'desc'));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するドリルが見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aDrill['dbSort'] == $iMax && $par['m'] == 'up') || ($aDrill['dbSort'] == 1 && $par['m'] == 'down'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下のドリルのため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Drill::sortDrill($aDrill,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m']));
		}
		$this->response($res);
		return;
	}

	public function post_Public()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillCategoryFromID($par['dc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDCategory = $result->current();

			$result = Model_Drill::getDrill(array(array('dcID','=',$par['dc']),array('dbNO','=',$par['db'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたドリルが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDrill = $result->current();

			if ($aDrill['dbPublicNum'] > $aDrill['dbQueryNum'])
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('登録問題数が出題数に満たないため、公開情報を変更することはできません。'));
				$this->response($res);
				return;
			}

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

			$result = Model_Drill::publicDrill($aDrill,$iPub);

			$res = array('err'=>0,'res'=>array('class'=>$sClass,'text'=>$sText),'msg'=>__('ドリル公開ステータスを変更しました。'));
		}
		$this->response($res);
		return;
	}

	public function post_QueryGroup()
	{
		$res = array();
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$par['dc']),array('dgName','LIKE','%'.$par['gname'].'%')));
			foreach ($result as $r)
			{
				$res[] = $r['dgName'];
			}
		}
		$this->response($res);
		return;
	}

	public function post_QueryLoad()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillQuery(array(array('dcID','=',$par['dc']),array('dbNO','=',$par['db']),array('dqNO','=',$par['qn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたドリル問題が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuery = $result->current();
			$result = Model_Drill::getDrillQueryGroup(array(array('dcID','=',$par['dc']),array('dgNO','=',$aQuery['dgNO'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたドリル問題が見つかりません。'));
				$this->response($res);
				return;
			}
			$aDGroup = $result->current();
			$aQuery['dgName'] = $aDGroup['dgName'];

			$sDbPath = DS.$aQuery['dcID'].DS.$aQuery['dbNO'].DS;
			$sTempPath = CL_UPPATH.$sDbPath.$aQuery['dqSort'].'_tmp';
			$sQueryPath = CL_UPPATH.$sDbPath.$aQuery['dqNO'];
			if (file_exists($sTempPath))
			{
				system('rm -rf '.$sTempPath);
			}
			if (file_exists($sQueryPath))
			{
				system('cp -Rfp '.$sQueryPath.' '.$sTempPath);
			}

			$res = array('err'=>0,'res'=>$aQuery,'msg'=>'', 'path'=>DS.CL_UPDIR.$sDbPath.$aQuery['dqSort'].'_tmp'.DS);
		}
		$this->response($res);
		return;
	}

	public function post_QueryImageDelete()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$sDbPath = DS.$par['dc'].DS.$par['db'].DS;
			$sTempPath = CL_UPPATH.$sDbPath.$par['qs'].'_tmp'.DS;
			list($fn,$ex) = explode('.', $par['fn']);

			$result = Model_Drill::getDrillQuery(array(array('dcID','=',$par['dc']),array('dbNO','=',$par['db']),array('dqSort','=',$par['qs'])));
			if (count($result))
			{
				$aQuery = $result->current();

				$sImgPath = CL_UPPATH.$sDbPath.$aQuery['dqNO'].DS;

				if ($fn == 'base')
				{
					$aUpdate = array('dqImage'=>'');
				}
				else if ($fn == 'explain')
				{
					$aUpdate = array('dqExplainImage'=>'');
				}
				else
				{
					$aUpdate = array('dqChoiceImg'.$fn=>'');
				}
				$result = Model_Drill::updateDrillQuery($aUpdate,array(array('dcID','=',$aQuery['dcID']),array('dbNO','=',$aQuery['dbNO']),array('dqNO','=',$aQuery['dqNO'])));
				if (file_exists($sImgPath.$par['fn']))
				{
					File::delete($sImgPath.$par['fn']);
					File::delete($sImgPath.CL_Q_SMALL_PREFIX.$par['fn']);
				}
			}
			if (file_exists($sTempPath.$par['fn']))
			{
				File::delete($sTempPath.$par['fn']);
				File::delete($sTempPath.CL_Q_SMALL_PREFIX.$par['fn']);
			}
			$res = array('err'=>0,'res'=>array('del'=>$fn), 'msg'=>'');
		}
		$this->response($res);
		return;
	}

	public function post_QuerySort()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillQuery(array(array('dcID','=',$par['dc']),array('dbNO','=',$par['db']),array('dqNO','=',$par['qn'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたドリル問題が見つかりません。'));
				$this->response($res);
				return;
			}
			$aQuery = $result->current();

			$result = Model_Drill::getDrillQuery(array(array('dcID','=',$par['dc']),array('dbNO','=',$par['db'])));
			if (!count($result))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('並び順を変更するドリルの問題が見つかりません。'));
				$this->response($res);
				return;
			}
			$iMax = count($result);
			if (($aQuery['dqSort'] == $iMax && $par['m'] == 'down') || ($aQuery['dqSort'] == 1 && $par['m'] == 'up'))
			{
				$res = array('err'=>-1,'res'=>'','msg'=>__('一番上か一番下の問題のため、並び順を変更することはできません。'));
				$this->response($res);
				return;
			}
			$result = Model_Drill::sortDrillQuery($aQuery,$par['m']);
			$res = array('err'=>0,'res'=>array('m'=>$par['m'],'qs'=>$aQuery['dqSort']));
		}
		$this->response($res);
		return;
	}

	public function post_PutReset()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillCategoryFromID($par['dc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDCategory = $result->current();

			$result = Model_Drill::getDrill(array(array('dcID','=',$par['dc']),array('dbNO','=',$par['db'])));
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたドリルが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDrill = $result->current();

			$result = Model_Drill::deleteDrillPut($aDrill);
			$res = array('err'=>0,'res'=>'','msg'=>__('提出状況をリセットしました。'));
		}
		$this->response($res);
		return;
	}

	public function post_AggregationState()
	{
		$res = array('err'=>-3,'res'=>'','msg'=>__('情報が正しく送信されていません。'));
		$par = Input::post();
		if ($par)
		{
			$result = Model_Drill::getDrillCategoryFromID($par['dc']);
			if (!count($result))
			{
				$res = array('err'=>-2,'res'=>'','msg'=>__('指定されたカテゴリが見つかりません。'));
				$this->response($res);
				return;
			}
			$aDCategory = $result->current();
			$res = array('err'=>0, 'res'=>(int)$aDCategory['dcAnalysisProgress'], 'msg'=>'');
		}
		$this->response($res);
		return;
	}


}
