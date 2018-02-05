<?php

class Controller_Getfile extends \Controller
{
	public function action_index()
	{
		Response::redirect('index/404','location',404);
	}

	public function action_download($dir = null, $file = null, $name = null)
	{
		try
		{
			if (is_null($dir) || is_null($file) || is_null($name))
			{
				throw new Exception('Invalid Argument.',404);
			}

			$filePath = CL_UPPATH.DS.$dir.DS.$file;

			if (!file_exists($filePath))
			{
				throw new Exception('File Not Found.',404);
			}

			$mime = shell_exec('file -bi '.escapeshellcmd($filePath));
			$mime = trim($mime);
			$mimeType = preg_replace('/ [^ ]*/', '', $mime);

			$size = filesize($filePath);
			$time = date('r',filemtime($filePath));
			$etag = md5($_SERVER["REQUEST_URI"]).$size;
			$length = $size;

			$fp = fopen($filePath,"rb");

			# ブラウザがHTTP_RANGEを要求してきたかどうか
			if (empty($_SERVER["HTTP_RANGE"]))
			{
				header("Accept-Ranges: bytes");     //HTTP_RANGEに対応してますよと返答
				header("Content-Type: ".$mimeType);
				header("Content-Length: ".$length);
				header("Etag: \"".$etag."\"");
			}
			else if (isset($_SERVER["HTTP_RANGE"]))
			{
				list($rangeOffset, $rangeLimit) = sscanf($_SERVER['HTTP_RANGE'], "bytes=%d-%d");
				if(!$rangeLimit){
					$rangeLimit = $size - 1;
				}

				header("HTTP/1.1 206 Partial Content");
				header("Accept-Ranges: bytes");
				header("Content-Type: ".$mimeType);

				$contentRange = sprintf("bytes %d-%d/%d", $rangeOffset, $rangeLimit, $size);
				header("Content-Range: ".$contentRange);

				$length = $rangeLimit - $rangeOffset + 1;

				header("Content-Length: ".$length);
				header("Etag: \"".$etag."\"");

				fseek($fp, $rangeOffset);
			}

			$buffer = fread($fp, $length);
			echo $buffer;

			header('Content-Disposition: inline; filename="'.$name.'"');
			header('Last-Modified: '.$time);
			header('Connection: Keep-Alive');

			fclose($fp);
			exit();
		}
		catch (Exception $e)
		{
			Response::forge($e->getMessage());
		}
	}

	public function action_s3file($fID = null, $sMode = null, $iNO = null, $sStID = null)
	{
		try
		{
			if (is_null($fID))
			{
				throw new Exception('Invalid Argument.',404);
			}
			$result = Model_File::getFileFromID($fID);
			if (!count($result))
			{
				throw new Exception('File Not Found.',404);
			}
			$aFile = $result->current();

			$sContentName = $aFile['fName'];
			$sFileName = $aFile['fID'].'.'.$aFile['fExt'];
			$sUrl = '';

			# 既読判定と既読処理
			switch ($sMode)
			{
				case 'me':
					if ($aFile['fFileType'] == 2)
					{
						$sUrl = 'e';
					}
				case 'm':
					$result = Model_Material::getMaterialAlready(array(array('mNO','=',$iNO),array('stID','=',$sStID)));
					if (!count($result))
					{
						$result = Model_Material::getMaterial(array(array('mt.mNO','=',$iNO)));
						if (count($result))
						{
							$aMat = $result->current();
							$aInsert = array(
								'mNO' => $iNO,
								'stID' => $sStID,
								'mcID' => $aMat['mcID'],
								'maDate' => date('YmdHis'),
							);
							$result = Model_Material::insertMaterialAlready($aInsert);
							\Session::delete('CL_STU_UNREAD_'.$sStID);
						}
					}
					$sURL = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$fID,'mode'=>$sUrl));
					header('Location: '.$sURL);
					exit();
				break;
				case 'e':
					if ($aFile['fFileType'] == 2)
					{
						$sFileName = CL_PREFIX_ENCODE.$aFile['fID'].CL_AWS_ENCEXT;
						$sContentName = $sFileName;
					}
				break;
				case 't':
					$sFileName = CL_PREFIX_THUMBNAIL.$aFile['fID'].'-00001.png';
					$sContentName = $sFileName;
				break;
				case 't2':
					$sFileName = CL_PREFIX_THUMBNAIL2.$sFileName;
					$sContentName = $sFileName;
				break;
				case 'tm':
					$sFileName = CL_PREFIX_THUMBNAIL.$sFileName;
					$sContentName = $sFileName;
				break;
			}

			$time = date('r',strtotime($aFile['fDate']));

			if (CL_AWS_DIRECT)
			{
				$sURL = Clfunc_Aws::getFileUrl($aFile['fPath'],$sFileName,$sContentName);
				header('Location: '.$sURL);
				exit();
			}

			# S3-Object取得
			$result = Clfunc_Aws::getFile($aFile['fPath'],$sFileName);
			$mimeType = $result['ContentType'];
			$size = $result['ContentLength'];
			$length = $size;

			$etag = md5($_SERVER["REQUEST_URI"]).$size;

			# ブラウザがHTTP_RANGEを要求してきたかどうか
			if (empty($_SERVER["HTTP_RANGE"]))
			{
				header("Accept-Ranges: bytes");     //HTTP_RANGEに対応してますよと返答
				header("Content-Type: ".$mimeType);
				header("Content-Length: ".$length);
				header("Etag: \"".$etag."\"");
			}
			else if (isset($_SERVER["HTTP_RANGE"]))
			{
				# S3-Objectの部分取得
				$result = Clfunc_Aws::getFile($aFile['fPath'],$sFileName,null,$_SERVER['HTTP_RANGE']);

				list($rangeOffset, $rangeLimit) = sscanf($_SERVER['HTTP_RANGE'], "bytes=%d-%d");
				if(!$rangeLimit){
					$rangeLimit = $size - 1;
				}

				header("HTTP/1.1 206 Partial Content");
				header("Accept-Ranges: bytes");
				header("Content-Type: ".$mimeType);

				$contentRange = sprintf("bytes %d-%d/%d", $rangeOffset, $rangeLimit, $size);
				header("Content-Range: ".$contentRange);

				$length = $rangeLimit - $rangeOffset + 1;

				header("Content-Length: ".$length);
				header("Etag: \"".$etag."\"");
			}

			$sDis = (strpos($mimeType, 'pdf') !== false || strpos($mimeType, 'image') !== false)? 'inline':'attachment';

			header('Content-Disposition: '.$sDis.'; filename*=UTF-8\'\''.rawurlencode($sContentName));
			header('Last-Modified: '.$time);
			header('Connection: Keep-Alive');

			echo $result['Body'];

			exit();
		}
		catch (Exception $e)
		{
			Response::forge($e->getMessage());
		}
	}

	public function action_externallink($iNO = null, $iRNO = null,$sStID = null)
	{
		try
		{
			if (is_null($iNO) || is_null($iRNO))
			{
				throw new Exception('Invalid Argument.',404);
			}
			$aMaterial = null;
			$result = Model_Material::getMaterial(array(array('mt.mNO','=',$iNO)));
			if (!count($result))
			{
				throw new Exception('Material Not Found.',404);
			}
			$aMaterial = $result->current();
			$urls = explode("\n",$aMaterial['mURL']);

			if (!isset($urls[$iRNO]) || !$urls[$iRNO])
			{
				throw new Exception('Material Not Found.',404);
			}

			# 既読判定と既読処理
			if (!is_null($sStID))
			{
				$result = Model_Material::getMaterialAlready(array(array('mNO','=',$iNO),array('stID','=',$sStID)));
				if (!count($result))
				{
					$aInsert = array(
						'mNO' => $iNO,
						'stID' => $sStID,
						'mcID' => $aMaterial['mcID'],
						'maDate' => date('YmdHis'),
					);
					$result = Model_Material::insertMaterialAlready($aInsert);
				}
			}

			header('Location: '.$urls[$iRNO]);
			exit();
		}
		catch (Exception $e)
		{
			Response::forge($e->getMessage());
		}
	}

	public function action_cllink($iNO = null, $sStID = null, $sJump = null)
	{
		try
		{
			if (is_null($iNO) || is_null($sStID) || is_null($sJump))
			{
				throw new Exception('Invalid Argument.',404);
			}
			$aMaterial = null;
			$result = Model_Material::getMaterial(array(array('mt.mNO','=',$iNO)));
			if (!count($result))
			{
				throw new Exception('Material Not Found.',404);
			}
			$aMaterial = $result->current();

			# 既読判定と既読処理
			$result = Model_Material::getMaterialAlready(array(array('mNO','=',$iNO),array('stID','=',$sStID)));
			if (!count($result))
			{
				$aInsert = array(
					'mNO' => $iNO,
					'stID' => $sStID,
					'mcID' => $aMaterial['mcID'],
					'maDate' => date('YmdHis'),
				);
				$result = Model_Material::insertMaterialAlready($aInsert);
			}

			header('Location: '.base64_decode($sJump));
			exit();
		}
		catch (Exception $e)
		{
			Response::forge($e->getMessage());
		}
	}

}