<h1 style="text-align:center;" align="center"><?php echo Asset::img($sLogo.'.gif',array('alt'=>CL_SITENAME)); ?></h1>
<?php echo View::forge('selectlang_m'); ?>

<div style="text-align:center; margin-top: 5px;" align="center"><span style="font-size:medium;"><?php echo __('学生ログイン'); ?></span></div>

<?php echo Form::open(array('action'=>'/s/login/loginchk','method'=>'post')) ; ?>

<?php if (isset($noCookie)): ?>
	<div style="color:#CC0000; margin-top: 5px;"><?php echo $noCookie; ?></div>
<?php endif; ?>

<?php if (isset($error['login'])): ?>
	<div style="color:#CC0000; margin-top: 5px;"><?php echo $error['login'] ?></div>
<?php endif; ?>

<?php
	$errClass = array('slgn_id'=>'','slgn_pass'=>'');
	$errMsg = $errClass;

	foreach ($errClass as $c => $v):
		if (isset($error[$c])):
			$errMsg[$c] = '<div style="color:#CC0000;">'.$error[$c].'</div>';
		endif;
	endforeach;
?>

<div style="margin-top: 5px;">
	<label><?php echo __('ログインIDまたはメールアドレス'); ?><br>
		<input type="text" name="slgn_id" value="<?php echo (isset($slgn_id))? $slgn_id:''; ?>" maxlength="200">
	</label>
</div>
<?php echo $errMsg['slgn_id']; ?>

<div style="margin-top: 5px;">
	<label><?php echo __('パスワード'); ?><br>
		<input type="password" name="slgn_pass" value="<?php echo (isset($slgn_pass))? $slgn_pass:''; ?>" maxlength="200">
	</label>
</div>
<?php echo $errMsg['slgn_pass']; ?>

<div style="text-align: center; margin-top: 5px;"><input type="submit" value="<?php echo __('ログイン'); ?>" name="sub_state"></div>

<?php echo Clfunc_Mobile::hr(); ?>

<div><?php echo Clfunc_Mobile::emj('MAILTO'); ?><a href="/s/password"><?php echo __('パスワードを忘れた方はこちら'); ?></a></div>
<div><?php echo Clfunc_Mobile::emj('BOOK'); ?><a href="/s/entry"><?php echo __('新規登録される方はこちら'); ?></a></div>
<?php echo Form::close(); ?>
