<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr><!-- <th><input type="checkbox" class="AllCheck" value="Close"></th> -->
				<th><?php echo __('タイトル'); ?></th>
				<th><?php echo __('公開'); ?></th>
				<th><?php echo __('ファイル'); ?></th>
				<th><?php echo __('登録日時'); ?></th>
				<th><?php echo __('既読'); ?></th>
				<th><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aMaterial)):
				$iMax = count($aMaterial);
				foreach ($aMaterial as $aM):
					$sJsKey = $aM['mcID'].'_'.$aM['mNO'];
					$aSort = array(' ',' ');
					$aPub = array(__('公開中'),'font-blue');
					if ($aM['mPublic'] == 0):
						$aPub = array(__('非公開'),'font-default');
					endif;
					if ($aM['mSort'] == $iMax):
						$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aM['mSort'] == 1):
						$aSort[1] = ' disabled="disabled"';
					endif;
					$sFile = null;
					if ($aM['fID']):
						$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aM['fID']));
						$sSize = \Clfunc_Common::FilesizeFormat($aM['fSize'],1);
						$sFile = '<p class="mt0"><a href="'.$sLink.'" target="_blank">'.$aM['fName'].'</a>('.$sSize.')</p>';
					endif;
					$sExtFile = null;

					if (is_array($aM['mURL'])):
						foreach ($aM['mURL'] as $i => $v):
							if (!$v) continue;
							if ($aM['clurl'][$i]):
								$sExtFile .= '<p class="mt0"><i class="fa fa-chain"></i> <a href="'.$aM['clurl'][$i]['url'].'" target="_blank" title="'.$aM['clurl'][$i]['url'].'" >'.$aM['clurl'][$i]['title'].'</a></p>';
							else:
								$sExtFile .= '<p class="mt0"><i class="fa fa-external-link"></i> <a href="'.$v.'" target="_blank" title="'.$v.'">'.mb_strimwidth($v,0,25,'…').'</a></p>';
							endif;
						endforeach;
					endif;

					$sDate = ($aM['mDate'] != '0000-00-00 00:00:00')? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aM['mDate']):'─';
		?>
		<tr id="n<?php echo $aM['mNO']; ?>">
			<!-- <td class="text-center" nowrap="nowrap"><input type="checkbox" name="Close[]" value="<?php echo $aM['mcID']; ?>"></td> -->
			<td class="sp-full">
				<?php echo $aM['mTitle']; ?>
			</td>
			<td class="" nowrap="nowrap">
				<div class="dropdown">
					<button type="button" class="quest-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sJsKey; ?>_pub"><div><?php echo $aPub[0]; ?></div></button>
				</div>
			</td>
			<td class="sp-full">
				<?php echo $sFile.$sExtFile; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('登録'); ?>:</span
				><?php echo $sDate; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('既読'); ?>:</span
				><a href="/t/material/already/<?php echo $aM['mcID'].DS.$aM['mNO']; ?>"><?php echo $aM['mAlreadyNum']; ?></a>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="quest-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div><?php echo __('管理'); ?></div></button>
				</div>
				<button<?php echo $aSort[0]; ?> class="MaterialSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="MaterialSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="MaterialEdit text-left"><span class="font-default"><?php echo __('教材の編集'); ?></span></a></li>
	<li><a href="#" class="MaterialDelete text-left"><span class="font-default"><?php echo __('教材の削除'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-pub" obj="">
	<li><a href="#" class="MaterialPublic" obj="public"><span class="font-blue"><?php echo __('公開中'); ?></span></a></li>
	<li><a href="#" class="MaterialPublic" obj="private"><span class="font-default"><?php echo __('非公開'); ?></span></a></li>
</ul>
