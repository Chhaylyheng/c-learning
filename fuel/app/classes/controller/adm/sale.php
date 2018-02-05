<?php
class Controller_Adm_Sale extends Controller_Adm_Base
{
	public function action_index()
	{
		# パンくずリスト生成
		$this->template->set_global('breadcrumbs',array(array('name'=>'販売履歴')));

		$sY = date('Y');
		$sM = 0;

		$aInput = Input::get(null,false);
		if ($aInput)
		{
			$sY = $aInput['year'];
			$sM = $aInput['month'];
		}

		if ($sM == '0' || $sM == '全')
		{
			$sM = '0';
			$sTitle = '（'.$sY.'年全月）';
			$aW = array($sY.'-01-01',$sY.'-12-31');
		}
		else
		{
			$sTitle = '（'.$sY.'年'.$sM.'月）';
			$aW = array(date('Y-m-01',strtotime($sY.'-'.$sM.'-01')),date('Y-m-t',strtotime($sY.'-'.$sM.'-01')));
		}

		# ページタイトル生成
		$this->template->set_global('pagetitle','販売履歴'.$sTitle);

		$aSale = array(
			'credit' => array(),
			'bank' => array(),
		);
		$aSum = array(
			'credit' => array(
				'price'  => 0,
				'point'  => 0,
				'tax'    => 0,
			),
			'bank' => array(
				'price'  => 0,
				'point'  => 0,
				'tax'    => 0,
			),
		);
		$aAnd = array(
			array('ph.bPayDate','between',$aW),
		);
		$result = Model_Payment::getPaymentHistory($aAnd,null,array('ph.bPayDate'=>'desc'));
		if (count($result))
		{
			$aTemp = $result->as_array();
			foreach ($aTemp as $aS)
			{
				if ($aS['billing'] == 1)
				{
					$aSale['credit'][] = $aS;
					$aSum['credit']['price'] += (int)$aS['price'];
					$aSum['credit']['point'] += (int)$aS['point'];
					$aSum['credit']['tax']   += (int)$aS['tax'];
				}
				else
				{
					$aSale['bank'][] = $aS;
					$aSum['bank']['price'] += (int)$aS['price'];
					$aSum['bank']['point'] += (int)$aS['point'];
					$aSum['bank']['tax']   += (int)$aS['tax'];
				}
			}
		}

		$this->template->content = View::forge('adm/sale_index');
		$this->template->content->set('sY',$sY);
		$this->template->content->set('sM',$sM);
		$this->template->content->set('aSale',$aSale);
		$this->template->content->set('aSum',$aSum);
		$this->template->javascript = array('cl.adm.sale.js');
		return $this->template;
	}
}