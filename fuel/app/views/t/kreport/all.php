	<section class="info-box">
		<p><a href="/t/kreport/put/<?php echo $aReport['krYear'].'/'.$aReport['krPeriod']; ?>" class="button do na text-center">回答一覧</a></p>
	</section>
		<?php
			if (!is_null($aTeachers)):
				foreach ($aTeachers as $sTtID => $aT):
					$sTName = (($aT['teach']['cmName'])? '<small style="font-size: 60%;">'.$aT['teach']['cmName'].'</small><br>':'').(($aT['teach']['ttName'])? $aT['teach']['ttName']:$aT['teach']['ttMail']);
					$aPut = null;
					$aAns = null;
					if (!isset($aT['put'])):
						$aPut[1] = '未提出';
						$aAns[1] = null;
					else:
						foreach ($aT['put'] as $aP):
							$aPut[$aP['krSub']] = 'レポート'.$aP['krSub'].'<br><small style="font-size: 70%;">'.ClFunc_Tz::tz('Y/m/d H:i',$tz,$aP['krDate']).'</small>';
							$aAns[$aP['krSub']] = null;
							if (isset($aAnswer[$sTtID][$aP['krSub']])):
								$aAns[$aP['krSub']] = $aAnswer[$sTtID][$aP['krSub']];
							endif;
						endforeach;
					endif;
		?>
			<div class="info-box">
				<h2 style="overflow: hidden; font-size: 150%; line-height: 1.1;"><?php echo ($aT['teach']['ttImage'])? '<img src="/upload/profile/t/'.$aT['teach']['ttImage'].'?'.mt_rand().'" style="width: 50px; height: 50px; float: left; margin-right: 5px;">':Asset::img('img_no_icon.png',array('style'=>'width: 50px; height: 50px; float: left; margin-right: 5px;')); ?> <?php echo $sTName; ?></h2>

				<?php foreach ($aPut as $iSub => $sPut): ?>

				<div class="info-box">
				<h2 style="font-size: 140%; line-height: 1.4;"><?php echo $sPut; ?></h2>
				<?php if (!is_null($aAns[$iSub])): ?>
				<hr>
				<?php foreach ($aQuery as $aQ): ?>
				<?php $iKrNO = $aQ['krNO']; ?>
				<div class="info-box mt0">
					<h2><i class="fa fa-chevron-right"></i> <?php echo $aQ['krText']; ?></h2>
					<?php
						if ($aQ['krStyle'] < 2):
							$aChoice = array();
							for ($i = 1; $i <= (int)$aQ['krChoiceNum']; $i++):
								$bSel = ($aAns[$iSub][$iKrNO]['krChoice'.$i])? true:false;
								$sColor = ($bSel !== false)? ' radio-chk':'';
								$sCheck = ($aQ['krStyle'])? (($bSel !== false)? 'fa-check-square-o':'fa-square-o'):(($bSel !== false)? 'fa-dot-circle-o':'fa-circle-o');
								$aChoice[$i] = '<label class="radio-prev'.$sColor.'"><i class="fa '.$sCheck.' fa-fw"></i> <span>'.$aQ['krChoice'.$i];
								$aChoice[$i] .= '</span></label>';
							endfor;
							echo '<div class="answer">';
							foreach ($aChoice as $sC):
								echo $sC;
							endforeach;
							echo '</div>';
						endif;
						if ($aQ['krStyle'] == 2):
							$sText = ($aAns[$iSub][$iKrNO]['krText'])? nl2br($aAns[$iSub][$iKrNO]['krText']):'（無回答）';
					?>
					<p class="font-blue"><?php echo $sText; ?></p>
						<?php endif; ?>
				</div>
				<hr>
				<?php endforeach; ?>
				<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
		<?php
				endforeach;
			endif;
		?>

