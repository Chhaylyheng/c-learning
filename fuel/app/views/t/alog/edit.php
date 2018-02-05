<?php
	$errClass = array(
		'at_name' =>'',
		'at_goal_label' =>'',
		'at_goal_desc' =>'',
		'at_title_label' =>'',
		'at_title_desc' =>'',
		'at_range_label' =>'',
		'at_range_desc' =>'',
		'at_opt1_label' =>'',
		'at_opt1_desc' =>'',
		'at_opt2_label' =>'',
		'at_opt2_desc' =>'',
		'at_text_label' =>'',
		'at_text_desc' =>'',
		'at_file_label' =>'',
		'at_file_desc' =>'',
		'at_com_label' =>'',
		'at_com_desc' =>'',
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

	$sSubBtn = '登録';
	$sAction = 'create';
	if (isset($aALTheme)):
		$sSubBtn = '更新';
		$sAction = 'edit'.DS.$aALTheme['altID'];
	endif;
?>


<div class="info-box">
<form action="/t/alog/<?php echo $sAction; ?>" method="post">
	<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>

	<table id="AlogThemeEdit">

	<tr>
		<td class="check-key"><sup>*</sup></td>
		<td>
			<div><?php echo __('テーマ名'); ?></div>
			<div>
				<input type="text" name="at_name" value="<?php echo $at_name; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('テーマ名を入力してください'); ?>" class="width-60em<?php echo $errClass['at_name']; ?>">
				<?php echo $errMsg['at_name']; ?>
			</div>
		</td>
	</tr>
	<tr>
		<td class="check-key"><sup>*</sup></td>
		<td>
			<div><?php echo __('目標'); ?></div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('ラベル'); ?>] <?php echo __(':num文字以内',array('num'=>10)); ?></p>
				<input type="text" name="at_goal_label" value="<?php echo $at_goal_label; ?>" maxlength="10" class="width-20em<?php echo $errClass['at_goal_label']; ?>">
				<?php echo $errMsg['at_goal_label']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('テーマに対する活動の目標等を記入する部分の表示ラベルを指定します。'); ?></p>
			</div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('補足'); ?>] <span class="ChrNum">0</span><?php echo __('文字'); ?>/100<?php echo __('文字'); ?></p>
				<textarea name="at_goal_desc" class="width-100<?php echo $errClass['at_goal_desc']; ?>"><?php echo $at_goal_desc; ?></textarea>
				<?php echo $errMsg['at_goal_desc']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('補足は省略可能です。表示される際に改行は無視されます。'); ?></p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="check-key"><sup>*</sup></td>
		<td>
			<div><?php echo __('内容'); ?></div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('ラベル'); ?>] <?php echo __(':num文字以内',array('num'=>10)); ?></p>
				<input type="text" name="at_text_label" value="<?php echo $at_text_label; ?>" maxlength="10" class="width-20em<?php echo $errClass['at_text_label']; ?>">
				<?php echo $errMsg['at_text_label']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('活動内容となる部分の表示ラベルを指定します。'); ?></p>
			</div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('補足'); ?>] <span class="ChrNum">0</span><?php echo __('文字'); ?>/100<?php echo __('文字'); ?></p>
				<textarea name="at_text_desc" class="width-100<?php echo $errClass['at_text_desc']; ?>"><?php echo $at_text_desc; ?></textarea>
				<?php echo $errMsg['at_text_desc']; ?>
				<p class="font-silver font-size-80 mt4"><?php echo __('補足は省略可能です。表示される際に改行は無視されます。'); ?></p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="check-key"><div><input type="checkbox" name="at_title" value="1" class="modify-check" id="key_title" autocomplete="off"<?php echo ($at_title)? ' checked':''; ?>></div><label for="key_title"></label></td>
		<td>
			<label class="tr-overlay" style="display: block;" for="key_title"></label>
			<div><?php echo __('タイトル'); ?></div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('ラベル'); ?>] <?php echo __(':num文字以内',array('num'=>10)); ?></p>
				<input type="text" name="at_title_label" value="<?php echo $at_title_label; ?>" maxlength="10" class="width-20em<?php echo $errClass['at_title_label']; ?>">
				<?php echo $errMsg['at_title_label']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('タイトルとなる部分の表示ラベルを指定します。'); ?></p>
			</div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('補足'); ?>] <span class="ChrNum">0</span><?php echo __('文字'); ?>/100<?php echo __('文字'); ?></p>
				<textarea name="at_title_desc" class="width-100<?php echo $errClass['at_title_desc']; ?>"><?php echo $at_title_desc; ?></textarea>
				<?php echo $errMsg['at_title_desc']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('補足は省略可能です。表示される際に改行は無視されます。'); ?></p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="check-key"><div><input type="checkbox" name="at_range" value="1" class="modify-check" id="key_range" autocomplete="off"<?php echo ($at_range)? ' checked':''; ?>></div><label for="key_range"></label></td>
		<td>
			<label class="tr-overlay" style="display: block;" for="key_range"></label>
			<div><?php echo __('期間'); ?></div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('ラベル'); ?>] <?php echo __(':num文字以内',array('num'=>10)); ?></p>
				<input type="text" name="at_range_label" value="<?php echo $at_range_label; ?>" maxlength="10" class="width-20em<?php echo $errClass['at_range_label']; ?>">
				<?php echo $errMsg['at_range_label']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('活動期間となる部分の表示ラベルを指定します。'); ?></p>
			</div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('補足'); ?>] <span class="ChrNum">0</span><?php echo __('文字'); ?>/100<?php echo __('文字'); ?></p>
				<textarea name="at_range_desc" class="width-100<?php echo $errClass['at_range_desc']; ?>"><?php echo $at_range_desc; ?></textarea>
				<?php echo $errMsg['at_range_desc']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('補足は省略可能です。表示される際に改行は無視されます。'); ?></p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="check-key"><div><input type="checkbox" name="at_opt1" value="1" class="modify-check" id="key_opt1" autocomplete="off"<?php echo ($at_opt1)? ' checked':''; ?>></div><label for="key_opt1"></label></td>
		<td>
			<label class="tr-overlay" style="display: block;" for="key_opt1"></label>
			<div><?php echo __('オプション1'); ?></div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('ラベル'); ?>] <?php echo __(':num文字以内',array('num'=>10)); ?></p>
				<input type="text" name="at_opt1_label" value="<?php echo $at_opt1_label; ?>" maxlength="10" class="width-20em<?php echo $errClass['at_opt1_label']; ?>">
				<?php echo $errMsg['at_opt1_label']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('その他取得したい情報（場所など）となる部分の表示ラベルを指定します。'); ?></p>
			</div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('補足'); ?>] <span class="ChrNum">0</span><?php echo __('文字'); ?>/100<?php echo __('文字'); ?></p>
				<textarea name="at_opt1_desc" class="width-100<?php echo $errClass['at_opt1_desc']; ?>"><?php echo $at_opt1_desc; ?></textarea>
				<?php echo $errMsg['at_opt1_desc']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('補足は省略可能です。表示される際に改行は無視されます。'); ?></p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="check-key"><div><input type="checkbox" name="at_opt2" value="1" class="modify-check" id="key_opt2" autocomplete="off"<?php echo ($at_opt2)? ' checked':''; ?>></div><label for="key_opt2"></label></td>
		<td>
			<label class="tr-overlay" style="display: block;" for="key_opt2"></label>
			<div><?php echo __('オプション2'); ?></div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('ラベル'); ?>] <?php echo __(':num文字以内',array('num'=>10)); ?></p>
				<input type="text" name="at_opt2_label" value="<?php echo $at_opt2_label; ?>" maxlength="10" class="width-20em<?php echo $errClass['at_opt2_label']; ?>">
				<?php echo $errMsg['at_opt2_label']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('その他取得したい情報（場所など）となる部分の表示ラベルを指定します。'); ?></p>
			</div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('補足'); ?>] <span class="ChrNum">0</span><?php echo __('文字'); ?>/100<?php echo __('文字'); ?></p>
				<textarea name="at_opt2_desc" class="width-100<?php echo $errClass['at_opt2_desc']; ?>"><?php echo $at_opt2_desc; ?></textarea>
				<?php echo $errMsg['at_opt2_desc']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('補足は省略可能です。表示される際に改行は無視されます。'); ?></p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="check-key"><div><input type="checkbox" name="at_file" value="1" class="modify-check" id="key_file" autocomplete="off"<?php echo ($at_file)? ' checked':''; ?>></div><label for="key_file"></label></td>
		<td>
			<label class="tr-overlay" style="display: block;" for="key_file"></label>
			<div><?php echo __('添付ファイル'); ?></div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('ラベル'); ?>] <?php echo __(':num文字以内',array('num'=>10)); ?></p>
				<input type="text" name="at_file_label" value="<?php echo $at_file_label; ?>" maxlength="10" class="width-20em<?php echo $errClass['at_file_label']; ?>">
				<?php echo $errMsg['at_file_label']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('活動内容のファイルをアップロードする部分の表示ラベルを指定します。'); ?></p>
			</div>
			<div>
				<p class="font-bold font-size-80">[<?php echo __('補足'); ?>] <span class="ChrNum">0</span><?php echo __('文字'); ?>/100<?php echo __('文字'); ?></p>
				<textarea name="at_file_desc" class="width-100<?php echo $errClass['at_file_desc']; ?>"><?php echo $at_file_desc; ?></textarea>
				<?php echo $errMsg['at_file_desc']; ?>
				<p class="font-silver font-size-80 mt0"><?php echo __('補足は省略可能です。表示される際に改行は無視されます。'); ?></p>
			</div>
		</td>
	</tr>
	</table>

	<div class="formControl" style="margin: auto;">
		<div class="formGroup">

		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubBtn; ?></button>
	</div>
</form>
</div>
