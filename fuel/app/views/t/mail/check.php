<div class="info-box">
<form action="/t/mail/send" method="post">
<?php if (isset($aReport)): ?>
<input type="hidden" name="func" value="report">
<input type="hidden" name="rb" value="<?php echo $aReport['rbID']; ?>">
<?php else: ?>
<input type="hidden" name="func" value="student">
<?php endif; ?>
<input type="hidden" name="mode" value="check">
	<p class="mt0 text-center"><?php echo __('以下の内容で連絡します。'); ?></p>
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
			<div class="formLabel"><?php echo __('送信者名'); ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo $m_name; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('件名'); ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo $m_subject; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('本文'); ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo nl2br($m_body); ?>
			</div>
		</div>
	</div>
	<div class="button-box mt16">
		<button type="back" class="button na cancel width-auto mt16" style="float: left;" name="back" value="1"><?php echo __('戻る'); ?></button>
		<button type="submit" class="button do" name="state" value="1"><?php echo __('連絡する'); ?></button>
	</div>
</form>
</div>
