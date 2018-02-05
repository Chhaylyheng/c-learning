<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr><!-- <th><input type="checkbox" class="AllCheck" value="Close"></th> -->
				<th><?php echo __('カテゴリ名'); ?></th>
				<th><?php echo __('公開 / 全件'); ?></th>
				<th><?php echo __('容量'); ?></th>
				<th><?php echo __('最終登録日時'); ?></th>
				<th><?php echo __('通知'); ?></th>
				<th><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aMatCate)):
				$iMax = count($aMatCate);
				foreach ($aMatCate as $aM):
					$sJsKey = $aM['ctID'].'_'.$aM['mcID'];
					$aSort = array(' ',' ');
					if ($aM['mcSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aM['mcSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;
					$sSize = \Clfunc_Common::FilesizeFormat($aM['mcTotalSize'],1);
					$sLast = ($aM['mcLastDate'] != CL_DATETIME_DEFAULT && $aM['mcLastDate'])? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aM['mcLastDate']):'─';
					$sMail = ($aM['mcMail'])? '○':'×';
		?>
		<tr id="<?php echo $aM['mcID']; ?>">
			<!-- <td class="text-center" nowrap="nowrap"><input type="checkbox" name="Close[]" value="<?php echo $aM['mcID']; ?>"></td> -->
			<td class="sp-full">
				<a href="/t/material/list/<?php echo $aM['mcID']; ?>" class="button na do width-auto"><?php echo $aM['mcName']; ?></a>
			</td>
			<td class="" nowrap="nowrap">
				<span class="sp-display-inline font-grey"><?php echo __('公開'); ?>:</span
				><?php echo $aM['mcPubNum']; ?>
				/
				<span class="sp-display-inline font-grey"><?php echo __('全件'); ?>:</span
				><?php echo $aM['mcNum']; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('容量'); ?>:</span
				><?php echo $sSize; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('最終'); ?>:</span
				><?php echo $sLast; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('通知'); ?>:</span
				><?php echo $sMail; ?>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="quest-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div><?php echo __('管理'); ?></div></button>
				</div>
				<button<?php echo $aSort[0]; ?> class="MatCateSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="MatCateSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="MatCateEdit text-left"><span class="font-default"><?php echo __('カテゴリ情報の編集'); ?></span></a></li>
	<li><a href="#" class="MatCateDelete text-left"><span class="font-default"><?php echo __('カテゴリの削除'); ?></span></a></li>
</ul>
