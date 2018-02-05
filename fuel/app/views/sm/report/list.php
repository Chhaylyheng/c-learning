<?php
	if (!is_null($aStu)):
		$aMine = $aStu[$aStudent['stID']];
		unset($aStu[$aStudent['stID']]);
		$aStu = array($aStudent['stID']=>$aMine) + $aStu;
		foreach ($aStu as $sStID => $aS):
			$sColor = ($sStID != $aStudent['stID'])? '':'color: #cc0000;';

			$aM = array('no'=>'','name'=>'','state'=>'','share'=>'','avg'=>'');
			if (isset($aS['put'])):
				$aP = $aS['put'];

				$aM['no'] = $aP['rpstNO'];
				$aM['name'] = $aP['rpstName'];
				$aM['state'] = ($aP['rpTeachPut'])? '<span style="color: #008800;">'.__('先生による提出').'</span>':(($aP['rpDate'] != CL_DATETIME_DEFAULT)? '<span style="color: #0000CC;">'.date('Y/m/d H:i',strtotime($aP['rpDate'])).'</span>':'<span>'.__('未提出').'</span>');

				$aM['share'] = '<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s">'.Clfunc_Mobile::emj('SMILE').$aP['rpComNum'].'</a>';
				$aM['avg']   = '<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s">★'.$aP['rpAvgScore'].'</a>';
			else:
				$aM['state'] = '<span class="font-gray">'.__('未提出').'</span>';
				$aM['share'] = '<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s">'.Clfunc_Mobile::emj('SMILE').'0</a>';
				$aM['avg']   = '<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s">★0</a>';
			endif;
			if (isset($aS['stu'])):
				$aM['no'] = $aS['stu']['stNO'];
				$aM['name'] = $aS['stu']['stName'];
			endif;
?>
<div>
<span style="<?php echo $sColor; ?>"><?php echo $aM['name']; ?></span><br>
┣<?php echo $aM['state']; ?><br>
<?php if ($aReport['rbShare'] == 2): ?>
┣<?php echo __('コメント数').$aM['share']; ?><br>
┗<?php echo __('平均点').$aM['avg']; ?><br>
<?php else: ?>
┗<?php echo __('コメント数').$aM['share']; ?><br>
<?php endif; ?>
</div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php
		endforeach;
	endif;
?>
