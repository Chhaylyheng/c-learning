<?php $sSubmit = ($bEdit)? '更新':'登録'; ?>

<?php echo Form::open(array('action'=>'/s/coop/rescheck/'.$aCCategory['ccID'].DS.$sCheck.DS.$iNO.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-bottom: 8px;">
<?php echo __('以下の内容で'.$sSubmit.'します。'); ?>
</div>

<?php if (!$bRes): ?>
<div style="margin-top: 8px;">
	<label><?php echo __('タイトル'); ?><br>
		<span style="color: blue;"><?php echo $c_title; ?></span>
	</label>
</div>
<?php endif; ?>
<?php
	if (isset($aCoop)):
		for ($i = 1; $i <= 3; $i++):
			if ($aCoop['fID'.$i]):
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aCoop['fID'.$i]));
				$sSize = \Clfunc_Common::FilesizeFormat($aCoop['fSize'.$i],1);
?>
<div style="font-size: 80%;"><?php echo \Clfunc_Mobile::emj('CLIP'); ?><a href="<?php echo $sLink; ?>"><?php echo $aCoop['fName'.$i].'('.$sSize.')'; ?></a></div>
<?php
			endif;
		endfor;
	endif;
?>
<div style="margin-top: 8px;">
	<label><?php echo __('本文'); ?><br>
		<div style="color: blue;"><?php echo nl2br(\Clfunc_Common::url2link($c_text, 0)); ?></div>
	</label>
</div>

<div style="margin-top: 8px;">
	<label><?php echo __('メール通知'); ?></label><br>
	<?php
		if (isset($aBaseCoop) && $aBaseCoop['cID'] != $aStudent['stID']):
			$bTeach = preg_match('/^[t|a]/', $aBaseCoop['cID']);
			$cName = ($aBaseCoop['atName'])? $aBaseCoop['atName']:(($aBaseCoop['ttName'])? $aBaseCoop['ttName']:(($aBaseCoop['stName'])? $aBaseCoop['stName']:$aBaseCoop['cName']));
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$sWriter = __('匿名');
				break;
				case 1:
					if ($bTeach):
						$sWriter = $cName;
					else:
						$sWriter = __('匿名');
					endif;
				break;
				case 2:
					$sWriter = $cName;
				break;
			endswitch;
	?>
	<label style="color: blue;"><input type="checkbox" name="mailr" value="1"><?php echo __('返信の通知'); ?>（<?php echo $sWriter; ?>）</label><br>
	<?php endif; ?>
	<label style="color: blue;"><input type="checkbox" name="mailt" value="1"><?php echo __('先生に通知'); ?></label><br>
	<label style="color: blue;"><input type="checkbox" name="mails" value="1"><?php echo __('学生に通知'); ?>（<?php echo __(':num名',array('num'=>$aCCategory['ccStuNum'])); ?>）</label>
</div>

<div style="text-align: center; margin-top: 8px;">
	<input type="submit" name="state" value="<?php echo __($sSubmit); ?>">
	<input type="submit" name="back" value="<?php echo __('戻る'); ?>">
</div>

<?php echo Form::close(); ?>