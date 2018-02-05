<?php
class ClFunc_UnreadCount
{

	public $aAssistant = null;
	public $aTeacher = null;
	public $aStudent = null;
	public $sID = null;
	public $bStu = false;
	public $aClass = null;
	public $sCtID = null;
	public $iMaterial = 0;
	public $iCoop = 0;
	public $iContact = 0;

	public function __constract()
	{
	}

	public function setStudent($aS)
	{
		$this->aStudent = $aS;
		$this->sID = $aS['stID'];
		$this->bStu = true;
	}
	public function setAssistant($aA)
	{
		$this->aAssistant = $aA;
		$this->sID = $aA['atID'];
	}
	public function setTeacher($aT)
	{
		$this->aTeacher = $aT;
		$this->sID = $aT['ttID'];
	}

	public function setClass($aC)
	{
		$this->aClass = $aC;
		$this->sCtID = $aC['ctID'];
	}

	# 未読教材倉庫
	public function getMaterial()
	{
		$aMCategory = null;
		$result = Model_Material::getMaterialCategoryFromClass($this->sCtID,null,null,array('mcSort'=>'desc'));
		if (count($result))
		{
			$aMCategory = $result->as_array('mcID');
		}

		$result = Model_Material::getMaterialAlreadyCountFromStudent($this->sID);
		if (count($result))
		{
			$aCnt = $result->as_array('mcID');
		}

		$iCount = 0;
		if (!is_null($aMCategory))
		{
			foreach ($aMCategory as $sID => $aMC)
			{
				if (isset($aCnt[$sID]))
				{
					if ($aMC['mcPubNum'] > $aCnt[$sID]['aCnt'])
					{
						$iCount += ((int)$aMC['mcPubNum'] - (int)$aCnt[$sID]['aCnt']);
					}
				}
				else
				{
					$iCount += (int)$aMC['mcPubNum'];
				}
			}
		}
		$this->iMaterial = $iCount;
		return $this->iMaterial;
	}

	# 未読協働板
	public function getCoop()
	{
		$aCCategory = null;
		$result = Model_Coop::getCoopCategoryFromClass($this->sCtID,null,null,array('ccSort'=>'desc'));
		if (count($result))
		{
			$aCCategory = $result->as_array('ccID');
		}
		$aStuCoop = null;
		if ($this->bStu)
		{
			$result = Model_Coop::getCoopStudents(array(array('stID','=',$this->sID)));
			if (count($result))
			{
				$aStuCoop = $result->as_array('ccID');
			}
		}
		$aCnt = null;
		$result = Model_Coop::getCoopAlreadyCountFromUser($this->sID);
		if (count($result))
		{
			$aCnt = $result->as_array('ccID');
		}

		$iCount = 0;
		if (!is_null($aCCategory))
		{
			foreach ($aCCategory as $sID => $aMC)
			{
				if ($this->bStu && !isset($aStuCoop[$sID]))
				{
					unset($aCCategory[$sID]);
					continue;
				}
				if (isset($aCnt[$sID]))
				{
					$iCount += ((int)$aMC['ccItemNum'] - (int)$aCnt[$sID]['aCnt']);
				}
				else
				{
					$iCount += (int)$aMC['ccItemNum'];
				}
			}
		}
		$this->iCoop = $iCount;
		return $this->iCoop;
	}

	# 未読相談取得
	public function getContact()
	{
		if ($this->bStu)
		{
			$result = Model_Contact::getContact($this->sCtID,$this->sID,array(array('co.coTeach','=',1),array('co.coRead','=',0)));
		}
		else
		{
			$result = Model_Contact::getContact($this->sCtID,null,array(array('co.coTeach','=',0),array('co.coRead','=',0)));
		}
		$this->iContact = count($result);
		return $this->iContact;
	}

	# 講義内未読取得
	public function getClassCount()
	{
		$iCount = 0;
		if ($this->bStu)
		{
			$iCount += $this->getMaterial();
		}
		$iCount += $this->getCoop();
		$iCount += $this->getContact();
		return $iCount;
	}

	# 所属講義の全てのカウント
	public function getUserCount()
	{
		$iCount = 0;
		if ($this->bStu)
		{
			$result = Model_Class::getStudentPosition($this->sID,1);
		}
		elseif (!is_null($this->aAssistant))
		{
			$result = Model_Class::getAssistantPosition($this->sID);
		}
		elseif (!is_null($this->aTeacher))
		{
			$result = Model_Class::getTeacherPosition($this->sID);
		}
		else
		{
			return $iCount;
		}

		if (!count($result)) {
			return $iCount;
		}
		foreach ($result as $aC)
		{
			$this->setClass($aC);
			$iCount += $this->getClassCount();
		}
		return $iCount;
	}

}
