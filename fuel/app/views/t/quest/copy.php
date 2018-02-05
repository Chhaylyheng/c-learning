<?php if (!is_null($aClasses)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_questcopy_sort';
	var defaultSort = [[1,0]];

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

	$('table.table-sort').tablesorter({
		cssHeader: 'headerSort',
		headers: {
			0: {sorter: false},
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

<?php
	$errClass = array('selclass'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
?>

	<section class="pt0">
		<div class="info-box table-box record-table admin-table">
		<?php echo $errMsg['selclass']; ?>

		<p class="mb8"><?php echo __('コピー先の講義にチェックをして、「コピー実行」をクリックします。'); ?></p>

		<form action="/t/quest/copy/<?php echo $aQuest['qbID'] ?>" method="post" id="ClassCheckForm">
		<input type="hidden" name="mode" value="copy">

		<h2 class="mt20 mb4 font-white line-height-1" style="padding: 8px 16px 6px; background-color: #545454; "><?php echo __('現在の講義'); ?></h2>
		<table class="kreport-data">
		<tbody>
<tr class="">
<td class="">
<input type="checkbox" name="selclass[]" class="Chk inline-block"  value="<?php echo $aClass['ctID']; ?>">
[<?php echo \Clfunc_Common::getCode($aClass['ctCode']); ?>]
<?php echo $aClass['ctName']; ?>
</td>
</tr>
</tbody>
</table>

<?php
	if (CL_CAREERTASU_MODE) unset($aClasses[1]);
	if (isset($aClasses[0])):
?>
		<h2 class="mt20 mb4 font-white line-height-1" style="padding: 8px 16px 6px; background-color: #545454; "><?php echo __('所有講義'); ?></h2>
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th><?php echo __('講義コード'); ?></th>
				<th><?php echo __('講義名'); ?></th>
				<th class="string-bottom"><?php echo __('年度'); ?></th>
				<th><?php echo __('期'); ?></th>
				<th><?php echo __('曜日'); ?></th>
				<th class="string-bottom"><?php echo __('時限'); ?></th>
				<th class="string-bottom"><?php echo __('履修人数'); ?></th>
				<?php if ($aTeacher['gtID']): ?>
				<th><?php echo __('先生'); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
<?php
		foreach ($aClasses[0] as $sCtID => $aC):
?>
<tr class="">
<td class="">
<input type="checkbox" name="selclass[]" class="Chk" value="<?php echo $sCtID; ?>">
</td>
<td class="">
<?php echo \Clfunc_Common::getCode($aC['ctCode']); ?>
</td>
<td class="">
<?php echo $aC['ctName']; ?>
</td>
<td class="">
<?php echo __(':year年度',array('year'=>$aC['ctYear'])); ?>
</td>
<td class="">
<span sortdata="<?php echo $aC['dpNO']; ?>"><?php echo ($aC['dpNO'])? $aPeriod[$aC['dpNO']]:'─'; ?></span>
</td>
<td class="">
<span sortdata="<?php echo $aC['ctWeekDay']; ?>"><?php echo ($aC['ctWeekDay'])? $aWeekDay[$aC['ctWeekDay']]:'─'; ?></span>
</td>
<td class="">
<span sortdata="<?php echo $aC['dhNO']; ?>"><?php echo ($aC['dhNO'])? $aHour[$aC['dhNO']]:'─'; ?></span>
</td>
<td class="">
<?php echo $aC['scNum']; ?>
</td>
<?php if ($aTeacher['gtID']): ?>
<td class="">
<?php echo $aC['ttName']; ?>
</td>
<?php endif; ?>
</tr>
<?php
		endforeach;
?>
		</tbody>
		</table>
<?php
	endif;
	if (isset($aClasses[1])):
?>
		<h2 class="mt20 mb4 font-white line-height-1" style="padding: 8px 16px 6px; background-color: #545454; "><?php echo __('団体講義'); ?></h2>
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th><?php echo __('講義コード'); ?></th>
				<th><?php echo __('講義名'); ?></th>
				<th class="string-bottom"><?php echo __('年度'); ?></th>
				<th><?php echo __('期'); ?></th>
				<th><?php echo __('曜日'); ?></th>
				<th class="string-bottom"><?php echo __('時限'); ?></th>
				<th class="string-bottom"><?php echo __('履修人数'); ?></th>
				<?php if ($aTeacher['gtID']): ?>
				<th><?php echo __('先生'); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
<?php
		foreach ($aClasses[1] as $sCtID => $aC):
?>
<tr class="">
<td class="">
<input type="checkbox" name="selclass[]" class="Chk" value="<?php echo $sCtID; ?>">
</td>
<td class="">
<?php echo \Clfunc_Common::getCode($aC['ctCode']); ?>
</td>
<td class="">
<?php echo $aC['ctName']; ?>
</td>
<td class="">
<?php echo __(':year年度',array('year'=>$aC['ctYear'])); ?>
</td>
<td class="">
<span sortdata="<?php echo $aC['dpNO']; ?>"><?php echo ($aC['dpNO'])? $aPeriod[$aC['dpNO']]:'─'; ?></span>
</td>
<td class="">
<span sortdata="<?php echo $aC['ctWeekDay']; ?>"><?php echo ($aC['ctWeekDay'])? $aWeekDay[$aC['ctWeekDay']]:'─'; ?></span>
</td>
<td class="">
<span sortdata="<?php echo $aC['dhNO']; ?>"><?php echo ($aC['dhNO'])? $aHour[$aC['dhNO']]:'─'; ?></span>
</td>
<td class="">
<?php echo $aC['scNum']; ?>
</td>
<?php if ($aTeacher['gtID']): ?>
<td class="">
<?php echo $aC['ttName']; ?>
</td>
<?php endif; ?>
</tr>
<?php
		endforeach;
?>
		</tbody>
		</table>
<?php
	endif;
?>

		</form>
		</div>
</section>

