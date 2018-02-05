<?php
if (!is_null($aMaterial)):
	foreach ($aMaterial as $iNO => $aM):
		$sNew = (!$aM['already'])? 'NEW':'MEMO';
		$sPath = '';
		$sSize = '';
		if ($aM['fID'] != ''):
			if (!$aM['already']):
				$sPath = \Uri::create('getfile/s3file/:fid/m/:mno/:sid',array('fid'=>$aM['fID'],'mno'=>$iNO,'sid'=>$aStudent['stID']));
			else:
				$sPath = \Uri::create('getfile/s3file/:fid',array('fid'=>$aM['fID']));
			endif;
			$sSize = \Clfunc_Common::FilesizeFormat($aM['fSize'],1);
			$sFile = \Clfunc_Mobile::emj('CLIP').'<a href="'.$sPath.Clfunc_Mobile::SesID().'" target="_blank">'.$aM['fName'].'（'.$sSize.'）</a>';
		endif;
?>
<div style="background-color: #CC0000; color: #FFFFFF; padding: 2px 0; margin-top: 5px;" id="m<?php echo $iNO; ?>"><?php echo \Clfunc_Mobile::emj($sNew).$aM['mTitle']; ?></div>
<div><?php echo \Clfunc_Mobile::emj('CLOCK').date('Y/n/d H:i',strtotime($aM['mDate'])); ?></div>
<?php if ($sPath): ?>
<div style="margin-top: 5px;"><?php echo $sFile; ?></div>
<?php endif; ?>
<?php
if (is_array($aM['mURL'])):
	foreach ($aM['mURL'] as $i => $v):
		if (!$v) continue;
		if (isset($aM['clurl'][$i]) && $aM['clurl'][$i]):
			$aU = $aM['clurl'][$i];

			if (!$aM['already'] && $aM['fID'] == ''):
				$sURL = \Uri::create('getfile/cllink/:mno/:sid/:link',array('mno'=>$iNO,'sid'=>$aStudent['stID'],'link'=>base64_encode($aU['url'].'/mat')));
			else:
				$sURL = $aU['url'].'/mat';
			endif;

			$sPut = ($aU['put'])? __('['.__('済').']'):'';
			if ($aU['public'] == 0):
?>
<div style="margin-top: 5px;"><span><?php echo \Clfunc_Mobile::emj('KEITAI').$aU['title'].$sPut; ?></span></div>
<?php elseif ($aU['public'] == 1): ?>
<div style="margin-top: 5px;"><a href="<?php echo $sURL; ?>"><?php echo \Clfunc_Mobile::emj('KEITAI').$aU['title'].$sPut; ?></a></div>
<?php
			else:
				if ($aU['put']):
?>
		<div style="margin-top: 5px;"><a href="<?php echo $sURL; ?>"><?php echo \Clfunc_Mobile::emj('KEITAI').$aU['title'].$sPut; ?></a></div>
<?php 	else: ?>
		<div style="margin-top: 5px;"><span><?php echo \Clfunc_Mobile::emj('KEITAI').$aU['title'].$sPut; ?></span></div>
<?php
				endif;
			endif;
		else:
			$sURL = null;
			if (!$aM['already'] && $aM['fID'] == ''):
				$sURL = \Uri::create('getfile/externallink/:mno/:eno/:sid',array('mno'=>$iNO, 'eno'=>$i,'sid'=>$aStudent['stID']));
			else:
				$sURL = \Uri::create('getfile/externallink/:mno/:eno',array('mno'=>$iNO, 'eno'=>$i));
			endif;
?>
<div style="margin-top: 5px;"><?php echo \Clfunc_Mobile::emj('KEITAI'); ?><a href="<?php echo $sURL; ?>"><?php echo $v; ?></a></div>
<?php
		endif;
	endforeach;
endif;
if ($aM['mText']):
?>
<div style="margin-top: 5px;"><?php echo nl2br($aM['mText']); ?></div>
<?php
endif;
endforeach;
endif;
