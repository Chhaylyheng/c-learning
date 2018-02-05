<div class="info-box">
	<div class="info-box table-box record-table admin-table mt0">
		<p class="error-box mb16" style="display: none;" id="stErr"></p>
		<form action="/t/mail/send" method="post" id="StudentCheckForm">
		<input type="hidden" name="mode" value="select">
		<input type="hidden" name="func" value="student">
		<table data="<?php echo $aTeacher['ttID'].'|'.$aClass['ctID']; ?>" class="table-sort kreport-data">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th><?php echo __('相談'); ?></th>
				<th><?php echo __('ログインID'); ?></th>
				<?php if (!CL_CAREERTASU_MODE): ?>
				<th class="string-bottom"><?php echo __('学籍番号'); ?></th>
				<?php endif; ?>
				<th><?php echo __('氏名'); ?></th>
				<?php if (CL_CAREERTASU_MODE): ?>
				<th><?php echo __('学校'); ?></th>
				<?php endif; ?>
				<?php if (!CL_CAREERTASU_MODE): ?>
				<th><?php echo __('学年'); ?></th>
				<th><?php echo __('クラス'); ?></th>
				<?php endif; ?>
				<th><?php echo __('メール'); ?></th>
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

					if ($aS['stMail']):
						$aMail['Main']['icon'] = 'envelope';
						$aMail['Main']['color'] = ' font-red';
						$aMail['Main']['title'] = __('未認証');
						if ($aS['stMailAuth']):
							$aMail['Main']['color'] = ' font-green';
							$aMail['Main']['title'] = __('認証済み');
						endif;
					endif;
					if ($aS['stSubMail']):
						$aMail['Sub']['icon'] = 'envelope';
						$aMail['Sub']['color'] = ' font-green';
						$aMail['Sub']['title'] = __('登録済み');
					endif;
					$aCon = array('num'=>$aContact[$aS['stID']]['num'], 'color'=>'default', 'unread'=>'');
					if ($aContact[$aS['stID']]['num'] > 0):
						$aCon['color'] = 'default';
						if ($aContact[$aS['stID']]['unread'] > 0):
							$aCon['unread'] = '<span class="unread-badge">'.$aContact[$aS['stID']]['unread'].'</span>';
						endif;
					endif;
		?>
			<tr>
				<td nowrap="nowrap">
					<input type="checkbox" name="StuChk[]" class="Chk" value="<?php echo $aS['stID']?>">
				</td>
				<td>
					<a href="/t/contact/thread/<?php echo $aS['stID']; ?>" class="button na width-auto contact-btn <?php echo $aCon['color']; ?>" title="<?php echo __('未読').' '.$aContact[$aS['stID']]['unread']; ?>"><?php echo $aCon['num'].$aCon['unread']; ?></a>
				</td>
				<td nowrap="nowrap"><?php echo $aS['stLogin']; ?></td>
				<?php if (!CL_CAREERTASU_MODE): ?>
				<td><?php echo $aS['stNO']; ?></td>
				<?php endif; ?>
				<td><?php echo $aS['stName']; ?></td>
				<?php if (CL_CAREERTASU_MODE): ?>
				<td><?php echo $aS['stSchool']; ?></td>
				<?php endif; ?>
				<?php if (!CL_CAREERTASU_MODE): ?>
				<td><?php echo ($aS['stYear'])? $aS['stYear']:''; ?></td>
				<td><?php echo $aS['stClass']; ?></td>
				<?php endif; ?>
				<td>
					<i class="fa fa-<?php echo $aMail['Main']['icon'].$aMail['Main']['color']; ?>" title="<?php echo $aMail['Main']['title']; ?>"></i>
					<i class="fa fa-<?php echo $aMail['Sub']['icon'].$aMail['Sub']['color']; ?>" title="<?php echo $aMail['Sub']['title']; ?>"></i>
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
	var sskey = 'cl_t_contact_sort';
	var defaultSort = [[1,1]];

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
			<?php echo $i; $i++; ?>: {sorter: 'digit'}, // 相談
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// ログインID
		<?php if (!CL_CAREERTASU_MODE): ?>
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 学籍番号
		<?php endif; ?>
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 氏名
		<?php if (CL_CAREERTASU_MODE): ?>
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 学校
		<?php endif; ?>
		<?php if (!CL_CAREERTASU_MODE): ?>
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 学年
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// クラス
		<?php endif; ?>
			<?php echo $i; $i++; ?>: {sorter: false},		// メール
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
