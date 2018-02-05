<?php echo View::forge('selectlang'); ?>
<?php $sD1 = Uri::segment(1); ?>


<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('ゲストログイン'); ?></h1>

	<div class="info-box mt16">
		<form action="/<?php echo $sD1; ?>/login/loginchk" method="post" role="form">
		<input type="hidden" name="ltzone" value="">

		<?php if (isset($noCookie)): ?>
			<p class="error-msg"><?php echo $noCookie; ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('cl_code'=>'');
			$errMsg = $errClass;

			foreach ($errClass as $k => $v):
				if (isset($error[$k])):
					$errClass[$k] = ' input-error';
					$errMsg[$k] = '<p class="error-msg mb4 mt4">'.$error[$k].'</p>';
				endif;
			endforeach;
		?>
		<p><input type="text" name="cl_code" id="form_olgn_id" value="<?php echo $cl_code; ?>" size="30" maxlength="200" placeholder="<?php echo __('講義コード'); ?>" class="allow_submit<?php echo $errClass['cl_code']; ?>"></p>
		<?php echo $errMsg['cl_code']; ?>
		<p class="subtext mt4"><?php echo __('※先生から指定された講義コードを入力してください。'); ?></p>

		<p class="button-box"><button type="submit" class="button do register" name="sub_state" value="1"><?php echo __('ログイン'); ?></button></p>
		</form>

<?php if ($sD1 == 'g2'): ?>
		<p class="mt32 text-center""><a href="https://pdcfa.actiontc.jp/ac/member"><?php echo Asset::img('dekitakoto.png',array('alt'=>'できたことノート', 'style'=>'vertical-align: bottom;')); ?> はこちら</a></p>
<?php else: ?>
		<p class="mt32 text-center"><a href="http://c-learning.jp/">C-Learningを使いたい先生はこちら</a></p>
<?php endif; ?>
	</div>
</div>
