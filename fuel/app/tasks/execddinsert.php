<?php
namespace Fuel\Tasks;

class Execddinsert
{
	public static function run()
	{
		$sDate = date('YmdHis');
		$aPeriodCol = array('cmKCode','dmNO','dpNO','dpName','dpStartDate','dpEndDate','dpDate');
		$aPeriodVal = array(
			array('','',1,'前期','04-01','09-30',$sDate),
			array('','',2,'後期','10-01','03-31',$sDate),
			array('','',3,'通期','04-01','03-31',$sDate)
		);
		$aHourCol = array('cmKCode','dmNO','dhNO','dhName','dhStartTime','dhEndTime','dhDate');
		$aHourVal = array(
				array('','',1,'1限','09:00:00','10:30:00',$sDate),
				array('','',2,'2限','10:40:00','12:10:00',$sDate),
				array('','',3,'3限','13:00:00','14:30:00',$sDate),
				array('','',4,'4限','14:40:00','16:10:00',$sDate),
				array('','',5,'5限','16:20:00','17:50:00',$sDate),
				array('','',6,'6限','18:00:00','19:30:00',$sDate),
				array('','',7,'7限','19:40:00','21:10:00',$sDate)
		);

		$result = \Model_College::getDeptList();
		if (count($result))
		{
			foreach ($result as $r)
			{
				$aPVal = $aPeriodVal;
				for($i = 0; $i < count($aPVal); $i++)
				{
					$aPVal[$i][0] = $r['cmKCode'];
					$aPVal[$i][1] = $r['dmNO'];
				}
				$oRes = \Model_College::insertPeriod($r['cmKCode'],$r['dmNO'],$aPeriodCol,$aPVal);
				$aHVal = $aHourVal;
				for($i = 0; $i < count($aHVal); $i++)
				{
					$aHVal[$i][0] = $r['cmKCode'];
					$aHVal[$i][1] = $r['dmNO'];
				}
				$oRes = \Model_College::insertHour($r['cmKCode'],$r['dmNO'],$aHourCol,$aHVal);
			}
		}
	}
}