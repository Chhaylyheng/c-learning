<?php
	$errClass = array('cslist'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sMemo = __('自身が管理している講義に履修している学生を、この講義へ履修登録します。');
	if (!is_null($aGroup))
	{
		$sMemo = __(':groupが管理している講義に履修している学生を、この講義へ履修登録します。',array('group'=>$aGroup['gtName']));
	}
?>

<div class="info-box">
<form action="/t/student/listadd" method="post">
	<p class="mt0 text-center"><?php echo $sMemo; ?></p>
	<p class="mt4 text-center"><?php echo __('学生のログインIDを改行で区切って入力してください。'); ?></p>
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('ログインID'); ?></div>
			<div class="formContent inline-box">
				<textarea name="cslist" rows="20" <?php echo $errClass['cslist']; ?>><?php echo $cslist; ?></textarea>
				<?php echo $errMsg['cslist']; ?>
			</div>
		</div>
	</div>
	<div class="button-box mt8 mb8">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo __('登録する'); ?></button>
	</div>
</form>
</div>
