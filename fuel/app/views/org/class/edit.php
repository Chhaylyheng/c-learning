<?php
$sAction = 'add';
$sButton = __('登録');
if (isset($aClass)):
	$sAction = 'edit'.DS.$aClass['ctID'];
	$sButton = __('更新');
endif;
?>
<div class="info-box">
	<form action="/org/class/<?php echo $sAction; ?>" method="post">
	<?php if (isset($error['classedit'])): ?>
		<p class="error-box"><?php echo $error['classedit'] ?></p>
	<?php endif; ?>

	<?php
		$errClass = array('c_name'=>'','c_code'=>'');
		$errMsg = $errClass;

		foreach ($errClass as $c => $v):
			if (isset($error[$c])):
				$errClass[$c] = ' input-error';
				$errMsg[$c] = '<p class="error-msg">'.$error[$c].'</p>';
			endif;
		endforeach;
	?>
<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>

<div style="margin: auto;" class="formControl">
	<div class="formGroup">
		<div class="formLabel"><?php echo __('講義コード'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['c_code']; ?>" maxlength="20" value="<?php echo $c_code; ?>" name="c_code">
			<p class="mt4 font-silver font-size-80" style="line-height: 1.2;">
				<?php echo __('半角大小英数字と一部記号【_（アンダースコア）-（ハイフン）】で入力。'); ?>
				<?php if (!isset($aClass)): ?>
				<br><?php echo __('空の場合は、講義コードを自動生成します。'); ?>
				<?php endif; ?>
			</p>
			<?php echo $errMsg['c_code']; ?>
		</div>
	</div>
	<div class="formGroup">
		<div class="formLabel"><?php echo __('講義名'); ?><sup>*</sup></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left<?php echo $errClass['c_name']; ?>" maxlength="30" value="<?php echo $c_name; ?>" name="c_name">
			<?php echo $errMsg['c_name']; ?>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('年度'); ?></div>
		<div class="formContent inline-box select-box">
			<?php echo Form::select('c_year',$c_year,$yearlist,array('style'=>'background-image: none;')); ?>
		</div>
	</div>
	<div class="formGroup">
		<div class="formLabel"><?php echo __('期'); ?></div>
		<div class="formContent inline-box select-box">
			<?php echo Form::select('c_period',$c_period,$periodlist,array('style'=>'background-image: none;')); ?>
		</div>
	</div>
	<div class="formGroup">
		<div class="formLabel"><?php echo __('曜日'); ?></div>
		<div class="formContent inline-box select-box">
			<?php echo Form::select('c_weekday',$c_weekday,$weekdaylist,array('style'=>'background-image: none;')); ?>
		</div>
	</div>
	<div class="formGroup">
		<div class="formLabel"><?php echo __('時限'); ?></div>
		<div class="formContent inline-box select-box">
			<?php echo Form::select('c_hour',$c_hour,$hourlist,array('style'=>'background-image: none;')); ?>
		</div>
	</div>
</div>

<div class="button-box mt16"><button type="submit" class="button do na" name="sub_state" value="1"><?php echo $sButton; ?></button></div>
	</form>
</div>
