<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table>
		<thead>
			<tr>
<?php if ($aQuest['qbAnsPublic'] == 2): ?>
				<th><?php echo __('学籍番号'); ?></th>
				<th><?php echo __('氏名'); ?></th>
<?php else: ?>
				<th><?php echo __('学生'); ?></th>
<?php endif; ?>
				<th><?php echo __('回答'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStu)):
				foreach ($aStu as $aS):
					$aM = array('no'=>'','name'=>'','ans'=>__('未回答'));
					if (isset($aS['put'])):
						$aM['no'] = $aS['put']['qpstNO'];
						$aM['name'] = $aS['put']['qpstName'];
						$sComIcon = null;
						if (($aQuest['qbComPublic'] == 2 || ($aQuest['qbComPublic'] == 1 && $aS['stu']['stID'] == $aStudent['stID'])) && $aS['put']['qpComment']):
							$sComIcon = '<i class="fa fa-commenting fa-fw fa-lg" style="vertical-align: top;"></i>';
						endif;
						$aM['ans'] = '<a href="/s/quest/ansdetail/'.$aQuest['qbID'].'/'.Crypt::encode($aS['stu']['stID']).'">'.__('回答を見る').$sComIcon.'</a>';
					endif;
					if (isset($aS['stu'])):
						$aM['no'] = $aS['stu']['stNO'];
						$aM['name'] = $aS['stu']['stName'];
					endif;
					if ($aQuest['qbAnsPublic'] < 2):
						if ($aS['stu']['stID'] != $aStudent['stID']):
							$aM['name'] = __('＜匿名＞');
						endif;
					endif;
		?>
			<tr>
		<?php if ($aQuest['qbAnsPublic'] == 2): ?>
				<td><?php echo $aM['no']; ?></td>
		<?php endif; ?>
				<td><?php echo $aM['name']; ?></td>
				<td><?php echo $aM['ans']; ?></td>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>
</div>

