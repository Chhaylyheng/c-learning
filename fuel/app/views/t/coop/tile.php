<?php
$sPWH = '';
$sSep = '';
if ($aClass['dpNO'])
{
	$sPWH .= $aPeriod[$aClass['dpNO']];
	$sSep = '/';
}
if ($aClass['ctWeekDay'])
{
	$sPWH .= $sSep.$aWeekDay[$aClass['ctWeekDay']];
	$sSep = '/';
}
if ($aClass['dhNO'])
{
	$sPWH .= $sSep.$aHour[$aClass['dhNO']];
}
if ($sPWH)
{
	$sPWH = '（'.$sPWH.'）';
}
$sClassName = $aClass['ctName'].$sPWH.__(':num名',array('num'=>$aClass['scNum']));

$iCnt = count($aStudent);
$iCol = ceil(sqrt($iCnt));
$iCol = ($iCol > 6)? 6:$iCol;
$iWidth = round((100/$iCol),2) - 0.4;
$iHeight = floor($iWidth * (3/4));

$sBase = $aParent['ccID'].'_'.$aParent['cNO'];
?>
<style>
header {
	display: none;
}
#content {
	margin: 0!important;
	padding-bottom: 0!important;
}
</style>
<div id="content-inner" style="padding: 4px;">
<h1 class="CoopTileTitle"><i class="fa fa-book"></i> <?php echo $sClassName; ?> - <?php echo $aParent['cTitle']; ?></h1>

<ul class="info-box mt0 CoopTile" id="CoopTile" style="padding: 4px;" data="<?php echo $sBase; ?>"
<?php foreach ($aStudent as $sStID => $aS): ?>
><li class="normal-border" style="width: <?php echo $iWidth; ?>%;" id="<?php echo $sStID; ?>" data=""
><div class="text-box" style="min-height: <?php echo $iHeight; ?>px;"><i class="fa fa-picture-o"></i><p></p></div
><div class="img-box" style="display: none; min-height: <?php echo $iHeight; ?>px;"
><img src="" alt="" class="CoopTileLoadImage"
></div
><p class="CoopTileName"><?php echo (($aCCategory['ccAnonymous'] == 2)? $aS['stName']:__('匿名')); ?></p
><div class="border-box"> </div
></li
<?php endforeach; ?>
></ul>

<?php if (!preg_match('/CL_AIR_ANDROID/i', $_SERVER['HTTP_USER_AGENT'])): ?>
<div class="info-box">
<div class="button-box mt0">
	<button type="button" class="button default window-close"><?php echo __('閉じる'); ?></button>
</div>
</div>
<?php endif; ?>

<button type="button" class="compare_button button confirm"><?php echo __('比較'); ?></button>