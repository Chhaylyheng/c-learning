<div style="text-align:center;" align="center"><span style="font-size:medium;"><?php echo __('初回ログインパスワードの変更'); ?></span></div>

<div style="margin-top: 5px;">
<?php echo __('現在利用されているパスワードは初期設定パスワードです。'); ?><br><?php echo __('パスワードを変更してください。'); ?>
</div>

<?php echo Form::open(array('action'=>'/s/password/first'.Clfunc_Mobile::SesID(),'method'=>'post')); ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<?php if (isset($error['pass'])): ?>
	<div style="margin-top: 5px;"><span style="color:#CC0000;"><?php echo $error['pass'] ?></span></div>
<?php endif; ?>

<?php
	$errClass = array('pre_pass'=>'','pre_passchk'=>'');
	$errMsg = $errClass;

	foreach ($errClass as $k => $v):
		if (isset($error[$k])):
			$errMsg[$k] = '<div style="color:#CC0000;">'.$error[$k].'</div>';
		endif;
	endforeach;
?>

<div style="margin-top: 5px;">
	<label><?php echo __('新しいパスワード'); ?><br>
		<input type="password" name="pre_pass" value="" maxlength="32">
	</label>
</div>
<?php echo $errMsg['pre_pass']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('パスワード（確認）'); ?><br>
		<input type="password" name="pre_passchk" value="" maxlength="32">
	</label>
</div>
<?php echo $errMsg['pre_passchk']; ?>

<div style="">
<?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?>
</div>

<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('パスワードの変更'); ?>" name="sub_state"></div>

<?php echo Form::close(); ?>