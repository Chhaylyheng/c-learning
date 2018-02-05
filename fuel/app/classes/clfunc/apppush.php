<?php
require_once APPPATH.DS.'vendor/ApnsPHP/Autoload.php';

class ClFunc_Apppush
{
	private static $sAndroidUrl = 'https://android.googleapis.com/gcm/send';
	private static $sApplePem = 'entrust_root_certification_authority.pem';

	public static function AndroidPush($aList = null, $sMsg = null, $aCustom = null, $sMode = null)
	{
		try
		{
			if (is_null($aList) || is_null($sMsg))
			{
				throw new Exception('処理に必要な情報がありません');
			}

			$data_array = array(
				'registration_ids' => $aList,
				'data' => array(
					'message' => $sMsg,
				),
				'time_to_live' => (60*60*24*7),
			);
			if (!is_null($aCustom))
			{
				$data_array['data'][$aCustom['name']] = $aCustom['value'];
			}
			$data = json_encode($data_array);

			$sKey = ($sMode == 'T')? CL_ANDROID_API_KEY_T:CL_ANDROID_API_KEY;
			$aHeader = array(
				'Authorization: key='.$sKey,
				'Content-Type: application/json',
				'Content-Length: '.strlen($data),
			);

			$oCHandler = curl_init(self::$sAndroidUrl);
			curl_setopt($oCHandler, CURLOPT_HTTPHEADER, $aHeader);
			curl_setopt($oCHandler, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($oCHandler, CURLOPT_POSTFIELDS, $data);
			curl_setopt($oCHandler, CURLOPT_RETURNTRANSFER, true);
			$aAndroid = curl_exec($oCHandler);

			if (curl_errno($oCHandler)) {
				throw new Exception('['.curl_errno($oCHandler).'] '.curl_error($oCHandler));
			}

			curl_close($oCHandler);

			\Log::write('ANDROID PUSH','['.date('Y-m-d H:i:s').'],ANDROID PUSH,'.count($aList));

			return $aAndroid;
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage().' - Android Push');
		}
	}

	public static function ApplePush($sAssetPath = null, $aList = null, $sMsg = null, $aCustom = null, $sMode = null)
	{
		try
		{
			if (is_null($sAssetPath) || is_null($aList) || is_null($sMsg))
			{
				throw new Exception('処理に必要な情報がありません');
			}

			$push = new ApnsPHP_Push(
				CL_APPLE_PUSH_MODE,
				$sAssetPath.'CLApp'.$sMode.CL_APPLE_PUSH_MODE.'.pem'
			);

			$push->setRootCertificationAuthority($sAssetPath.self::$sApplePem);
			$push->setLogger(new ApnsPHP_Log_Custom);
			$push->connect();

			foreach($aList as $u) {
				$mes = new ApnsPHP_Message($u['id']);
				$mes->setText(mb_substr($sMsg, 0, 38, 'UTF-8'));
				$mes->setExpiry(60*60*24*7);
				if (!is_null($aCustom))
				{
					$mes->setCustomProperty($aCustom['name'], $aCustom['value']);
				}
				$mes->setBadge($u['badge']);
				$push->add($mes);
			}

			$push->send();
			$push->disconnect();

			\Log::write('APPLE PUSH','['.date('Y-m-d H:i:s').'],APPLE PUSH,'.count($aList));
//			\Log::write('AP-LIST',print_r($aList,true));

			return true;
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage().' - Apple Push '.$sMode);
		}
	}
}

class ApnsPHP_Log_Custom implements ApnsPHP_Log_Interface
{
	public function log($sMessage)
	{
		// ログ無視
		// \Log::write('APPLE PUSH','['.date('Y-m-d H:i:s').']'.$sMessage);
	}
}
