<?php if (!is_null($aTeachers)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_org_teacherclasslist_sort';
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

	$('table.table-sort').tablesorter({
		cssHeader: 'headerSort',
		headers: {
			1: {sorter: false},
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
		<form action="/org/teacher/modify" method="post">
		<table class="kreport-data table-sort" obj="<?php echo $aClass['ctID']; ?>">
		<thead>
			<tr>
				<th>氏名</th>
				<th>主</th>
				<th>メールアドレス</th>
				<th>学部</th>
				<th>学科</th>
				<th class="string-bottom">実施中講義</th>
				<th class="string-bottom">終了講義</th>
				<th class="string-bottom">最終ログイン</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aTeachers)):
					$iMax = count($aTeachers);
					foreach ($aTeachers as $i => $aT):
						$sTtID = $aT['ttID'];
						$sMaster = ($aT['tpMaster'])? ' checked':'';
			?>
<tr class="">
<td class="">
<?php echo $aT['ttName']; ?>
</td>
<td class="">
<input type="radio" name="MasterCheck" value="<?php echo $sTtID; ?>"<?php echo $sMaster; ?>>
</td>
<td class="">
<?php echo $aT['ttMail']; ?>
</td>
<td class="">
<?php echo $aT['ttDept']; ?>
</td>
<td class="">
<?php echo $aT['ttSubject']; ?>
</td>
<td class="">
<a href="/org/class/index/<?php echo $sTtID; ?>" class="button na default width-auto"><?php echo (int)$aT['ttClassNum']; ?></a>
</td>
<td class="">
<a href="/org/class/index/<?php echo $sTtID; ?>" class="button na default width-auto"><?php echo (int)$aT['ttCloseNum']; ?></a>
</td>
<td class="">
<?php echo ($aT['ttLoginNum'])? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aT['ttLoginDate']).' ('.$aT['ttLoginNum'].')':'未ログイン'; ?>
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
