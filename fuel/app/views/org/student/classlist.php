<?php if (!is_null($aStudents)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_org_studentclasslist_sort';
	var defaultSort = [[2,0]];

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
			5: {sorter: false},
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
				<th>ログインID</th>
				<th>パスワード</th>
				<th class="string-bottom">学籍番号</th>
				<th>氏名</th>
				<th>性別</th>
				<th>学部</th>
				<th>学科</th>
				<th>学年</th>
				<th>クラス</th>
				<th>コース</th>
				<th class="string-bottom">履修講義数</th>
				<th>メール</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aStudents)):
					$iMax = count($aStudents);
					foreach ($aStudents as $i => $aS):
						$sStID = $aS['stID'];
			?>
<tr class="">
<td class="">
<?php echo $aS['stLogin']; ?>
</td>
<td nowrap>
	<span><?php echo ($aS['stFirst'])? $aS['stFirst']:'（変更済）'; ?></span>
	<a href="#" class="password_reset" title="パスワードリセット" data="<?php echo $sStID; ?>"><i class="fa fa-refresh"></i></a>
</td>
<td class="">
<?php echo $aS['stNO']; ?>
</td>
<td class="">
<?php echo $aS['stName']; ?>
</td>
<td class="">
<?php echo $aSex[$aS['stSex']]; ?>
</td>
<td class="">
<?php echo $aS['stDept']; ?>
</td>
<td class="">
<?php echo $aS['stSubject']; ?>
</td>
<td class="">
<?php echo $aS['stYear']; ?>
</td>
<td class="">
<?php echo $aS['stClass']; ?>
</td>
<td class="">
<?php echo $aS['stCourse']; ?>
</td>
<td class="">
<a href="/org/class/studentlist/<?php echo $sStID; ?>" class="button na default width-auto"><?php echo $aS['stGtClassNum']; ?></a>
</td>
<td class="">
<i class="fa fa-<?php echo ($aS['stMail'])? 'envelope':'times'; ?>"></i>
</td>
<td class="">
	<div class="dropdown inline-block">
		<button type="button" class="student-dropdown-toggle" id="<?php echo $sStID; ?>_edit_<?php echo $aClass['ctID']; ?>"><div>管理</div></button>
	</div>
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

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="StudentRemove text-left"><span class="font-default">履修削除</span></a></li>
</ul>
