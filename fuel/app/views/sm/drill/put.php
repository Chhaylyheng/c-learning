<?php
	if (!is_null($aPut)):
		foreach ($aPut as $aP):
			$iTime = strtotime($aP['dpDate']);
?>
<div>
	<?php echo Clfunc_Mobile::emj('BOOK'); ?> <?php echo $aP['dpAvg'].'%'; ?>
	<span style="font-size: 80%;"><?php echo Clfunc_Mobile::emj('CLOCK'); ?><?php echo date('Y/m/d H:i',$iTime)?></span>
</div>
<?php
		endforeach;
	endif;
?>
