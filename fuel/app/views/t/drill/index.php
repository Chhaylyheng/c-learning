<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr><!-- <th><input type="checkbox" class="AllCheck" value="Close"></th> -->
				<th><?php echo __('カテゴリ名'); ?></th>
				<th><?php echo __('公開 / 全件'); ?></th>
				<th><?php echo __('問題分析'); ?></th>
				<th><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aDrillCate)):
				$iMax = count($aDrillCate);
				foreach ($aDrillCate as $aM):
					$sJsKey = $aM['ctID'].'_'.$aM['dcID'];
					$aSort = array(' ',' ');
					if ($aM['dcSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aM['dcSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;
		?>
		<tr id="<?php echo $aM['dcID']; ?>">
			<!-- <td class="text-center" nowrap="nowrap"><input type="checkbox" name="Close[]" value="<?php echo $aM['dcID']; ?>"></td> -->
			<td class="sp-full">
				<a href="/t/drill/list/<?php echo $aM['dcID']; ?>" class="button na do width-auto"><?php echo $aM['dcName']; ?></a>
			</td>
			<td class="" nowrap="nowrap">
				<span class="sp-display-inline font-grey"><?php echo __('公開'); ?>:</span
				><?php echo $aM['dcPubNum']; ?>
				/
				<span class="sp-display-inline font-grey"><?php echo __('全件'); ?>:</span
				><?php echo $aM['dcNum']; ?>
			</td>
			<td class="">
				<a href="/t/drill/analysis/<?php echo $aM['dcID']; ?>" class="button na default width-auto"><i class="fa fa-bar-chart pr8"></i><?php echo __('問題分析'); ?></a>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="drill-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div><?php echo __('管理'); ?></div></button>
				</div>
				<button<?php echo $aSort[0]; ?> class="DrillCateSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="DrillCateSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="DrillQueryGroupEdit text-left"><span class="font-default"><?php echo __('問題グループの編集'); ?></span></a></li>
	<li><a href="#" class="DrillCateEdit text-left"><span class="font-default"><?php echo __('カテゴリ情報の編集'); ?></span></a></li>
	<li><a href="#" class="DrillCateDelete text-left"><span class="font-default"><?php echo __('カテゴリの削除'); ?></span></a></li>
</ul>

