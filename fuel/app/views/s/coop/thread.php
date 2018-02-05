<div class="res-field"></div>
<div id="thread-group" data="/s/coop/threadpiece/<?php echo $aCCategory['ccID']; ?>/">
<?php
if (!is_null($aParents)):
	foreach ($aParents as $aP):
?>
<div class="anchor-block" id="c<?php echo $aP['cNO']; ?>"><div class="text-center mt16 mb16 font-silver"><i class="fa fa-spinner fa-pulse fa-2x"></i></div></div>
<?php
endforeach;
endif;
?>
</div>

<form action="/s/coop/res/<?php echo $aCCategory['ccID']; ?>" method="post" class="res-box width-100" style="display: none;">
	<input type="hidden" name="c_no" value="0">
	<input type="hidden" name="c_id" value="">
	<input type="hidden" name="mode" value="input">
	<input type="hidden" name="ct" value="<?php echo $aClass['ctID']; ?>">
	<div class="res-msg-box"></div>
	<div class="formControl font-size-90 width-70em" style="margin: auto;">
		<div class="formGroup width-100" style="display: none;">
			<div class="formLabel" style="width: 9em; min-width: 9em;"><?php echo __('タイトル'); ?></div>
			<div class="formContent inline-box width-100">
				<input type="text" name="c_title" value="" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('タイトルを入力してください'); ?>" class="width-100 text-left">
			</div>
		</div>
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;"><a href="#" class="upload-box-toggle"><i class="fa fa-plus-square-o"></i><i class="fa fa-minus-square-o" style="display: none;"></i> <?php echo __('ファイル選択'); ?></a></div>
			<div class="formContent inline-box">
				<ul class="file-uploader" style="display: none;">
<?php
	for ($i = 1; $i <= 3; $i++):
?>
					<li class="width-16em file-box">
						<div class="input-cover text-center" style="background-size: cover;" data="<?php echo $i; ?>">
							<i class="fa fa-plus fa-3x mt16"></i>
							<p><?php echo __('ファイルを選択'); ?></p>
							<div class="uploaded-file" style="display: none;">
								<p><i class="fa fa-paperclip"></i> <a href="" class="file" target="_blank"><span class="name"></span></a><br><span class="size"></span></p>
								<p class="remove"><i class="fa fa-times fa-2x"></i></p>
							</div>
							<div class="upload-progress"><div class="upload-progress-bar"></div></div>
						</div>
						<span class="hidden-file"><input type="file" name="file-input" autocomplete="off"></span>
						<input type="hidden" name="c_file<?php echo $i; ?>" value="">
					</li>
<?php
	endfor;
?>
				</ul>
			</div>
		</div>
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;"><?php echo __('本文'); ?></div>
			<div class="formContent inline-box">
<?php if (!preg_match('/CL_AIR/i', $_SERVER['HTTP_USER_AGENT'])): ?>
				<div id="dbbtn" class="mb4"></div>
<?php endif; ?>
				<textarea name="c_text" class="width-100 text-left font-size-100" rows="6"></textarea>
			</div>
		</div>
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;"><?php echo __('メール通知'); ?></div>
			<div class="formContent inline-box">
				<label class="mr16"><input type="checkbox" name="mail-reply" value="1"><?php echo __('返信の通知'); ?>（<span class="reply-name"></span>）</label>
				<label class="mr16"><input type="checkbox" name="mail-teacher" value="1"><?php echo __('先生に通知'); ?></label>
				<label><input type="checkbox" name="mail-student" value="1"><?php echo __('学生に通知'); ?>（<?php echo __(':num名',array('num'=>$aCCategory['ccStuNum'])); ?>）</label>
			</div>
		</div>
	</div>
	<div class="res-button-box">
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 ThreadRegist" style="padding: 4px 8px; display: none;" name="sub_state" value="1"><?php echo __('スレッドを登録する'); ?></button>
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 toComment" style="padding: 4px 8px; display: none;"><?php echo __('コメントする'); ?></button>
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 toUpdate" style="padding: 4px 8px; display: none;"><?php echo __('更新する'); ?></button>
		<button type="button" class="button default na width-auto CoopReplyToQuote font-size-90" style="padding: 4px 8px;"><?php echo __('引用'); ?></button>
		<button type="button" class="button default na width-auto CoopReplyToCancel font-size-90" style="padding: 4px 8px;"><?php echo __('キャンセル'); ?></button>
	</div>
<?php
	if (!preg_match('/CL_AIR/i', $_SERVER['HTTP_USER_AGENT'])):
		\Clfunc_Common::DropboxChooseBtn();
	endif;
?>
</form>


<?php if (preg_match('/KITKAT_EAT/i', $_SERVER['HTTP_USER_AGENT'])): ?>
<script type="text/javascript">
var fileNo = 0;
$(function() {
	$('.file-uploader .input-cover').on('click', function() {
		fileNo = $(this).attr('data');
		Android.openGallary();
		return false;
	});
});
function setFileUri(uri) {
	var field = $('.file-uploader .input-cover[data='+fileNo+']').parents('li').find('input[type=file]');
	$(field).attr('value',uri);
	$(field).trigger('change');
	return false;
}
</script>
<?php endif; ?>

