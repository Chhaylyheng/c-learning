<?php if (!is_null($aAdmins)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_adm_groupadminlist_sort';
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
			4: {sorter: false},
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
		<p class="error-box mb16" style="display: none;" id="gaErr"></p>
		<table class="kreport-data table-sort">
		<thead>
			<tr><th>ログインID</th><th>パスワード</th><th>氏名</th><th>ログイン日時</th><th>管理</th><th>登録日時</th></tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aAdmins)):
					$iMax = count($aAdmins);
					foreach ($aAdmins as $i => $aA):
						$sJsKey = $aA['gtID'].'_'.$aA['gaID'];
			?>
<tr class="">
<td class="">
<?php echo $aA['gaLogin']; ?>
</td>
<td class="">
<span><?php echo ($aA['gaFirst'])? $aA['gaFirst']:'（変更済）'; ?></span>
<a href="#" class="password_reset" title="パスワードリセット" data="<?php echo $aA['gaID']; ?>"><i class="fa fa-refresh"></i></a>
</td>
<td class="">
<?php echo $aA['gaName']; ?>
</td>
<td class="">
<?php echo ($aA['gaLoginNum'])? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aA['gaLoginDate']).' ('.$aA['gaLoginNum'].')':'未ログイン'; ?>
</td>
<td class="">
	<div class="dropdown inline-block">
		<button type="button" class="group-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div>管理</div></button>
	</div>
</td>
<td class="">
<?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aA['gaDate']); ?>
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
	<li><a href="#" class="GroupAdminEdit text-left"><span class="font-default">管理者情報の編集</span></a></li>
	<li><a href="#" class="GroupAdminDelete text-left"><span class="font-default">管理者の削除</span></a></li>
</ul>
