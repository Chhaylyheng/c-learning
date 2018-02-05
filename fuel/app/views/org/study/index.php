<?php if (!is_null($aStudents)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_org_study_sort';
	var defaultSort = [[<?php echo (CL_CAREERTASU_MODE)? 3:1; ?>,0],[0,0]];

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

	<?php $i = 0; ?>

	$('table.table-sort').tablesorter({
		cssHeader: 'headerSort',
		headers: {
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// ログインID
		<?php if (!CL_CAREERTASU_MODE): ?>
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 学籍番号
		<?php endif; ?>
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 氏名
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 性別
		<?php if (CL_CAREERTASU_MODE): ?>
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 学校
		<?php endif; ?>
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 学部
		<?php if (!CL_CAREERTASU_MODE): ?>
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 学科
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 学年
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// クラス
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// コース
		<?php endif; ?>
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 履修数
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
$sNumCell = '';
$sClsCell = '';
if (!is_null($aClasses)):
	foreach ($aClasses as $sCtID => $aC):
		$sNumCell .= '<td class="text-center"><a href="/org/student/classlist/'.$sCtID.'" class="button na default width-auto">'.$aC['scNum'].'</a></td>';
		$sClsCell .= '<th style="min-width: 8em; max-width: 20em;">'.$aC['ctName'].'<br>['.$aC['ctCode'].']</th>';
	endforeach;
endif;
?>


	<section class="pt0">
		<div class="info-box table-box record-table admin-table scroll-box">
		<p class="error-box mb16" style="display: none;" id="stErr"></p>
		<form action="/org/study" method="post" id="CheckForm">
		<input type="hidden" name="mode" value="">

<?php echo $oPagination; ?>

		<table class="matrix-data table-sort mb8 mt8">
		<thead>
			<tr>
				<td colspan="<?php echo (CL_CAREERTASU_MODE)? 6:10; ?>" class="text-right"><?php echo __('履修人数'); ?></td>
				<?php echo $sNumCell; ?>
			</tr>
			<tr>
				<th nowrap style="min-width: 8em; max-width: 20em;"><?php echo __('ログインID'); ?></th>
<?php if (!CL_CAREERTASU_MODE): ?>
				<th nowrap style="min-width: 8em; max-width: 20em;" class="string-bottom"><?php echo __('学籍番号'); ?></th>
<?php endif; ?>
				<th nowrap style="min-width: 12em; max-width: 20em;"><?php echo __('氏名'); ?></th>
				<th nowrap style="min-width: 6em; max-width: 20em;"><?php echo __('性別'); ?></th>
<?php if (CL_CAREERTASU_MODE): ?>
				<th nowrap style="min-width: 8em; max-width: 20em;"><?php echo __('学校'); ?></th>
<?php endif; ?>
				<th nowrap style="min-width: 8em; max-width: 20em;"><?php echo __('学部'); ?></th>
<?php if (!CL_CAREERTASU_MODE): ?>
				<th nowrap style="min-width: 8em; max-width: 20em;"><?php echo __('学科'); ?></th>
				<th nowrap style="min-width: 8em; max-width: 20em;"><?php echo __('学年'); ?></th>
				<th nowrap style="min-width: 8em; max-width: 20em;"><?php echo __('クラス'); ?></th>
				<th nowrap style="min-width: 8em; max-width: 20em;"><?php echo __('コース'); ?></th>
<?php endif; ?>
				<th nowrap class="string-bottom"><?php echo __('履修数'); ?></th>
				<?php echo $sClsCell; ?>
			</tr>
		</thead>
		<tbody>
<?php
	if (!is_null($aStudents)):
		$iMax = count($aStudents);
		foreach ($aStudents as $sStID => $aS):
			$aStudy = null;
			if (isset($aStudies[$sStID])):
				$aStudy = $aStudies[$sStID];
			endif;
?>
<tr class="">
<td nowrap class="">
<?php echo $aS['stLogin']; ?>
</td>
<?php if (!CL_CAREERTASU_MODE): ?>
<td class="">
<?php echo $aS['stNO']; ?>
</td>
<?php endif; ?>
<td class="">
<?php echo $aS['stName']; ?>
</td>
<td class="">
<?php echo $aSex[$aS['stSex']]; ?>
</td>
<?php if (CL_CAREERTASU_MODE): ?>
<td class="">
<?php echo $aS['stSchool']; ?>
</td>
<?php endif; ?>
<td class="">
<?php echo $aS['stDept']; ?>
</td>
<?php if (!CL_CAREERTASU_MODE): ?>
<td class="">
<?php echo $aS['stSubject']; ?>
</td>
<td class="">
<?php echo ($aS['stYear'])? $aS['stYear']:''; ?>
</td>
<td class="">
<?php echo $aS['stClass']; ?>
</td>
<td class="">
<?php echo $aS['stCourse']; ?>
</td>
<?php endif; ?>
<td nowrap class="text-center">
<a href="/org/class/studentlist/<?php echo $sStID; ?>" class="button na default width-auto"><?php echo count($aStudy); ?></a>
</td>

<?php
if (!is_null($aClasses)):
	foreach ($aClasses as $sCtID => $aC):
?>
<td class="text-center"><?php echo (isset($aStudy[$sCtID]))? '○':''; ?></td>
<?php
	endforeach;
endif;
?>

</tr>
<?php
		endforeach;
	endif;
?>
				</tbody>
			</table>

<?php echo $oPagination; ?>

			</form>
		</div>
	</section>
