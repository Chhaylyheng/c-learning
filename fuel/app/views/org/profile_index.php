<?php
	$errClass = array('a_name'=>'','a_pass_now'=>'','a_pass_edit'=>'','a_pass_chk'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
?>

<?php if (isset($error['profile_error'])): ?>
<p class="error-box mb16"><?php echo $error['profile_error']; ?></p>
<?php endif; ?>

<section class="pt0">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('プロフィール変更'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner">
	<?php echo Form::open(array('action'=>'/org/profile')) ; ?>
	<p class="mt16"><?php echo __('ログインID'); ?></p>
	<p class="mt12 font-blue"><?php echo $aAdmin['gaLogin']; ?></p>

	<p class="mt16"><?php echo __('氏名'); ?></p>
	<p class="mt12"><input type="text" name="a_name" value="<?php echo $a_name; ?>" size="30" maxlength="50"<?php echo $errClass['a_name']; ?>></p>
	<?php echo $errMsg['a_name']; ?>

	<p class="mt12">Timezone</p>
	<?php $region = explode('/',$a_timezone,2); ?>
	<div class="mt4">
		<select class="dropdown" id="tz-region">
		<?php foreach ($tz_region as $r): ?>
			<?php $sSel = ($r == $region[0])? ' selected':'';?>
			<option value="<?php echo $r; ?>"<?php echo $sSel; ?>><?php echo $r; ?></option>
		<?php endforeach; ?>
		</select>
		<select class="dropdown" id="tz-timezone" name="a_timezone">
		<?php foreach ($tz_list as $r => $tzl): ?>
			<?php $sDisp = ($r == $region[0])? 'block':'none'; ?>
			<optgroup label="<?php echo $r; ?>" style="display: <?php echo $sDisp; ?>;">
			<?php foreach ($tzl as $t => $v): ?>
				<?php $sSel = ($t == $a_timezone)? ' selected':'';?>
				<option value="<?php echo $t; ?>"<?php echo $sSel; ?> class="text-left"><?php echo $v; ?></option>
			<?php endforeach; ?>
			</optgroup>
			<?php endforeach; ?>
		</select>
	</div>

	<p class="button-box mt16"><button type="submit" name="mode" value="profile" class="button do na"><?php echo __('変更する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>
</div>
</section>

<section class="">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('パスワード変更'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner">
	<?php echo Form::open(array('action'=>'/org/profile')) ; ?>
	<p class="mt16"><?php echo __('現在のパスワード'); ?></p>
	<p class="mt12"><input type="password" name="a_pass_now" value="" size="30" maxlength="200"<?php echo $errClass['a_pass_now']; ?>></p>
	<?php echo $errMsg['a_pass_now']; ?>
	<p class="mt16"><?php echo __('新しいパスワード'); ?></p>
	<p class="mt12"><input type="password" name="a_pass_edit" value="" size="30" maxlength="200"<?php echo $errClass['a_pass_edit']; ?>></p>
	<?php echo $errMsg['a_pass_edit']; ?>
	<p class="mt16"><?php echo __('新しいパスワード（確認）'); ?></p>
	<p class="mt12"><input type="password" name="a_pass_chk" value="" size="30" maxlength="200"<?php echo $errClass['a_pass_chk']; ?>></p>
	<?php echo $errMsg['a_pass_chk']; ?>
	<p class="button-box mt16"><button type="submit" name="mode" value="pass" class="button do na"><?php echo __('変更する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>
</div>
</section>
