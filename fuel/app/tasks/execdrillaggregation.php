<?php
namespace Fuel\Tasks;

class Execdrillaggregation
{
	public static function run($sID = null, $sTtID = null)
	{
		if (is_null($sID))
		{
			\Log::error('ドリルカテゴリが指定されていません。'.$sID);
			exit();
		}

		$result = \Model_Drill::getDrillCategoryFromID($sID);
		if (!count($result))
		{
			\Log::error('ドリルカテゴリが見つかりません。');
			exit();
		}
		$aDC = $result->current();

		$result = \Model_Drill::getDrill(array(array('dcID','=',$sID)));
		if (!count($result))
		{
			\Log::error('ドリルが見つかりません。');
			exit();
		}
		$aDrill = $result->as_array();

		$aQuery = null;
		$result = \Model_Drill::getDrillQuery(array(array('dcID','=',$sID)));
		if (!count($result))
		{
			\Log::error('ドリル問題が見つかりません。');
			exit();
		}
		foreach ($result as $aQ)
		{
			$aQuery[$aQ['dbNO'].'-'.$aQ['dqNO']] = (int)$aQ['dgNO'];
		}

		try
		{
			$aUpdate = array(
				'dcAnalysisProgress' => 1,
			);
			$result = \Model_Drill::updateDrillCategory($aUpdate,array(array('dcID','=',$sID)));
		}
		catch (\Exception $e)
		{
			\Log::error('カテゴリの更新に失敗しました。');
			exit();
		}

		sleep(5);

		try
		{
			foreach ($aDrill as $aD)
			{
				$result = \Model_Drill::setDrillQueryAnalysis($aD);
			}

			$result = \Model_Drill::getDrillQueryAnalysis(array(array('dcID','=',$sID)));
			if (!count($result))
			{
				throw new \Exception('問題集計情報が取得できませんでした。');
			}

			$aUpdate = null;
			foreach ($result as $aA)
			{
				if (!isset($aQuery[$aA['dbNO'].'-'.$aA['dqNO']]))
				{
					continue;
				}

				$iDgNO = (int)$aQuery[$aA['dbNO'].'-'.$aA['dqNO']];

				if (isset($aUpdate[$iDgNO]))
				{
					$aUpdate[$iDgNO]['dgANum'] += (int)$aA['dqaANum'];
					$aUpdate[$iDgNO]['dgRNum'] += (int)$aA['dqaRNum'];
				}
				else
				{
					$aUpdate[$iDgNO] = array(
						'dgANum' => (int)$aA['dqaANum'],
						'dgRNum' => (int)$aA['dqaRNum'],
					);
				}
			}


			try
			{
				$result = \Model_Drill::updateDrillQueryGroupAnalysis($aUpdate,$sID);
				$aUpdate = array(
					'dcAnalysisDate' => date('YmdHis'),
					'dcAnalysisProgress' => 0,
				);
				$result = \Model_Drill::updateDrillCategory($aUpdate,array(array('dcID','=',$sID)));
			}
			catch (\Exception $e)
			{
				\Clfunc_Common::LogOut($e,__CLASS__);
				throw new \Exception('集計情報の更新に失敗しました。'.$e->getMessage());
			}

		}
		catch (\Exception $e)
		{
			$aUpdate = array(
				'dcAnalysisProgress' => 2,
			);
			$result = \Model_Drill::updateDrillCategory($aUpdate,array(array('dcID','=',$sID)));

			\Log::error($e->getMessage());
			exit();
		}

	}
}