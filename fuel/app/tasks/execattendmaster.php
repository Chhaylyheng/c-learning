<?php
namespace Fuel\Tasks;

class Execattendmaster
{
	public static function run()
	{
		$sDate = date('YmdHis');
		$aAMColumn = array('ctID','amAttendState','amName','amShort','amAbsence','amDefault','amTime','amDate');
		$aAMValue = array(
			array('',0,'欠席','欠',1,0,0,$sDate),
			array('',1,'出席','出',0,1,0,$sDate),
			array('',2,'遅刻','遅',0,0,10,$sDate),
			array('',3,'早退','早',0,0,0,$sDate),
			array('',4,'その他','他',0,0,0,$sDate)
		);

		$result = \Model_Class::getClass();
		if (count($result))
		{
			$aClass = $result->as_array();
			$query = \DB::delete('AttendState_Master')->execute();
			foreach ($aClass as $c)
			{
				$sCtID = $c['ctID'];
				foreach ($aAMValue as $aV) {
					$aV[0] = $sCtID;
					$query = \DB::insert('AttendState_Master')->columns($aAMColumn)->values($aV)->execute();
				}
			}
		}
	}
}