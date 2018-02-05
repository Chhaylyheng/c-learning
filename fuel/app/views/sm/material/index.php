<?php
	if (!is_null($aMCategory)):
		foreach ($aMCategory as $sID => $aC):
			$sIcon = 'BOOK';
			$sNew = null;
			if ($aC['mcPubNum'] > $aC['already']):
				$sIcon = 'NEW';
				$sNew = '<span style="color: #cc0000;">['.($aC['mcPubNum'] - $aC['already']).']</span>';
			endif;
?>
<div>
	<?php echo Clfunc_Mobile::emj($sIcon); ?><a href="/s/material/list/<?php echo $aC['mcID'].Clfunc_Mobile::SesID(); ?>"><?php echo $aC['mcName']; ?></a><?php echo $sNew; ?>(<?php echo $aC['mcPubNum']; ?>)
</div>
<?php
		endforeach;
	endif;
?>
