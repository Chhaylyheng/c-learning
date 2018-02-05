<?php
$default = '1';
if ($aGroup['gtLDAP']):
	$default = '2';
endif;
?>

	<section class="pt0">
		<div class="info-box table-box record-table admin-table">
		<p class="error-box mb16" style="display: none;" id="ttErr"></p>
		<form action="/org/teacher/modify" method="post" id="CheckForm">
		<input type="hidden" name="mode" value="">
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
			<?php if ($aGroup['gtLDAP']): ?>
				<th>uid</th>
			<?php endif; ?>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('メールアドレス'); ?></th>
			<?php if (!$aGroup['gtLDAP']): ?>
				<th><?php echo __('パスワード'); ?></th>
			<?php endif; ?>

			<?php if (CL_CAREERTASU_MODE): ?>
				<th><?php echo __('プラン'); ?></th>
				<th><?php echo __('利用開始日'); ?></th>
				<th><?php echo __('利用終了日'); ?></th>
			<?php else: ?>
				<th><?php echo __('学部'); ?></th>
				<th><?php echo __('学科'); ?></th>
			<?php endif; ?>

				<th class="string-bottom"><?php echo __('実施中講義'); ?></th>
				<th class="string-bottom"><?php echo __('終了講義'); ?></th>
				<th><?php echo __('操作'); ?></th>
				<th class="string-bottom"><?php echo __('最終ログイン'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aTeachers)):
					$iMax = count($aTeachers);
					foreach ($aTeachers as $i => $aT):
						$sTtID = $aT['ttID'];
			?>
<tr>
<td class="">
	<input type="checkbox" name="TeachChk[]" class="Chk" value="<?php echo $sTtID; ?>">
</td>
<?php if ($aGroup['gtLDAP']): ?>
<td class="">
<?php echo $aT['ttLoginID']; ?>
</td>
<?php endif; ?>
<td class="">
<?php echo $aT['ttName']; ?>
</td>
<td class="">
<?php echo $aT['ttMail']; ?>
</td>
<?php if (!$aGroup['gtLDAP']): ?>
<td class="">
<span><?php echo ($aT['ttFirst'])? $aT['ttFirst']:__('（変更済）'); ?></span>
<a href="#" class="password_reset" title="<?php echo __('パスワードリセット'); ?>" data="<?php echo $sTtID; ?>"><i class="fa fa-refresh"></i></a>
</td>
<?php endif; ?>

<?php if (CL_CAREERTASU_MODE): ?>

<td class="">
<?php echo $aCTPlan[$aT['ttCTPlan']]; ?>
</td>
<td class="">
<?php echo ($aT['ttCTStart'] != '0000-00-00')? date('Y/m/d',strtotime($aT['ttCTStart'])):''; ?>
</td>
<td class="">
<?php echo ($aT['ttCTEnd'] != '0000-00-00')? date('Y/m/d',strtotime($aT['ttCTEnd'])):''; ?>
</td>

<?php else: ?>

<td class="">
<?php echo $aT['ttDept']; ?>
</td>
<td class="">
<?php echo $aT['ttSubject']; ?>
</td>

<?php endif; ?>

<td class="">
<a href="/org/class/index/<?php echo $sTtID; ?>" class="button na default width-auto"><?php echo (int)$aT['ttClassNum']; ?></a>
</td>
<td class="">
<a href="/org/class/index/<?php echo $sTtID; ?>" class="button na default width-auto"><?php echo (int)$aT['ttCloseNum']; ?></a>
</td>
<td class="">
	<div class="dropdown inline-block">
		<button type="button" class="teacher-dropdown-toggle" id="<?php echo $sTtID; ?>_edit"><div><?php echo __('管理'); ?></div></button>
	</div>
</td>
<td class="">
<?php echo ($aT['ttLoginNum'])? ClFunc_Tz::tz('Y/m/d<\b\r>H:i',$tz,$aT['ttLoginDate']).' ('.$aT['ttLoginNum'].')':__('未ログイン'); ?>
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
	<li><a href="#" class="TeacherEdit text-left"><span class="font-default"><?php echo __('編集'); ?></span></a></li>
	<li><a href="#" class="TeacherDelete text-left"><span class="font-default"><?php echo __('削除'); ?></span></a></li>
</ul>

<?php if (!is_null($aTeachers)): ?>
<script type="text/javascript">
$(window).load(function() {
	var sskey = 'cl_org_teacher_sort';
	var defaultSort = [[<?php echo $default; ?>,0]];

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
		theme : 'blue',
		cssHeader: 'headerSort',
		headers: {
			0 : {sorter: false},
		<?php if (!$aGroup['gtLDAP']): ?>
			3 : {sorter: false},
		<?php endif; ?>
		<?php if (CL_CAREERTASU_MODE): ?>
			9 : {sorter: false}
		<?php else: ?>
			8 : {sorter: false}
		<?php endif; ?>
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

