<div class="info-box mt0">
	<div class="QBNumHeader"><?php echo __('提出'); ?>：<span><?php echo ($aQuest['qpNum'] + $aQuest['qpGNum'] + $aQuest['qpTNum']); ?></span></div>
	<hr>
	<?php if ($aQuest['qbComment']): ?>
	<dl class="QBList">
		<dt>
			<h3>
				<p><span><?php echo __('先生からのコメント'); ?></span></p>
				<p><?php echo nl2br($aQuest['qbComment']); ?></p>
			</h3>
		</dt>
	</dl>
	<hr>
	<?php endif; ?>

<?php
	$sMode = 'ALL';
	$aAlfa = array(
		'fill'  => 1,
		'hover' => 0.85,
		'high'  => 0.7,
	);

	$bQuick = ($aQuest['qbQuickMode'])? true:false;
	$bIF = false;
	$bCom = false;
	$bComOnly = false;

	if ($aQuest['qbQuickMode'] == 1)
	{
		$bComOnly = true;
	}
	elseif ($aQuest['qbQuickMode'] < 30)
	{
		$bIF = true;
	}

	if ($bQuick && $bIF):
		$aColor = \Clfunc_Common::getChartColors(2);
		$bCom = isset($aQuery['qq2']);
?>

<style>
<?php foreach ($aColor as $i => $aC): ?>
.LD<?php echo $i; ?>{ background-color: <?php echo $aC['code']; ?>!important; }
.choice<?php echo $i; ?>{ background-color: <?php echo $aC['code']; ?>!important; }
.LC<?php echo $i; ?>{ border-left: 12px solid <?php echo $aC['code']; ?>!important; }
<?php endforeach; ?>
</style>
<script type="text/javascript">
var defaultValue = {
	'ALL': [<?php echo (int)$aBent['ALL']['qq1'][1]['qbNum']; ?>,<?php echo (int)$aBent['ALL']['qq1'][2]['qbNum']; ?>],
};
var Chart = {'ALL': null};
var objChart = {'ALL': null};
var ChartData = {'ALL': null};
function initQChart(mode) {
	var iS;
	var iH = ($(window).height() - $("#QChartALL").offset().top - 20);
	var iW = $("#QChart"+mode).width();
	iS = iH;
	if (iH > iW) {
		iH = iW;
		iS = iW;
	}
	$("#QChart"+mode).width(iS);
	$("#QChart"+mode).height(iS);

	ChartData[mode] = [
		{
			value: defaultValue.ALL[0],
			color: "rgba(<?php echo $aColor[1]['rgb'].','.$aAlfa['fill']; ?>)",
			highlight: "rgba(<?php echo $aColor[1]['rgb'].','.$aAlfa['hover']; ?>)",
			label: "<?php echo Clfunc_Common::TextToGraphLegend($aQuery['qq1']['qqChoice1']); ?>"
		},
		{
			value: defaultValue.ALL[1],
			color: "rgba(<?php echo $aColor[2]['rgb'].','.$aAlfa['fill']; ?>)",
			highlight: "rgba(<?php echo $aColor[2]['rgb'].','.$aAlfa['hover']; ?>)",
			label: "<?php echo Clfunc_Common::TextToGraphLegend($aQuery['qq1']['qqChoice2']); ?>"
		},
	];

	var ctx = $("#QChart"+mode).get(0).getContext("2d");
	Chart[mode] = new Chart(ctx);
	objChart[mode] = Chart[mode].Doughnut(ChartData[mode], {
		responsive : false,
		animationEasing : 'easeInOutCubic',
		percentageInnerCutout : 50,
	});

	DLegendSet(mode,iH,iW);
}
</script>

	<h2 class="QBTEXT mt0" obj="<?php echo $aQuest['qbID']; ?>">
		<?php echo nl2br($aQuery['qq1']['qqText']); ?>
	</h2>

	<hr>
	<div class="QuestChart" obj="<?php echo $aQuest['qbID']; ?>_<?php echo (int)isset($aQuery['qq2']); ?>">
		<div class="ChartBox" style="width: 48%;">
			<canvas id="QChart<?php echo $sMode; ?>" style="width: 100%;"></canvas>
			<ul class="DLegend" id="DLegend<?php echo $sMode; ?>">
				<li class="LD1"><span><?php echo $aBent[$sMode]['qq1'][1]['qbNum']; ?></span> (<span><?php echo ($aBent[$sMode]['qq1'][1]['qbAll'])? round(($aBent[$sMode]['qq1'][1]['qbNum']/$aBent[$sMode]['qq1'][1]['qbAll'])*100,1):0; ?></span>%)</li>
				<li class="LD2"><span><?php echo $aBent[$sMode]['qq1'][2]['qbNum']; ?></span> (<span><?php echo ($aBent[$sMode]['qq1'][2]['qbAll'])? round(($aBent[$sMode]['qq1'][2]['qbNum']/$aBent[$sMode]['qq1'][2]['qbAll'])*100,1):0; ?></span>%)</li>
			</ul>
		</div>
		<?php if ($bCom): ?>
		<div class="QBCommentFilter mb8">
			<span class="choiceAll"><?php echo __('全て表示'); ?></span>
			<?php for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++): ?>
			<span class="choice<?php echo $i; ?>"><?php echo $aQuery['qq1']['qqChoice'.$i]; ?></span>
			<?php endfor; ?>
		</div>
		<ul class="QBCommentList" mode="all" id="QChartCom<?php echo $sMode; ?>">
			<?php if (isset($aComment[$sMode])): ?>
			<?php foreach ($aComment[$sMode] as $sStID => $aC): ?>
			<?php if ($aC['text']): ?>
			<?php
					$sJsKey = $aQuest['qbID'].'_2_'.$sStID.'_'.$sMode;
					switch ((int)$aC['cPick']):
						case 1:
							$sBack = ' back-yellow';
							$sIcon = 'icon_pick_a.png';
						break;
						case -1:
							$sBack = ' back-silver';
							$sIcon = 'icon_pick_c.png';
						break;
						default:
							$sBack = '';
							$sIcon = 'icon_pick_b.png';
						break;
					endswitch;
			?>
			<li class="<?php echo $sStID.$sBack; ?>">
				<span class="choice<?php echo $aC['cNO']; ?>" style="cursor: default;" data="<?php echo $aC['cNO']; ?>"><?php echo $aC['cName']; ?></span>
				<?php if (preg_match('/^s.+/',$sStID)): ?>
				<div><?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aC['cPick'])); ?></div>
				<?php endif; ?>
				<span><?php echo nl2br($aC['text']); ?></span>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		</ul>
		<?php endif; ?>
	</div>
</div>

<?php
	elseif ($bQuick && !$bComOnly):
		$aColor = \Clfunc_Common::getChartColors($aQuery['qq1']['qqChoiceNum']);
		$bCom = isset($aQuery['qq2']);
		$aLabel    = null;
		$aFill     = null;
		$aStroke   = null;
		$aHFill    = null;
		$aHStroke  = null;
		$aData     = null;
		for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++):
			$aLabel[]   = ($bCom)? '"'.$i.'"':'"'.Clfunc_Common::TextToGraphLegend($aQuery['qq1']['qqChoice'.$i]).'"';
			$aFill[]    = '"rgba('.$aColor[$i]['rgb'].','.$aAlfa['hover'].')"';
			$aStroke[]  = '"rgba('.$aColor[$i]['rgb'].','.$aAlfa['fill'].')"';
			$aHFill[]   = '"rgba('.$aColor[$i]['rgb'].','.$aAlfa['high'].')"';
			$aHStroke[] = '"rgba('.$aColor[$i]['rgb'].','.$aAlfa['hover'].')"';
			foreach ($aBent as $sM => $aB):
				$aData[$sM][] = $aB['qq1'][$i]['qbNum'];
			endforeach;
		endfor;
?>

<style>
<?php foreach ($aColor as $i => $aC): ?>
.L<?php echo $i; ?>{ background-color: <?php echo $aC['code']; ?>!important; }
.choice<?php echo $i; ?>{ background-color: <?php echo $aC['code']; ?>!important; }
.LC<?php echo $i; ?>{ border-left: 12px solid <?php echo $aC['code']; ?>!important; }
<?php endforeach; ?>
</style>
<script type="text/javascript">
var defaultValue = {
	'ALL': [<?php echo implode(',', $aData['ALL']); ?>],
};
var Chart = {'ALL': null};
var objChart = {'ALL': null};
var ChartData = {'ALL': null};
function initQChart(mode) {
	var iH = ($(window).height() - $("#QChartALL").offset().top - 40);
	var iW = $("#QChart"+mode).width();
	if (iH > iW) { iH = iW; }
	if (iH < iW) { iH = iW; }
	$("#QChart"+mode).css({'height':iH+'px'});

	ChartData[mode] = {
		labels: [<?php echo implode(',', $aLabel); ?>],
		datasets: [
			{
				fillColor      : [<?php echo implode(',', $aFill); ?>],
				strokeColor    : [<?php echo implode(',', $aStroke); ?>],
				highlightFill  : [<?php echo implode(',', $aHFill); ?>],
				highlightStroke: [<?php echo implode(',', $aHStroke); ?>],
				data           : defaultValue[mode]
			}
		]
	};

	var ctx = $("#QChart"+mode).get(0).getContext("2d");
	Chart[mode] = new Chart(ctx);
	objChart[mode] = Chart[mode].Bar(ChartData[mode], {
		responsive : false,
		animationEasing : 'easeInOutCubic',
		scaleGridLineColor : "rgba(0,0,0,0.4)",
		scaleLineColor: "rgba(0,0,0,0.8)",
		scaleFontColor: "#333",
		scaleFontSize: 8,
	});
}
</script>

	<h2 class="QBTEXT mt0" obj="<?php echo $aQuest['qbID']; ?>">
		<?php echo nl2br($aQuery['qq1']['qqText']); ?>
	</h2>

	<hr>
	<div class="QuestChart" obj="<?php echo $aQuest['qbID']; ?>_<?php echo (int)isset($aQuery['qq2']); ?>">
		<div class="ChartBox" style="width: 48%;">
			<canvas id="QChart<?php echo $sMode; ?>" style="width: 100%;"></canvas>
		</div>
		<?php if ($bCom): ?>
		<div class="QBCommentFilter mb8">
			<span class="choiceAll"><?php echo __('全て表示'); ?></span>
			<?php for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++): ?>
			<span class="choice<?php echo $i; ?>"><?php echo $aQuery['qq1']['qqChoice'.$i]; ?></span>
			<?php endfor; ?>
		</div>
		<ul class="QBCommentList" mode="all" id="QChartCom<?php echo $sMode; ?>">
			<?php if (isset($aComment[$sMode])): ?>
			<?php foreach ($aComment[$sMode] as $sStID => $aC): ?>
			<?php if ($aC['text']): ?>
			<?php
					$sJsKey = $aQuest['qbID'].'_2_'.$sStID.'_'.$sMode;
					switch ((int)$aC['cPick']):
						case 1:
							$sBack = ' back-yellow';
							$sIcon = 'icon_pick_a.png';
						break;
						case -1:
							$sBack = ' back-silver';
							$sIcon = 'icon_pick_c.png';
						break;
						default:
							$sBack = '';
							$sIcon = 'icon_pick_b.png';
						break;
					endswitch;
			?>
			<li class="<?php echo $sStID.$sBack; ?>">
				<span class="choice<?php echo $aC['cNO']; ?>" style="cursor: default;" data="<?php echo $aC['cNO']; ?>"><?php echo $aC['cName']; ?></span>
				<?php if (preg_match('/^s.+/',$sStID)): ?>
				<div><?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aC['cPick'])); ?></div>
				<?php endif; ?>
				<span><?php echo nl2br($aC['text']); ?></span>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		</ul>
		<?php endif; ?>
	</div>
</div>


<?php
	elseif ($bComOnly):
?>

	<h2 class="QBTEXT mt0" obj="<?php echo $aQuest['qbID']; ?>">
		<?php echo nl2br($aQuery['qq1']['qqText']); ?>
	</h2>

	<hr>
	<div class="QuestCommentOnly" obj="<?php echo $aQuest['qbID']; ?>">
		<ul class="QBCommentList" mode="all" id="QChartCom<?php echo $sMode; ?>">
			<?php if (isset($aBent[$sMode]['qq1'])): ?>
			<?php
				$aComment = $aBent[$sMode]['qq1'];
				krsort($aComment);
			?>
			<?php foreach ($aComment as $aC): ?>
			<?php if ($aC['qbText']): ?>
			<?php
					$sStID = $aC['stID'];
					$sJsKey = $aQuest['qbID'].'_1_'.$sStID.'_'.$sMode;
					switch ((int)$aC['qaPick']):
						case 1:
							$sBack = ' back-yellow';
							$sIcon = 'icon_pick_a.png';
						break;
						case -1:
							$sBack = ' back-silver';
							$sIcon = 'icon_pick_c.png';
						break;
						default:
							$sBack = '';
							$sIcon = 'icon_pick_b.png';
						break;
					endswitch;
			?>
			<li class="<?php echo $aC['stID'].$sBack; ?>">
				<?php if (preg_match('/^s.+/',$sStID)): ?>
				<div><?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aC['qaPick'])); ?></div>
				<?php else: ?>
				<div></div>
				<?php endif; ?>
				<span><?php echo nl2br($aC['qbText']); ?></span>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>
</div>


<?php else: ?>

	<dl class="QBList">
		<?php if (!is_null($aQuery)): ?>
		<?php foreach ($aQuery as $sK => $aQ): ?>
		<?php $qNO = (int)$aQ['qqSort']; ?>
		<dt>
			<h3>
				<p><span><?php echo __('設問.:no',array('no'=>$qNO)); ?></span><span class="font-size-70"><?php echo ($aQ['qqStyle'] == 1)? '※'.__('複数回答'):''; ?></span></p>
				<p><?php echo nl2br($aQ['qqText']); ?></p>
			</h3>
			<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage'])): ?>
			<p><span class="ShowToggle" data="qbi-<?php echo $qNO; ?>"><i class="fa fa-picture-o fa-fw"></i><?php echo __('画像の表示/非表示'); ?></span></p>
			<div class="QBImage" id="qbi-<?php echo $qNO; ?>"><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage']; ?>" style="max-width: 100%; max-height: 480px; width: auto; height: auto;"></div>
			<?php endif; ?>
		</dt>
		<dd>
			<h4><span><?php echo __('回答'); ?></span></h4>
			<table class="QBBentAnsList">
			<tr>
				<?php if ($aQ['qqStyle'] < 2): ?>
				<th class="width-10 text-center"><?php echo __('番号'); ?></th>
				<th class="width-40 text-center"><?php echo __('回答内容'); ?></th>
				<th class="width-50 text-center QBGraphCell"><?php echo __('グラフ'); ?></th>
				<?php else: ?>
				<th class="width-100 text-center"><?php echo __('回答内容'); ?></th>
				<?php endif; ?>
			</tr>
				<?php
					if ($aQ['qqStyle'] < 2):
						$aQB = $aBent['ALL'][$sK];
						if ($aQuest['qbQuerySort'] == 1):
							krsort($aQB);
						endif;
						foreach ($aQB as $i => $aB):
							if ($i == 0):
								continue;
							endif;
							if ($aQ['qqStyle'] == 0):
								$sAvg = ($aB['qbTotal'])? round((($aB['qbNum']/$aB['qbTotal'])*100),1):0;
							else:
								$sAvg = ($aB['qbAll'])? round((($aB['qbNum']/$aB['qbAll'])*100),1):0;
							endif;
							$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
				?>
				<tr>
					<td class="text-center"><?php echo $i; ?></td>
					<td>
						<?php echo nl2br($aQ['qqChoice'.$i]); ?>
						<?php if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i])): ?>
						<span class="ShowToggleClass" data="<?php echo $sK; ?>"><i class="fa fa-picture-o"></i></span>
						<div class="QBQImage <?php echo $sK; ?>"><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i]; ?>" style="max-width: 100%; max-height: 120px; width: auto; height: auto;"></div>
						<?php endif; ?>
					</td>
					<td class="QBGraphCell">
						<div class="QBGraphBox">
							<div class="QBGraph" style="width: <?php echo $sAvg; ?>%; height: 1.3em;"></div>
							<div class="QBNum"><?php echo $sAvg; ?>% / <?php echo __(':num名',array('num'=>$aB['qbNum'])); ?></div>
						</div>
					</td>
				</tr>
				<?php
						endforeach;
						if ($aQB[0]['qbNum'] > 0):
							$i = 0;
							$aB = $aQB[$i];
							if ($aQ['qqStyle'] == 0):
								$sAvg = ($aB['qbTotal'])? round((($aB['qbNum']/$aB['qbTotal'])*100),1):0;
							else:
								$sAvg = ($aB['qbAll'])? round((($aB['qbNum']/$aB['qbAll'])*100),1):0;
							endif;
							$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
				?>
				<tr>
					<td class="text-center"></td>
					<td><?php echo __('（無回答）'); ?></td>
					<td class="QBGraphCell">
						<div class="QBGraphBox">
							<div class="QBGraph" style="width: <?php echo $sAvg; ?>%; height: 1.3em;"></div>
							<div class="QBNum"><?php echo $sAvg; ?>% / <?php echo __(':num名',array('num'=>$aB['qbNum'])); ?></div>
						</div>
					</td>
				</tr>
				<?php
						endif;
					else:
						if (isset($aBent['ALL'][$sK])):
							$aQB = $aBent['ALL'][$sK];
							$iBlank = 0;
							foreach ($aQB as $i => $aB):
								if ($aB['qbText'] != ''):
									$sJsKey = $aB['qbID'].'_'.$aB['qqNO'].'_'.$aB['stID'];
									switch ($aB['qaPick']):
										case 1:
											$sBack = 'back-yellow';
											$sIcon = 'icon_pick_a.png';
										break;
										case -1:
											$sBack = 'back-silver';
											$sIcon = 'icon_pick_c.png';
										break;
										default:
											$sBack = '';
											$sIcon = 'icon_pick_b.png';
										break;
									endswitch;
				?>
				<tr class="<?php echo $sBack; ?>">
					<td class="QBAnsText">
						<?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aB['qaPick'])); ?>
						<?php echo nl2br($aB['qbText']); ?>
					</td>
				</tr>
				<?php
								else:
									$iBlank++;
								endif;
							endforeach;
							if ($iBlank > 0):
								$sBlank = '<tr><td class="QBAnsText">'.__('（無回答：:num名）',array('num'=>$iBlank)).'</td></tr>';
								echo $sBlank;
							endif;
						endif;
					endif;
				?>
			</table>
		</dd>
		<?php
				endforeach;
			endif;
		?>
	</dl>
</div>

<?php endif; ?>
