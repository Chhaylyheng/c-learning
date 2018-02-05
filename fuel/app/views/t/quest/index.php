<?php if ($aQSAL['caProgress'] == 1): ?>
<script type="text/javascript">
var intervalTime = 2000;
var timerID;

timerID = setInterval(function() { ArchiveDownload() },intervalTime);
</script>
<?php endif; ?>

<?php
	if ($aClass['ctStatus']):
		$sDisp = 'block';
		if ((!is_null($aQuest) || count($aQuest) > 2) && ($aTeacher['gtID'] || $aTeacher['coTermDate'] > date('Y-m-d'))):
			$sDisp = 'none';
		endif;
		if ($aTeacher['ttStatus'] == 2):
			$sDisp = 'block';
		endif;
		global $gaQuickTitle;
?>
<div class="mt16">
	<h2><a href="#" class="link-out accordion"><?php echo __('クイックアンケート'); ?></a></h2>
	<div class="accordion-content acc-content-open" style="display: <?php echo $sDisp; ?>;">
	<div class="accordion-content-inner pt8">
		<?php $sQURL = '/t/quest/quick/'; ?>
		<ul class="QuestQuickBtn">
			<?php
				foreach ($gaQuickTitle as $iKey => $sTitle):
					$sSub = '';
					if (mb_substr($sTitle, -1, 1, 'UTF-8') == '※'):
						$sTitle = mb_substr($sTitle, 0, -1, 'UTF-8');
						$sSub = '<br><span>'.__('※コメントあり').'</span>';
					endif;
					$id = ($iKey == 23)? ' id="QuickQuestTutorial"':'';
			?>
			<li><a href="<?php echo $sQURL.$iKey; ?>" class="QuickBtn font-size-110" target="_blank"<?php echo $id; ?>><?php echo __($sTitle).$sSub; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	</div>
</div>
<?php endif; ?>

<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr>
				<!-- <th><input type="checkbox" class="AllCheck" value="Close"></th> -->
				<th><?php echo __('タイトル'); ?></th>
				<th><?php echo __('提出状況'); ?></th>
				<th><?php echo __('アンケート公開'); ?></th>
				<th><?php echo __('集計公開'); ?></th>
				<th><?php echo __('操作'); ?></th>
				<th><?php echo __('登録日時'); ?></th>
				<th style="width: 5em;">G</th>
<?php if (!is_null($aGroup)): ?>
				<th style="width: 4em;"><?php echo __('匿名'); ?></th>
<?php endif; ?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aQuest)):
				$iMax = count($aQuest);
				foreach ($aQuest as $aQ):
					$sQuick = ($aQ['qbQuickMode'])? '[Q] ':'';
					$sGuest = '<span class="font-silver">─</span>';
					if ($aQ['qbOpen'] == 1):
						$sGuest = '<span class="font-green">'.__('匿名').'</span>';
					elseif ($aQ['qbOpen'] == 2):
						$sGuest = '<span class="font-blue">'.__('記名').'</span>';
					endif;
					$sJsKey = $aQ['ctID'].'_'.$aQ['qbID'];
					$aPub = array(__('締切'),'font-red');
					$aBent = array(__('公開中'),'font-blue');
					if ($aQ['qbPublic'] == 1):
						$aPub = array(__('公開中'),'font-blue');
						if ($aQ['qbAutoCloseDate'] != CL_DATETIME_DEFAULT):
							$aPub[2] = '～ '.ClFunc_Tz::tz('n/j H:i',$tz,$aQ['qbAutoCloseDate']);
						endif;
					elseif ($aQ['qbPublic'] == 0):
						$aPub = array(__('非公開'),'font-default');
						if ($aQ['qbAutoPublicDate'] != CL_DATETIME_DEFAULT):
							$aPub[2] = ClFunc_Tz::tz('n/j H:i',$tz,$aQ['qbAutoPublicDate']).' ～';
						endif;
					endif;
					if ($aQ['qbBentPublic'] == 0):
						$aBent = array(__('非公開'),'font-default');
					endif;
					$aSort = array(' ',' ');
					if ($aQ['qbSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aQ['qbSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;

					$sPNum = '<span class="PutNum">'.$aQ['qpNum'].'</span>';

					$sGNum = null;
					if ($aQ['qbOpen'] || $aQ['qpGNum'] > 0):
						$sGNum = '：<span class="GPutNum">'.$aQ['qpGNum'].'</span>';
					endif;
					$sTNum = null;
					if ($aClass['gtID'] && $aQ['qpTNum'] > 0):
						$sTNum = '：<span class="TPutNum">'.$aQ['qpTNum'].'</span>';
					endif;

					if ($aQ['qbAnonymous'] == 1):
						$iAnony = 1;
						$sAnony = '<i class="fa fa-check"></i>';
					else:
						$sAnony = '─';
						$iAnony = 0;
					endif;

		?>
		<tr id="<?php echo $aQ['qbID']; ?>">
			<!-- <td class="text-center" nowrap="nowrap"><input type="checkbox" name="Close[]" value="<?php echo $aQ['qbID']; ?>"></td> -->
			<td class="sp-full">
			<?php if ($aQ['qbNum']): ?>
				<a href="/t/quest/bent/<?php echo $aQ['qbID']; ?>" class="button na do width-auto" target="_blank"><i class="fa fa-bar-chart pr8"></i><?php echo $sQuick.mb_strimwidth($aQ['qbTitle'], 0, 42, '…'); ?></a>
			<?php else: ?>
				<?php echo $sQuick.$aQ['qbTitle']; ?>
			<?php endif; ?>
			</td>
			<td class="" nowrap="nowrap">
				<a href="/t/quest/put/<?php echo $aQ['qbID']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo $sPNum.$sGNum.$sTNum; ?></a>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="quest-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sJsKey; ?>_public"><div><?php echo $aPub[0]; ?><?php if (isset($aPub[2])): ?><br><span class="font-size-80"><?php echo $aPub[2]; ?></span><?php endif;?></div></button>
				</div>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="quest-dropdown-toggle <?php echo $aBent[1]; ?>" id="<?php echo $sJsKey; ?>_bent"><div><?php echo $aBent[0]; ?></div></button>
				</div>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="quest-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit" mode="<?php echo $aQ['qbQuickMode']; ?>" anony="<?php echo $iAnony; ?>"><div><?php echo __('管理'); ?></div></button>
				</div>
				<?php if ($aQ['qbNum']): ?>
					<a href="/t/quest/preview/<?php echo $aQ['qbID']; ?>" target="questpreview" class="button na default width-auto" style="padding: 8px 4px;"><?php echo __('Preview'); ?></a>
				<?php else: ?>
					<button class="button na default width-auto back-silver" title="<?php echo __('設問を作成してください'); ?>" style="padding: 7px 1px;"><?php echo __('Preview'); ?></button>
				<?php endif; ?>
				<button<?php echo $aSort[0]; ?> class="QuestSort button na default width-auto text-center" style="padding: 7px 2px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="QuestSort button na default width-auto text-center" style="padding: 7px 2px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>

				<?php if ($aClass['gtID']): ?>
					<div class="QuestTeacherAns" style="display: inline-block;">
					<?php $sPut = (isset($aPut[$aQ['qbID']]))? '<span class="attention attn-emp font-size-90" style="padding: 1px 4px 1px; height: auto;">'.__('済').'</span>':''; ?>

					<?php if ($aQ['qbPublic'] == 1 && $sPut): ?>
					<a href="/t/quest/result/<?php echo $aQ['qbID']; ?>" class="button na default width-auto font-size-90" style="padding: 8px 5px; line-height: 14px;"><?php echo __('回答').$sPut; ?></a>
					<?php elseif ($aQ['qbPublic'] == 1): ?>
					<a href="/t/quest/ans/<?php echo $aQ['qbID']; ?>" class="button na default width-auto font-size-90" style="padding: 9px 6px; line-height: 14px;"><?php echo __('回答').$sPut; ?></a>
					<?php elseif ($aQ['qbPublic'] == 2 && $sPut): ?>
					<a href="/t/quest/ansdetail/<?php echo $aQ['qbID'].DS.$aTeacher['ttID']; ?>" class="button na default width-auto font-size-90" style="padding: 8px 5px; line-height: 14px;"><?php echo __('回答').$sPut; ?></a>
					<?php else: ?>
						<?php if ($aQ['qbNum'] && $sPut): ?>
							<a href="/t/quest/ansdetail/<?php echo $aQ['qbID'].DS.$aTeacher['ttID']; ?>" class="button na default width-auto font-size-90" style="padding: 8px 5px; line-height: 14px;"><?php echo __('回答').$sPut; ?></a>
						<?php else: ?>
							<button class="button na default width-auto back-silver font-size-90" disabled="disabled" style="padding: 7px 2px; line-height: 14px;"><?php echo __('回答').$sPut; ?></a>
						<?php endif; ?>
					<?php endif; ?>
					</div>
				<?php endif; ?>

			</td>
			<td class="font-size-80" style="line-height: 1.1;">
				<?php echo ClFunc_Tz::tz('Y/m/d\<\\b\\r\> H:i',$tz,$aQ['qbDate']); ?>
			</td>

			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey">G:</span
				><?php echo $sGuest; ?>
			</td>

<?php if (!is_null($aGroup)): ?>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('匿名'); ?>:</span
				><?php echo $sAnony; ?>
			</td>
<?php endif; ?>

		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-public" obj="">
	<li><a href="#" class="QuestPublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="#" class="QuestPublic" obj="close"><span class="font-red"><?php echo __('締切'); ?></span></a></li>
	<li><a href="#" class="QuestPublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-bent" obj="">
	<li><a href="#" class="QuestBentPublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="#" class="QuestBentPublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="QuestEdit text-left"><span class="font-default"><?php echo __('アンケート情報の編集'); ?></span></a></li>
<?php if (!CL_CAREERTASU_MODE || $aTeacher['ttCTPlan'] > 0): ?>
	<li><a href="#" class="QuestQueryEdit text-left"><span class="font-default"><?php echo __('設問の編集'); ?></span></a></li>
<?php endif; ?>
	<li><a href="#" class="QuestCopy text-left"><span class="font-default"><?php echo __('アンケートのコピー'); ?></span></a></li>
	<li><a href="#" class="QuestToCSV text-left"><span class="font-default"><i class="fa fa-download"></i> <?php echo __('アンケート情報のCSVの保存'); ?></span></a></li>
	<li><a href="#" class="QuestResultToCSV text-left"><span class="font-default"><i class="fa fa-download"></i> <?php echo __('回答内容CSVのダウンロード'); ?></span></a></li>

	<li><a href="#" class="QuestPutReset text-left"><span class="font-default"><?php echo __('提出状況をリセット'); ?></span></a></li>
	<li><a href="#" class="QuestDelete text-left"><span class="font-default"><?php echo __('アンケートの削除'); ?></span></a></li>
</ul>

<?php if ($aClass['gtID']): ?>
<div class="QuestTeacherTemplate" style="display: none;">
<a href="" class="button na default width-auto font-size-90 QTT-Default" style="padding: 8px 5px; line-height: 14px;"><?php echo __('回答'); ?><span class="attention attn-emp font-size-90" style="padding: 1px 4px 1px; height: auto;"><?php echo __('済'); ?></span></a>
<a href="" class="button na confirm width-auto font-size-90 QTT-Confirm" style="padding: 9px 6px; line-height: 14px;"><?php echo __('回答'); ?><span class="attention attn-emp font-size-90" style="padding: 1px 4px 1px; height: auto;"><?php echo __('済'); ?></span></a>
<button class="button na default width-auto back-silver font-size-90 QTT-Disable" disabled="disabled" style="padding: 7px 2px; line-height: 14px;"><?php echo __('回答'); ?><span class="attention attn-emp font-size-90" style="padding: 1px 4px 1px; height: auto;"><?php echo __('済'); ?></span></a>
</div>
<?php endif; ?>


<?php if ($aTeacher['ttStatus'] == 2): ?>

<?php echo Asset::js('cl.tutorial.js'); ?>
<script type="text/javascript">
$(function() {
	$(window).on('load', function() {
		TutorialQuestIndex();
	});
});
</script>

<div id="TutoText4" style="display: none;">
<p><?php echo __('TutoText04'); ?></p>
</div>

<?php endif; ?>



