<?php
	if (!is_null($aCCategory)):
		foreach ($aCCategory as $sID => $aC):
			$sIcon = 'BOOK';
			$sNew = null;
			if ($aC['ccItemNum'] > $aC['already']):
				$sIcon = 'NEW';
				$sNew = '<span style="color: #cc0000;">['.((int)$aC['ccItemNum'] - (int)$aC['already']).']</span>';
			endif;
?>
<div>
	<?php echo Clfunc_Mobile::emj($sIcon); ?><a href="/s/coop/thread/<?php echo $aC['ccID'].Clfunc_Mobile::SesID(); ?>"><?php echo $aC['ccName']; ?></a><?php echo $sNew; ?>(<?php echo $aC['ccItemNum']; ?>)
</div>
<?php
		endforeach;
	endif;
?>
