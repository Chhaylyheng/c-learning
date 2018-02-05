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
?>
<style>
header {
	display: none;
}
#content {
	margin: 0!important;
}
</style>
<div id="content-inner" style="padding: 16px;">
<h1 class="BentTitle"><i class="fa fa-book"></i> <?php echo $sClassName; ?></h1>
<div class="info-box mt0">
	<h2 class="QBTitle">
		<?php echo $sTitle; ?>
	</h2>
	<hr>
	<div class="QBNumHeader"><?php echo __('提出'); ?>：<span><?php echo $aTest['tpNum']; ?></span> / <span><?php echo $aTest['scNum']; ?></span></div>
	<dl class="QBList">
		<?php if (!is_null($aQuery)): ?>
		<?php foreach ($aQuery as $sK => $aQ): ?>
		<?php $qNO = (int)$aQ['tqSort']; ?>
		<dt>
			<h3>
				<p><span><?php echo __('問題').'.'.$qNO ?></span><span class="font-size-70"><?php echo ($aQ['tqStyle'] == 1)? '※'.__('複数回答'):''; ?></span></p>
				<p><?php echo nl2br($aQ['tqText']); ?></p>
			</h3>
			<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage'])): ?>
			<p><span class="ShowToggle" data="tbi-<?php echo $qNO; ?>"><i class="fa fa-picture-o fa-fw"></i><?php echo __('画像の表示/非表示'); ?></span></p>
			<div class="QBImage" id="tbi-<?php echo $qNO; ?>"><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></div>
			<?php endif; ?>
		</dt>
		<dd>
			<h4><span><?php echo __('解答'); ?></span></h4>
			<table class="QBBentAnsList">
			<thead>
			<tr>
				<th class="width-10 text-center"><?php echo __('番号'); ?></th>
				<th class="width-40 text-center"><?php echo __('解答内容'); ?></th>
				<th class="width-30 text-center"><?php echo __('グラフ'); ?></th>
				<th class="width-10 text-center"><?php echo __('割合'); ?></th>
				<th class="width-10 text-center"><?php echo __('解答数'); ?></th>
			</tr>
			</thead>
			<tbody>
				<?php
					if ($aQ['tqStyle'] < 2):
						$aRight = explode('|',$aQ['tqRight1']);
						$aQB = $aBent[$sK];
						$sMemberBase = $aQ['tbID'].'_'.$aQ['tqNO'].'_';
						foreach ($aQB as $i => $aB):
							if ($i == 0):
								continue;
							endif;
							if ($aQ['tqStyle'] == 0):
								$sAvg = ($aB['tbTotal'])? round((($aB['tbNum']/$aB['tbTotal'])*100),1):0;
							else:
								$sAvg = ($aB['tbAll'])? round((($aB['tbNum']/$aB['tbAll'])*100),1):0;
							endif;
							$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
							$sColor = (array_search($i, $aRight) !== false)? ' back-green font-white':'';
							$sMemberData = $sMemberBase.$i;
				?>
				<tr>
					<td class="text-center<?php echo $sColor; ?>"><?php echo $i; ?></td>
					<td>
						<?php echo nl2br($aQ['tqChoice'.$i]); ?>
						<?php if ($aQ['tqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i])): ?>
						<span class="ShowToggleClass" data="<?php echo $sK; ?>"><i class="fa fa-picture-o"></i></span>
						<div class="QBQImage <?php echo $sK; ?>"><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i]; ?>" style="max-width: 100%; max-height: 120px; width: auto; height: auto;"></div>
						<?php endif; ?>
					</td>
					<td class="QBGraphCell">
						<div class="QBGraphBox">
							<div class="QBGraph" style="width: <?php echo $sAvg; ?>%; height: 1.3em;"></div>
						</div>
					</td>
					<td class="text-right"><?php echo $sAvg; ?>%</td>
					<td class="text-right"><a href="#" class="TBNumMember" data="<?php echo $sMemberData; ?>"><?php echo __(':num名',array('num'=>$aB['tbNum'])); ?></a></td>
				</tr>
				<?php
						endforeach;
						if ($aQB[0]['tbNum'] > 0):
							$i = 0;
							$aB = $aQB[$i];
							if ($aQ['tqStyle'] == 0):
								$sAvg = ($aB['tbTotal'])? round((($aB['tbNum']/$aB['tbTotal'])*100),1):0;
							else:
								$sAvg = ($aB['tbAll'])? round((($aB['tbNum']/$aB['tbAll'])*100),1):0;
							endif;
							$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
							$sMemberData = $sMemberBase.$i;
				?>
				<tr>
					<td class="text-center"></td>
					<td><?php echo __('（無解答）'); ?></td>
					<td class="QBGraphCell">
						<div class="QBGraphBox">
							<div class="QBGraph" style="width: <?php echo $sAvg; ?>%; height: 1.3em;"></div>
						</div>
					</td>
					<td class="text-right"><?php echo $sAvg; ?>%</td>
					<td class="text-right"><a href="#" class="TBNumMember" data="<?php echo $sMemberData; ?>"><?php echo __(':num名',array('num'=>$aB['tbNum'])); ?></a></td>
				</tr>
				<?php
						endif;
					else:
						if (isset($aBent[$sK])):
							$aQB = $aBent[$sK];
							$iTotal = 0;
							$sMemberBase = $aQ['tbID'].'_'.$aQ['tqNO'].'_text_';
							foreach ($aQB as $i => $aB):
								$sMemberData = $sMemberBase;
								$sAvg = ($aB['tbTotal'])? round((($aB['tbNum']/$aB['tbTotal'])*100),1):0;
								$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
								$sColor = '';
								if ($aB['tbText'] != ''):
									if (
										($aQ['tqRight1'] == $aB['tbText']) ||
										($aQ['tqRight2'] == $aB['tbText']) ||
										($aQ['tqRight3'] == $aB['tbText']) ||
										($aQ['tqRight4'] == $aB['tbText']) ||
										($aQ['tqRight5'] == $aB['tbText'])
									):
										$sColor = ' back-green font-white';
									endif;
									$sMemberData .= urldecode($aB['tbText']);
				?>
				<tr class="">
					<td class="text-center<?php echo $sColor; ?>">
						<?php echo $i; ?>
						<span class="choiceDefault QBTextZoom largeShow" data="Default" title="拡大"><i class="fa fa-search" style="vertical-align: middle;"></i></span>
					</td>
					<td class="QBAnsText">
						<span><?php echo nl2br($aB['tbText']); ?></span>
					</td>
					<td class="QBGraphCell">
						<div class="QBGraphBox">
							<div class="QBGraph" style="width: <?php echo $sAvg; ?>%; height: 1.3em;"></div>
						</div>
					</td>
					<td class="text-right"><?php echo $sAvg; ?>%</td>
					<td class="text-right"><a href="#" class="TBNumMember" data="<?php echo $sMemberData; ?>"><?php echo __(':num名',array('num'=>$aB['tbNum'])); ?></a></td>
				</tr>
				<?php
									$iTotal += $aB['tbNum'];
								endif;
							endforeach;
							if ($iTotal < $aTest['tpNum']):
								$iBlank = $aTest['tpNum'] - $iTotal;
								$sAvg = round((($iBlank/$aTest['tpNum'])*100),1);
								$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
								$sMemberData = $sMemberBase;
				?>
				<tr>
					<td></td>
					<td class="QBAnsText"><?php echo __('（無解答）'); ?></td>
					<td class="QBGraphCell">
						<div class="QBGraphBox">
							<div class="QBGraph" style="width: <?php echo $sAvg; ?>%; height: 1.3em;"></div>
						</div>
					</td>
					<td class="text-right"><?php echo $sAvg; ?>%</td>
					<td class="text-right"><a href="#" class="TBNumMember" data="<?php echo $sMemberData; ?>"><?php echo __(':num名',array('num'=>$iBlank)); ?></a></td>
				</tr>
				<?php
							endif;
						endif;
					endif;
				?>
			</tbody>
			</table>
		</dd>
		<?php
				endforeach;
			endif;
		?>
	</dl>
</div>

<?php if (!preg_match('/CL_AIR_ANDROID/i', $_SERVER['HTTP_USER_AGENT'])): ?>
<div class="info-box">
<div class="button-box mt0">
	<button type="button" class="button default window-close"><?php echo __('集計結果を閉じる'); ?></button>
</div>
</div>
<?php endif; ?>

<div class="QBFloatBox" style="display: none;">
	<div class="QBFloatClose"><i class="fa fa-times"></i></div>
	<div class="QBFloatMember"></div>
</div>

</div>

