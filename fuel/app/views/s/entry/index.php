<?php if (isset($ses['SES_S_ERROR_MSG'])): ?>
<p class="error-box mb16"><?php echo nl2br($ses['SES_S_ERROR_MSG']); ?></p>
<?php Session::delete('SES_S_ERROR_MSG'); ?>
<?php endif; ?>
<?php if (isset($ses['SES_S_NOTICE_MSG'])): ?>
<div class="info-box tmp mb16">
	<p><?php echo nl2br($ses['SES_S_NOTICE_MSG']); ?></p>
	<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png',array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
</div>
<?php Session::delete('SES_S_NOTICE_MSG'); ?>
<?php endif; ?>

<div id="content-inner" class="login">

	<h1 class="mr0"><?php echo __('学生アカウントの新規登録'); ?></h1>

	<div class="info-box">
		<form action="/s/entry/entryform" method="post">
		<?php if (isset($error['entry'])): ?>
			<p class="error-msg"><?php echo $error['entry'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('sent_name'=>'','sent_login'=>'','sent_mail'=>'','sent_pass'=>'','sent_passchk'=>'');
			$errMsg = $errClass;

			foreach ($errClass as $c => $v):
				if (isset($error[$c])):
					$errClass[$c] = ' class="input-error"';
					$errMsg[$c] = '<p class="error-msg">'.$error[$c].'</p>';
				endif;
			endforeach;
		?>

		<p><input type="text" name="sent_name" id="form_sent_name" value="<?php echo (isset($sent_name))? $sent_name:''; ?>" size="30" maxlength="50" placeholder="<?php echo __('氏名'); ?>"<?php echo $errClass['sent_name']; ?>></p>
		<?php echo $errMsg['sent_name']; ?>

		<p><input type="text" name="sent_mail" id="form_sent_mail" value="<?php echo (isset($sent_mail))? $sent_mail:''; ?>" size="30" maxlength="200" placeholder="<?php echo __('メールアドレス'); ?>"<?php echo $errClass['sent_mail']; ?>></p>
		<?php echo $errMsg['sent_mail']; ?>

		<p><input type="password" name="sent_pass" id="form_sent_pass" value="" size="30" maxlength="32" placeholder="<?php echo __('パスワード'); ?>"<?php echo $errClass['sent_pass']; ?>></p>
		<p class="mt4 font-silver font-size-90"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?></p>
		<?php echo $errMsg['sent_pass']; ?>

		<p><input type="password" name="sent_passchk" id="form_sent_passchk" value="" size="30" maxlength="32" placeholder="<?php echo __('パスワード（確認）'); ?>"<?php echo $errClass['sent_passchk']; ?>></p>
		<?php echo $errMsg['sent_passchk']; ?>

		<p>Timezone</p>
		<div id="tz-init" default="<?php echo (isset($sent_timezone))? $sent_timezone:''; ?>">
			<select class="dropdown" id="tz-region">
			<?php foreach ($tz_region as $r): ?>
				<option value="<?php echo $r; ?>"><?php echo $r; ?></option>
			<?php endforeach; ?>
			</select>
			<select class="dropdown" id="tz-timezone" name="sent_timezone">
			<?php foreach ($tz_list as $r => $tzl): ?>
				<?php $sDisp = 'none'; ?>
				<optgroup label="<?php echo $r; ?>" style="display: <?php echo $sDisp; ?>;">
				<?php foreach ($tzl as $t => $v): ?>
					<option value="<?php echo $t; ?>" class="text-left"><?php echo $v; ?></option>
				<?php endforeach; ?>
				</optgroup>
				<?php endforeach; ?>
			</select>
		</div>

		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('登録する'); ?></button></p>
		<p class="subtext mt20"><?php echo __('■入力したメールアドレスは今後ログインする際に利用します。'); ?></p>
		<p class="subtext mt4"><?php echo __('■メールアドレスを受信する端末で迷惑メール対策を行っている方は、ドメイン指定受信設定で『c-learning.jp』を受信できるようにしてください。'); ?></p>
		<p class="subtext mt4 font-red"><?php echo __('※登録したメールアドレスが履修している講義の先生に知られることはありません。'); ?></p>
		<p class="subtext mt20"><a href="/s/login" class="button na default width-auto"><?php echo __('すでにアカウントをお持ちの方はこちら'); ?></a></p>
		</form>
	</div>
</div>
<div id="footer-menu">
	<nav>
		<ul>
		<li><a href="http://c-learning.jp/"><?php echo __(':siteについて',array('site'=>CL_SITENAME)); ?></a></li>
		<li><a href="http://c-learning.jp/prd/truste.html" target="_blank"><?php echo __('プライバシーポリシー'); ?></a></li>
		<li><a href="http://netman.co.jp/"><?php echo __('運営会社について'); ?></a></li>
		</ul>
	</nav>
</div>
