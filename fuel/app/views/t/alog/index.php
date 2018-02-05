<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr><!-- <th><input type="checkbox" class="AllCheck" value="Close"></th> -->
				<th><?php echo __('テーマ名'); ?></th>
				<th><?php echo __('入力状況'); ?></th>
				<th><?php echo __('公開'); ?></th>
				<th><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aALTheme)):
				$iMax = count($aALTheme);
				foreach ($aALTheme as $aT):
					$sJsKey = $aT['ctID'].'_'.$aT['altID'];
					$aSort = array(' ',' ');
					if ($aT['altSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aT['altSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;
					$aPub = array(__('公開中'),'font-blue');
					if ($aT['altPublic'] == 0):
						$aPub = array(__('非公開'),'font-default');
					endif;
		?>
		<tr id="<?php echo $aT['altID']; ?>">
			<td class="sp-full">
				<?php echo $aT['altName']; ?>
			</td>
			<td>
				<a href="/t/alog/list/<?php echo $aT['altID']; ?>" class="button na default width-auto"><?php echo $aT['alNum']; ?></a>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="alog-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sJsKey; ?>_pub"><div><?php echo $aPub[0]; ?></div></button>
				</div>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="alog-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div><?php echo __('管理'); ?></div></button>
				</div>
				<button<?php echo $aSort[0]; ?> class="AltThemeSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="AltThemeSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="AltThemeEdit text-left"><span class="font-default"><?php echo __('テーマの編集'); ?></span></a></li>
	<li><a href="#" class="AltPreview text-left"><span class="font-default"><?php echo __('プレビュー'); ?></span></a></li>
	<li><a href="#" class="AltToCSV text-left"><span class="font-default"><?php echo __('入力内容のCSV出力'); ?></span></a></li>
	<li><a href="#" class="AltThemeDelete text-left"><span class="font-default"><?php echo __('テーマの削除'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-pub" obj="">
	<li><a href="#" class="AltThemePublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="#" class="AltThemePublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>
