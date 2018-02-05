<?php
namespace Fuel\Tasks;

class Exectestansarchive
{
	public static function run($sCtID = null, $sTtID = null, $sLang = 'ja')
	{
		\Config::set('language', $sLang);
		\Lang::load('i18n');

		$sTempFilePath = DOCROOT.DS.'public'.DS.CL_UPDIR.DS.'temp';
		$sAwsSavePath = 'teacher'.DS.$sTtID;

		if (is_null($sCtID))
		{
			\Log::error('講義情報が指定されていません。'.$sCtID);
			exit();
		}

		$result = \Model_Class::getClassArchive($sCtID,'TestStuAnsList');
		if (!count($result))
		{
			\Log::error('講義情報が見つかりません。'.$sCtID);
			exit();
		}
		$aTSAL = $result->current();

		$result = \Model_Teacher::getTeacherFromID($sTtID);
		if (!count($result))
		{
			\Log::error('先生情報が見つかりません。'.$sTtID);
			exit();
		}
		$aTeacher = $result->current();

		$result = \Model_Test::getTestBaseFromClass($sCtID,null,null,array('tb.tbSort'=>'desc'));
		if (!count($result))
		{
			\Log::error('アンケートが見つかりません。');
			exit();
		}
		$aTest = $result->as_array();

		$result = \Model_Student::getStudentFromClass($sCtID);
		if (!count($result))
		{
			\Log::error('学生が見つかりません。');
			exit();
		}
		$aStudent = $result->as_array('stID');

		try
		{
			$aUpdate = array(
				'caProgress' => 1,
				'caDate' => date('YmdHis'),
			);
			$result = \Model_Class::updateClassArchive($aUpdate,array(array('ctID','=',$sCtID),array('caType','=','TestStuAnsList')));
		}
		catch (\Exception $e)
		{
			\Log::error('講義アーカイブ情報の更新に失敗しました。');
			exit();
		}

		$sZipName = '_TestStuAnsList_'.$sCtID.'_'.date('YmdHis').'.zip';
		$aSavePath = null;

		try
		{
			$zip = new \ZipArchive;
			$res = $zip->open($sTempFilePath.DS.$sZipName, \ZipArchive::CREATE);
			if ($res)
			{
				foreach ($aStudent as $sStID => $aStu)
				{
					$prefix = $aStu['stNO'].'_'.$aStu['stName'];

					$aRes = \Clfunc_Createcsv::TestStuAnsList($aStu,$aTest,$aTeacher['ttTimeZone']);

					$csv = new \SplFileObject('php://memory', 'wr+');
					foreach ($aRes as $f)
					{
						mb_convert_variables('sjis-win','UTF-8',$f);
						$csv->fputcsv($f);
					}
					$sCSV = null;
					foreach ($csv as $t)
					{
						$sCSV .= $t;
					}

					$filename = $prefix.'.csv';
					$filename = preg_replace('/[\s\/]/', '_', $filename);
					$filename = mb_convert_encoding($filename, 'SJIS-win', 'UTF-8');

					if (!$zip->addFromString($filename, $sCSV))
					{
						throw new \Exception('アーカイブにファイル追加できませんでした。');
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
				'fName'        => '_TestStuAnsList_'.$sCtID.'.zip',
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
					'fID' => $fID,
					'caProgress' => 0,
					'caDate' => date('YmdHis'),
				);
				$result = \Model_Class::updateClassArchive($aUpdate,array(array('ctID','=',$sCtID),array('caType','=','TestStuAnsList')));
			}
			catch (\Exception $e)
			{
				\Clfunc_Aws::deleteFile($sAwsSavePath,$sFile);
				\Clfunc_Common::LogOut($e,__CLASS__);
				throw new \Exception('アーカイブテーブルへの書込に失敗しました。'.$e->getMessage());
			}

			# 既存のファイルを削除
			if ($aTSAL['fID'])
			{
				$result = \Model_File::deleteFile($aTSAL['fID']);
				\Clfunc_Aws::deleteFile($aTSAL['fPath'],$aTSAL['fID'].'.'.$aTSAL['fExt']);
			}

			# 作成ファイルを削除
			unlink($sTempFilePath.DS.$sZipName);
		}
		catch (\Exception $e)
		{
			$aUpdate = array(
				'caProgress' => 2,
				'caDate' => date('YmdHis'),
			);
			$result = \Model_Class::updateClassArchive($aUpdate,array(array('ctID','=',$sCtID),array('caType','=','TestStuAnsList')));

			\Log::error($e->getMessage()."\n".$e->getTraceAsString());
			exit();
		}
	}
}