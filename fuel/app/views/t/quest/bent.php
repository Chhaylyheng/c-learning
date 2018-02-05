<script type="text/javascript">
<?php if (!preg_match('/(CL_AIR|iPhone|Android)/',$_SERVER['HTTP_USER_AGENT'])): ?>
if (window.opener && !window.opener.closed) {
	window.opener.location.reload();
}
<?php endif; ?>
var intervalTime = 7000;
</script>
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
$sClassName = $aClass['ctName'].$sPWH.' <i class="fa fa-user"></i>'.$aClass['scNum'];

$aOpen = array(__('ゲストなし'),'font-default');
switch ($aQuest['qbOpen']):
	case 1:
		$aOpen = array(__('匿名'),'font-green');
	break;
	case 2:
		$aOpen = array(__('記名'),'font-blue');
	break;
endswitch;
$sDropKey = $aQuest['ctID'].'_'.$aQuest['qbID'];
$sSwitchURL = '/t/quest/switchOpen/'.$aQuest['qbID'].'/';

$bQuick = ($aQuest['qbQuickMode'])? true:false;
$bIF = false;
$bCom = false;
$bComOnly = false;
$iChartWidth = 48;

if ($aQuest['qbQuickMode'] == 1):
	$bComOnly = true;
elseif ($aQuest['qbQuickMode'] < 30):
	$bIF = true;
endif;
if ($bQuick):
	$bCom = isset($aQuery['qq2']);
	$sAlign = 'left';
	if (!$bCom):
		$sAlign = 'none';
	endif;
endif;

$aPub = array(__('締切'),'font-red');
if ($aQuest['qbPublic'] == 1):
	$aPub = array(__('公開中'),'font-blue');
	if ($aQuest['qbAutoCloseDate'] != CL_DATETIME_DEFAULT):
		$aPub[2] = '～ '.ClFunc_Tz::tz('n/j H:i',$tz,$aQuest['qbAutoCloseDate']);
	endif;
elseif ($aQuest['qbPublic'] == 0):
	$aPub = array(__('非公開'),'font-default');
	if ($aQuest['qbAutoPublicDate'] != CL_DATETIME_DEFAULT):
		$aPub[2] = ClFunc_Tz::tz('n/j H:i',$tz,$aQuest['qbAutoPublicDate']).' ～';
	endif;
endif;
?>
<style>
header {
	display: none;
}
#content {
	margin: 0!important;
}
</style>
<div id="content-inner" style="padding: 4px;">
<h1 class="BentTitle mt8 mb8"><i class="fa fa-book"></i> <?php echo $sClassName; ?> [<?php echo $aClass['ctCode']; ?>]</h1>

<div style="position: absolute; top: 4px; right: 8px;">

<div class="dropdown" style="display: inline-block;">
	<button type="button" class="open-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sDropKey; ?>_public"><div><?php echo $aPub[0]; ?><?php if (isset($aPub[2])): ?> <span class="font-size-80"><?php echo $aPub[2]; ?></span><?php endif;?></div></button>
</div>

<?php if ($bQuick && ($bComOnly || $bCom)): ?>
	<div class="button-group" style="display: inline-block;">
		 <button class="text-center QBCommentFontSize font-size-70"  data="small" title="<?php echo __('コメント サイズ').'：'.__('小'); ?>"><?php echo __('小'); ?></button
		><button class="text-center QBCommentFontSize font-size-100" data="middle" title="<?php echo __('コメント サイズ').'：'.__('中'); ?>"><?php echo __('中'); ?></button
		><button class="text-center QBCommentFontSize font-size-140" data="large" title="<?php echo __('コメント サイズ').'：'.__('大'); ?>"><?php echo __('大'); ?></button>
	</div>
<?php endif; ?>
	<div class="dropdown" style="display: inline-block;">
		<button type="button" class="open-dropdown-toggle <?php echo $aOpen[1]; ?>" id="<?php echo $sDropKey; ?>_guest"><div><?php echo $aOpen[0]; ?></div></button>
	</div>
<?php if ($aQuest['qbOpen']): ?>
	<a href="/print/t/GuestLogin/<?php echo $aClass['ctID']; ?>" target="_blank" class="button default na width-auto text-center" style="padding: 8px 8px 7px; vertical-align: top; display: inline-block;" title="<?php echo __('ゲストログイン配布資料印刷'); ?>"><i class="fa fa-file mr0"></i></a>
<?php endif; ?>
	<button class="button default na width-auto text-center VisibleToggle" data="TeachComment" style="padding: 6px 6px 7px; vertical-align: top; display: inline-block;" title="<?php echo __('全体コメント入力'); ?>"><i class="fa fa-comment mr0"></i></button>
</div>

<div style="position: absolute; top: 8px; right: 8px;">
</div>

<?php
	$aTab = array(
		'ALL' => array(
			'title' => __('全体集計'),
			'num'   => __('提出').'：<span class="strong sp-num">'.$aQuest['qpNum'].'</span>',
		),
	);
	if ($aQuest['qbOpen'] > 0 || $aQuest['qpGNum'] > 0 || $aQuest['qpTNum'] > 0):
		$aTab['ALL']['num'] = __('学生').'：<span class="strong sp-num">'.$aQuest['qpNum'].'</span>';
		$aTab['STUDENT'] = array(
			'title' => __('学生集計'),
			'num'   => __('学生').'：<span class="strong sp-num">'.$aQuest['qpNum'].'</span>',
		);
		$sAct = 'QBTabActive';
		if ($aQuest['qbOpen'] > 0 || $aQuest['qpGNum'] > 0):
			$aTab['ALL']['num'] .= '　'.__('ゲスト').'：<span class="strong g-num">'.$aQuest['qpGNum'].'</span>';
			$aTab['GUEST'] = array(
				'title' => __('ゲスト集計'),
				'num'   => __('ゲスト').'：<span class="strong g-num">'.$aQuest['qpGNum'].'</span>',
			);
		endif;
		if ($aQuest['qpTNum'] > 0):
			$aTab['ALL']['num'] .= '　'.__('先生').'：<span class="strong t-num">'.$aQuest['qpTNum'].'</span>';
			$aTab['TEACH'] = array(
				'title' => __('先生集計'),
				'num'   => __('先生').'：<span class="strong t-num">'.$aQuest['qpTNum'].'</span>',
			);
		endif;
?>
<ul class="QBTabMenu">
<?php
	foreach ($aTab as $sMode => $aT):
		echo '<li class="'.$sAct.'" data="'.$sMode.'">'.$aT['title'].'</li>';
		$sAct = '';
	endforeach;
?>
</ul>
<?php endif; ?>

<?php
	$aAlfa = array(
		'fill'  => 1,
		'hover' => 0.85,
		'high'  => 0.7,
	);

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
	'STUDENT': [<?php echo (int)$aBent['STUDENT']['qq1'][1]['qbNum']; ?>,<?php echo (int)$aBent['STUDENT']['qq1'][2]['qbNum']; ?>],
	'GUEST': [<?php echo (int)$aBent['GUEST']['qq1'][1]['qbNum']; ?>,<?php echo (int)$aBent['GUEST']['qq1'][2]['qbNum']; ?>],
	'TEACH': [<?php echo (int)$aBent['TEACH']['qq1'][1]['qbNum']; ?>,<?php echo (int)$aBent['TEACH']['qq1'][2]['qbNum']; ?>]
};
var Chart = {'ALL': null, 'STUDENT': null, 'GUEST': null, 'TEACH': null};
var objChart = {'ALL': null, 'STUDENT': null, 'GUEST': null, 'TEACH': null};
var ChartData = {'ALL': null, 'STUDENT': null, 'GUEST': null, 'TEACH': null};
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
	setInterval(function() { DoughnutChartUpdate(mode) },intervalTime);

	if (mode != 'ALL') {
		$('#'+mode).css({'display':'none'});
	}
}
</script>

<?php foreach ($aTab as $sMode => $aT): ?>
<div class="info-box mt0 QBTabContents" id="<?php echo $sMode; ?>" style="display: block;">
	<h2 class="QBTEXT" obj="<?php echo $aQuest['qbID']; ?>">
		<span class="QBTitle"><?php echo $sTitle; ?></span>
		<button class="QBTextEditShow" style="cursor: pointer;"><i class="fa fa-edit"></i></button>
		<span class="QBText"><?php echo nl2br($aQuery['qq1']['qqText']); ?></span>
		<span class="QBTextEdit" style="display: none;"><input type="text" value="<?php echo $aQuery['qq1']['qqText']; ?>" name="qbtext" style="width: 24em;"><button class="button na default width-auto"><?php echo __('更新'); ?></button></span>
	</h2>

	<hr>
	<div class="QuestChart" obj="<?php echo $aQuest['qbID']; ?>_<?php echo (int)isset($aQuery['qq2']); ?>">
		<div class="ChartBox" style="width: <?php echo $iChartWidth; ?>%; margin: 0 auto;">
			<?php if ($bCom): ?>
			<div class="ChartBoxResize font-silver" data="small"><i class="fa fa-caret-left fa-2x"></i></div>
			<?php endif; ?>
			<div class="QBNumHeader" id="QChartNum<?php echo $sMode; ?>"><?php echo $aT['num']; ?></div>
			<canvas id="QChart<?php echo $sMode; ?>" style="width: 100%;"></canvas>
			<ul class="DLegend" id="DLegend<?php echo $sMode; ?>">
				<li class="LD1"><span><?php echo $aBent[$sMode]['qq1'][1]['qbNum']; ?></span> (<span><?php echo ($aBent[$sMode]['qq1'][1]['qbAll'])? round(($aBent[$sMode]['qq1'][1]['qbNum']/$aBent[$sMode]['qq1'][1]['qbAll'])*100,1):0; ?></span>%)</dd>
				<li class="LD2"><span><?php echo $aBent[$sMode]['qq1'][2]['qbNum']; ?></span> (<span><?php echo ($aBent[$sMode]['qq1'][2]['qbAll'])? round(($aBent[$sMode]['qq1'][2]['qbNum']/$aBent[$sMode]['qq1'][2]['qbAll'])*100,1):0; ?></span>%)</dd>
			</ul>
		</div>
		<?php if (!$bCom): ?>
		<ul class="LegendBox">
			<?php for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++): ?>
			<li><span class="choice<?php echo $i; ?>">　</span> <?php echo $aQuery['qq1']['qqChoice'.$i]; ?></li>
			<?php endfor; ?>
		</ul>
		<?php endif; ?>

		<?php if ($bCom): ?>
		<div class="CommentBox">
			<div class="QBCommentFilter mb8 font-size-140">
				<span class="choiceAll"><?php echo __('全て表示'); ?></span>
				<?php for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++): ?>
				<span class="choice<?php echo $i; ?>"><?php echo $aQuery['qq1']['qqChoice'.$i]; ?></span>
				<?php endfor; ?>
				<span class="posted-toggle" title="<?php echo __('回答者の表示/非表示'); ?>"><i class="fa fa-user"></i></span>
			</div>
			<ul class="QBCommentList font-size-140" mode="all" id="QChartCom<?php echo $sMode; ?>">
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
					<span class="choice<?php echo $aC['cNO']; ?> largeShow" data="<?php echo $aC['cNO']; ?>"><?php echo $aC['cName']; ?></span>
					<?php if (preg_match('/^s.+/',$sStID)): ?>
					<div class="dropdown inline-block">
						<button type="button" class="ans-dropdown-toggle" id="<?php echo $sJsKey; ?>"><div><?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aC['cPick'])); ?></div></button>
					</div>
					<?php endif; ?>
					<span><?php echo nl2br($aC['text']); ?></span>
					<span class="posted font-size-60" style="display: none;">（<?php echo $aC['cPosted']; ?>）</span>
				</li>
				<?php endif; ?>
				<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endforeach; ?>

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
			foreach ($aBent as $sMode => $aB):
				$aData[$sMode][] = $aB['qq1'][$i]['qbNum'];
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
	'STUDENT': [<?php echo implode(',', $aData['STUDENT']); ?>],
	'GUEST': [<?php echo implode(',', $aData['GUEST']); ?>],
	'TEACH': [<?php echo implode(',', $aData['TEACH']); ?>]
};
var Chart = {'ALL': null, 'STUDENT': null, 'GUEST': null, 'TEACH': null};
var objChart = {'ALL': null, 'STUDENT': null, 'GUEST': null, 'TEACH': null};
var ChartData = {'ALL': null, 'STUDENT': null, 'GUEST': null, 'TEACH': null};
function initQChart(mode) {
	var iH = ($(window).height() - $("#QChartALL").offset().top - 40);
	var iW = $("#QChart"+mode).width();
	if (iH > iW) { iH = iW; }
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
		scaleFontSize: 11,
	});

	setInterval(function() { BarChartUpdate(mode) },intervalTime);

	if (mode != 'ALL') {
		$('#'+mode).css({'display':'none'});
	}
}
</script>

<?php foreach ($aTab as $sMode => $aT): ?>
<div class="info-box mt0 QBTabContents" id="<?php echo $sMode; ?>" style="display: block;">
	<h2 class="QBTEXT" obj="<?php echo $aQuest['qbID']; ?>">
		<span class="QBTitle"><?php echo $sTitle; ?></span>
		<button class="QBTextEditShow" style="cursor: pointer;"><i class="fa fa-edit"></i></button>
		<span class="QBText"><?php echo nl2br($aQuery['qq1']['qqText']); ?></span>
		<span class="QBTextEdit" style="display: none;"><input type="text" value="<?php echo $aQuery['qq1']['qqText']; ?>" name="qbtext" style="width: 24em;"><button class="button na default width-auto"><?php echo __('更新'); ?></button></span>
	</h2>

	<hr>
	<div class="QuestChart" obj="<?php echo $aQuest['qbID']; ?>_<?php echo (int)isset($aQuery['qq2']); ?>">
		<div class="ChartBox" style="width: <?php echo $iChartWidth; ?>%; margin: 0 auto;">
			<div class="QBNumHeader" id="QChartNum<?php echo $sMode; ?>"><?php echo $aT['num']; ?></div>
			<canvas id="QChart<?php echo $sMode; ?>" style="width: 100%;"></canvas>
		</div>
		<?php if (!$bCom): ?>
		<ul class="LegendBox">
			<?php for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++): ?>
			<li><span class="choice<?php echo $i; ?>">　</span> <?php echo $aQuery['qq1']['qqChoice'.$i]; ?></li>
			<?php endfor; ?>
		</ul>
		<?php endif; ?>

		<?php if ($bCom): ?>
		<div class="CommentBox">
			<div class="QBCommentFilter mb8 font-size-140">
				<span class="choiceAll"><?php echo __('全て表示'); ?></span>
				<?php for ($i = 1; $i <= $aQuery['qq1']['qqChoiceNum']; $i++): ?>
				<span class="choice<?php echo $i; ?>"><?php echo $aQuery['qq1']['qqChoice'.$i]; ?></span>
				<?php endfor; ?>
				<span class="posted-toggle" title="<?php echo __('回答者の表示/非表示'); ?>"><i class="fa fa-user"></i></span>
			</div>
			<ul class="QBCommentList font-size-140" mode="all" id="QChartCom<?php echo $sMode; ?>">
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
					<span class="choice<?php echo $aC['cNO']; ?> largeShow" data="<?php echo $aC['cNO']; ?>"><?php echo $aC['cName']; ?></span>
					<?php if (preg_match('/^s.+/',$sStID)): ?>
					<div class="dropdown inline-block">
						<button type="button" class="ans-dropdown-toggle" id="<?php echo $sJsKey; ?>"><div><?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aC['cPick'])); ?></div></button>
					</div>
					<?php endif; ?>
					<span><?php echo nl2br($aC['text']); ?></span>
					<span class="posted font-size-60" style="display: none;">（<?php echo $aC['cPosted']; ?>）</span>
				</li>
				<?php endif; ?>
				<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endforeach; ?>

<?php
	elseif ($bComOnly):
?>
<script type="text/javascript">
function initQCom(mode) {
	setInterval(function() { ComUpdate(mode) },intervalTime);

	if (mode != 'ALL') {
		$('#'+mode).css({'display':'none'});
	}
}
</script>

<?php foreach ($aTab as $sMode => $aT): ?>
<div class="info-box mt0 QBTabContents" id="<?php echo $sMode; ?>" style="display: block;">
	<h2 class="QBTEXT" obj="<?php echo $aQuest['qbID']; ?>">
		<span class="QBTitle"><?php echo $sTitle; ?></span>
		<button class="QBTextEditShow" style="cursor: pointer;"><i class="fa fa-edit"></i></button>
		<span class="QBText"><?php echo nl2br($aQuery['qq1']['qqText']); ?></span>
		<span class="QBTextEdit" style="display: none;"><input type="text" value="<?php echo $aQuery['qq1']['qqText']; ?>" name="qbtext" style="width: 24em;"><button class="button na default width-auto"><?php echo __('更新'); ?></button></span>
	</h2>

	<hr>
	<div class="QuestCommentOnly" obj="<?php echo $aQuest['qbID']; ?>">
		<div class="QBNumHeader" id="QChartNum<?php echo $sMode; ?>" style="display: inline-block;">
			<?php echo $aT['num']; ?>
		</div>
		<div class="QBCommentFilter ml8 width-auto font-size-140" style="display: inline-block;">
			<span class="posted-toggle" title="<?php echo __('回答者の表示/非表示'); ?>"><i class="fa fa-user"></i></span>
		</div>

		<ul class="QBCommentList font-size-140" mode="all" id="QChartCom<?php echo $sMode; ?>">
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
				<span class="choiceDefault largeShow" data="Default" title="<?php echo __('拡大'); ?>"><i class="fa fa-search" style="vertical-align: middle;"></i></span>
				<?php if (preg_match('/^s.+/',$sStID)): ?>
				<div class="dropdown inline-block">
					<button type="button" class="ans-dropdown-toggle" id="<?php echo $sJsKey; ?>"><div><?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aC['qaPick'])); ?></div></button>
				</div>
				<?php endif; ?>
				<span><?php echo nl2br($aC['qbText']); ?></span>
				<span class="posted font-size-60" style="display: none;">（<?php echo $aC['cPosted']; ?>）</span>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>
</div>
<?php endforeach; ?>
<?php else: ?>

<style>
.LC { border-left-style: solid!important; border-left-width: 12px!important; }
.LCN { border-left-color: #c3ccd3!important; }
</style>

<?php $sDisp = 'block'; ?>
<?php foreach ($aTab as $sMode => $aT): ?>
<div class="info-box mt0 QBTabContents" id="<?php echo $sMode; ?>" style="display: <?php echo $sDisp; ?>;">
	<h2 class="QBTitle">
		<?php echo $sTitle; ?>
	</h2>
	<hr>
	<div class="QBNumHeader"><?php echo $aT['num']; ?></div>
	<dl class="QBList">
		<?php
			if (!is_null($aQuery)):
				foreach ($aQuery as $sK => $aQ):
					$qNO = (int)$aQ['qqSort'];
					$qID = (int)$aQ['qqNO'];
					$aColor = \Clfunc_Common::getChartColors($aQ['qqChoiceNum']);
		?>
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
			<thead>
			<tr>
				<?php if ($aQ['qqStyle'] < 2): ?>
				<th class="width-10 text-center"><?php echo __('番号'); ?></th>
				<th class="width-40 text-center"><?php echo __('回答内容'); ?></th>
				<th class="width-30 text-center QBGraphCell"><?php echo __('グラフ'); ?><?php if ($aQ['qqStyle'] == 0): ?> <a href="#" class="QBChartOn"><i class="fa fa-pie-chart"></i></a><?php endif; ?></th>
				<?php if ($aQ['qqStyle'] == 0): ?>
				<th class="width-30 text-center QBChartCell" style="display: none;"><?php echo __('グラフ'); ?> <a href="#" class="QBChartOff"><i class="fa fa-bar-chart"></i></a></th>
				<?php endif; ?>
				<th class="width-10 text-center QBNumCell"><?php echo __('割合'); ?></th>
				<th class="width-10 text-center QBNumCell"><?php echo __('回答数'); ?></th>
				<?php else: ?>
				<th class="width-10 text-center"><?php echo __('番号'); ?></th>
				<th class="width-<?php echo (!$aQuest['qbAnonymous'])? 80:90; ?> text-center QBAnsTextHeader"><?php echo __('回答内容'); ?></th>
				<?php if (!$aQuest['qbAnonymous']): ?>
				<th class="width-10 text-center QBAnsStarHeader">★<a href="#" class="QBPersonalOn" style="float: right;"><i class="fa fa-user"></i></a></th>
				<th class="width-10 text-center QBAnsPersonalHeader" style="display: none;"><a href="#" class="QBPersonalOff"><i class="fa fa-user"></i></a></th>
				<?php endif; ?>
				<?php endif; ?>
			</tr>
			</thead>
			<tbody>
				<?php
					if ($aQ['qqStyle'] < 2):
						$aQB = $aBent[$sMode][$sK];
						if ($aQuest['qbQuerySort'] == 1):
							krsort($aQB);
						endif;
						$bChart = true;
						$aChartData = null;
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
							$sMemberData = $aQ['qbID'].'_'.$aQ['qqNO'].'_'.$i.'_'.$sMode;

							$aChartData[$i]['num'] = (int)$aB['qbNum'];
							$aChartData[$i]['label'] = '（'.$i.'）'.$aQ['qqChoice'.$i];
							$aChartData[$i]['color'] = '"rgba('.$aColor[($i%count($aColor))]['rgb'].','.$aAlfa['fill'].')"';
							$aChartData[$i]['hover'] = '"rgba('.$aColor[($i%count($aColor))]['rgb'].','.$aAlfa['hover'].')"';
				?>
				<tr>
					<td class="text-center"><?php echo $i; ?></td>
					<td class="QAns LCN" data="<?php echo ($i % count($aColor)); ?>" style="border-left-color: <?php echo $aColor[($i%count($aColor))]['code']; ?>">
						<?php echo nl2br($aQ['qqChoice'.$i]); ?>
						<?php if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i])): ?>
						<span class="ShowToggleClass" data="<?php echo $sK; ?>"><i class="fa fa-picture-o"></i></span>
						<div class="QBQImage <?php echo $sK; ?>"><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i]; ?>" style="max-width: 100%; max-height: 120px; width: auto; height: auto;"></div>
						<?php endif; ?>
					</td>
					<td class="QBGraphCell">
						<div class="QBGraphBox">
							<div class="QBGraph" style="width: <?php echo $sAvg; ?>%; height: 1.3em;"></div>
						</div>
					</td>
					<?php if ($aQ['qqStyle'] == 0 && $bChart): $bChart = false; ?>
					<td class="QBChartCell" rowspan="<?php echo $aQ['qqChoiceNum']; ?>" style="display: none;">
						<canvas id="QBChart_<?php echo $qID.$sMode; ?>" class="QBChartBox" style="width: 100%;"></canvas>
					</td>
					<?php endif; ?>
					<td class="text-right QBNumCell"><?php echo $sAvg; ?>%</td>
					<td class="text-right QBNumCell">
						<?php if (($sMode == 'GUEST' && $aQuest['qbOpen'] == 1) || $aQuest['qbAnonymous']): ?>
						<?php echo __(':num名',array('num'=>$aB['qbNum'])); ?>
						<?php else: ?>
						<a href="#" class="QBNumMember" data="<?php echo $sMemberData; ?>"><?php echo __(':num名',array('num'=>$aB['qbNum'])); ?></a>
						<?php endif; ?>
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
							$sMemberData = $aQ['qbID'].'_'.$aQ['qqNO'].'_0_'.$sMode;
				?>
				<tr>
					<td class="text-center"></td>
					<td><?php echo __('（無回答）'); ?></td>
					<td class="QBGraphCell">
						<div class="QBGraphBox">
							<div class="QBGraph" style="width: <?php echo $sAvg; ?>%; height: 1.3em;"></div>
						</div>
					</td>
					<td class="text-right"><?php echo $sAvg; ?>%</td>
					<td class="text-right"><a href="#" class="QBNumMember" data="<?php echo $sMemberData; ?>"><?php echo __(':num名',array('num'=>$aB['qbNum'])); ?></a></td>
				</tr>
				<?php
						endif;
						if ($aQ['qqStyle'] == 0):
?>
<script type="text/javascript">
function initChart<?php echo $qID.$sMode; ?>() {
	var insChart = null;
	var objChart = null;
	var ChartData = null;

	var cBox = $("#QBChart_<?php echo $qID.$sMode; ?>");

	var iW = cBox.width();
	cBox.width(iW);
	cBox.height(iW);
	cBox.css({'background-color': 'white'});

	ChartData = [
<?php $sep = ''; ?>
<?php foreach ($aChartData as $aD): ?>
<?php echo $sep; ?>{
	value: <?php echo $aD['num']; ?>,
	color: <?php echo $aD['color']; ?>,
	highlight: <?php echo $aD['hover']; ?>,
	label: "<?php echo $aD['label']; ?>"
}
<?php $sep = ','; ?>
<?php endforeach; ?>
		];

	var ctx = cBox.get(0).getContext("2d");
	insChart = new Chart(ctx);
	insChart.tooltipFontFamily = '"Hiragino Kaku Gothic ProN","ヒラギノ角ゴ ProN W3","Meiryo","メイリオ","sans-serif"';
	insChart.tooltipTitleFontFamily = '"Hiragino Kaku Gothic ProN","ヒラギノ角ゴ ProN W3","Meiryo","メイリオ","sans-serif"';
	objChart = insChart.Doughnut(ChartData, {
		responsive : false,
		animationEasing : 'easeInOutCubic',
		percentageInnerCutout : 20
	});
}
</script>
<?php
						endif;
					else:
						if (isset($aBent[$sMode][$sK])):
							$aQB = $aBent[$sMode][$sK];
							$iBlank = 0;
							$iCnt = 0;
							foreach ($aQB as $i => $aB):
								if ($aB['qbText'] != ''):
									$iCnt++;
									$sJsKey = $aB['qbID'].'_'.$aB['qqNO'].'_'.$aB['stID'].'_'.$sMode;
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
					<td class="text-center">
						回答<?php echo $iCnt; ?>
						<span class="choiceDefault QBTextZoom largeShow" data="Default" title="拡大"><i class="fa fa-search" style="vertical-align: middle;"></i></span>
					</td>
					<td class="QBAnsText">
						<span><?php echo nl2br($aB['qbText']); ?></span>
					</td>
					<?php if (!$aQuest['qbAnonymous']): ?>
					<td class="text-center">
						<?php if (preg_match('/^s/',$aB['stID'])): ?>
						<div class="dropdown">
							<button type="button" class="ans-dropdown-toggle" id="<?php echo $sJsKey; ?>"><div><?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aB['qaPick'])); ?></div></button>
						</div>
						<?php endif; ?>
					</td>
					<td class="QBAnsPersonal" style="display: none!important;"><?php echo $aB['cPosted']; ?></td>
					<?php endif; ?>
				</tr>
				<?php
								else:
									$iBlank++;
								endif;
							endforeach;
							if ($iBlank > 0):
				?>
				<tr>
					<td></td>
					<td class="QBAnsText"><?php echo __('（無回答：:num名）',array('num'=>$iBlank)); ?></td>
					<?php if (!$aQuest['qbAnonymous']): ?>
					<td></td>
					<td class="QBAnsPersonal" style="display: none!important;"></td>
					<?php endif; ?>
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
<?php $sDisp = 'none'; ?>
<?php endforeach; ?>
<?php endif; ?>

<?php if (!preg_match('/CL_AIR_ANDROID/i', $_SERVER['HTTP_USER_AGENT'])): ?>
<div class="info-box">
<div class="button-box mt0">
	<button type="button" class="button default window-close"><?php echo __('集計結果を閉じる'); ?></button>
</div>
</div>
<?php endif; ?>

<ul class="dropdown-list dropdown-list-pick" obj="">
	<li><a href="#" class="QuestTextPickUp text-left" obj="1"><?php echo Asset::img('icon_pick_a.png',array('alt'=>__('良回答'),'style'=>'vertical-align: top;')); ?> <?php echo __('良回答'); ?></a></li>
	<li><a href="#" class="QuestTextPickUp text-left" obj="0"><?php echo Asset::img('icon_pick_b.png',array('alt'=>__('可（通常）'),'style'=>'vertical-align: top;')); ?> <?php echo __('可（通常）'); ?></a></li>
	<li><a href="#" class="QuestTextPickUp text-left" obj="-1"><?php echo Asset::img('icon_pick_c.png',array('alt'=>__('不可'),'style'=>'vertical-align: top;')); ?> <?php echo __('不可'); ?></a></li>
</ul>

</div>

<div class="comment-write-box ml0" id="TeachComment" style="visibility: hidden;">
<table>
<tr>
	<td>
		<button class="VisibleToggle" data="TeachComment" style="cursor: pointer;"><i class="fa fa-comment-o fa-2x fa-flip-horizontal"></i></button>
	</td>
	<td>
		<textarea class="comment-write-text" rows="1" placeholder="<?php echo __('先生コメントを入力'); ?>"><?php echo $aQuest['qbComment']; ?></textarea>
		<div class="font-white"><?php echo __('※先生からのコメントを入力'); ?></div>
	</td>
	<td><button type="button" class="button na do TeachCommentUpdate" style="min-width: 1em;" data="<?php echo $aQuest['qbID']; ?>_ALL"><?php echo __('更新'); ?></button></td>
</tr>
</table>
</div>

<div class="QBFloatBox" style="display: none;">
	<div class="QBFloatClose"><i class="fa fa-times"></i></div>
	<div class="QBFloatMember"></div>
</div>

<ul class="dropdown-list dropdown-list-public" obj="">
	<li><a href="#" class="QuestPublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="#" class="QuestPublic" obj="close"><span class="font-red"><?php echo __('締切'); ?></span></a></li>
	<li><a href="#" class="QuestPublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-guest" obj="">
	<li><a href="#" class="SwitchOpen" obj="close"><span class="font-default"><?php echo __('ゲストなし'); ?></span></a></li>
	<li><a href="#" class="SwitchOpen" obj="anonymous" ><span class="font-green"><?php echo __('匿名（ゲスト回答あり）'); ?></span></a></li>
	<li><a href="#" class="SwitchOpen" obj="signature" ><span class="font-blue"><?php echo __('記名（ゲスト回答あり）'); ?></span></a></li>
</ul>
