<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('初期設定'); ?></h1>

	<ul class="init-process mt16">
		<li><?php echo __('①先生設定'); ?><i class="fa fa-angle-right"></i></li
		><li class="active"><?php echo __('②講義登録'); ?><i class="fa fa-angle-right"></i></li
		><li><?php echo __('③完了'); ?></li>
	</ul>

	<div class="info-box mt8">

		<p class="text-center font-blue font-size-160 mt8 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-info-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('講義を作成しましょう'); ?></p>

	<form action="/t/init/classset" method="post">
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

	<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>

	<table style="margin: auto;" class="formTable">
	<tr>
		<th class="width-30"><?php echo __('講義名'); ?><sup>*</sup></th>
		<td>
			<input type="text" class="width-24em text-left" placeholder="<?php echo __('講義名'); ?>" maxlength="30" value="<?php echo $c_name; ?>" name="c_name"<?php echo $errClass['c_name']; ?>>
			<?php echo $errMsg['c_name']; ?>
		</td>
	</tr>
	<tr>
		<th class="width-30"><?php echo __('年度'); ?></th>
		<td>
			<?php echo Form::select('c_year',$c_year,$yearlist,array('style'=>'background-image: none;')); ?>
		</td>
	</tr>
	<tr>
		<th class="width-30"><?php echo __('期').DS.__('曜日').DS.__('時限'); ?></th>
		<td>
			<?php echo Form::select('c_period',$c_period,$periodlist,array('style'=>'background-image: none;')); ?>
			<?php echo Form::select('c_weekday',$c_weekday,$weekdaylist,array('style'=>'background-image: none;')); ?>
			<?php echo Form::select('c_hour',$c_hour,$hourlist,array('style'=>'background-image: none;')); ?>
		</td>
	</tr>
	<tr>
		<th class="width-30"><?php echo __('利用機能'); ?></th>
		<td>
			<p><?php echo __('利用する機能を選択してください。'); ?><br><?php echo __('利用しない機能は、画面や学生側に表示されなくなります。'); ?></p>
			<ul class="QuestAnsChoice">
<?php
	$aChoice = array();
	$aStyle = array('square-o','check-square-o');

	foreach ($aClassFlag['C_FUNC'] as $i => $v):
		# ASPのクイックユーザー
		if (!$aTeacher['gtID'] && $aTeacher['coTermDate'] < date('Y-m-d')):
			if ($i != \Clfunc_Flag::C_FUNC_QUEST):
				continue;
			endif;
		endif;
		# C+Lのアンケートユーザー
		if (CL_CAREERTASU_MODE && !$aTeacher['ttCTPlan']):
			if ($i != \Clfunc_Flag::C_FUNC_QUEST):
				continue;
			endif;
		endif;

		$sCheck = (array_search($i, $C_FUNC) !== false)? ' checked':'';
		$sIcon  = ($sCheck)? $aStyle[1]:$aStyle[0];
		$sLabel = ($sCheck)? 'check':'default';
?><li class="width-auto" style="margin-left: 6px!important; margin-right: 6px!important;"><label class="QueryChoice text-left <?php echo $sLabel; ?>"
	><input type="checkbox" name="C_FUNC[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>
	><p><i class="fa fa-<?php echo $sIcon; ?> fa-fw"></i><?php echo $v; ?></p></label></li><?php
	endforeach;
?>
			</ul>
		</td>
	</tr>
	<tr>
		<th class="width-30"><?php echo __('学生プロフィールの取得'); ?></th>
		<td>
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
		</td>
	</tr>
	</table>

	<p class="mt16 font-green text-center"><i class="fa fa-question-circle"></i> <?php echo __('先生および講義の情報は後からでも変更できます。'); ?></p>

	<hr>

		<div class="button-box mt8" style="overflow: hidden;">
			<button type="submit" name="back" class="button default na width-auto" style="float: left;" value="1"><i class="fa fa-chevron-left ml0 mr16"></i><?php echo __('戻る'); ?></button>
			<button type="submit" class="button do na register width-auto" style="float: right;" name="sub_state" value="1"><?php echo __('次へ'); ?><i class="fa fa-chevron-right ml16 mr0"></i></button>
		</div>
		</form>
	</div>
</div>
