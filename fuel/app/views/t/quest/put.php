<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_questputstudent_sort';
	var defaultSort = [[0,0]];

	var currentSort = new Array();
	if(('sessionStorage' in window) && (window.sessionStorage !== null)) {
		store = sessionStorage.getItem(sskey);
		if (store) {
			store = store.split('|');
			for (i = 0; i < store.length; i++) {
				currentSort.push(store[i].split(','));
			}
		}
	}
	if (!currentSort || currentSort == null || currentSort.length == 0) {
		currentSort = defaultSort;
	}

	$('#Student-Table').tablesorter({
		cssHeader: 'headerSort',
		headers: {
		},
		sortList: currentSort,
		widgets: ['zebra']
	}).bind("sortEnd", function(sorter) {
		currentSort = sorter.target.config.sortList;
		currentSort = currentSort.join('|');
		setSessionStorage(sskey, currentSort);
	});
});
</script>
<?php
	endif;
	$sTableName = __('提出').'：<span class="font-red font-size-160">'.$aQuest['qpNum'].'</span> / '.$aQuest['scNum'];
	if (!is_null($aGuest)):
		$sTableName = __('学生').'：<span class="font-red font-size-160">'.$aQuest['qpNum'].'</span> / '.$aQuest['scNum'];
?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_questputguest_sort';
	var defaultSort = [[1,1]];

	var currentSort = new Array();
	if(('sessionStorage' in window) && (window.sessionStorage !== null)) {
		store = sessionStorage.getItem(sskey);
		if (store) {
			store = store.split('|');
			for (i = 0; i < store.length; i++) {
				currentSort.push(store[i].split(','));
			}
		}
	}
	if (!currentSort || currentSort == null || currentSort.length == 0) {
		currentSort = defaultSort;
	}

	$('#Guest-Table').tablesorter({
		cssHeader: 'headerSort',
		headers: {
		},
		sortList: currentSort,
		widgets: ['zebra']
	}).bind("sortEnd", function(sorter) {
		currentSort = sorter.target.config.sortList;
		currentSort = currentSort.join('|');
		setSessionStorage(sskey, currentSort);
	});
});
</script>
<?php
	endif;
	if (!is_null($aTeach)):
?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_questputteach_sort';
	var defaultSort = [[2,1]];

	var currentSort = new Array();
	if(('sessionStorage' in window) && (window.sessionStorage !== null)) {
		store = sessionStorage.getItem(sskey);
		if (store) {
			store = store.split('|');
			for (i = 0; i < store.length; i++) {
				currentSort.push(store[i].split(','));
			}
		}
	}
	if (!currentSort || currentSort == null || currentSort.length == 0) {
		currentSort = defaultSort;
	}

	$('#Teach-Table').tablesorter({
		cssHeader: 'headerSort',
		headers: {
		},
		sortList: currentSort,
		widgets: ['zebra']
	}).bind("sortEnd", function(sorter) {
		currentSort = sorter.target.config.sortList;
		currentSort = currentSort.join('|');
		setSessionStorage(sskey, currentSort);
	});
});
</script>
<?php endif; ?>

<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<h2 class="mb4"><?php echo $sTableName; ?></h2>
		<table class="kreport-data table-sort" id="Student-Table">
		<thead>
			<tr>
				<th class="string-bottom"><?php echo __('学籍番号'); ?></th>
				<th><?php echo __('氏名'); ?></th>

				<th><?php echo __('学年'); ?></th>

				<th><?php echo __('クラス'); ?></th>
				<th><?php echo ($aQuest['qbAnonymous'])? __('提出'):__('提出日時'); ?></th>
				<?php if (!$aQuest['qbAnonymous']): ?>
				<th class="string-bottom"><?php echo __('文字数'); ?></th>
				<th class="string-bottom"><?php echo Asset::img('icon_pick_a.png',array('alt'=>'')); ?></th>
				<th class="string-bottom"><?php echo Asset::img('icon_pick_c.png',array('alt'=>'')); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
		<?php
			$bQuick = ($aQuest['qbQuickMode'])? true:false;
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$aM = array('no'=>'','name'=>'','year'=>'','class'=>'','date'=>'─','length'=>0,'pickup'=>0,'pickdown'=>0);

					if (isset($aS['stu'])):
						$aM['no'] = $aS['stu']['stNO'];
						$aM['name'] = $aS['stu']['stName'];
						$aM['year'] = $aS['stu']['stYear'];
						$aM['class'] = $aS['stu']['stClass'];
					endif;
					if (isset($aS['put'])):
						$aM['no'] = $aS['put']['qpstNO'];
						$aM['name'] = $aS['put']['qpstName'];
						$aM['class'] = $aS['put']['qpstClass'];

						if ($bQuick || $aQuest['qbAnonymous']):
							$sCom = '';
						else:
							$sCom = (($aS['put']['qpComment'])? ' <i class="fa fa-commenting mr0"></i>':' <i class="fa fa-commenting-o mr0"></i>');
						endif;
						if ($aQuest['qbAnonymous']):
							$aM['date'] = '<i class="fa fa-check font-green"></i>';
						else:
							$aM['date'] = '<a href="/t/quest/ansdetail/'.$aS['put']['qbID'].'/'.$aS['put']['stID'].'" class="button na width-auto do font-size-80">'.ClFunc_Tz::tz('Y/m/d H:i',$tz,$aS['put']['qpDate']).$sCom.'</a>';
						endif;
						$aM['length'] = $aS['put']['qpLetterNum'];
						$aM['pickup'] = $aS['put']['qpPickUp'];
						$aM['pickdown'] = $aS['put']['qpPickDown'];
					endif;
		?>
			<tr>
				<td>
					<?php echo $aM['no']; ?>
				</td>
				<td>
					<?php echo $aM['name']; ?>
				</td>
				<td>
					<?php echo $aM['year']; ?>
				</td>
				<td>
					<?php echo $aM['class']; ?>
				</td>
				<td class="sp-full">
					<?php echo $aM['date']; ?>
				</td>
				<?php if (!$aQuest['qbAnonymous']): ?>
				<td><span class="sp-display-inline font-grey"><?php echo __('文字数'); ?>:</span
					><?php echo $aM['length']; ?>
				</td>
				<td><span class="sp-display-inline font-grey"><?php echo Asset::img('icon_pick_a.png',array('alt'=>'')); ?>:</span
					><?php echo $aM['pickup']; ?>
				</td>
				<td><span class="sp-display-inline font-grey"><?php echo Asset::img('icon_pick_c.png',array('alt'=>'')); ?>:</span
					><?php echo $aM['pickdown']; ?>
				</td>
				<?php endif; ?>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
	</div>

	<?php if (!is_null($aGuest)): ?>
	<div class="info-box table-box record-table admin-table mt24">
		<h2 class="mb4"><?php echo __('ゲスト'); ?>：<span class="font-red font-size-160"><?php echo $aQuest['qpGNum']; ?></span></h2>
		<table class="kreport-data table-sort" id="Guest-Table">
		<thead>
			<tr>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('提出日時'); ?></th>
				<th class="string-bottom"><?php echo __('文字数'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($aGuest as $sGtID => $aG):
				$aM = array('name'=>'','date'=>'─','length'=>0);

				if (!isset($aG['put'])):
					continue;
				else:
					$aM['name'] = ($aQuest['qbOpen'] == 2)? (($aG['put']['qpstName'])? $aG['put']['qpstName']:(($aG['put']['gtName'])? $aG['put']['gtName']:__('─無記名─'))):__('─匿名─');
					$aM['date'] = '<a href="/t/quest/ansdetail/'.$aG['put']['qbID'].'/'.$aG['put']['stID'].'" class="button na width-auto do font-size-80">'.ClFunc_Tz::tz('Y/m/d H:i',$tz,$aG['put']['qpDate']).'</a>';
					$aM['length'] = $aG['put']['qpLetterNum'];
				endif;
		?>
			<tr>
				<td>
					<?php echo $aM['name']; ?>
				</td>
				<td class="sp-full">
					<?php echo $aM['date']; ?>
				</td>
				<td><span class="sp-display-inline font-grey"><?php echo __('文字数'); ?>:</span
					><?php echo $aM['length']; ?>
				</td>
			</tr>
		<?php
			endforeach;
		?>
		</tbody>
		</table>
	</div>
	<?php endif; ?>

	<?php if (!is_null($aTeach)): ?>
	<div class="info-box table-box record-table admin-table mt24">
		<h2 class="mb4"><?php echo __('先生'); ?>：<span class="font-red font-size-160"><?php echo $aQuest['qpTNum']; ?></span></h2>
		<table class="kreport-data table-sort" id="Guest-Table">
		<thead>
			<tr>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('学部・学科'); ?></th>
				<th><?php echo __('提出日時'); ?></th>
				<th class="string-bottom"><?php echo __('文字数'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($aTeach as $sTtID => $aT):
				$aM = array('name'=>'','subject'=>'','date'=>'─','length'=>0);

				if (!isset($aT['put'])):
					continue;
				else:
					$aM['name'] = ($aT['put']['qpstName'])? $aT['put']['qpstName']:$aT['put']['ttName'];
					$aM['subject'] = ($aT['put']['qpstClass'])? $aT['put']['qpstClass']:$aT['put']['ttDept'].$aT['put']['ttSubject'];
					$aM['date'] = '<a href="/t/quest/ansdetail/'.$aT['put']['qbID'].'/'.$aT['put']['stID'].'" class="button na width-auto do font-size-80">'.ClFunc_Tz::tz('Y/m/d H:i',$tz,$aT['put']['qpDate']).'</a>';
					$aM['length'] = $aT['put']['qpLetterNum'];
				endif;
		?>
			<tr>
				<td>
					<?php echo $aM['name']; ?>
				</td>
				<td>
					<?php echo $aM['subject']; ?>
				</td>
				<td class="sp-full">
					<?php echo $aM['date']; ?>
				</td>
				<td><span class="sp-display-inline font-grey"><?php echo __('文字数'); ?>:</span
					><?php echo $aM['length']; ?>
				</td>
			</tr>
		<?php
			endforeach;
		?>
		</tbody>
		</table>
	</div>
	<?php endif; ?>

</div>
