<?php $disp = (!is_null($aALogList))? 'none':'block'; ?>
<?php $sAltID = $aALTheme['altID']; ?>

<div class="mt0">
	<h2><a href="#" class="link-out accordion" style="padding: 6px 0 6px 30px; background-position: 8px center;"><?php echo __('表示条件設定'); ?></a></h2>
	<div class="accordion-content acc-content-open" style="display: <?php echo $disp; ?>;">
	<div class="accordion-content-inner pt8">
<?php echo Form::open(array('action'=>'/t/alog/fulltext/'.$sAltID,'method'=>'get','role'=>'form','class'=>'form-inline')) ; ?>
<div class="form-group">
	<p class="mt4">
		<?php echo __('期間'); ?>
		<input type="text" name="sd" value="<?php echo date('Y/m/d',strtotime($aY[0])); ?>" id="datepick1" class="width-10em text-center inline-block"> ～
		<input type="text" name="ed" value="<?php echo date('Y/m/d',strtotime($aY[1])); ?>" id="datepick2" class="width-10em text-center inline-block">
	</p>
	<p class="mt4">
	<?php echo __('キーワード検索'); ?>
	<input type="text" name="w" value="<?php echo $sWords; ?>" placeholder="" class="width-30em inline-block">
	</p>
</div>
<div class="form-group mt8 button-box text-left">
	<button type="submit" class="button na do width-auto" style="padding: 8px;" name="sub_state" value="1"><?php echo __('表示条件設定'); ?></button>
</div>
<?php echo Form::close(); ?>
	</div>
	</div>
</div>

<?php if (!is_null($aALogList)): ?>
<div class="mt8 info-box" style="z-index: 1;">

<h2><?php echo __(':num件の記録が見つかりました。', array('num'=>count($aALogList))); ?></h2>

<?php
foreach ($aALogList as $aL):
	$iNO = $aL['no'];

	if ($aALTheme['altFile']):
		$sPath = '';
		$sSize = '';
		if ($aL['fID'] != ''):
			$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aL['fID'],'mode'=>'e'));
			$sSize = \Clfunc_Common::FilesizeFormat($aL['fSize'],1);
			$sIcon = 'paperclip';
			$sThumb = null;
			if ($aL['fFileType'] == 2):
				$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aL['fID'],'mode'=>'t'));
				$sIcon = 'film';
			endif;
			$sFile = '<p><i class="fa fa-'.$sIcon.'"></i> <a href="'.$sPath.'" target="_blank">'.Clfunc_Common::SearchWordsReplace($sWords,$aL['fName']).'（'.$sSize.'）</a></p>';
		endif;
	endif;
?>
<ul class="ALogText">
<li class="ALTitle">
	<a href="/t/alog/detail/<?php echo $sAltID.DS.$iNO; ?>" title="<?php echo __('コメント'); ?>"><i class="fa fa-commenting"></i></a>
	<span class="font-bold"><?php echo $aALTheme['altName']; ?></span> <span class="font-size-80"><?php echo Clfunc_Tz::tz('Y/m/d H:i',$tz,$aL['alDate']); ?></span>
	<h4 class="mt4 ml8"><?php echo '['.$aStudent[$aL['stID']]['stNO'].'] '.$aStudent[$aL['stID']]['stName']; ?></h4>
	<?php if ($aALTheme['altTitle']): ?>
		<?php echo ($aL['alTitle'])? '<h3>'.Clfunc_Common::SearchWordsReplace($sWords,$aL['alTitle']).'</h3>':''; ?>
	<?php endif; ?>
</li>
<?php if ($aALTheme['altRange']): ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aALTheme['altRangeLabel']; ?>】</p>
	<?php if ($aL['alStart'] && $aL['alStart'] != CL_DATETIME_DEFAULT): ?>
	<?php echo Clfunc_Tz::tz('Y/m/d H:i',$tz,$aL['alStart']); ?> ～ <?php echo Clfunc_Tz::tz('Y/m/d H:i',$tz,$aL['alEnd']); ?>
	<?php endif; ?>
</li>
<?php endif; ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aALTheme['altTextLabel']; ?>】</p>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alText'])); ?>
</li>
<?php if ($aALTheme['altFile']): ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aALTheme['altFileLabel']; ?>】</p>
	<?php if ($sPath): ?>
	<div class="" style="max-width: 640px;">
	<?php
			switch ($aL['fFileType']):
				case 2:	# 映像の場合
	?>
	<video class="width-100"  controls="controls" preload="none" src="<?php echo $sPath; ?>" poster="<?php echo $sThumb; ?>"></video><br>
	<?php echo Clfunc_Common::SearchWordsReplace($sWords,$aL['fName']).'（'.$sSize.'）'; ?>
	<?php
				break;
				case 1:
	?>
	<img class="width-100" src="<?php echo $sPath; ?>" alt="<?php echo $aL['fName'].'（'.$sSize.'）'; ?>"><br>
	<?php echo Clfunc_Common::SearchWordsReplace($sWords,$aL['fName']).'（'.$sSize.'）'; ?>
	<?php
				break;
				default:
					echo $sFile;
				break;
			endswitch;
	?>
	</div>
	<?php endif; ?>
	</li>
<?php endif; ?>
<?php if ($aALTheme['altOpt1']): ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aALTheme['altOpt1Label']; ?>】</p>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alOpt1'])); ?>
</li>
<?php endif; ?>
<?php if ($aALTheme['altOpt2']): ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aALTheme['altOpt2Label']; ?>】</p>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alOpt2'])); ?>
</li>
<?php endif; ?>
<li class="ALComment">
	<p class="font-bold font-green">【<?php echo __('先生コメント'); ?>】</p>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alCom'])); ?>
</li>

</ul>

<?php endforeach; ?>

</div>
<?php endif; ?>
