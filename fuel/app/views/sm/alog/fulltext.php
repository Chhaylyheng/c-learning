<?php if (is_null($aALTheme)): ?>
<div class="font-size: 80%;">
	<?php echo __('公開されている活動履歴テーマがありません。'); ?>
</div>
<?php else: ?>

<style>
strong {
	background-color: #FFCCAA;
}
</style>

<div style="margin-bottom: 5px; font-size: 80%;">
<?php echo Form::open(array('action'=>'/s/alog/fulltext'.Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div>
	<label><?php echo __('期間'); ?>:</label>
	<input type="text" name="sd" value="<?php echo date('Y/m/d',strtotime($aY[0])); ?>" style="width: 6em; text-align: center;"> -
	<input type="text" name="ed" value="<?php echo date('Y/m/d',strtotime($aY[1])); ?>" style="width: 6em; text-align: center;">
</div>
<div>
	<label><?php echo __('キーワード検索'); ?></label>
	<input type="text" name="w" value="<?php echo $sWords; ?>" placeholder="" class="width-30em inline-block">
</div>
<div>
	<?php echo Form::label(__('テーマ選択').':','sd'); ?>
	<select name="alt">
		<option value="0"><?php echo __('全て'); ?></option>
		<?php foreach ($aALTheme as $sAltID => $aT): ?>
		<?php $sSel = ($sAltID == $sAlt)? ' selected':''; ?>
		<option value="<?php echo $sAltID; ?>"<?php echo $sSel?>><?php echo $aT['altName']; ?></option>
		<?php endforeach; ?>
	</select>
</div>


<div style="text-align: center; margin-top: 4px;">
	<button type="submit" style="padding: 2px;" name="sub_state" value="1"><?php echo __('表示条件設定'); ?></button>
</div>
<?php Form::close(); ?>
</div>

<?php echo Clfunc_Mobile::hr(); ?>


<?php if (!is_null($aALogList)): ?>
<div>

<div style="font-size: 90%; color: #cc0000;"><?php echo __(':num件の記録が見つかりました。', array('num'=>count($aALogList))); ?></div>

<?php
foreach ($aALogList as $aL):
	$aT = $aALTheme[$aL['altID']];
	$sAltID = $aL['altID'];
	$iNO = $aL['no'];

	if ($aT['altFile']):
		$sPath = '';
		$sSize = '';
		if ($aL['fID'] != ''):
			$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aL['fID'],'mode'=>'e'));
			$sSize = \Clfunc_Common::FilesizeFormat($aL['fSize'],1);
			$sThumb = null;
			if ($aL['fFileType'] == 2):
				$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aL['fID'],'mode'=>'t'));
			endif;
			$sFile = '<div>'.ClFunc_Mobile::emj('CLIP').'<a href="'.$sPath.'" target="_blank">'.Clfunc_Common::SearchWordsReplace($sWords,$aL['fName']).'('.$sSize.')</a></div>';
		endif;
	endif;
?>

<div style="margin-top: 8px; font-size: 80%;">
<div style="background-color: #CCCCFF; padding: 3px 0px;">
	<a href="/s/alog/edit/<?php echo $sAltID.DS.$iNO; ?>"><?php echo ClFunc_Mobile::emj('PENCIL').__('編集'); ?></a>
	<span class="font-bold"><?php echo $aT['altName']; ?></span> <span class="font-size-80"><?php echo date('y/n/j H:i',strtotime($aL['alDate'])); ?></span>
	<?php if ($aT['altTitle']): ?>
		<?php echo ($aL['alTitle'])? '<div style="font-weight: bold;">'.Clfunc_Common::SearchWordsReplace($sWords,$aL['alTitle']).'</div>':''; ?>
	<?php endif; ?>
</div>
<?php if ($aT['altRange']): ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altRangeLabel']; ?>】</div>
	<?php if ($aL['alStart'] && $aL['alStart'] != CL_DATETIME_DEFAULT): ?>
	<?php echo date('y/n/j H:i',strtotime($aL['alStart'])); ?> ～ <?php echo date('y/n/j H:i',strtotime($aL['alEnd'])); ?>
	<?php endif; ?>
</div>
<?php endif; ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altTextLabel']; ?>】</div>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alText'])); ?>
</div>
<?php if ($aT['altFile']): ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altFileLabel']; ?>】</div>
	<?php if ($sPath): ?>
		<?php echo $sFile; ?>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($aT['altOpt1']): ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altOpt1Label']; ?>】</div>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alOpt1'])); ?>
</div>
<?php endif; ?>
<?php if ($aT['altOpt2']): ?>
<div style="border-bottom: 1px dashed silver; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo $aT['altOpt2Label']; ?>】</div>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alOpt2'])); ?>
</div>
<?php endif; ?>
<div style="border-bottom: 1px dashed silver; background-color: #FFCCCC; padding: 3px 0px;">
	<div style="color: #006600;">【<?php echo __('先生コメント'); ?>】</div>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alCom'])); ?>
</div>

</div>

<?php endforeach; ?>

</div>
<?php else: ?>

<?php if ($bPost): ?>

<div style="font-size: 80%;"><?php echo __('表示条件に一致する記録がありません。'); ?></div>

<?php else: ?>

<div style="font-size: 80%;"><?php echo __('表示条件を設定してください。'); ?></div>

<?php endif; ?>

<?php endif; ?>

<?php endif; ?>
