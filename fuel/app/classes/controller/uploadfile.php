<?php

class Controller_Uploadfile extends \Controller_Hybrid
{
	public function action_index()
	{
		Response::redirect('index/404','location',404);
	}

	public function post_index()
	{
		Fuel::$profiling = false;

		$prefix = \Input::post('prefix','');
		$mode = \Input::post('mode','');

		// アップロード設定
		$config = array(
			'max_size'   => CL_FILESIZE*1024*1024,
			'path'       => CL_UPPATH.DS.'temp',
			'randomize'  => true,
			'prefix'     => $prefix,
			'path_chmod' => 0777,
			'file_chmod' => 0666,
		);
		if ($mode == 'image')
		{
			$config['ext_whitelist']  = array('jpg','jpeg','png','gif');
			$config['type_whitelist'] = array('image');
			$config['max_size']       = CL_IMGSIZE*1024*1024;
		}

		$json = array();
		\Upload::process($config);

		try
		{
			if (!Upload::is_valid())
			{
				$upload = Upload::get_errors(0);
				if ($upload['errors'])
				{
					switch ($upload['errors'][0]['error'])
					{
						case Upload::UPLOAD_ERR_INI_SIZE:
						case Upload::UPLOAD_ERR_FORM_SIZE:
						case Upload::UPLOAD_ERR_MAX_SIZE:
							throw new Exception('登録できるファイルのサイズは'.CL_FILESIZE.'MBまでです。');
						break;
						case Upload::UPLOAD_ERR_EXTENSION:
						case Upload::UPLOAD_ERR_EXT_BLACKLISTED:
						case Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED:
						case Upload::UPLOAD_ERR_TYPE_BLACKLISTED:
						case Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED:
						case Upload::UPLOAD_ERR_MIME_BLACKLISTED:
						case Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED:
							throw new Exception('指定のファイル形式はアップロードできません。');
						break;
						default:
							throw new Exception('ファイルアップロードに失敗しました。');
						break;
					}
				}
			}
			\Upload::save();
			$temp = \Upload::get_files(0);
			$json = array(
				'name' => $temp['name'],
				'file' => $temp['saved_as'],
				'size' => $temp['size'],
				'isimg' => \Clfunc_Common::isImg($temp['saved_to'].$temp['saved_as']),
			);
			if ($json['isimg'])
			{
				if ($json['isimg'] == 'jpg')
				{
					ClFunc_Common::OrientationFixedImage($temp['saved_to'].$temp['saved_as']);
				}

				$image=Image::load($temp['saved_to'].$temp['saved_as']);
				$image->config('quality',CL_Q_SMALL_QUALITY)->resize(CL_Q_SMALL_SIZE,(CL_Q_SMALL_SIZE / 4 * 3),true)->save($temp['saved_to'].CL_PREFIX_THUMBNAIL.$temp['saved_as']);
				$image=Image::load($temp['saved_to'].$temp['saved_as']);
				$image->config('quality',CL_Q_IMG_QUALITY)->resize(CL_Q_IMG_SIZE,(CL_Q_IMG_SIZE / 4 * 3),true)->save($temp['saved_to'].CL_PREFIX_THUMBNAIL2.$temp['saved_as']);
			}

// 網ログ
\Log::warning('Uploaded Files:'.$temp['saved_to'].$temp['saved_as'].' - '.\Clfunc_Common::FilesizeFormat($json['size'],1));

			$hash = serialize($json);
			$json['hval'] = $hash;
			$json['file'] = \Uri::create('getfile/download/temp/:file/:name', array('file'=>$json['file'],'name'=>$json['name']));
			$json['size'] = \Clfunc_Common::FilesizeFormat($json['size'],1);
		}
		catch (\Exception $e)
		{
			$json['error'] = $e->getMessage();
		}
		$this->response($json);
	}
}