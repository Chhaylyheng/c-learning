<?php
class Controller_G_Index extends Controller_G_Base
{
	public function action_index()
	{
		$sPWH = '';
		$sSep = '';
		if ($this->aClass['dpNO'])
		{
			$sPWH .= $this->aPeriod[$this->aClass['dpNO']];
			$sSep = '/';
		}
		if ($this->aClass['ctWeekDay'])
		{
			$sPWH .= $sSep.$this->aWeekday[$this->aClass['ctWeekDay']];
			$sSep = '/';
		}
		if ($this->aClass['dhNO'])
		{
			$sPWH .= $sSep.$this->aHour[$this->aClass['dhNO']];
		}
		if ($sPWH)
		{
			$sPWH = '（'.$sPWH.'）';
		}

		# タイトル
		$sTitle = '<i class="fa fa-book fa-fw"></i>'.$this->aClass['ctName'].$sPWH;
		$this->template->set_global('pagetitle',__('アンケート'));
		$this->template->set_global('classtitle',$sTitle,false);
		$this->template->set_global('subtitle',__('講義コード').'［'.(\Clfunc_Common::getCode($this->aClass['ctCode'])).'］');

		$aQuest = null;
		$result = Model_Quest::getQuestBaseFromClass($this->aClass['ctID'],array(array('qb.qbOpen','>',0),array('qb.qbPublic','>',0)),null,array('qb.qbSort'=>'desc'));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aQ)
			{
				$aQuest[$aQ['qbID']] = $aQ;
			}
		}
		if (!is_null($aQuest))
		{
			$aPut = null;
			$result = Model_Quest::getQuestPut(array(array('qb.ctID','=',$this->aClass['ctID']),array('qp.stID','=',$this->aGuest['gtID'])));
			if (count($result))
			{
				$aPut = $result->as_array();
				foreach ($aPut as $aP)
				{
					if (array_key_exists($aP['qbID'],$aQuest))
					{
						$aQuest[$aP['qbID']]['QPut'] = $aP;
					}
				}
			}
		}


		$this->template->content = View::forge($this->vDir.DS.'index');
		$this->template->content->set('sTitle',$sTitle);
		$this->template->content->set('aQuest',$aQuest);
		$this->template->javascript = array('cl.s.quest.js');
		return $this->template;
	}

	public function action_logout()
	{
		Response::redirect('g/login');
	}

}