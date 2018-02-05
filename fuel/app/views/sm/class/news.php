<div>

<?php echo \Clfunc_Common::url2link(nl2br($aNews['cnBody']), 0); ?>


<?php
$sLink = null;
if ($aNews['cnChain']):
	$aU = $aNews['cnChain'];
	$sPut = ($aU['put'])? __('[済]'):'';
	if ($aU['public'] == 0):
		$sLink = '<div style="margin-top: 10px;"><span>'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</span></div>';
	else:
		if ($aU['public'] == 1):
			$sLink = '<div style="margin-top: 10px;"><a href="'.$aU['url'].'">'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</a></div>';
		else:
			if ($sPut):
				$sLink = '<div style="margin-top: 10px;"><a href="'.$aU['url'].'">'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</a></div>';
			else:
				$sLink = '<div style="margin-top: 10px;"><span>'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</span></div>';
			endif;
		endif;
	endif;
endif;

echo $sLink;
?>
</div>
