<?php
	if (!is_null($aDCategory)):
		foreach ($aDCategory as $sID => $aC):
?>
<div>
	<a href="/s/drill/list/<?php echo $aC['dcID'].Clfunc_Mobile::SesID(); ?>"><?php echo $aC['dcName']; ?></a>(<?php echo $aC['dcPubNum']; ?>)
</div>
<?php
		endforeach;
	endif;
?>

