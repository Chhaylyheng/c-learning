<?php
$dir = (isset($dir))? $dir:'';
if (preg_match("/DoCoMo/", $_SERVER['HTTP_USER_AGENT']))
{
	header('Content-Type: application/xhtml+xml');
}
echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo View::forge('template_header_m'); ?>
</head>
<body>
<div style="font-size:x-small;">
<a name="top" id="top"></a>

<?php if (isset($aClassNews) && !is_null($aClassNews)): ?>
<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_NEWS): ?>
<?php foreach ($aClassNews as $aN): ?>
<?php
$sLink = null;
if ($aN['cnChain']):
	$aU = $aN['cnChain'];

	$aAnc = explode('#', $aU['url']);
	$sURL = $aAnc[0];
	$sAnc = (isset($aAnc[1]))? '#'.$aAnc[1]:null;

	$sPut = ($aU['put'])? __('[済]'):'';
	if ($aU['public'] == 0):
		$sLink = '<span>'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</span>';
	else:
		if ($aU['public'] == 1):
			$sLink = '<a href="'.$sURL.Clfunc_Mobile::SesID().$sAnc.'">'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</a>';
		else:
			if ($sPut):
				$sLink = '<a href="'.$sURL.Clfunc_Mobile::SesID().$sAnc.'">'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</a>';
			else:
				$sLink = '<span>'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</span>';
			endif;
		endif;
	endif;
endif;
?>

<?php $sBHead = mb_strimwidth($aN['cnBody'],0,60,'…','UTF-8'); ?>
<marquee behavior="scroll" scrolldelay="30" style="background-color: #cc00000; margin-bottom: 1px; padding: 2px 0; font-size: 80%; white-space: norap; width: 100%; text-overflow: ellipsis;"><a href="/s/news/detail/<?php echo $aN['no']; ?><?php echo Clfunc_Mobile::SesID(); ?>" style="color: white;"><?php echo $sBHead; ?></a><?php echo $sLink; ?></marquee>
<?php endforeach; ?>
<div style="margin-bottom: 5px;"></div>
<?php endif; ?>
<?php endif; ?>

<?php if (isset($pagetitle)): ?>
<?php if (isset($subtitle)): ?>
<?php
	elseif (!is_null($aClass) && (isset($iALL) && !$iALL)):
		$sPWH = '';
		$sSep = '';
		if ($aClass['dpNO'])
		{
			$sPWH .= $aPeriod[$aClass['dpNO']];
			$sSep = '/';
		}
		if ($aClass['ctWeekDay'])
		{
			$sPWH .= $sSep.$aWeekDay[$aClass['ctWeekDay']];
			$sSep = '/';
		}
		if ($aClass['dhNO'])
		{
			$sPWH .= $sSep.$aHour[$aClass['dhNO']];
		}
		if ($sPWH)
		{
			$sPWH = '（'.$sPWH.'）';
		}
?>
	<div style="text-align:center; font-size: 80%;" align="center"><?php echo $aClass['ctName'].$sPWH; ?></div>
	<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<div style="text-align:center; font-size: 80%;" align="center">
<?php if (isset($classtitle)): ?>
	<?php echo $classtitle; ?>
<?php else: ?>
	<?php echo $pagetitle; ?>
<?php endif; ?>
</div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<?php if (isset($ses['SES_S_ERROR_MSG'])): ?>
<div style="color: #CC0000;"><?php echo nl2br($ses['SES_S_ERROR_MSG']); ?></div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php Session::delete('SES_S_ERROR_MSG'); ?>
<?php endif; ?>
<?php if (isset($ses['SES_S_NOTICE_MSG'])): ?>
<div style="color: #008800;"><?php echo nl2br($ses['SES_S_NOTICE_MSG']); ?></div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php Session::delete('SES_S_NOTICE_MSG'); ?>
<?php endif; ?>

<?php if (isset($aCustomBtn)): ?>
<div style="text-align:center; font-size: 80%;" align="center">
	<?php
	foreach ($aCustomBtn as $aB):
		if ($aClass['ctStatus'] && $aB['show'] == -1) continue;
		if (!$aClass['ctStatus'] && $aB['show'] == 1) continue;
	?>
		<a href="<?php echo $aB['url']; ?>"><?php echo $aB['name']; ?></a><br>
	<?php endforeach; ?>
</div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<div>
<?php echo $content; ?>
</div>

<?php
if (isset($aCustomMenu)):
	echo Clfunc_Mobile::hr();
	$iM = 0;
	foreach ($aCustomMenu as $aCM):
		if ($aClass['ctStatus'] && $aCM['show'] == -1) continue;
		if (!$aClass['ctStatus'] && $aCM['show'] == 1) continue;
		$sIcon = (isset($aCM['icon']))? '<i class="fa '.$aCM['icon'].' mr0"></i> ':'';
		$sOption = '';
		$sCls = '';
		if (isset($aCM['option'])):
			foreach ($aCM['option'] as $sK => $sV):
				if ($sK == 'class'):
					$sCls .= ' '.$sV;
					continue;
				endif;
				$sOption .= ' '.$sK.'="'.$sV.'"';
			endforeach;
		endif;
		$iM++;
?>
	<div style="font-size: 80%;"><a href="<?php echo $aCM['url']; ?>"><?php echo $aCM['name']; ?></a></div>
<?php
	endforeach;
endif;
?>


<?php if (isset($breadcrumbs)): ?>
<?php krsort($breadcrumbs); ?>
<?php echo Clfunc_Mobile::hr(); ?>
	<?php foreach ($breadcrumbs as $i => $bm): ?>
		<?php if (isset($bm['link'])): ?>
			<div style="font-size: 80%;"><?php echo Clfunc_Mobile::emj($i+1); ?><a href="/s<?php echo $bm['link'].Clfunc_Mobile::SesID(); ?>" accesskey="<?php echo $i+1; ?>"><?php echo $bm['name']; ?></a></div>
		<?php endif;?>
	<?php endforeach; ?>
	<div style="font-size: 80%;"><?php echo Clfunc_Mobile::emj('0'); ?><a href="/s/index<?php echo Clfunc_Mobile::SesID(); ?>" accesskey="0"><?php echo __('トップに戻る'); ?></a></div>
<?php endif; ?>

<?php echo $footer; ?>
</div>
</body>
</html>
