<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr><th><?php echo __('講義名'); ?></th><th><?php echo __('講義コード'); ?></th><th><?php echo __('期/曜日/時限'); ?></th><th><?php echo __('履修人数'); ?></th>
			<?php if ($aTeacher['gtID']): ?>
			<th><?php echo __('先生'); ?></th>
			<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aClsClass)):
					$iMax = count($aClsClass);
					foreach ($aClsClass as $aC):
						$sMaster = '';
						$sColor = ($aC['tpMaster'])? 'do':'confirm';
						if ($aTeacher['gtID']):
							$sTpNum = ($aC['tpNum'])? ' ['.__('他:num名',array('num'=>$aC['tpNum'])).']':'';
							if (isset($aMasters[$aC['ctID']])):
								$sMaster = $aMasters[$aC['ctID']]['ttName'];
							endif;
						endif;
						$sPeriod = __(':year年度',array('year'=>$aC['ctYear']));
						$sPeriod .= ($aC['dpNO'])? ' '.$aPeriod[$aC['dpNO']]:'';
						$sPeriod .= ($aC['ctWeekDay'])? '/'.$aWeekDay[$aC['ctWeekDay']]:'';
						$sPeriod .= ($aC['dhNO'])? '/'.$aHour[$aC['dhNO']]:'';
				?>
				<tr>
					<td><span class="sp-display font-grey"><?php echo __('講義名'); ?></span>
						<a href="/t/class/index/<?php echo $aC['ctID']; ?>" class="text-left button na width-100 font-size-120 <?php echo $sColor; ?>" style="padding: 8px;"><?php echo $aC['ctName']; ?></a>
					</td>

					<td><span class="sp-display font-grey"><?php echo __('講義コード'); ?></span>
						<?php echo \Clfunc_Common::getCode($aC['ctCode']); ?>
					</td>

					<td class=""><span class="sp-display font-grey"><?php echo __('期/曜日/時限'); ?></span>
						<?php echo $sPeriod; ?>
					</td>

					<td class=""><span class="sp-display font-grey"><?php echo __('履修人数'); ?></span>
						<a href="/t/student/index/<?php echo $aC['ctID']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo __(':num名',array('num'=>$aC['scNum'])); ?></a>
					</td>

					<?php if ($aTeacher['gtID']): ?>
					<td class=""><span class="sp-display font-grey"><?php echo __('先生'); ?></span>
						<a href="/t/class/teacher/<?php echo $aC['ctID']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo $sMaster.$sTpNum; ?></a>
					</td>
					<?php endif; ?>

				</tr>
				<?php endforeach;?>
			<?php endif; ?>
		</tbody>
		</table>
	</div>
</div>
