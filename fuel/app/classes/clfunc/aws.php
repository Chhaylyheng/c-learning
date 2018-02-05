<?php
require_once APPPATH.DS.'vendor/aws/aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\CloudFront\CloudFrontClient;
use Aws\ElasticTranscoder\ElasticTranscoderClient;

class ClFunc_Aws
{
	public static $sharedConfig = array(
		'region'  => CL_AWS_REGION,
		'version' => 'latest',
		'credentials' => array(
			'key'    => CL_AWS_KEY,
			'secret' => CL_AWS_SECRET,
		),
	);

	public static function getFile($sPath = null, $sFile = null, $sSavePath = null, $sRange = null)
	{
		$sdk = new Aws\Sdk(self::$sharedConfig);
		$s3c = $sdk->createS3();

		try
		{
			$aOpt = array(
				'Bucket' => CL_AWS_BUCKET,
				'Key'    => CL_AWS_SYSPATH.$sPath.DS.$sFile,
			);
			if (!is_null($sSavePath))
			{
				$aOpt['SaveAs'] = $sSavePath;
			}
			if (!is_null($sRange))
			{
				$aOpt['Range'] = $sRange;
			}
			$result = $s3c->getObject($aOpt);
		}
		catch (\Aws\S3\Exception\S3Exception $e)
		{
			$status_code = $e->getAwsErrorCode();
			throw new Exception('S3 File Get Error.['.$status_code.']');
		}
		return $result;
	}

	public static function getFileUrl($sPath = null, $sFile = null, $sContentName = null)
	{
		if (is_null($sContentName))
		{
			$sContentName = $sFile;
		}

		$s3c = S3Client::factory(self::$sharedConfig);
		try
		{
			$cmd = $s3c->getCommand('GetObject', [
				'Bucket' => CL_AWS_BUCKET,
				'Key' => CL_AWS_SYSPATH.$sPath.DS.$sFile,
				'ResponseContentDisposition' => 'inline; filename*=UTF-8\'\''.rawurlencode($sContentName),
			]);
			$request = $s3c->createPresignedRequest(
				$cmd,
				'+180 minutes'
			);
			$result = (string)$request->getUri();
		}
		catch (\Aws\S3\Exception\S3Exception $e)
		{
			$status_code = $e->getAwsErrorCode();
			throw new Exception('S3 File GetURL Error.['.$status_code.']');
		}

/*
		try
		{
			$sDesc = rawurlencode("inline;filename*=UTF-8''".$sContentName);
			$sRCD = '?response-content-disposition='.$sDesc;
			$cf = new CloudFrontClient(self::$sharedConfig);
			//期限付きURL作成
			$result = $cf->getSignedUrl(array(
				'url'     => 'https://'.CL_AWS_CF_DOMAIN.DS.CL_AWS_SYSPATH.$sPath.DS.$sFile,
				'expires' => strtotime('+1 minutes'),
				'private_key' => DOCROOT.'assets/docs/pk-'.CL_AWS_CF_KEY.'.pem',
				'key_pair_id' => CL_AWS_CF_KEY,
			));
		}
		catch (\Aws\CloudFront\Exception $e)
		{
			$status_code = $e->getAwsErrorCode();
			throw new Exception('CloudFront File GetURL Error.['.$status_code.']');
		}
*/

		return $result;
	}

	public static function putFile($sSavePath = null, $sFile = null, $sSourseFile = null, $sContentType = null)
	{
		$sdk = new Aws\Sdk(self::$sharedConfig);
		$s3c = $sdk->createS3();
		$sKey = CL_AWS_SYSPATH.$sSavePath.DS.$sFile;

		try
		{
			$result = $s3c->putObject(array(
				'Bucket'      => CL_AWS_BUCKET,
				'Key'         => $sKey,
				'SourceFile'  => $sSourseFile,
				'ContentType' => $sContentType,
			));
		}
		catch (\Aws\S3\Exception\S3Exception $e) {
			$status_code = $e->getAwsErrorCode();
			throw new Exception('S3 File Put Error.['.$status_code.']');
		}
		return $result;
	}

	public static function deleteFile($sSavePath = null, $sFile = null)
	{
		$sdk = new Aws\Sdk(self::$sharedConfig);
		$s3c = $sdk->createS3();

		try
		{
			$result = $s3c->deleteObject(array(
				'Bucket' => CL_AWS_BUCKET,
				'Key'    => CL_AWS_SYSPATH.$sSavePath.DS.$sFile,
			));
		}
		catch (\Aws\S3\Exception\S3Exception $e)
		{
			$status_code = $e->getAwsErrorCode();
			throw new Exception('S3 File Delete Error.['.$status_code.']');
		}
		return $result;
	}

	public static function encodeMovie($sSavePath = null, $sSfID = null, $sExt = null)
	{
		$sInKey = CL_AWS_SYSPATH.$sSavePath.DS.$sSfID.'.'.$sExt;
		$sOutKey = CL_AWS_SYSPATH.$sSavePath.DS.CL_PREFIX_ENCODE.$sSfID.CL_AWS_ENCEXT;
		$sThumb = CL_AWS_SYSPATH.$sSavePath.DS.CL_PREFIX_THUMBNAIL.$sSfID.'-{count}';

		$codeOption = array(
			'PipelineId' => CL_AWS_PIPELINE,
			'Input' => array(
				'Key' => $sInKey,
			),
			'Output' => array(
				'Key' => $sOutKey,
				'PresetId' => CL_AWS_PRESETID,
				'Rotate' => 'auto',
				'ThumbnailPattern' => $sThumb,
			),
		);

		try
		{
			$et = ElasticTranscoderClient::factory(self::$sharedConfig);
			$result = $et->createJob($codeOption);
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage().' InKey:'.$sInKey);
		}

		return $result;
	}

}