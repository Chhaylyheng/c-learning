<?php
	if (!is_null($aDrill)):
		foreach ($aDrill as $aD):
			$fAvg = '0%';
			$iPut = 0;
			$sDate = __('未実施');
			if (isset($aD['dpDate'])):
				$sDate = date('Y/m/d H:i', strtotime($aD['dpDate']));
				$fAvg = round(((int)$aD['dpTotal'] / (int)$aD['dpNum']),1).'%';
				$iPut = $aD['dpNum'];
			endif;
?>
<div>
	<?php echo Clfunc_Mobile::emj('PENCIL'); ?><a href="/s/drill/ans/<?php echo $aD['dcID'].DS.$aD['dbNO'].Clfunc_Mobile::SesID(); ?>"><?php echo $aD['dbTitle']; ?></a><br>
	┣<?php echo Clfunc_Mobile::emj('BOOK'); ?><a href="/s/drill/put/<?php echo $aD['dcID'].DS.$aD['dbNO'].Clfunc_Mobile::SesID(); ?>"><?php echo $iPut.' ('.$fAvg.')'; ?></a><br>
	┗<?php echo Clfunc_Mobile::emj('CLOCK'); ?><?php echo $sDate; ?><br>
</div><br>
<?php
		endforeach;
	endif;
?>
