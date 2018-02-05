<?php
namespace Fuel\Tasks;

class Execreportarchive
{
	public static function run($sRbID = null, $sTtID = null)
	{
		$sTempFilePath = DOCROOT.DS.'public'.DS.CL_UPDIR.DS.'temp';
		$sAwsSavePath = 'teacher'.DS.$sTtID;

		if (is_null($sRbID))
		{
			\Log::error('レポート情報が指定されていません。'.$sRbID);
			exit();
		}

		$result = \Model_Report::getReportBase(array(array('rb.rbID','=',$sRbID)));
		if (!count($result))
		{
			\Log::error('レポートが見つかりません。');
			exit();
		}
		$aReport = $result->current();

		$result = \Model_Student::getStudentFromClass($aReport['ctID']);
		if (!count($result))
		{
			\Log::error('学生が見つかりません。');
			exit();
		}
		$aStudent = $result->as_array('stID');

		try
		{
			$aUpdate = array(
				'zipProgress' => 1,
			);
			$result = \Model_Report::updateReport($aUpdate,array(array('rbID','=',$sRbID)));
		}
		catch (\Exception $e)
		{
			\Log::error('レポート情報の更新に失敗しました。');
			exit();
		}


		$sZipName = '_report_'.$sRbID.'_'.date('YmdHis').'.zip';
		$aSavePath = null;

		try
		{
			$zip = new \ZipArchive;
			$res = $zip->open($sTempFilePath.DS.$sZipName, \ZipArchive::CREATE);
			if ($res)
			{
				$result = \Model_Report::getReportPut(array(array('rp.rbID','=',$sRbID)));
				if (count($result))
				{
					$aRes = $result->as_array('stID');
					foreach ($aRes as $sStID => $aP)
					{
						if (isset($aStudent[$sStID]))
						{
							if ($aP['fID1'] || $aP['fID2'] || $aP['fID3'])
							{
								$prefix = $aStudent[$sStID]['stNO'].'_'.$aStudent[$sStID]['stName'];
								for ($i = 1; $i <= 3; $i++)
								{
									if ($aP['fID'.$i] != '')
									{
										$filename = $prefix.'_'.$i.'_'.$aP['fName'.$i];
										$filename = preg_replace('/[\s\/]/', '_', $filename);
										$filename = mb_convert_encoding($filename, 'SJIS-win', 'UTF-8');

										$sTempName = $aP['fID'.$i].'.'.$aP['fExt'.$i];
										$sSavePath = $sTempFilePath.DS.'_report_'.$sTempName;

										\Log::error($aP['fPath'.$i]."\n".$sTempName."\n".$sSavePath);

										$result = \Clfunc_Aws::getFile($aP['fPath'.$i],$sTempName,$sSavePath);
										if (!$zip->addFile($sSavePath,$filename))
										{
											throw new \Exception('アーカイブにファイル追加できませんでした。');
										}
										$aSavePath[] = $sSavePath;
									}
								}
							}
						}
					}
				}
			}
			else
			{
				throw new \Exception('アーカイブファイルの作成に失敗しました。');
			}
			$zip->close();


			# ZipFileをs3に
			$sSourseFile = $sTempFilePath.DS.$sZipName;
			$sContentType = \Clfunc_Common::GetContentType($sSourseFile);
			$sExt = pathinfo($sSourseFile,PATHINFO_EXTENSION);
			$iFileType = \Clfunc_Common::GetFileType($sSourseFile);

			$fID = \Model_File::getFileID();
			$sFile = $fID.'.'.$sExt;

			# 登録情報作成
			$aInsert = array(
				'fID'          => $fID,
				'fName'        => '_report_'.$sRbID.'.zip',
				'fSize'        => filesize($sSourseFile),
				'fExt'         => $sExt,
				'fContentType' => $sContentType,
				'fFileType'    => $iFileType,
				'fPath'        => $sAwsSavePath,
				'fUserType'    => 0,
				'fUser'        => $sTtID,
				'fDate'        => date('YmdHis'),
			);
			try
			{
				$result = \Clfunc_Aws::putFile($sAwsSavePath, $sFile, $sSourseFile, $sContentType);
				$result = \Model_File::insertFile($aInsert);
			}
			catch (\Exception $e)
			{
				\Clfunc_Aws::deleteFile($sAwsSavePath,$sFile);
				\Clfunc_Common::LogOut($e,__CLASS__);
				throw new \Exception('指定した提出ファイルが保存できませんでした。'.$e->getMessage());
			}

			try
			{
				$aUpdate = array(
					'zipFID' => $fID,
					'zipProgress' => 0,
				);
				$result = \Model_Report::updateReport($aUpdate,array(array('rbID','=',$sRbID)));
			}
			catch (\Exception $e)
			{
				\Clfunc_Aws::deleteFile($sAwsSavePath,$sFile);
				\Clfunc_Common::LogOut($e,__CLASS__);
				throw new \Exception('基本テーブルへの書込に失敗しました。'.$e->getMessage());
			}

			# 既存のファイルを削除
			if ($aReport['zipFID'])
			{
				$result = \Model_File::deleteFile($aReport['zipFID']);
				\Clfunc_Aws::deleteFile($aReport['zipFPath'],$aReport['zipFID'].'.'.$aReport['zipFExt']);
			}

			# 作成ファイルを削除
			unlink($sTempFilePath.DS.$sZipName);
			if (!is_null($aSavePath))
			{
				foreach ($aSavePath as $sP)
				{
					unlink($sP);
				}
			}
		}
		catch (\Exception $e)
		{
			if (!is_null($aSavePath))
			{
				foreach ($aSavePath as $sP)
				{
					unlink($sP);
				}
			}

			$aUpdate = array(
				'zipProgress' => 2,
			);
			$result = \Model_Report::updateReport($aUpdate,array(array('rbID','=',$sRbID)));

			\Log::error($e->getMessage());
			exit();
		}
	}
}