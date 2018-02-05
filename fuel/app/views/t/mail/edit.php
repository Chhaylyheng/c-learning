<?php
	$errClass = array(
		'm_name'=>'',
		'm_subject'  =>'',
		'm_body' =>'',
	);
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' input-error';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
?>


<div class="info-box">
<form action="/t/mail/send" method="post">
<?php if (isset($aReport)): ?>
<input type="hidden" name="func" value="report">
<input type="hidden" name="rb" value="<?php echo $aReport['rbID']; ?>">
<?php else: ?>
<input type="hidden" name="func" value="student">
<?php endif; ?>
<input type="hidden" name="mode" value="input">
	<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>
	<div class="formControl width-60em" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel width-20"><a href="#" class="sendto-box-toggle"><i class="fa fa-plus-square-o"></i><i class="fa fa-minus-square-o" style="display: none;"></i> <?php echo __('連絡先'); ?></a></div>
			<div class="formContent inline-box">
				<p><?php echo __(':num名',array('num'=>count($aStudent))); ?></p>
				<ul class="sendto-list font-blue" style="display: none;">
<?php
	foreach ($aStudent as $aS):
?>
<li class="sendto"><?php echo $aS['name']; ?></li>
<?php
	endforeach;
?>
				</ul>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('送信者名'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="m_name" value="<?php echo $m_name; ?>" maxlength="50" class="width-40em text-left<?php echo $errClass['m_name']; ?>">
				<?php echo $errMsg['m_name']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('件名'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="m_subject" value="<?php echo $m_subject; ?>" maxlength="50" class="width-40em text-left<?php echo $errClass['m_subject']; ?>">
				<?php echo $errMsg['m_subject']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('本文'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<textarea name="m_body" class="width-50em text-left<?php echo $errClass['m_body']; ?>" rows="6"><?php echo ($m_body)? $m_body:"\n\n".$sMailTemplate; ?></textarea>
				<?php echo $errMsg['m_body']; ?>
			</div>
		</div>
	</div>
	<div class="button-box mt16">
		<p class="font-red"><?php echo __('※こちらから連絡した内容は、連絡・相談に登録され、学生からも連絡・相談より返答があります。'); ?></p>
		<button type="submit" class="button do mt8" name="sub_state" value="1"><?php echo __('確認'); ?></button>
	</div>
</form>
</div>