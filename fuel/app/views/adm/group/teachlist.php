<?php if (!is_null($aTeachers)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_adm_groupteacherlist_sort';
	var defaultSort = [[2,0],[3,0]];

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

	<section class="pt0">
		<div class="info-box table-box record-table admin-table">
		<form action="/adm/group/teachmod/<?php echo $aGroup['gtID']; ?>" method="post">
		<table class="kreport-data table-sort">
		<thead>
			<tr><!-- <th><input type="checkbox" class="AllChk"></th> --><th>先生ID</th><th>学校</th><th>氏名</th><th>メールアドレス</th><th>実施中講義</th><th>終了講義</th><th>最終ログイン</th></tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aTeachers)):
					$iMax = count($aTeachers);
					foreach ($aTeachers as $i => $aT):
						$sTtID = $aT['ttID'];
			?>
<tr class=""><!--
<td class="">
<input type="checkbox" name="check[]" value="<?php echo $sTtID; ?>">
</td> -->
<td class="">
<?php echo $aT['ttID']; ?>
</td>
<td class="">
<?php echo $aT['cmName']; ?>
</td>
<td class="">
<?php echo $aT['ttName']; ?>
</td>
<td class="">
<?php echo $aT['ttMail']; ?>
</td>
<td class="">
<?php echo $aT['ttClassNum']; ?>
</td>
<td class="">
<?php echo $aT['ttCloseNum']; ?>
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
