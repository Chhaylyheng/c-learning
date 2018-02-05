<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('初期設定'); ?></h1>

	<?php if (Session::get('SES_T_NOTICE_MSG', false)): ?>
	<div class="info-box tmp mb16">
		<p><?php echo nl2br(Session::get('SES_T_NOTICE_MSG', false)); ?></p>
		<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png',array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
	</div>
	<?php Session::delete('SES_T_NOTICE_MSG'); ?>
	<?php endif; ?>

	<ul class="init-process mt16">
		<li class="active"><?php echo __('①先生設定'); ?><i class="fa fa-angle-right"></i></li
		><li><?php echo __('②講義登録'); ?><i class="fa fa-angle-right"></i></li
		><li><?php echo __('③完了'); ?></li>
	</ul>

	<div class="info-box mt8">

		<p class="text-center font-blue font-size-160 mt8 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-info-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('先生の情報を入力してください'); ?></p>

		<form action="/t/init/profile" method="post" role="form">
		<?php if (isset($error['init'])): ?>
			<p class="error-msg"><?php echo $error['init'] ?></p>
		<?php endif; ?>

		<?php
			$errClass = array('tent_pass'=>'','tent_passchk'=>'', 'tent_name'=>'','tent_tel'=>'','tent_school'=>'','tent_dept'=>'','tent_subject'=>'', 'c_name'=>'');
			$errMsg = $errClass;

			foreach ($errClass as $c => $v):
				if (isset($error[$c])):
					$errClass[$c] = ' input-error';
					$errMsg[$c] = '<p class="error-msg mt4">'.$error[$c].'</p>';
				endif;
			endforeach;
			$aTelSup = array('','');
			if (isset($tent_telsupport)):
				$aTelSup[$tent_telsupport] = ' checked';
			endif;
		?>

		<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>

		<table style="margin: auto;" class="formTable">
			<tr>
				<th class="width-30"><?php echo __('メールアドレス'); ?></th>
				<td class="font-bold font-blue"><?php echo $aTeacher['ttMail']; ?></td>
			</tr>
			<tr>
				<th class="width-30"><?php echo __('パスワード'); ?><sup>*</sup></th>
				<td>
					<p><input type="password" name="tent_pass" value="" class="width-24em<?php echo $errClass['tent_pass']; ?>" autocomplete="off" maxlength="32" placeholder="<?php echo __('パスワードを設定してください'); ?>"></p>
					<p class="mt4 font-silver font-size-90"><?php echo __('※8文字以上32文字以内で半角英数字と一部記号（./-_）を二種類以上組み合わせてください。'); ?></p>
					<?php echo $errMsg['tent_pass']; ?>
					<p class="mt8"><input type="password" name="tent_passchk" value="" class="width-24em<?php echo $errClass['tent_passchk']; ?>" autocomplete="off" maxlength="32" placeholder="<?php echo __('確認のため再度入力してください'); ?>"></p>
					<?php echo $errMsg['tent_passchk']; ?>
				</td>
			</tr>
			<tr>
				<th class="width-30"><?php echo __('氏名'); ?><sup>*</sup></th>
				<td>
					<input type="text" name="tent_name" value="<?php echo (isset($tent_name))? $tent_name:''; ?>" class="width-24em<?php echo $errClass['tent_name']; ?>" maxlength="50" placeholder="<?php echo __('氏名を入力します'); ?>">
					<?php echo $errMsg['tent_name']; ?>
				</td>
			</tr>

<?php if (!CL_CAREERTASU_MODE): ?>
		<tr>
			<th class="width-30"><?php echo __('電話番号'); ?><sup>*</sup></th>
			<td>
				<input type="text" name="tent_tel" value="<?php echo (isset($tent_tel))? $tent_tel:''; ?>" class="width-24em<?php echo $errClass['tent_tel']; ?>" maxlength="15" placeholder="<?php echo __('電話番号を入力します'); ?>">
				<p class="mt4 font-gray font-size-90"><?php echo __('ハイフンなしで入力してください。'); ?></p>
				<?php echo $errMsg['tent_tel']; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo __('所属学校名'); ?><sup>*</sup></th>
			<td>
				<input type="text" name="tent_school" value="<?php echo (isset($tent_school))? $tent_school:''; ?>" class="width-24em<?php echo $errClass['tent_school']; ?>" placeholder="<?php echo __('所属学校名を入力します'); ?>" id="form_t_school">
				<p class="mt4 font-gray font-size-90"><?php echo __('学校名の一部分を入力すると候補が表示されますので、候補のリストより選択してください。'); ?><?php echo __('候補にない場合は、新規に登録されます。'); ?></p>
				<?php echo $errMsg['tent_school']; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo __('学部名'); ?></th>
			<td>
				<input type="text" name="tent_dept" value="<?php echo (isset($tent_dept))? $tent_dept:''; ?>" class="width-24em<?php echo $errClass['tent_dept']; ?>" maxlength="50" placeholder="<?php echo __('学部名を入力します'); ?>">
				<?php echo $errMsg['tent_dept']; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo __('学科名'); ?></th>
			<td>
				<input type="text" name="tent_subject" value="<?php echo (isset($tent_subject))? $tent_subject:''; ?>" class="width-24em<?php echo $errClass['tent_subject']; ?>" maxlength="50" placeholder="<?php echo __('学科名を入力します'); ?>">
				<?php echo $errMsg['tent_subject']; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo __('電話による初期サポート'); ?><sup>*</sup></th>
			<td>
				<label><input type="radio" name="tent_telsupport" value="1"<?php echo $aTelSup[1]; ?>><?php echo __('希望する'); ?></input></label>
				<label class="ml16"><input type="radio" name="tent_telsupport" value="0"<?php echo $aTelSup[0]; ?>><?php echo __('希望しない'); ?></input></label>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<th>Timezone<sup>*</sup></th>
			<td id="tz-init" default="<?php echo (isset($tent_timezone))? $tent_timezone:''; ?>">
				<select class="dropdown" id="tz-region">
				<?php foreach ($tz_region as $r): ?>
					<option value="<?php echo $r; ?>"><?php echo $r; ?></option>
				<?php endforeach; ?>
				</select>
				<select class="dropdown" id="tz-timezone" name="tent_timezone">
				<?php foreach ($tz_list as $r => $tzl): ?>
					<?php $sDisp = 'none'; ?>
					<optgroup label="<?php echo $r; ?>" style="display: <?php echo $sDisp; ?>;">
					<?php foreach ($tzl as $t => $v): ?>
						<option value="<?php echo $t; ?>" class="text-left"><?php echo $v; ?></option>
					<?php endforeach; ?>
					</optgroup>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		</table>

		<p class="mt16 font-green text-center"><i class="fa fa-question-circle"></i> <?php echo __('先生および講義の情報は後からでも変更できます。'); ?></p>

		<hr>

		<p class="mt16 font-red2 text-center"><?php echo __('「登録情報不備または詐称（さしょう）」行為が行われていると判断した場合は、ご利用ログインIDの停止措置をおこなわせていただく場合がございます。'); ?></p>

		<div class="button-box mt8" style="overflow: hidden;">
			<button type="submit" class="button do na register width-auto" style="float: right;" name="sub_state" value="1"><?php echo __('次へ'); ?><i class="fa fa-chevron-right ml16 mr0"></i></button>
		</div>
		</form>
	</div>
</div>
