<?php if (!is_null($aGroups)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_adm_group_sort';
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
			7: {sorter: false},
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
			<tr>
				<th>団体名称</th><th>接頭辞</th><th>管理者</th><th>所属先生数</th><th>講義数</th><th>学生数</th><th>LDAP連携</th><th>管理</th><th>登録日時</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aGroups)):
					$iMax = count($aGroups);
					foreach ($aGroups as $i => $aG):
						$sJsKey = $aG['gtID'];
			?>
<tr class="">
<td class="">
<?php echo $aG['gtName']; ?>
</td>
<td class="">
<?php echo $aG['gtPrefix']; ?>
</td>
<td class="">
<a data="<?php echo $aG['gtANum']; ?>" href="/adm/group/admlist/<?php echo $aG['gtID']; ?>" class="button na default width-auto"><?php echo $aG['gtANum']; ?></a>
</td>
<td class="">
<a data="<?php echo $aG['gtTNum']; ?>" href="/adm/group/teachlist/<?php echo $aG['gtID']; ?>" class="button na default width-auto"><?php echo $aG['gtTNum']; ?></a>
</td>
<td class=""><?php /*
<a data="<?php echo $aG['gtCNum']; ?>" href="/adm/group/classlist/<?php echo $aG['gtID']; ?>" class="button na default width-auto"><?php echo $aG['gtCNum']; ?></a>
*/ ?>
<?php echo $aG['gtCNum']; ?>
</td>
<td class=""><?php /*
<a data="<?php echo $aG['gtSNum']; ?>" href="/adm/group/stulist/<?php echo $aG['gtID']; ?>" class="button na default width-auto"><?php echo $aG['gtSNum']; ?></a>
*/ ?>
<?php echo $aG['gtSNum']; ?>
</td>
<td class="">
<?php echo (($aG['gtLDAP'])? '<i class="fa fa-check"></i>':'─'); ?>
</td>
<td class="">
	<div class="dropdown inline-block">
		<button type="button" class="group-dropdown-toggle" id="<?php echo $sJsKey; ?>_edit"><div>管理</div></button>
	</div>
</td>
<td class="">
<?php echo ClFunc_Tz::tz('Y/m/d<\b\r>H:i',$tz,$aG['gtDate']); ?>
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
	<li><a href="#" class="GroupEdit text-left"><span class="font-default">団体情報の編集</span></a></li>
	<li><a href="#" class="GroupDelete text-left"><span class="font-default">団体の削除</span></a></li>
</ul>
