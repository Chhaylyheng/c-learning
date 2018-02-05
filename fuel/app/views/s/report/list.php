<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table>
		<thead>
			<tr>
				<th><?php echo __('学籍番号'); ?></th>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('提出'); ?></th>
				<th>
					<?php echo __('コメント数'); ?>
<?php if ($aReport['rbShare'] == 2): ?>
				/ <?php echo __('平均点'); ?>
<?php endif; ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStu)):
				$aMine = $aStu[$aStudent['stID']];
				unset($aStu[$aStudent['stID']]);
				$aStu = array($aStudent['stID']=>$aMine) + $aStu;
				foreach ($aStu as $sStID => $aS):
					$sColor = ($sStID != $aStudent['stID'])? '':'font-red';

					$aM = array('no'=>'','name'=>'','state'=>'','share'=>'','avg'=>'');
					if (isset($aS['put'])):
						$aP = $aS['put'];

						$aM['no'] = $aP['rpstNO'];
						$aM['name'] = $aP['rpstName'];
						$aM['state'] = ($aP['rpTeachPut'])?
							'<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s" class="button na width-auto confirm">'.__('先生による提出').'</a>':
							(($aP['rpDate'] != CL_DATETIME_DEFAULT)?
								'<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s" class="button na width-auto do">'.Clfunc_Tz::tz('Y/m/d H:i',$tz,$aP['rpDate']).'</a>':
								'<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s" class="button na width-auto default">'.__('未提出').'</a>');

						$aM['share'] = '<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s" class="button na width-auto default"><i class="fa fa-comments mr4"></i>'.$aP['rpComNum'];
						if ($aReport['rbShare'] == 2):
							$aM['share'] .= ' <i class="fa fa-star mr4"></i>'.$aP['rpAvgScore'];
						endif;
						$aM['share'] .= '</a>';
					else:
						$aM['state'] = '<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s" class="button na width-auto default">'.__('未提出').'</a>';
						$aM['share'] = '<a href="/s/report/shareboard/'.$aReport['rbID'].'/'.$sStID.'/s" class="button na width-auto default"><i class="fa fa-comments mr4"></i>0';
						if ($aReport['rbShare'] == 2):
							$aM['share'] .= ' <i class="fa fa-star mr4"></i>0';
						endif;
						$aM['share'] .= '</a>';
					endif;
					if (isset($aS['stu'])):
						$aM['no'] = $aS['stu']['stNO'];
						$aM['name'] = $aS['stu']['stName'];
					endif;
		?>
			<tr>
				<td><span class="sp-display-inline">(</span><?php echo $aM['no']; ?><span class="sp-display-inline">) </span></td>
				<td class="<?php echo $sColor; ?>"><?php echo $aM['name']; ?></td>
				<td class="sp-full"><?php echo $aM['state']; ?></td>
				<td class="sp-right"><?php echo $aM['share']; ?></td>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>
</div>

