<?php
class Controller_S_Material extends Controller_S_Baseclass
{
	private $baseName = 'material';

	private $aMCategory = null;
	private $aMaterial = null;

	public function action_index()
	{

		$aMCategory = null;
		$result = Model_Material::getMaterialCategoryFromClass($this->aClass['ctID'],null,null,array('mcSort'=>'desc'));
		if (count($result))
		{
			$aMCategory = $result->as_array('mcID');
		}

		$aCnt = null;
		$result = Model_Material::getMaterialAlreadyCountFromStudent($this->aStudent['stID']);
		if (count($result))
		{
			$aCnt = $result->as_array('mcID');
		}

		if (!is_null($aMCategory))
		{
			foreach ($aMCategory as $sID => $aMC)
			{
				if (count($aMCategory) == 1)
				{
					Response::redirect('/s/'.$this->baseName.'/list/'.$sID);
				}
				$aMCategory[$sID]['already'] = 0;
				if (isset($aCnt[$sID]))
				{
					$aMCategory[$sID]['already'] = (int)$aCnt[$sID]['aCnt'];
				}
			}
		}

		# タイトル
		$sTitle = __('教材倉庫');
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/index');
		$this->template->content->set('aMCategory',$aMCategory);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}

	public function action_list($sID = null)
	{
		$aMCategory = null;
		$aMaterial = null;
		$aChk = self::MaterialCategoryChecker($sID);

		if (is_array($aChk))
		{
			Session::set('SES_S_ERROR_MSG',$aChk['msg']);
			Response::redirect($aChk['url']);
		}

		$aMaterial = null;
		$result = Model_Material::getMaterial(array(array('mt.mcID','=',$sID),array('mt.mPublic','=',1)),null,array('mt.mSort'=>'desc'));
		if (count($result))
		{
			$aMaterial = $result->as_array('mNO');
		}

		if ($this->iDevice == CL_DEV_MB)
		{
			$aAlr = null;
			$result = Model_Material::getMaterialAlready(array(array('mcID','=',$sID),array('stID','=',$this->aStudent['stID'])));
			if (count($result))
			{
				$aAlr = $result->as_array('mNO');
			}

			if (!is_null($aMaterial))
			{
				foreach ($aMaterial as $iNO => $aM)
				{
					$bYoutube = true;
					$urls = null;
					if ($aM['mURL'] != '')
					{
						$urls = explode("\n", $aM['mURL']);
						foreach ($urls as $v)
						{
							if ($v == '') continue;
							if (!\Clfunc_Common::createYoutubeTag($v))
							{
								$bYoutube = false;
							}
						}
					}

					$aMaterial[$iNO]['already'] = 0;
					$aMaterial[$iNO]['clurl'] = null;

					if (isset($aAlr[$iNO]))
					{
						$aMaterial[$iNO]['already'] = 1;
					}
					else if ($aM['fID'] == '' && ($aM['mURL'] == '' || $bYoutube))
					{
						try
						{
							$aInsert = array(
								'mNO' => $iNO,
								'stID' => $this->aStudent['stID'],
								'mcID' => $sID,
								'maDate' => date('YmdHis'),
							);
							$result = Model_Material::insertMaterialAlready($aInsert);
							\Session::delete('CL_STU_UNREAD_'.$this->aStudent['stID']);
						}
						catch (Exception $e)
						{
							# 既読情報書き込みなので、エラー処理いらないかな
						}
					}

					$urls = explode("\n", $aM['mURL']);
					$aMaterial[$iNO]['mURL'] = $urls;
					if (is_array($urls))
					{
						foreach ($urls as $i => $v)
						{
							$aMaterial[$iNO]['clurl'][$i] = \Clfunc_Common::ExtUrlDetectForStudent($v, $this->aStudent['stID']);
						}
					}
				}
			}
		}

		# タイトル
		$sTitle = $this->aMCategory['mcName'];
		$this->template->set_global('pagetitle',$sTitle);
		# パンくずリスト生成
		$this->aBread[] = array('link'=>DS.$this->baseName,'name'=>__('教材倉庫'));
		$this->aBread[] = array('name'=>$sTitle);
		$this->template->set_global('breadcrumbs',$this->aBread);

		$this->template->content = View::forge($this->vDir.DS.$this->baseName.'/list');
		$this->template->content->set('aMCategory',$this->aMCategory);
		$this->template->content->set('aMaterial',$aMaterial);
		$this->template->javascript = array('cl.s.'.$this->baseName.'.js');
		return $this->template;
	}


	public function action_listpiece($sID = null, $iNO = null)
	{
		$this->template = View::forge($this->vDir.DS.$this->baseName.'/list-piece');

		$aChk = self::MaterialCategoryChecker($sID);
		if (is_array($aChk))
		{
			$this->template = View::forge('piece-error');
			$this->template->set_global('sMsg',$aChk['msg']);
			return $this->template;
		}

		$aChk = self::MaterialChecker($sID,$iNO);
		if (is_array($aChk))
		{
			$this->template = View::forge('piece-error');
			$this->template->set_global('sMsg',$aChk['msg']);
			return $this->template;
		}

		$aAlr = null;
		$result = Model_Material::getMaterialAlready(array(array('mNO','=',$iNO),array('stID','=',$this->aStudent['stID'])));
		if (count($result))
		{
			$aAlr = $result->current();
		}

		$bYoutube = true;
		$urls = null;
		if ($this->aMaterial['mURL'] != '')
		{
			$urls = explode("\n", $this->aMaterial['mURL']);
			foreach ($urls as $v)
			{
				if ($v == '') continue;
				if (!\Clfunc_Common::createYoutubeTag($v))
				{
					$bYoutube = false;
				}
			}
		}

		$this->aMaterial['already'] = 0;
		$this->aMaterial['clurl'] = null;

		if (!is_null($aAlr))
		{
			$this->aMaterial['already'] = 1;
		}
		else if ($this->aMaterial['fID'] == '' && ($this->aMaterial['mURL'] == '' || $bYoutube))
		{
			try
			{
				$aInsert = array(
					'mNO' => $iNO,
					'stID' => $this->aStudent['stID'],
					'mcID' => $sID,
					'maDate' => date('YmdHis'),
				);
				$result = Model_Material::insertMaterialAlready($aInsert);
				\Session::delete('CL_STU_UNREAD_'.$this->aStudent['stID']);
			}
			catch (Exception $e)
			{
			}
		}

		$urls = explode("\n", $this->aMaterial['mURL']);
		$this->aMaterial['mURL'] = $urls;
		if (is_array($urls))
		{
			foreach ($urls as $i => $v)
			{
				$this->aMaterial['clurl'][$i] = \Clfunc_Common::ExtUrlDetectForStudent($v, $this->aStudent['stID']);
			}
		}

		$this->template->set_global('aMCategory',$this->aMCategory);
		$this->template->set_global('aMaterial',$this->aMaterial);
		return $this->template;
	}

	private function MaterialCategoryChecker($sID = null)
	{
		if (is_null($sID))
		{
			return array('msg'=>__('カテゴリ情報が送信されていません。'),'url'=>'/s/material'.$this->sesParam);
		}
		$result = Model_Material::getMaterialCategoryFromID($sID);
		if (!count($result))
		{
			return array('msg'=>__('指定されたカテゴリが見つかりません。'),'url'=>'/s/material'.$this->sesParam);
		}
		$this->aMCategory = $result->current();

		return true;
	}

	private function MaterialChecker($sMcID = null, $iMtNO = null)
	{
		if (is_null($this->aClass))
		{
			return array('msg'=>__('講義情報が確認できませんでした。'),'url'=>'/s/index');
		}
		if (is_null($sMcID) || is_null($iMtNO))
		{
			return array('msg'=>__('必要な情報が送信されていません。'),'url'=>'/s/'.$this->baseName);
		}
		$result = Model_Material::getMaterial(array(array('mt.mcID','=',$sMcID),array('mt.mNO','=',$iMtNO)));
		if (!count($result))
		{
			return array('msg'=>__('指定された記事が見つかりません。'),'url'=>'/s/'.$this->baseName);
		}
		$this->aMaterial = $result->current();

		return true;
	}

}