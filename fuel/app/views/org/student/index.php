<?php if (!is_null($aStudents)): ?>
<script type="text/javascript">
$(window).load(function() {
	var sskey = 'cl_org_student_sort';
	var defaultSort = [[<?php echo (CL_CAREERTASU_MODE)? 5:3; ?>,0],[1,0]];

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
			<?php echo $i; $i++; ?>: {sorter: false},		// チェック
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// ログインID
		<?php if (!$aGroup['gtLDAP']): ?>
			<?php echo $i; $i++; ?>: {sorter: false},		// パスワード
		<?php endif; ?>
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
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 履修講義数
			<?php echo $i; $i++; ?>: {sorter: false},		// メール
			<?php echo $i; $i++; ?>: {sorter: false}		// 操作
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
		<form action="/org/student/modify" method="post" id="CheckForm">
		<input type="hidden" name="mode" value="">
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th><?php echo __('ログインID'); ?></th>
<?php if (!$aGroup['gtLDAP']): ?>
				<th><?php echo __('パスワード'); ?></th>
<?php endif; ?>
<?php if (!CL_CAREERTASU_MODE): ?>
				<th class=""><?php echo __('学籍番号'); ?></th>
<?php endif; ?>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('性別'); ?></th>
<?php if (CL_CAREERTASU_MODE): ?>
				<th><?php echo __('学校'); ?></th>
<?php endif; ?>
				<th><?php echo __('学部'); ?></th>
<?php if (!CL_CAREERTASU_MODE): ?>
				<th><?php echo __('学科'); ?></th>
				<th><?php echo __('学年'); ?></th>
				<th><?php echo __('クラス'); ?></th>
				<th><?php echo __('コース'); ?></th>
<?php endif; ?>
				<th class="string-bottom"><?php echo __('履修講義数'); ?></th>
				<th><?php echo __('メール'); ?></th>
				<th><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aStudents)):
					$iMax = count($aStudents);
					foreach ($aStudents as $i => $aS):
						$sStID = $aS['stID'];

						$aMail = array(
							'Main' => array(
								'icon' => 'minus',
								'color' => ' font-silver',
								'title' => __('未登録'),
							),
							'Sub' => array(
								'icon' => 'minus',
								'color' => ' font-silver',
								'title' => __('未登録'),
							),
						);

						$bMail = false;
						if ($aS['stMail']):
							$aMail['Main']['icon'] = 'envelope';
							$aMail['Main']['color'] = ' font-red';
							$aMail['Main']['title'] = __('未認証');
							if ($aS['stMailAuth']):
								$aMail['Main']['color'] = ' font-green';
								$aMail['Main']['title'] = __('認証済み');
							endif;
							$bMail = true;
						endif;
						if ($aS['stSubMail']):
							$aMail['Sub']['icon'] = 'envelope';
							$aMail['Sub']['color'] = ' font-green';
							$aMail['Sub']['title'] = __('登録済み');
							$bMail = true;
						endif;
			?>
<tr class="">
<td class="">
	<input type="checkbox" name="StuChk[]" class="Chk" value="<?php echo $sStID; ?>">
</td>
<td class="">
<?php echo $aS['stLogin']; ?>
</td>
<?php if (!$aGroup['gtLDAP']): ?>
<td nowrap>
	<span><?php echo ($aS['stFirst'])? $aS['stFirst']:__('（変更済）'); ?></span>
	<a href="#" class="password_reset" title="<?php echo __('パスワードリセット'); ?>" data="<?php echo $sStID; ?>"><i class="fa fa-refresh"></i></a>
</td>
<?php endif; ?>
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
<td class="">
<a href="/org/class/studentlist/<?php echo $sStID; ?>" class="button na default width-auto"><?php echo $aS['stGtClassNum']; ?></a>
</td>
<td class="">
	<i class="fa fa-<?php echo $aMail['Main']['icon'].$aMail['Main']['color']; ?>" title="<?php echo $aMail['Main']['title']; ?>"></i>
	<i class="fa fa-<?php echo $aMail['Sub']['icon'].$aMail['Sub']['color']; ?>" title="<?php echo $aMail['Sub']['title']; ?>"></i>
</td>
<td class="">
	<div class="dropdown inline-block">
		<button type="button" class="student-dropdown-toggle" id="<?php echo $sStID; ?>_edit"><div><?php echo __('管理'); ?></div></button>
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
	<li><a href="#" class="StudentEdit text-left"><span class="font-default"><?php echo __('編集'); ?></span></a></li>
	<li><a href="#" class="StudentDelete text-left"><span class="font-default"><?php echo __('削除'); ?></span></a></li>
</ul>
