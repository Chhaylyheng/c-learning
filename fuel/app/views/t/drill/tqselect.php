<div class="info-box">
	<div class="info-box table-box record-table admin-table mt0">
		<h4 class="mb16">1. <?php echo __('小テストにコピーする問題を選択してください。'); ?></h4>
		<table>
		<form action="/t/drill/tqselect/<?php echo $aDrill['dcID'].DS.$aDrill['dbNO']; ?>" method="post">
		<input type="hidden" name="subchk" value="1">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th nowrap="nowrap">No.</th>
				<th><?php echo __('回答形式'); ?></th>
				<th><?php echo __('問題文'); ?></th>
				<th><?php echo __('選択肢'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$aStyleName = array(__('択一形式'),__('選択形式（複数回答可）'),__('テキスト入力形式'));
			if (!is_null($aQuery)):
				foreach ($aQuery as $aQ):
					$iSort = $aQ['dqSort'];
					$iNO = $aQ['dqNO'];
					$sChk = (array_search($iNO, $aQChk) !== false)? ' checked':'';
		?>
			<tr>
				<td nowrap="nowrap">
					<input type="checkbox" name="QueryChk[]" class="Chk" value="<?php echo $iNO; ?>"<?php echo $sChk; ?>>
				</td>
				<td nowrap="nowrap"><?php echo $iSort; ?></td>
				<td><?php echo $aStyleName[$aQ['dqStyle']]; ?></td>
				<td><?php echo $aQ['dqText']; ?></td>
				<td>
		<?php if ($aQ['dqStyle'] != 2): ?>
			<?php
				$aR = explode('|', $aQ['dqRight1']);
			?>
			<?php $aQCL = array_fill(1, $aQ['dqChoiceNum'], ''); ?>
			<ul class="ListQueryChoice">
			<?php foreach ($aQCL as $i => $v): ?>
				<?php
					$sIcon = ($aQ['dqStyle'] == 1)? 'fa-square-o':'fa-circle-o';
					$sColor = (array_search($i, $aR) !== false)? 'confirm':'default';
				?>
				<li><button type="button" class="button na <?php echo $sColor; ?> text-left" style="padding: 8px 8px;"><i class="fa <?php echo $sIcon; ?>"></i>
				<?php echo nl2br($aQ['dqChoice'.$i]); ?>
				<?php if ($aQ['dqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['dcID'].DS.$aQ['dbNO'].DS.$aQ['dqNO'].DS.$aQ['dqChoiceImg'.$i])): ?>
					<br><img src="<?php echo DS.CL_UPDIR.DS.$aQ['dcID'].DS.$aQ['dbNO'].DS.$aQ['dqNO'].DS.$aQ['dqChoiceImg'.$i].'?'.mt_rand(); ?>" style="max-width: 160px; max-height: 120px;">
				<?php endif; ?>
				</button></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<p>[<?php echo __('正解文字列'); ?>]</p>
			<?php for($i = 1; $i <= 5; $i++): ?>
				<?php if (isset($aQ['dqRight'.$i])): ?>
					<p class="font-green mt4 ml8" style="display: inline-block;"><?php echo $aQ['dqRight'.$i]; ?></p>
				<?php endif; ?>
			<?php endfor;?>
		<?php endif; ?>
				</td>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
		<div class="button-box mt16 text-center">
			<a href="/t/drill/list/<?php echo $aDrill['dcID']; ?>" class="button na default width-auto" style="float: left;"><?php echo __('戻る'); ?></a>
			<button type="submit" class="button na do width-auto"><?php echo __('次へ'); ?></button>
		</div>
		</form>
	</div>
</div>
