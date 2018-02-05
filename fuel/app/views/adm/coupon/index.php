<?php if (!is_null($aCoupons)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_adm_coupon_sort';
	var defaultSort = [[0,1]];

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
		<table class="kreport-data table-sort">
		<thead>
			<tr><th>No.</th><th>クーポンコード</th><th>クーポン名称</th><th>割引率</th><th>支払条件</th><th>契約期間条件</th><th>利用期間</th><th>管理</th><th>登録日時</th></tr>
		</thead>
		<tbody>
<?php
	if (!is_null($aCoupons)):
		$iMax = count($aCoupons);
		foreach ($aCoupons as $i => $aC):
			$sJsKey = $aC['no'];
?>
<tr class="">
<td class="">
<?php echo $aC['no']; ?>
</td>
<td class="">
<?php echo $aC['cpCode']; ?>
</td>
<td class="">
<?php echo $aC['cpName']; ?>
</td>
<td class="">
<?php echo $aC['cpDiscount']; ?>%
</td>
<td class="">
<?php
	foreach ($aPaymentType as $i => $v):
		if ($aC['cpPaymentType'] & $i):
			echo $v.'<br>';
		endif;
	endforeach;
?>
</td>
<td class="">
<?php echo $aC['cpRange']; ?>ヶ月以上
</td>
<td class="">
<?php echo ($aC['cpTermDate'] == '9999-12-31')? '無期限':date('Y/m/d',strtotime($aC['cpTermDate'])).'まで'; ?>
</td>
<td class="">
	<div class="dropdown inline-block">
		<button type="button" class="coupon-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div>管理</div></button>
	</div>
</td>
<td class="">
<?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aC['cpDate']); ?>
</td>
</tr>
<?php
		endforeach;
	endif;
?>
		</tbody>
		</table>
		</div>
	</section>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="CouponEdit text-left"><span class="font-default">クーポンの編集</span></a></li>
	<li><a href="#" class="CouponDelete text-left"><span class="font-default">クーポンの削除</span></a></li>
</ul>
