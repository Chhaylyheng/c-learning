<div class="info-box">
	<form action="/t/class/create" method="get">
	<?php if (isset($error['classcreate'])): ?>
		<p class="error-box"><?php echo $error['classcreate'] ?></p>
	<?php endif; ?>

	<?php
		$errClass = array('c_name'=>'');
		$errMsg = $errClass;

		foreach ($errClass as $c => $v):
			if (isset($error[$c])):
				$errClass[$c] = ' class="input-error"';
				$errMsg[$c] = '<p class="error-msg">'.$error[$c].'</p>';
			endif;
		endforeach;
	?>

<div style="margin: auto;" class="formControl">
	<div class="formGroup">
		<div class="formLabel"><?php echo __('講義名'); ?></div>
		<div class="formContent inline-box">
			<input type="text" class="width-24em text-left" placeholder="<?php echo __('講義名'); ?>" maxlength="30" value="<?php echo $c_name; ?>" name="c_name"<?php echo $errClass['c_name']; ?>>
			<?php echo $errMsg['c_name']; ?>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"></div>
		<div class="formContent">
			<a href="#" class="ShowToggle" data="ClassDetail"><?php echo __('詳細設定'); ?></a>
		</div>
	</div>

	<div class="formRowGroup" id="ClassDetail" style="display: none;">

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

	<div class="formGroup">
		<div class="formLabel"><?php echo __('機能'); ?></div>
		<div class="formContent inline-box">
			<p><?php echo __('利用する機能を選択してください。'); ?><br><?php echo __('利用しない機能は、画面や学生側に表示されなくなります。'); ?></p>
			<ul class="QuestAnsChoice">
<?php
	$aChoice = array();
	$aStyle = array('square-o','check-square-o');

	foreach ($aClassFlag['C_FUNC'] as $i => $v):
		$sCheck = (array_search($i, $C_FUNC) !== false)? ' checked':'';
		$sIcon  = ($sCheck)? $aStyle[1]:$aStyle[0];
		$sLabel = ($sCheck)? 'check':'default';
?><li class="width-auto" style="margin-left: 6px!important; margin-right: 6px!important;"><label class="QueryChoice text-left <?php echo $sLabel; ?>"
	><input type="checkbox" name="C_FUNC[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>
	><p><i class="fa fa-<?php echo $sIcon; ?> fa-fw"></i><?php echo $v; ?></p></label></li><?php
	endforeach;
?>
			</ul>
		</div>
	</div>

	<div class="formGroup">
		<div class="formLabel"><?php echo __('学生プロフィールの取得'); ?></div>
		<div class="formContent inline-box">
			<p><?php echo __('講義を履修する学生から必ず取得したい情報を選択してください。'); ?></p>
			<ul class="QuestAnsChoice">
<?php
	$aChoice = array();
	$aStyle = array('square-o','check-square-o');

	foreach ($aClassFlag['S_GET'] as $i => $v):
		if (CL_CAREERTASU_MODE && ($i == 2 || $i == 16 || $i == 32 || $i == 64 || $i == 128)):
			continue;
		endif;
		if (!CL_CAREERTASU_MODE && $i == 512):
			continue;
		endif;
		$sCheck = (array_search($i, $S_GET) !== false)? ' checked':'';
		$sIcon  = ($sCheck)? $aStyle[1]:$aStyle[0];
		$sLabel = ($sCheck)? 'check':'default';
?><li class="width-auto" style="margin-left: 6px!important; margin-right: 6px!important;"><label class="QueryChoice text-left <?php echo $sLabel; ?>"
	><input type="checkbox" name="S_GET[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>
	><p><i class="fa fa-<?php echo $sIcon; ?> fa-fw"></i><?php echo $v; ?></p></label></li><?php
	endforeach;
?>
			</ul>
		</div>
	</div>

	</div>

</div>
<div class="button-box mt16"><button type="submit" class="button do na" name="sub_state" value="1"><?php echo __('作成する'); ?></button></div>
</form>
</div>

