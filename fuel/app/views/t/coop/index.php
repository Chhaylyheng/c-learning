<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr>
				<!-- <th><input type="checkbox" class="AllCheck" value="Close"></th> -->
				<th><?php echo __('協働板名'); ?></th>
				<th><?php echo __('対象学生'); ?></th>
				<th><?php echo __('学生投稿'); ?></th>
				<th><?php echo __('匿名'); ?></th>
				<th><?php echo __('記事数'); ?></th>
				<th><?php echo __('文字数'); ?></th>
				<th><?php echo __('容量'); ?></th>
				<th><?php echo __('最終投稿日時'); ?></th>
				<th><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aCoopCate)):
				$iMax = count($aCoopCate);
				foreach ($aCoopCate as $aM):
					$sJsKey = $aM['ctID'].'_'.$aM['ccID'];
					$aStuRange = array(__('全員'),'font-blue');
					if ($aM['ccStuRange'] == 1):
						$aStuRange = array(__('選択').'('.__(':num名',array('num'=>$aM['ccStuNum'])).')','font-green');
					elseif ($aM['ccStuRange'] == 0):
						$aStuRange = array('なし','font-default');
					endif;

					$aSort = array(' ',' ');
					if ($aM['ccSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aM['ccSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;
					$sSize = \Clfunc_Common::FilesizeFormat($aM['ccTotalSize'],1);
					$sLast = ($aM['ccLastDate'] != '0000-00-00 00:00:00')? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aM['ccLastDate']):'─';
					$sStuWrite = ($aM['ccStuWrite'])? '○':'×';
					$sAnonymous = __('記名');
					if ($aM['ccAnonymous'] == 1):
						$sAnonymous = __('先生記名');
					elseif ($aM['ccAnonymous'] == 0):
						$sAnonymous = __('匿名');
					endif;

					$sNew = '';
					$iNum = (isset($aAlready[$aM['ccID']]['cNum']))? $aAlready[$aM['ccID']]['cNum']:0;
					$sNew = ($aM['ccItemNum'] > $iNum)? '<span class="attention attn-emp">'.((int)$aM['ccItemNum'] - $iNum).'</span>':'';
		?>
		<tr id="<?php echo $aM['ccID']; ?>">
			<!-- <td class="text-center" nowrap="nowrap"><input type="checkbox" name="Close[]" value="<?php echo $aM['ccID']; ?>"></td> -->
			<td class="sp-full">
				<a href="/t/coop/thread/<?php echo $aM['ccID']; ?>" class="button na do width-auto"><?php echo $aM['ccName']; ?></a><?php echo $sNew; ?>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="quest-dropdown-toggle <?php echo $aStuRange[1]; ?>" id="<?php echo $sJsKey; ?>_range"><div><?php echo $aStuRange[0]; ?></div></button>
				</div>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('学生投稿'); ?>:</span
				><?php echo $sStuWrite; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('匿名'); ?>:</span
				><?php echo $sAnonymous; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('記事'); ?>:</span
				><?php echo number_format($aM['ccItemNum']); ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('文字数'); ?>:</span
				><?php echo number_format($aM['ccCharNum']); ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('容量'); ?>:</span
				><?php echo $sSize; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('最終'); ?>:</span
				><?php echo $sLast; ?>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="quest-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div><?php echo __('管理'); ?></div></button>
				</div>
				<button<?php echo $aSort[0]; ?> class="CoopCateSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="CoopCateSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="CoopCateEdit text-left"><span class="font-default"><?php echo __('協働板情報の編集'); ?></span></a></li>
	<li><a href="#" class="CoopCateDelete text-left"><span class="font-default"><?php echo __('協働板の削除'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-range" obj="">
	<li><a href="#" class="CoopRange" obj="all"><span class="font-blue"><?php echo __('全員'); ?></span></a></li>
	<li><a href="#" class="CoopRange" obj="select"><span class="font-green"><?php echo __('選択'); ?></span></a></li>
	<li><a href="#" class="CoopRange" obj="none"><span class="font-default"><?php echo __('なし'); ?></span></a></li>
</ul>
