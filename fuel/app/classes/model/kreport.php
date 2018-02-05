<?php
class Model_KReport extends \Model
{
	public static function getKReportBase($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('KtaiReportBase_Table');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}



	public static function getKReportTarget($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('KtaiReportTarget_View');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}



	public static function getKReportQuery($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('KtaiReportQuery_Table');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}



	public static function getKReportAns($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('KtaiReportAns_View');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}



	public static function getKReportPut($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('KtaiReportPut_Table');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}



	public static function insertKReport($aInput = null, $aQInsert = null)
	{
		if (is_null($aInput) || is_null($aQInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('KtaiReportBase_Table')->set($aInput)->execute();
			foreach ($aQInsert as $aI) {
				$result = DB::insert('KtaiReportQuery_Table')->set($aI)->execute();
			}
			DB::commit_transaction();
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}



	public static function updateKReportTarget($aInsert,$aReport)
	{
		if (is_null($aInsert) || is_null($aReport))
		{
			throw new Exception('処理に必要な情報がありません');
		}
			try
		{
			DB::start_transaction();

			$result = DB::delete('KtaiReportTarget_Table')->where('krYear',$aReport['krYear'])->and_where('krPeriod',$aReport['krPeriod'])->execute();

			foreach ($aInsert as $aI) {
				$result = DB::insert('KtaiReportTarget_Table')->set($aI)->execute();
			}
			$query = DB::update('KtaiReportBase_Table');
			$query->and_where('no','=',$aReport['no']);
			$query->set(array('krSetNum'=>(int)$aReport['krSetNum']));
			$result = $query->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}



	public static function setKReportPut($aReport = null,$aQuery = null,$aTeacher = null,$iSub = null,$aInput = null,$aUploads = null,$iStatus = null,$bUpdate = false)
	{
		if (is_null($aReport) || is_null($aTeacher) || is_null($iSub) || is_null($aInput) || is_null($iStatus))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		$iCNum = 15;

		$aFiles = null;
		try
		{
			$sDate = date('YmdHis');
			DB::start_transaction();

			// ファイル保管処理
			$prefix = $aReport['krYear'].'-'.$aReport['krPeriod'].'-'.$aTeacher['ttID'].'-'.$iSub;
			$tempPath = CL_UPPATH.DS.'temp'.DS;
			$basePath = CL_UPPATH.DS.'kreport'.DS;

			if (!\Clfunc_Common::DirMake($basePath.$prefix))
				throw new Exception('ファイルを格納するディレクトリの作成に失敗しました');

			system('mv '.$basePath.$prefix.'_* '.$basePath.$prefix.DS);
			for ($i = 1; $i <= 5; $i++)
			{
				if (isset($aUploads[($i - 1)]))
				{
					$f = $aUploads[($i - 1)];
					$newfile = $prefix.'_'.$i.'.'.substr($f['file'], strrpos($f['file'], '.') + 1);

					if (preg_match('/^_kreport_/',$f['file']))
					{
						if (!rename($tempPath.$f['file'],$basePath.$newfile))
							throw new Exception('ファイルの格納に失敗しました');
					}
					else
					{
						if (!rename($basePath.$prefix.DS.$f['file'],$basePath.$newfile))
							throw new Exception('ファイルの格納に失敗しました');
					}

					$aFiles['krFile'.$i.'File'] = $newfile;
					$aFiles['krFile'.$i.'Name'] = $f['name'];
					$aFiles['krFile'.$i.'Size'] = $f['size'];
				} else {
					$aFiles['krFile'.$i.'File'] = null;
					$aFiles['krFile'.$i.'Name'] = null;
					$aFiles['krFile'.$i.'Size'] = null;
				}
			}
			\Clfunc_Common::DirRemove($basePath.$prefix);

			if ($bUpdate)
			{
				foreach ($aQuery as $aQ)
				{
					$iKrNO = $aQ['krNO'];
					$aA = $aInput[$iKrNO];
					$query = DB::update('KtaiReportAns_Table');
					$query->and_where('krYear','=',$aReport['krYear']);
					$query->and_where('krPeriod','=',$aReport['krPeriod']);
					$query->and_where('ttID','=',$aTeacher['ttID']);
					$query->and_where('krSub','=',$iSub);
					$query->and_where('krNO','=',$iKrNO);
					$aUpdate = null;
					switch ($aQ['krStyle'])
					{
						case 0:
						case 1:
							$aUpdate['krText'] = '';
							$aChoice = explode('|', $aA['select']);
							for ($i = 1; $i <= $iCNum; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aUpdate['krChoice'.$i] = $iV;
							}
						break;
						case 2:
							$aUpdate['krText'] = $aA['text'];
							for ($i = 1; $i <= $iCNum; $i++)
							{
								$aUpdate['krChoice'.$i] = 0;
							}
						break;
					}
					$query->set($aUpdate);
					$result = $query->execute();

					$aUpdate = array(
						'krDate'     => $sDate,
						'krttName'   => $aTeacher['ttName'],
						'krttSchool' => $aTeacher['cmName'],
						'krStatus'   => $iStatus,
					);
					if (is_array($aFiles))
					{
						$aUpdate = array_merge($aUpdate,$aFiles);
					}

					$query = DB::update('KtaiReportPut_Table');
					$query->and_where('krYear','=',$aReport['krYear']);
					$query->and_where('krPeriod','=',$aReport['krPeriod']);
					$query->and_where('ttID','=',$aTeacher['ttID']);
					$query->and_where('krSub','=',$iSub);
					$query->set($aUpdate);
					$result = $query->execute();
				}
			}
			else
			{
				$aColumn = array('krYear','krPeriod','krNO','ttID','krSub','krDate','krText');
				for ($i = 1; $i <= $iCNum; $i++)
				{
					$aColumn[] = 'krChoice'.$i;
				}
				$query = DB::insert('KtaiReportAns_Table',$aColumn);
				foreach ($aQuery as $aQ)
				{
					$iKrNO = $aQ['krNO'];
					$aA = $aInput[$iKrNO];
					$aInsert = array($aReport['krYear'],$aReport['krPeriod'],$iKrNO,$aTeacher['ttID'],$iSub,$sDate);

					switch ($aQ['krStyle'])
					{
						case 0:
						case 1:
							$aInsert[] = '';
							$aChoice = explode('|', $aA['select']);
							for ($i = 1; $i <= $iCNum; $i++)
							{
								$iV = (array_search($i,$aChoice) !== false)? 1:0;
								$aInsert[] = $iV;
							}
						break;
						case 2:
							$aInsert[] = $aA['text'];
							for ($i = 1; $i <= $iCNum; $i++)
							{
								$aInsert[] = 0;
							}
						break;
					}
					$query->values($aInsert);
				}
				$result = $query->execute();

				$aInsert = array(
					'krYear'     => $aReport['krYear'],
					'krPeriod'   => $aReport['krPeriod'],
					'ttID'       => $aTeacher['ttID'],
					'krSub'      => $iSub,
					'krStatus'   => $iStatus,
					'krDate'     => $sDate,
					'krttName'   => $aTeacher['ttName'],
					'krttSchool' => $aTeacher['cmName'],
				);
				if (is_array($aFiles))
				{
					$aInsert = array_merge($aInsert,$aFiles);
				}

				$query = DB::insert('KtaiReportPut_Table');
				$query->set($aInsert);
				$result = $query->execute();
			}

			$result = \DB::select()
				->from('KtaiReportPut_Table')
				->and_where('krYear','=',$aReport['krYear'])
				->and_where('krPeriod','=',$aReport['krPeriod'])
				->and_where('krStatus','=',1)
				->group_by('ttID')
				->execute();
			;


			$aUpdate = array('krPutNum'=>count($result));
			$query = DB::update('KtaiReportBase_Table');
			$query->and_where('krYear','=',$aReport['krYear']);
			$query->and_where('krPeriod','=',$aReport['krPeriod']);
			$query->set($aUpdate);
			$result = $query->execute();

			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}



	public static function deleteKReportPut($iYear = null,$iPeriod = null)
	{
		if (is_null($iYear) || is_null($iPeriod))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::delete('KtaiReportAns_Table')->where('krYear',$iYear)->and_where('krPeriod',$iPeriod)->execute();
			$result = DB::delete('KtaiReportPut_Table')->where('krYear',$iYear)->and_where('krPeriod',$iPeriod)->execute();
			$result = DB::update('KtaiReportBase_Table')->where('krYear',$iYear)->and_where('krPeriod',$iPeriod)->set(array('krPutNum'=>0))->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function updateKReport($aUpdate = null,$aAndWhere = null,$aOrWhere = null)
	{
		if (is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('KtaiReportBase_Table');
			if (!is_null($aAndWhere))
			{
				foreach ($aAndWhere as $aW)
				{
					$query->and_where($aW[0],$aW[1],$aW[2]);
				}
			}
			if (!is_null($aOrWhere))
			{
				foreach ($aOrWhere as $aW)
				{
					$query->or_where($aW[0],$aW[1],$aW[2]);
				}
			}
			$query->set($aUpdate);
			$result = $query->execute();
			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}



	public static function getKReportAlready($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('KtaiReportAlready_Table');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}



	public static function setKReportAlready($sMode = null,$aMine = null,$aPut = false,$bUpdate = false)
	{
		if (is_null($sMode) || is_null($aMine) || is_null($aPut))
		{
			throw new Exception('処理に必要な情報がありません');
		}

		try
		{
			$sDate = date('YmdHis');
			DB::start_transaction();
			if ($bUpdate)
			{
				$query = DB::update('KtaiReportAlready_Table');
				$query->and_where('krYear','=',$aPut['krYear']);
				$query->and_where('krPeriod','=',$aPut['krPeriod']);
				$query->and_where('ttID','=',$aPut['ttID']);
				$query->and_where('krSub','=',$aPut['krSub']);
				$query->and_where('kaID','=',$aMine['ttID']);
				if ($sMode == 'like')
				{
					$query->set(array('kaLike'=>1,'kaLDate'=>$sDate));
				}
				else
				{
					$query->set(array('kaAlready'=>1,'kaADate'=>$sDate));
				}
				$result = $query->execute();
			}
			else
			{
				$aInsert = array(
					'krYear'=>$aPut['krYear'],
					'krPeriod'=>$aPut['krPeriod'],
					'ttID'=>$aPut['ttID'],
					'krSub'=>$aPut['krSub'],
					'kaID'=>$aMine['ttID'],
				);
				if ($sMode == 'like')
				{
					$aInsert['kaLike'] = 1;
					$aInsert['kaLDate'] = $sDate;
				}
				else
				{
					$aInsert['kaAlready'] = 1;
					$aInsert['kaADate'] = $sDate;
				}
				$query = DB::insert('KtaiReportAlready_Table');
				$query->set($aInsert);
				$result = $query->execute();
			}

			if ($sMode == 'like')
			{
				$aUpdate = array('krLike'=>DB::expr('krLike + 1'));
			}
			else
			{
				$aUpdate = array('krAlready'=>DB::expr('krAlready + 1'));
			}
			$query = DB::update('KtaiReportPut_Table');
			$query->and_where('krYear','=',$aPut['krYear']);
			$query->and_where('krPeriod','=',$aPut['krPeriod']);
			$query->and_where('ttID','=',$aPut['ttID']);
			$query->and_where('krSub','=',$aPut['krSub']);
			$query->set($aUpdate);
			$result = $query->execute();

			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}



	public static function getKReportComment($aAndWhere = null,$aOrWhere = null,$aSort = null,$aLimit = null)
	{
		$query = DB::select_array()->from('KtaiReportComment_View');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		if (!is_null($aLimit))
		{
			$query->limit($aLimit[0]);
			if (isset($aLimit[1]))
			{
				$query->offset($aLimit[1]);
			}
		}

		$result = $query->execute();
		return $result;
	}



	public static function setKReportComment($aInput = null)
	{
		if (is_null($aInput))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::insert('KtaiReportComment_Table')->set($aInput)->execute();

			if ($aInput['putID'] != 'ALL')
			{
				$query = DB::update('KtaiReportPut_Table');
				$query->and_where('krYear',$aInput['krYear']);
				$query->and_where('krPeriod',$aInput['krPeriod']);
				$query->and_where('ttID',$aInput['putID']);
				$query->and_where('krSub',$aInput['krSub']);
				$query->set(array('krCom'=>DB::expr('krCom + 1')));
				$result = $query->execute();
			}

			DB::commit_transaction();
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}
}
