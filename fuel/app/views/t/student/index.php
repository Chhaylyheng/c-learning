<div class="info-box">
	<div class="info-box table-box record-table admin-table mt0">
		<p class="error-box mb16" style="display: none;" id="stErr"></p>
		<form action="/t/mail/send" method="post" id="StudentCheckForm">
		<input type="hidden" name="mode" value="select">
		<input type="hidden" name="func" value="student">
		<table data="<?php echo $aTeacher['ttID'].'|'.$aClass['ctID']; ?>" class="table-sort">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th><?php echo __('ログインID'); ?></th>
				<?php if (!$aGroup['gtLDAP']): ?>
				<th><?php echo __('パスワード'); ?></th>
				<?php endif; ?>
				<?php if (!CL_CAREERTASU_MODE): ?>
				<th class="string-bottom"><?php echo __('学籍番号'); ?></th>
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
				<th><?php echo __('最終ログイン'); ?></th>
				<th><?php echo __('メール'); ?></th>
				<th><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $aS):
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
			<tr>
				<td nowrap="nowrap">
				<?php if ($bMail): ?>
					<input type="checkbox" name="StuChk[]" class="Chk" value="<?php echo $aS['stID']?>">
				<?php endif; ?>
				</td>
				<td nowrap="nowrap"><?php echo $aS['stLogin']; ?></td>
				<?php if (!$aGroup['gtLDAP']): ?>
				<td nowrap="nowrap">
					<span><?php echo ($aS['stFirst'])? $aS['stFirst']:__('（変更済）'); ?></span>
<?php if (!CL_CAREERTASU_MODE): ?>
					<a href="#" class="password_reset" title="<?php echo __('パスワードリセット'); ?>" data="<?php echo $aS['stID']; ?>"><i class="fa fa-refresh"></i></a>
<?php endif; ?>
				</td>
				<?php endif; ?>
				<?php if (!CL_CAREERTASU_MODE): ?>
				<td><?php echo $aS['stNO']; ?></td>
				<?php endif; ?>
				<td><?php echo $aS['stName']; ?></td>
				<td><?php echo $aSex[$aS['stSex']]; ?></td>
				<?php if (CL_CAREERTASU_MODE): ?>
				<td><?php echo $aS['stSchool']; ?></td>
				<?php endif; ?>
				<td><?php echo $aS['stDept']; ?></td>
				<?php if (!CL_CAREERTASU_MODE): ?>
				<td><?php echo $aS['stSubject']; ?></td>
				<td><?php echo ($aS['stYear'])? $aS['stYear']:''; ?></td>
				<td><?php echo $aS['stClass']; ?></td>
				<td><?php echo $aS['stCourse']; ?></td>
				<?php endif; ?>
				<td><?php echo ($aS['stLoginDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('\'y/m/d<\\b\\r>H:i',$tz,$aS['stLoginDate']).' ('.$aS['stLoginNum'].')':''; ?></td>
				<td>
					<i class="fa fa-<?php echo $aMail['Main']['icon'].$aMail['Main']['color']; ?>" title="<?php echo $aMail['Main']['title']; ?>"></i>
					<i class="fa fa-<?php echo $aMail['Sub']['icon'].$aMail['Sub']['color']; ?>" title="<?php echo $aMail['Sub']['title']; ?>"></i>
				</td>
				<td>
<?php if (is_null($aGroup) || !($aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDENT)): ?>
					<a href="/t/student/edit/<?php echo $aS['stID']; ?>" title="<?php echo __('編集'); ?>"><i class="fa fa-edit"></i></a>
<?php endif; ?>
					<a href="/print/t/StuIdList/<?php echo $aClass['ctID'].DS.$aS['stID']; ?>" title="<?php echo __('配布資料印刷'); ?>" target="_blank"><i class="fa fa-print"></i></a>
<?php if (is_null($aGroup) || !($aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDY)): ?>
					<a class="deleteBtn" data="t-stu" href="/t/student/remove/<?php echo $aS['stID']; ?>" title="<?php echo __('履修解除'); ?>"><i class="fa fa-trash"></i></a>
<?php endif; ?>
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
</div>

<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(window).load(function() {
	var sskey = 'cl_t_student_sort';
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
			<?php echo $i; $i++; ?>: {sorter: 'text'},		// 最終ログイン
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
