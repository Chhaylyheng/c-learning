<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr>
				<th><?php echo __('テーマタイトル'); ?></th>
				<th class="width-10"><?php echo __('提出状況'); ?></th>
				<th class="width-10"><?php echo __('テーマの公開'); ?></th>
				<th class="width-10"><?php echo __('先生評価の公開'); ?></th>
				<th class="width-10"><?php echo __('共有'); ?></th>
				<th class="width-10"><?php echo __('最終提出日時'); ?></th>
				<th class="width-20"><?php echo __('操作'); ?></th></tr>
		</thead>
		<?php
			if (!is_null($aReport)):
				$iMax = count($aReport);
				foreach ($aReport as $aR):
					$sJsKey = $aR['ctID'].'_'.$aR['rbID'];

					$aAnony = array(__('匿名'),'font-default');
					if ($aR['rbAnonymous'] == 1):
						$aAnony = array(__('先生のみ記名'),'font-green');
					elseif ($aR['rbAnonymous'] == 2):
						$aAnony = array(__('記名'),'font-blue');
					endif;

					$sADisp = 'none';
					$aShare = array(__('共有なし'),'font-default');
					if ($aR['rbShare'] == 1):
						$aShare = array(__('共有中'),'font-green');
						$sADisp = 'inline-block';
					elseif ($aR['rbShare'] == 2):
						$aShare = array(__('共有中（相互評価）'),'font-blue');
						$sADisp = 'inline-block';
					endif;

					$aPub = array(__('締切'),'font-red');
					if ($aR['rbPublic'] == 1):
						$aPub = array(__('公開中'),'font-blue');
						if ($aR['rbAutoCloseDate'] != CL_DATETIME_DEFAULT):
							$aPub[2] = '～ '.ClFunc_Tz::tz('n/j H:i',$tz,$aR['rbAutoCloseDate']);
						endif;
					elseif ($aR['rbPublic'] == 0):
						$aPub = array(__('非公開'),'font-default');
						if ($aR['rbAutoPublicDate'] != CL_DATETIME_DEFAULT):
							$aPub[2] = ClFunc_Tz::tz('n/j H:i',$tz,$aR['rbAutoPublicDate']).' ～';
						endif;
					endif;

					$aRate = array(__('非公開'),'font-default');
					if ($aR['rbRatePublic'] == 1):
						$aRate = array(__('公開中'),'font-blue');
					endif;

					$aSort = array(' ',' ');
					if ($aR['rbSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aR['rbSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;

					$sBase = null;
					if ($aR['baseFID']):
						$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aR['baseFID'])).DS.$aR['baseFExt'];
						$sSize = \Clfunc_Common::FilesizeFormat($aR['baseFSize'],1);
						$sBase = '<p class="mt0">'.__('添付ファイル').'：<a href="'.$sLink.'" target="_blank">'.$aR['baseFName'].'</a>('.$sSize.')</p>';
					endif;

					$sResult = null;
					if ($aR['resultFID']):
						$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aR['resultFID'])).DS.$aR['resultFExt'];
						$sSize = \Clfunc_Common::FilesizeFormat($aR['resultFSize'],1);
						$sResult = '<p class="mt0">'.__('結果ファイル').'：<a href="'.$sLink.'" target="_blank">'.$aR['resultFName'].'</a>('.$sSize.')</p>';
					endif;
					$sLast = ($aR['rbLastPutDate'] != CL_DATETIME_DEFAULT && $aR['rbLastPutDate'])? ClFunc_Tz::tz('Y/m/d<\b\r>H:i',$tz,$aR['rbLastPutDate']):'─';
		?>
		<tbody>
		<tr id="<?php echo $aR['rbID']; ?>">
			<td class="">
				<a href="#" class="report-detail-show" data="0"><i class="fa fa-plus-square-o"></i> <?php echo $aR['rbTitle']; ?></a>
			</td>
			<td class="" nowrap="nowrap">
				<a href="/t/report/put/<?php echo $aR['rbID']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo $aR['rbPutNum']; ?></a>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="report-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sJsKey; ?>_public"><div><?php echo $aPub[0]; ?><?php if (isset($aPub[2])): ?><br><span class="font-size-80"><?php echo $aPub[2]; ?></span><?php endif;?></div></button>
				</div>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="report-dropdown-toggle <?php echo $aRate[1]; ?>" id="<?php echo $sJsKey; ?>_rate"><div><?php echo $aRate[0]; ?></div></button>
				</div>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="report-dropdown-toggle <?php echo $aShare[1]; ?>" id="<?php echo $sJsKey; ?>_share"><div><?php echo $aShare[0]; ?></div></button>
					<button type="button" class="report-dropdown-toggle <?php echo $aAnony[1]; ?>" id="<?php echo $sJsKey; ?>_anony" style="display: <?php echo $sADisp; ?>"><div><?php echo $aAnony[0]; ?></div></button>
				</div>
			</td>
			<td class="" nowrap="nowrap">
				<?php echo $sLast; ?>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="report-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div><?php echo __('管理'); ?></div></button>
				</div>
				<button<?php echo $aSort[0]; ?> class="ReportSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="ReportSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
		</tr>
		<tr id="<?php echo $aR['rbID']; ?>_detail" style="display: none;">
			<td colspan="7">
				<p class="mt0 mb4"><?php echo nl2br($aR['rbText']); ?></p>
				<?php echo $sBase.$sResult; ?>
			</td>
		</tr>
		</tbody>
		<?php endforeach; ?>
		<?php endif; ?>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-share" obj="">
	<li><a href="#" class="ShareOpen" obj="share0"><span class="font-default"><?php echo __('共有なし'); ?></span></a></li>
	<li><a href="#" class="ShareOpen" obj="share1" ><span class="font-green"><?php echo __('共有中'); ?></span></a></li>
	<li><a href="#" class="ShareOpen" obj="share2" ><span class="font-blue"><?php echo __('共有中（相互評価）'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-anony" obj="">
	<li><a href="#" class="ShareAnony" obj="anony0"><span class="font-default"><?php echo __('匿名'); ?></span></a></li>
	<li><a href="#" class="ShareAnony" obj="anony1" ><span class="font-green"><?php echo __('先生のみ記名'); ?></span></a></li>
	<li><a href="#" class="ShareAnony" obj="anony2" ><span class="font-blue"><?php echo __('記名'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-public" obj="">
	<li><a href="#" class="ReportPublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="#" class="ReportPublic" obj="close"><span class="font-red"><?php echo __('締切'); ?></span></a></li>
	<li><a href="#" class="ReportPublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-rate" obj="">
	<li><a href="#" class="ReportResultPublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="#" class="ReportResultPublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="ReportEdit text-left"><span class="font-default"><?php echo __('レポートテーマ情報の編集'); ?></span></a></li>
	<li><a href="#" class="ReportDelete text-left"><span class="font-default"><?php echo __('レポートテーマの削除'); ?></span></a></li>
</ul>
