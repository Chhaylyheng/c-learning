<?php
namespace Fuel\Tasks;

class Cron5min
{
	public static function run()
	{
		# 出席の自動開始
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('acAStart','<=',$sNow),
			array('acAStart','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'acAStart'=>CL_DATETIME_DEFAULT,
			'acStart'=>$sNow,
		);
		try
		{
			$result = \Model_Attend::updateAttendBatch($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('出席の自動開始処理に失敗しました。'.$dump);
			exit();
		}

		# 出席の自動停止
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('acAEnd','<=',$sNow),
			array('acAEnd','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'acAEnd'=>CL_DATETIME_DEFAULT,
			'acEnd'=>$sNow,
		);
		try
		{
			$result = \Model_Attend::updateAttendBatch($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('出席の自動停止処理に失敗しました。'.$dump);
			exit();
		}

		# ケータイ研アンケートの自動開始
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('krAutoPublicDate','<=',$sNow),
			array('krAutoPublicDate','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'krAutoPublicDate'=>CL_DATETIME_DEFAULT,
			'krPublicDate'=>$sNow,
			'krPublic'=>1,
		);
		try
		{
			$result = \Model_KReport::updateKReport($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('ケータイ研リポートの自動開始処理に失敗しました。'.$dump);
			exit();
		}

		# ケータイ研アンケートの自動停止
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('krAutoCloseDate','<=',$sNow),
			array('krAutoCloseDate','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'krAutoCloseDate'=>CL_DATETIME_DEFAULT,
			'krCloseDate'=>$sNow,
			'krPublic'=>2,
		);
		try
		{
			$result = \Model_KReport::updateKReport($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('ケータイ研リポートの自動停止処理に失敗しました。'.$dump);
			exit();
		}

		# アンケートの自動開始
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('qbNum','>',0),
			array('qbAutoPublicDate','<=',$sNow),
			array('qbAutoPublicDate','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'qbAutoPublicDate'=>CL_DATETIME_DEFAULT,
			'qbPublicDate'=>$sNow,
			'qbPublic'=>1,
		);
		try
		{
			$result = \Model_Quest::updateQuest($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('アンケートの自動開始処理に失敗しました。'.$dump);
			exit();
		}

		# アンケートの自動停止
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('qbAutoCloseDate','<=',$sNow),
			array('qbAutoCloseDate','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'qbAutoCloseDate'=>CL_DATETIME_DEFAULT,
			'qbCloseDate'=>$sNow,
			'qbPublic'=>2,
		);
		try
		{
			$result = \Model_Quest::updateQuest($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('アンケートの自動停止処理に失敗しました。'.$dump);
			exit();
		}

		# 小テストの自動開始
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('tbNum','>',0),
			array('tbAutoPublicDate','<=',$sNow),
			array('tbAutoPublicDate','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'tbAutoPublicDate'=>CL_DATETIME_DEFAULT,
			'tbPublicDate'=>$sNow,
			'tbPublic'=>1,
		);
		try
		{
			$result = \Model_Test::updateTest($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('小テストの自動開始処理に失敗しました。'.$dump);
			exit();
		}

		# 小テストの自動停止
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('tbAutoCloseDate','<=',$sNow),
			array('tbAutoCloseDate','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'tbAutoCloseDate'=>CL_DATETIME_DEFAULT,
			'tbCloseDate'=>$sNow,
			'tbPublic'=>2,
		);
		try
		{
			$result = \Model_Test::updateTest($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('小テストの自動停止処理に失敗しました。'.$dump);
			exit();
		}

		# レポートの自動開始
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
			array('rbAutoPublicDate','<=',$sNow),
			array('rbAutoPublicDate','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
			'rbAutoPublicDate'=>CL_DATETIME_DEFAULT,
			'rbPublicDate'=>$sNow,
			'rbPublic'=>1,
		);
		try
		{
			$result = \Model_Report::updateReport($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('レポートテーマの自動開始処理に失敗しました。'.$dump);
			exit();
		}

		# レポートの自動停止
		$sNow = date('Y-m-d H:i:s');
		$aWhere = array(
				array('rbAutoCloseDate','<=',$sNow),
				array('rbAutoCloseDate','!=',CL_DATETIME_DEFAULT),
		);
		$aUpdate = array(
				'rbAutoCloseDate'=>CL_DATETIME_DEFAULT,
				'rbCloseDate'=>$sNow,
				'rbPublic'=>2,
		);
		try
		{
			$result = \Model_Report::updateReport($aUpdate,$aWhere);
		}
		catch (\Exception $e)
		{
			$dump = \Clfunc_Common::vdumpStr($e);
			\Log::error('レポートテーマの自動停止処理に失敗しました。'.$dump);
			exit();
		}

		# ニュース通知
		$sNow = date('Y-m-d H:i:s');
		$sPre = date('Y-m-d H:i:s', strtotime('-4 min'));
		$aWhere = array(
			array('cnStart','BETWEEN',array($sPre,$sNow)),
			array('cnSend','=',1),
		);
		$result = \Model_Class::getNews(null,$aWhere);
		if (count($result))
		{
			$aNews = $result->as_array();

			foreach ($aNews as $aN)
			{
				$sCtID = $aN['ctID'];

				$result = \Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$sCtID),array('tp.tpMaster','=',1)));
				$sTtID = null;
				if (count($result))
				{
					$aTeacher = $result->current();
					$sTtID = $aTeacher['ttID'];
				}

				// 学生
				try
				{
					\ClFunc_Mailsend::MailSendToClassStudents($sTtID, $sCtID, 'NewsSend', array('body'=>$aN['cnBody'],'docroot'=>DOCROOT.'public/'));
				}
				catch (\Exception $e)
				{
					$dump = \Clfunc_Common::vdumpStr($e);
					\Log::error('ニュースのメール送信に失敗しました。'.$dump);
				}

				// 先生
				/*
				try
				{
					\ClFunc_Mailsend::MailSendToClassStudents($sTtID, $sCtID, 'NewsSend', array('body'=>$aN['cnBody']));
				}
				catch (\Exception $e)
				{
					$dump = \Clfunc_Common::vdumpStr($e);
					\Log::error('ニュースのメール送信に失敗しました。'.$dump);
				}
				*/

				$aDTs = array('Android'=>null, 'Apple'=>null);
				$result = \Model_Teacher::getTeacherFromClass(array(array('tp.ctID','=',$sCtID),array('tt.ttApp','>',0)));
				if (count($result))
				{
					foreach ($result as $aS)
					{
						if ($aS['ttApp'] == 1 && $aS['ttDeviceToken'] != '')
						{
							$aDTs['Apple'][] = array(
								'id' => $aS['ttDeviceToken'],
								'badge' => 0,
							);
						}
						else if ($aS['ttApp'] == 2 && $aS['ttDeviceToken'] != '')
						{
							$aDTs['Android'][] = $aS['ttDeviceToken'];
						}
					}
				}

				try
				{
					$aCustom = array('name'=>'url', 'value'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/t/class/index/'.$sCtID);
					if (!is_null($aDTs['Apple']))
					{
						\ClFunc_Apppush::ApplePush(DOCROOT.'public/assets/docs/', $aDTs['Apple'], '['.$aTeacher['ctName'].'] '.$aN['cnBody'], $aCustom, 'T');
						\ClFunc_Apppush::ApplePush(DOCROOT.'public/assets/docs/', $aDTs['Apple'], '['.$aTeacher['ctName'].'] '.$aN['cnBody'], $aCustom, 'TT');
					}
					if (!is_null($aDTs['Android']))
					{
						\ClFunc_Apppush::AndroidPush($aDTs['Android'], '['.$aTeacher['ctName'].'] '.$aN['cnBody'], $aCustom, 'T');
					}
				}
				catch (\Exception $e)
				{
					$dump = \Clfunc_Common::vdumpStr($e);
					\Log::error('ニュースのプッシュ通知に失敗しました。'.$dump);
				}

			}
		}
	}
}