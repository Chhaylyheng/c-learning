<?php
$sRbID = $aReport['rbID'];
$sStID = $aStu['stID'];
$sTab = $sM;
$aDisp = array('s'=>'none','r'=>'none',);
$aDisp[$sTab] = 'block';

$aTab['s'] = array(
	'title' => __('共有板').'<i class="fa fa-comments mr4 ml8"></i><span class="rpComNum">'.$aPut['rpComNum'].'</span>',
);
if ($aReport['rbShare'] == 2):
$aTab['r'] = array(
	'title' => __('評価分布').'<i class="fa fa-star mr4 ml8"></i><span class="rpAvgScore">'.$aPut['rpAvgScore'].'</span>',
);
endif;
?>
<ul class="QBTabMenu" style="position: relative; z-index: 29;">
<?php
	foreach ($aTab as $sMode => $aT):
		$sAct = ($sTab == $sMode)? 'QBTabActive':'';
		echo '<li class="'.$sAct.'" data="'.$sMode.'">'.$aT['title'].'</li>';
	endforeach;
?>
</ul>

<?php
$aFiles = null;
for ($i = 1; $i <= 3; $i++):
	if ($aPut['fID'.$i] != ''):
		$aFiles[$i]['name'] = $aPut['fName'.$i];
		$aFiles[$i]['path'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aPut['fID'.$i],'mode'=>'e')).$aPut['fExt'.$i];
		$aFiles[$i]['size'] = \Clfunc_Common::FilesizeFormat($aPut['fSize'.$i],1);
		$aFiles[$i]['icon'] = 'paperclip';
		$aFiles[$i]['thumb'] = null;
		$aFiles[$i]['tag'] = '<i class="fa fa-'.$aFiles[$i]['icon'].' mr4"></i><a href="'.$aFiles[$i]['path'].'" target="_blank">'.$aFiles[$i]['name'].'（'.$aFiles[$i]['size'].'）</a><br>';

		switch ($aPut['fFileType'.$i]):
			case 2:
				$aFiles[$i]['thumb'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aPut['fID'.$i],'mode'=>'t'));
				$aFiles[$i]['tag'] = $aFiles[$i]['tag'].'<video style="width: 18em; max-width: 100%;" controls="controls" preload="none" src="'.$aFiles[$i]['path'].'" poster="'.$aFiles[$i]['thumb'].'"></video><br>';
			break;
			case 1:
				$aFiles[$i]['tag'] = $aFiles[$i]['tag'].'<img style="width: 18em; max-width: 100%;" src="'.$aFiles[$i]['path'].'" alt="'.$aFiles[$i]['name'].'('.$aFiles[$i]['size'].')"><br>';
			break;
		endswitch;
	endif;
	$sDate = ($aPut['rpDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aPut['rpDate']):'─';
endfor;
?>
<div class="info-box mt0 QBTabContents" id="s" style="display:<?php echo $aDisp['s'] ?>">

<?php if (isset($error)): ?>
	<p class="error-box"><?php echo $error; ?></p>
<?php endif; ?>

<div class="anchor-block" id="c0">
<div class="thread-box" style="border: none; padding: 0;">
<h2 class="thread-title"><?php echo __('提出内容') ?></h2>

<div class="thread-details">by <span class="thread-writer font-green"><?php echo $aStu['stName']; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
<div class="thread-body">
<p class="thread-text"><?php echo ($aPut['rpTeachPut'])? '['.__('先生による提出').']':($aPut['rpDate'] != CL_DATETIME_DEFAULT)? nl2br(\Clfunc_Common::url2link($aPut['rpText'],480)):'['.__('未提出').']'; ?></p>
<div class="">
<?php
if (!is_null($aFiles)):
	foreach ($aFiles as $aF):
		echo '<p class="mt0 mb8 mr4 ml4 va-top" style="display: inline-block;">'.$aF['tag'].'</p>';
	endforeach;
endif;
?>
</div>
</div>

<?php if ($aReport['rbShare'] == 2 && $aStudent['stID'] != $sStID): ?>
<div class="RRateGroup">
<button class="button na do width-auto ReportRateStart" style="padding: 8px 8px; position: relative; z-index: 100; display: inline-block; border-radius: 5px 0 0 5px;" obj="<?php echo $sRbID.'_'.$sStID; ?>"><?php echo __('評価する'); ?></button
><ul class="RRateStars" data="<?php echo $sRbID.'_'.$sStID.'_'.$aClass['ctID']; ?>"><?php
for ($i = 1; $i <= 5; $i++):
	$sStar = 'fa-star-o font-gray';
	if (!is_null($aRate) && $aRate['rrScore'] >= $i):
		$sStar = 'fa-star font-red';
	endif;
?><li class="RRStarBtn" value="<?php echo $i; ?>"><i class="fa mr0 <?php echo $sStar; ?>"></i></li><?php endfor; ?>
</ul>
<script>
var StarMem = new Object();
$.each($('.RRStarBtn'), function () {
	StarMem[$(this).val()] = $(this).find('i').attr('class');
});
</script>
</div>
<?php endif; ?>

<div class="thread-option">
<span class="thread-coms"><span class="font-size-160 rpComNum"><?php echo (int)$aPut['rpComNum']; ?></span><?php echo __('コメント'); ?></span>
<button class="button na do width-auto ReportCommentCreate" style="padding: 8px 8px; position: relative; z-index: 100;" obj="<?php echo $sRbID.'_'.$sStID; ?>"><?php echo __('コメントする'); ?></button>
</div>
<div class="res-field"></div>

<?php
	if (!is_null($aParents)):
?>
<ul class="comment-list">
<?php
	foreach ($aParents as $sP => $aC):
		$bTeach = preg_match('/^[t|a]/', $aC['rcID']);
		$cName = ($bTeach)? (($aC['atName'])? $aC['atName']:$aC['ttName']):$aC['stName'];
		$cColor = ($bTeach)? 'font-red':'font-green';
		$sJsKey = $aC['rbID'].'_'.$aC['stID'].'_'.$aC['no'];
		$sDate = ($aC['rcDate'] != CL_DATETIME_DEFAULT)? Clfunc_Tz::tz('Y/m/d H:i',$tz,$aC['rcDate']):'─';
		$iComNum = (int)(isset($aCnt['p'.$aC['no']]))? $aCnt['p'.$aC['no']]:0;
		if ($aC['rcID'] == $aStudent['stID']):
			$aWriter = array('font-green', $aStudent['stName']);
		else:
			switch ($aReport['rbAnonymous']):
				case 0:
					$aWriter = array('font-gray', __('匿名'));
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array('font-gray', __('匿名'));
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName);
				break;
			endswitch;
		endif;
		$sEditBtnDisp = ($aC['rcID'] == $aStudent['stID'])? '':' display: none;';
		$sDeleteBtnDisp = ($aC['rcID'] == $aStudent['stID'] && !$iComNum)? '':' display: none;';

?>
<li class="anchor-block" id="c<?php echo $aC['no']; ?>"><span class="tree-line"></span>
<div class="thread-box">
<div class="thread-details">by <span class="thread-writer <?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
<div class="thread-body">
<p class="thread-text"><?php echo nl2br(\Clfunc_Common::url2link($aC['rcComment'],480)); ?></p>
<p class="thread-text-raw"><?php echo $aC['rcComment']; ?></p>
</div>
<div class="thread-option">
<span class="thread-coms"><span class="font-size-160 thread-comnum"><?php echo $iComNum; ?></span><?php echo __('コメント'); ?></span>
<button type="button" class="button na do width-auto CoopReplyTo ml4" style="padding: 4px 8px; vertical-align: middle;" value="<?php echo $sJsKey; ?>"><?php echo __('返信する'); ?></button>
<button type="button" class="button na default width-auto CoopResEdit ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sEditBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('編集'); ?></button>
<button type="button" class="button na default width-auto CoopDelete ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sDeleteBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('削除'); ?></button>
</div>
<div class="thread-res"></div>

<?php
	if (isset($aComments[$sP]['children'])):
?>
<ul class="comment-list">
<?php
	foreach ($aComments[$sP]['children'] as $aCG):
		$bTeach = preg_match('/^[t|a]/', $aCG['rcID']);
		$cName = ($bTeach)? (($aCG['atName'])? $aCG['atName']:$aCG['ttName']):$aCG['stName'];
		$cColor = ($bTeach)? 'font-red':'font-green';
		$sJsKey = $aCG['rbID'].'_'.$aCG['stID'].'_'.$aCG['no'];
		$sDate = ($aCG['rcDate'] != CL_DATETIME_DEFAULT)? Clfunc_Tz::tz('Y/m/d H:i',$tz,$aCG['rcDate']):'─';
		if ($aCG['rcID'] == $aStudent['stID']):
			$aWriter = array('font-green', $aStudent['stName']);
		else:
			switch ($aReport['rbAnonymous']):
				case 0:
					$aWriter = array('font-gray', __('匿名'));
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array('font-gray', __('匿名'));
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName);
				break;
			endswitch;
		endif;
		$sEditBtnDisp = ($aCG['rcID'] == $aStudent['stID'])? '':' display: none;';
		$sDeleteBtnDisp = ($aCG['rcID'] == $aStudent['stID'])? '':' display: none;';
	?>
<li class="anchor-block" id="c<?php echo $aCG['no']; ?>"><span class="tree-line"></span>
<div class="thread-box">
<div class="thread-details">by <span class="thread-writer <?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
<div class="thread-body">
<p class="thread-text"><?php echo nl2br(\Clfunc_Common::url2link($aCG['rcComment'],480)); ?></p>
<p class="thread-text-raw"><?php echo $aCG['rcComment']; ?></p>
</div>
<div class="thread-option">
<button type="button" class="button na default width-auto CoopResEdit ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sEditBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('編集'); ?></button>
<button type="button" class="button na default width-auto CoopDelete ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sDeleteBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('削除'); ?></button>
</div>
<div class="thread-res"></div>
</div>
</li>
<?php
	endforeach;
?>
</ul>
<?php
	endif;
?>

</div>
</li>
<?php
	endforeach;
?>
</ul>
<?php
	endif;
?>
</div>
</div>

<div class="thread-box" style="display: none;">
<div class="thread-details">by <span class="thread-writer font-green"></span> on <span class="thread-date"></span></div>
<div class="thread-body">
<p class="thread-text"></p>
<p class="thread-text-raw"></p>
</div>
<div class="thread-option">
<span class="thread-coms"><span class="font-size-160 thread-comnum">0</span><?php echo __('コメント'); ?></span>
<button type="button" class="button na do width-auto CoopReplyTo ml4" style="padding: 4px 8px; vertical-align: middle;" value=""><?php echo __('返信する'); ?></button>
<button type="button" class="button na default width-auto CoopResEdit ml4" style="padding: 4px 8px; vertical-align: middle;" value=""><?php echo __('編集'); ?></button>
<button type="button" class="button na default width-auto CoopDelete ml4" style="padding: 4px 8px; vertical-align: middle;" value=""><?php echo __('削除'); ?></button>
</div>
<div class="thread-res"></div>
</div>

</div>

<div class="info-box mt0 QBTabContents" id="r" style="display:<?php echo $aDisp['r'] ?>">

<?php if ($aReport['rbShare'] == 2 && $aStudent['stID'] != $sStID): ?>
<div class="RRateGroup">
<button class="button na do width-auto ReportRateStart" style="padding: 8px 8px; position: relative; z-index: 100; display: inline-block; border-radius: 5px 0 0 5px;" obj="<?php echo $sRbID.'_'.$sStID; ?>"><?php echo __('評価する'); ?></button
><ul class="RRateStars" data="<?php echo $sRbID.'_'.$sStID.'_'.$aClass['ctID']; ?>"><?php
for ($i = 1; $i <= 5; $i++):
	$sStar = 'fa-star-o font-gray';
	if (!is_null($aRate) && $aRate['rrScore'] >= $i):
		$sStar = 'fa-star font-red';
	endif;
?><li class="RRStarBtn" value="<?php echo $i; ?>"><i class="fa mr0 <?php echo $sStar; ?>"></i></li><?php endfor; ?>
</ul>
</div>
<?php endif; ?>

<table id="RPRateDistribution">
<thead>
<tr>
	<th><?php echo __('評価点'); ?></th>
	<th><?php echo __('グラフ'); ?></th>
	<th><?php echo __('割合'); ?></th>
	<th><?php echo __('人数'); ?></th>
</tr>
</thead>
<tbody>
<?php
for ($i = 5; $i >= 1; $i--):
	$sAvg = ($aCount['rcNum'] > 0)?  round(($aCount['rc'.$i] / $aCount['rcNum']) * 100, 1):0;
	$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
?>
<tr id="gr<?php echo $i; ?>">
<td class="text-center"><?php echo $i; ?></td>
<td class="RPGraphCell">
	<div class="RPGraph" style="width: <?php echo $sAvg; ?>%; height: 1.2em;"></div>
</td>
<td class="text-right RPAvg"><?php echo $sAvg; ?>%</td>
<td class="text-right RPNum"><?php echo (int)$aCount['rc'.$i]; ?></td>
</tr>
<?php endfor; ?>
</tbody>
</table>

</div>

<form action="#" method="post" class="res-box width-100" style="display: none;">
	<input type="hidden" name="rb" value="<?php echo $sRbID; ?>">
	<input type="hidden" name="st" value="<?php echo $sStID; ?>">
	<input type="hidden" name="no" value="">
	<input type="hidden" name="mode" value="input">
	<input type="hidden" name="ct" value="<?php echo $aClass['ctID']; ?>">
	<div class="res-msg-box"></div>
	<div class="formControl font-size-90 width-100" style="margin: auto;">
		<div class="formGroup width-100">
			<div class="formLabel" style="min-width: 6em; width: 6em;"><?php echo __('コメント'); ?></div>
			<div class="formContent inline-box">
				<textarea name="c_text" class="width-100 text-left font-size-100" rows="6"></textarea>
			</div>
		</div>
	</div>
	<div class="res-button-box mt8">
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 toComment" style="padding: 4px 8px; display: none;"><?php echo __('コメントする'); ?></button>
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 toUpdate" style="padding: 4px 8px; display: none;"><?php echo __('更新する'); ?></button>
		<button type="button" class="button default na width-auto CoopReplyToQuote font-size-90" style="padding: 4px 8px;"><?php echo __('引用'); ?></button>
		<button type="button" class="button default na width-auto CoopReplyToCancel font-size-90" style="padding: 4px 8px;"><?php echo __('キャンセル'); ?></button>
	</div>
</form>

