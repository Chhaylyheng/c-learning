<?php
	$sAction = 'add';
	$sButton = __('追加する');
	if ($no):
		$sAction = 'edit/'.$no;
		$sButton = __('更新する');
	endif;

	$errClass = array(
		's_date'=>'',
		's_time'=>'',
		'e_date'=>'',
		'e_time'=>'',
		'n_body' =>'',
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
<form action="/t/news/<?php echo $sAction; ?>" method="post">
	<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>
	<div class="formControl width-60em" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('掲載日時'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="s_date" value="<?php echo $s_date; ?>" id="from" class="width-10em text-center" readonly>
				<input type="text" name="s_time" value="<?php echo $s_time; ?>" class="timepick width-8em text-center ml4 mr8" maxlength="5">
				～
				<input type="text" name="e_date" value="<?php echo $e_date; ?>" id="to"   class="width-10em text-center ml8" readonly>
				<input type="text" name="e_time" value="<?php echo $e_time; ?>" class="timepick width-8em text-center ml4" maxlength="5">
				<?php echo $errMsg['s_date']; ?>
				<?php echo $errMsg['s_time']; ?>
				<?php echo $errMsg['e_date']; ?>
				<?php echo $errMsg['e_time']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('ニュース内容'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<textarea name="n_body" class="width-50em text-left<?php echo $errClass['n_body']; ?>" rows="6"><?php echo $n_body; ?></textarea>
				<?php echo $errMsg['n_body']; ?>
			</div>
		</div>

		<div class="formGroup">
			<div class="formLabel"><?php echo __('コンテンツの差し込み'); ?></div>
			<div class="formContent inline-box">
				<div id="news-chain" class="mt4 mb4 font-blue font-bold">
					<?php if ($n_url): ?>
						<i class="fa fa-chain"></i> <?php echo $n_url_title; ?> <a href="#" class="ChoiceContentsDelete button na default width-auto" style="padding: 4px;"><i class="fa fa-times mr0"></i></a>
					<?php endif; ?>
				</div>
				<input type="hidden" name="n_url" value="<?php echo $n_url; ?>" id="dburl">
				<div class="mt8" data="<?php echo $aClass['ctID']; ?>">
					<?php
						$aChoice = array(
							'Quest'=>array('color'=>'default','disable'=>''),
							'Test'=>array('color'=>'default','disable'=>''),
							'Mat'=>array('color'=>'default','disable'=>''),
							'Coop'=>array('color'=>'default','disable'=>''),
							'Report'=>array('color'=>'default','disable'=>''),
						);
						if (!$aNums['Quest']):
							$aChoice['Quest']['color'] = 'back-silver';
							$aChoice['Quest']['disable'] = 'disabled="disabled"';
						endif;
						if (!$aNums['Test']):
							$aChoice['Test']['color'] = 'back-silver';
							$aChoice['Test']['disable'] = 'disabled="disabled"';
						endif;
						if (!$aNums['Mat']):
							$aChoice['Mat']['color'] = 'back-silver';
							$aChoice['Mat']['disable'] = 'disabled="disabled"';
						endif;
						if (!$aNums['Coop']):
							$aChoice['Coop']['color'] = 'back-silver';
							$aChoice['Coop']['disable'] = 'disabled="disabled"';
						endif;
						if (!$aNums['Report']):
							$aChoice['Report']['color'] = 'back-silver';
							$aChoice['Report']['disable'] = 'disabled="disabled"';
						endif;
					?>
					<button type="button" class="button na <?php echo $aChoice['Quest']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Quest']['disable']; ?> style="padding: 4px 8px;" data="quest"><?php echo __('アンケートから選択'); ?></button>
					<button type="button" class="button na <?php echo $aChoice['Test']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Test']['disable']; ?> style="padding: 4px 8px;" data="test"><?php echo __('小テストから選択'); ?></button>
					<button type="button" class="button na <?php echo $aChoice['Mat']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Mat']['disable']; ?> style="padding: 4px 8px;" data="material"><?php echo __('教材倉庫から選択'); ?></button>
					<button type="button" class="button na <?php echo $aChoice['Coop']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Coop']['disable']; ?> style="padding: 4px 8px;" data="coop"><?php echo __('協働板から選択'); ?></button>
					<button type="button" class="button na <?php echo $aChoice['Report']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Report']['disable']; ?> style="padding: 4px 8px;" data="report"><?php echo __('レポートから選択'); ?></button>
				</div>
			</div>
		</div>

		<div class="formGroup">
			<div class="formLabel"><?php echo __('通知'); ?></div>
			<div class="formContent inline-box">
				<?php $sChk = ($n_send)? ' checked':''; ?>
				<label><input type="checkbox" name="n_send" value="1"<?php echo $sChk; ?>><?php echo __('掲載と同時に学生に通知する。'); ?></label>
			</div>
		</div>
	</div>
	<div class="button-box mt16">
		<button type="submit" class="button do mt8" name="sub_state" value="1"><?php echo $sButton; ?></button>
	</div>
</form>
</div>