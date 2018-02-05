<script type="text/javascript">
var intervalTime = 10000;

var all = 3;
var gBent = {
	'ALL':{
		'1':{
			'num':<?php echo $aBent['ALL']['qq1']['1']['qbNum']; ?>,
			'per':<?php echo round(($aBent['ALL']['qq1'][1]['qbNum']/$aBent['ALL']['qq1'][1]['qbAll'])*100,1); ?>
		},
		'2':{
			'num':<?php echo $aBent['ALL']['qq1']['2']['qbNum']; ?>,
			'per':<?php echo round(($aBent['ALL']['qq1'][2]['qbNum']/$aBent['ALL']['qq1'][2]['qbAll'])*100,1); ?>
		}
	}
};
var gComment = {
	'ALL':{
<?php $sep = ''; ?>
<?php foreach ($aComment['ALL'] as $sStID => $aC): ?>
		<?php echo $sep.'"'.$sStID.'"'; ?>: {
			'text': '<?php echo $aC['text']; ?>',
			'cName': '<?php echo $aC['cName']; ?>',
			'cNO': <?php echo $aC['cNO']; ?>,
			'cPick': <?php echo $aC['cPick']; ?>,
			'cPosted': '<?php echo $aC['cPosted']; ?>'
		}
		<?php $sep = ','; ?>
<?php endforeach; ?>
	}
};
var gQuest = {
	'qpNum':3,
	'scNum':0,
	'qpGNum':0,
	'qpTNum':0
};
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
$sDropKey = $aQuest['ctID'].'_'.$aQuest['qbID'];
$sSwitchURL = '/t/quest/switchOpen/'.$aQuest['qbID'].'/';

$bQuick = ($aQuest['qbQuickMode'])? true:false;
$bIF = false;
$bCom = false;
$bComOnly = false;

$bIF = true;
$bCom = isset($aQuery['qq2']);

$aPub = array(__('公開中'),'font-blue');
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
<h1 class="BentTitle mt8 mb8"><i class="fa fa-book"></i> <?php echo $sClassName; ?></h1>

<div style="position: absolute; top: 4px; right: 8px;">

<div class="dropdown" style="display: inline-block;">
	<button type="button" class="open-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sDropKey; ?>_public" disabled="disabled"><div><?php echo $aPub[0]; ?><?php if (isset($aPub[2])): ?> <span class="font-size-80"><?php echo $aPub[2]; ?></span><?php endif;?></div></button>
</div>

<?php if ($bQuick && ($bComOnly || $bCom)): ?>
	<div class="button-group" style="display: inline-block;">
		 <button class="text-center QBCommentFontSize font-size-70"  data="small" title="<?php echo __('コメント サイズ').'：'.__('小'); ?>"><?php echo __('小'); ?></button
		><button class="text-center QBCommentFontSize font-size-100" data="middle" title="<?php echo __('コメント サイズ').'：'.__('中'); ?>"><?php echo __('中'); ?></button
		><button class="text-center QBCommentFontSize font-size-140" data="large" title="<?php echo __('コメント サイズ').'：'.__('大'); ?>"><?php echo __('大'); ?></button>
	</div>
<?php endif; ?>
	<div class="dropdown" style="display: inline-block;" disabled="disabled">
		<button type="button" class="open-dropdown-toggle <?php echo $aOpen[1]; ?>" id="<?php echo $sDropKey; ?>_guest" disabled="disabled"><div><?php echo $aOpen[0]; ?></div></button>
	</div>
	<button class="button default na width-auto text-center" data="TeachComment" style="padding: 6px 6px 7px; vertical-align: top; display: inline-block;" title="<?php echo __('全体コメント入力'); ?>"><i class="fa fa-comment mr0"></i></button>
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

	$aAlfa = array(
		'fill'  => 1,
		'hover' => 0.85,
		'high'  => 0.7,
	);

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
	// setInterval(function() { DoughnutChartUpdateDemo(mode) },intervalTime);

	if (mode != 'ALL') {
		$('#'+mode).css({'display':'none'});
	}
}
</script>

<?php foreach ($aTab as $sMode => $aT): ?>
<div class="info-box mt0 QBTabContents" id="<?php echo $sMode; ?>" style="display: block;">
	<h2 class="QBTEXT" obj="<?php echo $aQuest['qbID']; ?>">
		<span class="QBTitle"><?php echo $sTitle; ?></span>
		<button class="" style="cursor: pointer;"><i class="fa fa-edit"></i></button>
		<span class="QBText"><?php echo nl2br($aQuery['qq1']['qqText']); ?></span>
		<span class="QBTextEdit" style="display: none;"><input type="text" value="<?php echo $aQuery['qq1']['qqText']; ?>" name="qbtext" style="width: 24em;"><button class="button na default width-auto"><?php echo __('更新'); ?></button></span>
	</h2>

	<hr>
	<div class="QuestChart" obj="<?php echo $aQuest['qbID']; ?>_<?php echo (int)isset($aQuery['qq2']); ?>">
		<div class="ChartBox" style="width: 48%;">
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

</div>

<div class="QBFloatBox" style="display: none;">
	<div class="QBFloatClose"><i class="fa fa-times"></i></div>
	<div class="QBFloatMember"></div>
</div>

<script type="text/javascript">
$(function() {
	$(window).on('load', function() {
		TutorialBentStart();
	});
});
</script>

<div id="TutoText5" style="display: none;">
<p><?php echo __('TutoText05'); ?></p>
<div class="button-box">
<button class="button na do width-auto TutorialBentStart"><?php echo __('次へ'); ?></button>
</div>
</div>

<div id="TutoText6" style="display: none;">
<p><?php echo __('TutoText06'); ?></p>
<div class="button-box">
<button class="button na do width-auto TutorialBentMsgClose"><?php echo __('次へ'); ?></button>
</div>
</div>

<div id="smartphone-frame" style="display: none;">
	<div class="screen">
		<p style="background-color: #cc0000; padding: 2px 0; display: none;" class="font-size-80 font-white mt0 SFErr"><?php echo __('「はい」か「いいえ」を選択してください。'); ?></p>

		<?php $aQ = $aQuery['qq1']; ?>
		<p class="mt8"><?php echo nl2br($aQ['qqText']); ?></p>
		<ul class="mt4 QuestAnsChoice">
		<?php
			$iWidth = ((int)$aQuest['qbQueryStyle'] == 2)? 45:(((int)$aQuest['qbQueryStyle'] == 3)? 30:95);
			$aChoice = array();
			for ($i = 1; $i <= (int)$aQ['qqChoiceNum']; $i++):
				$aChoice[$i]  = '<li class="width-'.$iWidth.'" style=""><label class="QueryChoice text-left default"><input type="radio" name="radioSel" value="'.$i.'" autocomplete="off" label="'.$aQ['qqChoice'.$i].'"><p class="font-size-90"><i class="fa fa-circle-o fa-fw"></i>'.nl2br($aQ['qqChoice'.$i]).'</p></label></li>';
			endfor;
			foreach ($aChoice as $sC):
				echo $sC;
			endforeach;
		?>
		</ul>
		<hr>
		<?php $aQ = $aQuery['qq2']; ?>
		<p class="mt8"><?php echo nl2br($aQ['qqText']); ?></p>
		<div class="mt4"><label for="textAns"><textarea name="textAns" class="width-100" style="padding: 4px;" rows="2" autocomplete="off" id="textAns"></textarea></label></div>
		<div class="button-box mt8 text-center">
			<button type="button" class="button do na TutorialAnsSubmit"><?php echo __('提出する'); ?></button>
		</div>

		<p class="font-size-80 mt16 mb4 font-silver"><?php echo __('TutoText06-01',array('url'=>CL_PROTOCOL.'://'.CL_DOMAIN.'/s')); ?></p>
	</div>


	<div id="tour-end">
		<a href="/t/tutorial/start" class="button cancel na width-auto"><?php echo __('クイックツアーを終了する'); ?></a>
	</div>

</div>

<div class="font-size-80 width-100 font-red text-center" style="position: fixed; bottom: 0; background-color: white; padding: 2px; z-index: 145;">
	<span style="display: inline-block;"><?php echo __('※この画面はデモ用のため、一部機能が制限されています。'); ?></span>
	<span style="display: inline-block;"><?php echo __('※この画面に表示される氏名やコメント内容はデモデータです。'); ?></span>
</p>



