<?php if (!is_null($aClasses)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_org_classstudentlist_sort';
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

	<section class="pt0">
		<div class="info-box table-box record-table admin-table">
		<p class="error-box mb16" style="display: none;" id="stErr"></p>
		<form action="/org/class/modify" method="post">
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th>状況</th>
				<th>講義コード</th>
				<th>講義名</th>
				<th class="string-bottom">年度</th>
				<th>期</th>
				<th>曜日</th>
				<th class="string-bottom">時限</th>
				<th class="string-bottom">履修人数</th>
				<th>先生名</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aClasses)):
					$iMax = count($aClasses);
					foreach ($aClasses as $i => $aC):
						$sTtID = $aC['ttID'];
						$sCtID = $aC['ctID'];
			?>
<tr class="">
<td class="">
<?php echo ($aC['ctStatus'])? '<span class="font-red">実施</span>':'<span class="font-silver">終了</span>'; ?>
</td>
<td class="">
<?php echo \Clfunc_Common::getCode($aC['ctCode']); ?>
</td>
<td class="">
<?php echo $aC['ctName']; ?>
</td>
<td class="">
<?php echo $aC['ctYear']; ?>年度
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
<a href="/org/student/classlist/<?php echo $sCtID; ?>" class="button na default width-auto"><?php echo $aC['scNum']; ?></a>
</td>
<td class="">
<?php echo $aC['ttName']; ?>
</td>
</tr>
					<?php
							endforeach;
						endif;
					?>
				</tbody>
			</table>
			</form>
		</div>
	</section>
