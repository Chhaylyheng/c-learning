<?php
	$sSubBtn = '登録';
	$sAction = 'create/'.$aALTheme['altID'];
?>


<div class="info-box">
	<p class="mt0 text-right"><?php echo __(':astは必須項目',array('ast'=>'<sup>*</sup>')); ?></p>
	<div class="formControl" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altGoalLabel']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-blue">[<?php echo __('記入した目標が表示されます。'); ?>]</p>
			</div>
		</div>
<?php if ($aALTheme['altTitle']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altTitleLabel']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altTitleDescription']; ?></p>
				<input type="text" name="al_title" value="" maxlength="<?php echo CL_TITLE_LENGTH; ?>" class="width-40em">
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altRange']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altRangeLabel']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altRangeDescription']; ?></p>

				<input type="text" name="al_date_s" value="<?php echo Clfunc_Tz::tz('Y/m/d',$tz); ?>" id="datepick3" class="width-10em text-center inline-block"
				><input type="text" name="al_time_s" value="<?php echo Clfunc_Tz::tz('H:i',$tz); ?>" maxlength="5" class="timepick1 width-8em text-center ml8 inline-block">
				～
				<input type="text" name="al_date_e" value="<?php echo Clfunc_Tz::tz('Y/m/d',$tz); ?>" id="datepick4" class="width-10em text-center inline-block"
				><input type="text" name="al_time_e" value="<?php echo Clfunc_Tz::tz('H:i',$tz); ?>" maxlength="5" class="timepick2 width-8em text-center ml8 inline-block">

			</div>
		</div>
<?php endif; ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altTextLabel']; ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altTextDescription']; ?></p>
				<textarea name="al_text" class="width-60em" rows="10"></textarea>
			</div>
		</div>
<?php if ($aALTheme['altFile']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altFileLabel']; ?></div>
			<div class="formContent inline-box">
				<ul class="file-uploader">
					<li class="width-20em">
						<div class="input-cover text-center" style="">
							<i class="fa fa-plus fa-3x mt16"></i>
							<p><?php echo __('ファイルを選択'); ?></p>
							<div class="upload-progress"><div class="upload-progress-bar"></div></div>
						</div>
						<span class="hidden-file"><input type="file" name="file-input" autocomplete="off"></span>
						<input type="hidden" name="al_file" value="">
					</li>
				</ul>
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt1']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altOpt1Label']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altOpt1Description']; ?></p>
				<textarea name="al_opt1" class="width-60em"></textarea>
			</div>
		</div>
<?php endif; ?>
<?php if ($aALTheme['altOpt2']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altOpt2Label']; ?></div>
			<div class="formContent inline-box">
				<p class="font-size-90 font-gray"><?php echo $aALTheme['altOpt2Description']; ?></p>
				<textarea name="al_opt2" class="width-60em"></textarea>
			</div>
		</div>
<?php endif; ?>
	</div>
	<div class="button-box mt32">
		<button type="button" class="button do" disabled><?php echo $sSubBtn; ?><?php echo __('確認'); ?></button>
	</div>
</form>
</div>
