<div>
	<span style="color: #cc0000; font-size: 120%;"><?php echo Clfunc_Mobile::emj('WARN').__('まだ提出は完了していません。'); ?></span><br>
	<?php echo __('提出内容を確認の上、「提出する」ボタンを押してください。'); ?>
	<?php echo Clfunc_Mobile::hr(); ?>

<form action="/s/report/check/<?php echo $aReport['rbID'].Clfunc_Mobile::SesID(); ?>" method="POST">
<?php echo Clfunc_Mobile::SesID('post'); ?>

■<?php echo __('提出テキスト'); ?><br>
<div style="color: #0000CC;">
<?php echo nl2br($aInput['rpText']); ?>
</div>



<?php
$sFiles = null;
for ($i = 1; $i <= 3; $i++):
	if (isset($aInput['f'.$i]) && $aInput['f'.$i] != ''):
		$sName = $aInput['fileinfo'.$i]['name'];
		$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>'temp','file'=>$aInput['fileinfo'.$i]['file'], 'name'=>$aInput['fileinfo'.$i]['name']));
		$sSize = \Clfunc_Common::FilesizeFormat($aInput['fileinfo'.$i]['size'],1);
		$sFiles .= '<div>'.Clfunc_Mobile::emj('CLIP').'<a href="'.$sFile.'">'.$sName.'</a>('.$sSize.')</div>';
	endif;
endfor;
if ($sFiles):
?>
■<?php echo __('提出ファイル'); ?><br>
<div style="color: #0000CC;">
<?php echo $sFiles; ?>
</div>
<?php endif; ?>

<?php echo Clfunc_Mobile::hr(); ?>

<div style="text-align: center;">
	<input type="submit" name="check" value="<?php echo __('提出する'); ?>"><br>
	<input type="submit" name="back" value="<?php echo __('戻る'); ?>">
</div>
</form>
</div>

