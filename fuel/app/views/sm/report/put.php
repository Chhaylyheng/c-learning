<?php
$aR = $aReport;

$sFile = null;
if ($aReport['baseFID'] != ''):
	$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aR['baseFID'],'mode'=>'e'));
	$sSize = \Clfunc_Common::FilesizeFormat($aR['baseFSize'],1);
	$sThumb = null;
	if ($aReport['baseFFileType'] == 1):
		$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aR['baseFID'],'mode'=>'tm'));
	endif;
	$sFile = __('添付ファイル').'<br>'.Clfunc_Mobile::emj('CLIP').'<a href="'.$sPath.'" target="_blank">'.$aR['baseFName'].'（'.$sSize.'）</a></p>';
endif;

?>

<?php if (isset($error)): ?>
	<div style="color: #CC0000; margin-bottom: 5px;"><?php echo Clfunc_Mobile::emj('WARN'); ?><?php echo __('提出テキストを入力してください。') ?></div>
<?php endif; ?>

<div>
<form action="/s/report/put/<?php echo $aR['rbID'].Clfunc_Mobile::SesID(); ?>" method="POST">
<?php echo Clfunc_Mobile::SesID('post'); ?>

■<?php echo __('内容/備考'); ?><br>
<div style="color: #006600;">
		<?php echo nl2br(\Clfunc_Common::url2link($aR['rbText'],false)); ?><br>
<?php
switch ($aR['baseFFileType']):
	case 1:
?>
<img style="max-width: 100%;" src="<?php echo $sThumb; ?>" alt="<?php echo $aR['baseFName'].'（'.$sSize.'）'; ?>">
<?php
	break;
	default:
		echo $sFile;
	break;
endswitch;
?>
</div>

<?php echo Clfunc_Mobile::hr(); ?>

■<?php echo __('提出テキスト'); ?><br>
<textarea name="rpText" rows="6" style="width: 100%;"><?php echo (isset($aInput['rpText']))? $aInput['rpText']:''; ?></textarea>
<br><br>

■<?php echo __('提出ファイル'); ?><br>
<?php
for ($i = 1; $i <= 3; $i++):
	if (isset($aInput['f'.$i]) && $aInput['f'.$i] != ''):
		$sName = $aInput['fileinfo'.$i]['name'];
		$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>'temp','file'=>$aInput['fileinfo'.$i]['file'], 'name'=>$aInput['fileinfo'.$i]['name']));
		$sSize = \Clfunc_Common::FilesizeFormat($aInput['fileinfo'.$i]['size'],1);
?>
<div><?php echo Clfunc_Mobile::emj('CLIP'); ?><a href="<?php echo $sFile; ?>"><?php echo $sName; ?></a>(<?php echo $sSize; ?>)</div>
<input type="hidden" name="<?php echo 'f'.$i; ?>" value="<?php echo htmlspecialchars(serialize($aInput['fileinfo'.$i])); ?>">
<?php else: ?>
<input type="hidden" name="<?php echo 'f'.$i; ?>" value="">
<?php endif; ?>
<?php endfor; ?>
<span style="color: gray; font-size: 80%;"><?php echo __('※提出ファイルは携帯電話からアップロードすることはできません。'); ?></span><br>

<?php echo Clfunc_Mobile::hr(); ?>
<div style="text-align: center;">
	<input type="submit" value="<?php echo __('提出確認'); ?>" name="sub_state">
</div>
</form>

</div>
