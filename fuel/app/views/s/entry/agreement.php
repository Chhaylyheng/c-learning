<div id="content-inner" class="login">

	<h1 class="mr0"><?php echo __('会員規約'); ?></h1>

	<div class="info-box">

		<div style="width: 100%; padding: 8px; line-height: 1.5;"><?php echo nl2br($sTerm); ?></div>

		<hr>
		<form action="/s/entry/agreement" method="post">
			<p class="text-center font-red mt16"><?php echo __('上記規約に同意いただくことで登録が完了します。'); ?></p>
			<p class="button-box"><button type="submit" name="agree" class="button do register" value="1"><?php echo __('同意する'); ?></button></p>
			<p class="button-box mt8"><button type="submit" name="disagree" class="button default na register width-auto" value="1"><?php echo __('同意しない'); ?></button></p>
		</form>
	</div>
</div>