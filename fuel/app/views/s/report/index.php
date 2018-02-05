<?php if (!is_null($aReport)): ?>
<?php
foreach ($aReport as $aQ):
	$sLink = null;
	$sPath = '';
	$sSize = '';
	$sRPath = '';
	$sRSize = '';
	$aPub = array(__('締切'),'font-red');
	if ($aQ['rbPublic'] == 1):
		$sLink = 'ans';
		$aPub = array(__('公開中'),'font-blue');
		if ($aQ['rbAutoCloseDate'] != CL_DATETIME_DEFAULT):
			$aPub[2] = '～'.Clfunc_Tz::tz('n/j H:i',$tz,$aQ['rbAutoCloseDate']);
		endif;
	endif;

	if ($aQ['baseFID'] != ''):
		$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aQ['baseFID'],'mode'=>'e')).DS.$aQ['baseFExt'];
		$sSize = \Clfunc_Common::FilesizeFormat($aQ['baseFSize'],1);
		$sIcon = 'paperclip';
		$sThumb = null;
		if ($aQ['baseFFileType'] == 2):
			$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aQ['baseFID'],'mode'=>'t'));
			$sIcon = 'film';
		endif;
		$sFile = '<i class="fa fa-'.$sIcon.'"></i> <a href="'.$sPath.'" target="_blank">'.$aQ['baseFName'].'</a> ('.$sSize.')';
	endif;

	if ($aQ['resultFID'] != ''):
		$sRPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aQ['resultFID'],'mode'=>'e')).DS.$aQ['resultFExt'];
		$sRSize = \Clfunc_Common::FilesizeFormat($aQ['resultFSize'],1);
		$sRIcon = 'paperclip';
		$sRThumb = null;
		if ($aQ['resultFFileType'] == 2):
			$sRThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aQ['resultFID'],'mode'=>'t'));
			$sRIcon = 'film';
		endif;
		$sRFile = '<i class="fa fa-'.$sRIcon.'"></i> <a href="'.$sRPath.'" target="_blank">'.$aQ['resultFName'].'</a> ('.$sRSize.')';
	endif;

	$pIcon = '<i class="fa fa-exclamation-circle mr4 font-red"></i>';
	$pDate = '<span class="font-red">'.__('未提出').'</span>';
	$pText = null;
	$pFiles = null;
	if (isset($aQ['RPut'])):
		$aP = $aQ['RPut'];

		if ($aP['rpTeachPut'])
		{
			$pIcon = '<i class="fa fa-check-circle mr4 font-green"></i>';
			$pDate = '<span class="font-green">'.__('先生による提出').'</span>';
			$pText = '';
		}
		elseif ($aP['rpDate'] != CL_DATETIME_DEFAULT)
		{
			$pIcon = '<i class="fa fa-check-circle mr4 font-green"></i>';
			$pDate = '<span class="font-green">'.__('提出済み').'('.Clfunc_Tz::tz('Y/m/d H:i',$tz,$aP['rpDate']).')</span>';
			$pText = ($aP['rpText'])? '<dt>'.__('提出テキスト').'</dt><dd>'.nl2br(\Clfunc_Common::url2link($aP['rpText'],0)).'</dd>':'';
			$pFiles = '';
			for ($i = 1; $i <= 3; $i++):
				if (isset($aP['fID'.$i])):
					$pFiles .= '<i class="fa fa-paperclip mr4 ml2"></i><a href="'.\Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aP['fID'.$i],'mode'=>'e')).DS.$aP['fExt'.$i].'" target="_blank">'.$aP['fName'.$i].'</a> ('.\Clfunc_Common::FilesizeFormat($aP['fSize'.$i],1).')<br>';
				endif;
			endfor;
			if ($pFiles):
				$pFiles = '<dt>'.__('提出ファイル').'</dt><dd>'.$pFiles.'</dd>';
			endif;
		}
		else
		{
			$pIcon = '<i class="fa fa-exclamation-circle mr4 font-red"></i>';
			$pDate = '<span class="font-red">'.__('未提出').'</span>';
		}

		$rScore = null;
		$rComment = null;
		$rFiles = null;

		if ($aQ['rbRatePublic']):
			$rScore = ($aP['rpScore'])? '<dt>'.__('評価').'</dt><dd><span class="font-size-300 font-blue line-height-14">'.$aRateMaster[$aP['rpScore']]['rrName'].'</span></dd>':'<dt>'.__('評価').'</dt><dd><span class="text-center font-size-120">'.__('未評価').'</span></dd>';
			$rComment = ($aP['rpComment'])? '<dt>'.__('先生コメント').'</dt><dd>'.nl2br(\Clfunc_Common::url2link($aP['rpComment'],0)).'</dd>':'';
			$rFiles = '';
			for ($i = 1; $i <= 3; $i++):
				if (isset($aP['rID'.$i])):
					$rFiles .= '<i class="fa fa-paperclip mr4"></i><a href="'.\Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aP['rID'.$i],'mode'=>'e')).DS.$aP['rExt'.$i].'" target="_blank">'.$aP['rName'.$i].'</a> ('.\Clfunc_Common::FilesizeFormat($aP['rSize'.$i],1).')<br>';
				endif;
			endfor;
			if ($rFiles):
				$rFiles = '<dt>'.__('評価ファイル').'</dt><dd>'.$rFiles.'</dd>';
			endif;
		endif;
	endif;
?>

<div class="info-box mb16 mt0 report-list">
<h2 class="font-size-160">
	<?php echo $pIcon.$aQ['rbTitle']; ?>
	<br><span class="font-size-80 <?php echo $aPub[1]; ?>"><?php echo $aPub[0]; ?></span>
	<?php if (isset($aPub[2])): ?><span class="font-size-80 <?php echo $aPub[1]; ?>"><?php echo $aPub[2]; ?></span><?php endif; ?>
</h2>

<p class="mt8 mb8 font-size-120"><?php echo $pDate; ?></p>

<?php if (isset($aQ['RPut'])): ?>
<dl class="report-put mb8">
<?php
	echo $rScore;
	echo $pFiles;
	echo $pText;
	echo $rComment;
	echo $rFiles;
?>
</dl>
<?php endif; ?>

<div class="report-option mb8">
<?php if ($aQ['rbPublic'] == 1 && (!isset($aQ['RPut']) || $aQ['RPut']['rpScore'] == 0)): ?>
<a href="/s/report/put/<?php echo $aQ['rbID']; ?>" class="button na do width-auto ml4 font-size-120 va-top" style="padding: 8px 8px;"><?php echo (isset($aQ['RPut']) && ($aQ['RPut']['rpTeachPut'] || $aQ['RPut']['rpDate'] != CL_DATETIME_DEFAULT))?  __('レポート再提出'):__('レポート提出'); ?></a>
<?php endif; ?>
<?php if ($aQ['rbShare'] > 0): ?>

<a href="/s/report/list/<?php echo $aQ['rbID']; ?>" class="report-rate button na confirm width-auto ml8 va-top font-size-120" style="padding: 8px 8px;">
	<?php echo __('共有板'); ?>
</a>

<a href="/s/report/shareboard/<?php echo $aQ['rbID'].DS.$aStudent['stID']; ?>/s" class="report-rate button na default width-auto ml8 va-top" style="padding: 4px 8px;">
	<?php if ($aQ['rbShare'] == 2): ?>
	<i class="fa fa-star mr4"></i><span class="font-size-160"><?php echo (isset($aQ['RPut']['rpAvgScore']))? $aQ['RPut']['rpAvgScore']:0; ?></span>
	<?php endif; ?>
	<i class="fa fa-comments mr4"></i><span class="font-size-160"><?php echo (isset($aQ['RPut']['rpComNum']))? $aQ['RPut']['rpComNum']:0; ?></span>
</a>

<?php endif; ?>
</div>

<dl class="report-put mb8">

<dt><?php echo __('内容/備考'); ?></dt>
<dd>
	<?php echo nl2br(\Clfunc_Common::url2link($aQ['rbText'],400)); ?><br>
<?php
if ($sPath):
switch ($aQ['baseFFileType']):
	case 2:	# 映像の場合
?>
<video style="width: 400px; max-width: 100%;"  controls="controls" preload="none" src="<?php echo $sPath; ?>" poster="<?php echo $sThumb; ?>"></video>
<?php
	break;
	case 1:
?>
<img style="width: 400px; max-width: 100%;" src="<?php echo $sPath; ?>" alt="<?php echo $aQ['baseFName'].' ('.$sSize.')'; ?>">
<?php
	break;
	default:
		echo $sFile;
	break;
endswitch;
?>
</dd>
<?php endif; ?>

<?php if ($sRPath): ?>
<dt><?php echo __('結果ファイル'); ?></dt>
<dd>
<?php
switch ($aQ['resultFFileType']):
	case 2:	# 映像の場合
?>
<video style="width: 400px; max-width: 100%;" controls="controls" preload="none" src="<?php echo $sRPath; ?>" poster="<?php echo $sRThumb; ?>"></video>
<?php
	break;
	case 1:
?>
<img style="width: 400px; max-width: 100%;" src="<?php echo $sRPath; ?>" alt="<?php echo $aQ['baseFName'].' ('.$sRSize.')'; ?>">
<?php
	break;
	default:
		echo $sRFile;
	break;
endswitch;
?>
</dd>
<?php endif; ?>
</dl>
</div>

<?php endforeach; ?>
<?php else: ?>

<div class="info-box mt8 text-center">
	<p><?php echo __('提出可能なレポートはありません。'); ?></p>
</div>

<?php endif; ?>
