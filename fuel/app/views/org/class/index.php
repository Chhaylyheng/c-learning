<?php if (!is_null($aClasses)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_org_class_sort';
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
			0: {sorter: false},
			10: {sorter: false},
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
		<form action="/org/class/modify" method="post" id="CheckForm">
		<input type="hidden" name="mode" value="">
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th><?php echo __('状況'); ?></th>
				<th><?php echo __('講義コード'); ?></th>
				<th><?php echo __('講義名'); ?></th>
				<th class="string-bottom"><?php echo __('年度'); ?></th>
				<th><?php echo __('期'); ?></th>
				<th><?php echo __('曜日'); ?></th>
				<th class="string-bottom"><?php echo __('時限'); ?></th>
				<th class="string-bottom"><?php echo __('履修人数'); ?></th>
				<th><?php echo __('先生').__('（共同講義設定）'); ?></th>
				<th><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aClasses)):
					$iMax = count($aClasses);
					foreach ($aClasses as $i => $aC):
						$sTtID = $aC['ttID'];
						$sCtID = $aC['ctID'];
						$sTpNum = ($aC['tpNum'])? ' ['.__('他:num名', array('num'=>$aC['tpNum'])).']':'';
			?>
<tr class="">
<td class="">
	<input type="checkbox" name="ClassChk[]" class="Chk" value="<?php echo $sCtID; ?>">
</td>
<td class="">
<?php
	$aPub = array(__('実施'),'font-red');
	if (!$aC['ctStatus']):
		$aPub = array(__('終了'),'font-silver');
	endif;
?>
<div class="dropdown">
	<button type="button" class="class-dropdown-toggle <?php echo $aPub[1]; ?>" id="<?php echo $sCtID; ?>_public"><div><?php echo $aPub[0]; ?></div></button>
</div>
</td>
<td class="">
<?php echo $aC['ctCode']; ?>
</td>
<td class="">
<?php echo $aC['ctName']; ?>
</td>
<td class="">
<?php echo __(':year年度', array('year'=>$aC['ctYear'])); ?>
</td>
<td class="">
<span sortdata="<?php echo $aC['dpNO']; ?>"><?php echo ($aC['dpNO'])? $aPeriod[$aC['dpNO']]:'─'; ?></span>
</td>
<td class="">
<span sortdata="<?php echo $aC['ctWeekDay']; ?>"><?php echo ($aC['ctWeekDay'])? $aWeekDay[$aC['ctWeekDay']]:'─'; ?></span>
</td>
<td class="">
<span sortdata="<?php echo $aC['dhNO']; ?>"><?php echo ($aC['dhNO'])? $aHour[$aC['dhNO']]:'─'; ?></span>
</td>
<td class="">
<a href="/org/student/classlist/<?php echo $sCtID; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo $aC['scNum']; ?></a>
</td>
<td class="">
<?php
	if (isset($aMasters[$aC['ctID']])):
		$sColor = 'default';
		$sText = $aMasters[$aC['ctID']]['ttName'].$sTpNum;
	else:
		$sColor = 'cancel';
		$sText = '<i class="fa fa-exclamation-circle mr4"></i>'.__('主担当未設定');
	endif;
?>
<a href="/org/teacher/classlist/<?php echo $sCtID; ?>" class="button na width-auto <?php echo $sColor; ?>" style="padding: 8px;"><?php echo $sText; ?></a>
</td>
<td class="">
	<div class="dropdown inline-block">
		<button type="button" class="class-dropdown-toggle" id="<?php echo $sCtID; ?>_edit"><div><?php echo __('管理'); ?></div></button>
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
	<li><a href="#" class="ClassEdit text-left"><span class="font-default"><?php echo __('編集'); ?></span></a></li>
	<li><a href="#" class="ClassDelete text-left"><span class="font-default"><?php echo __('削除'); ?></span></a></li>
</ul>

<ul class="dropdown-list dropdown-list-public" obj="">
	<li><a href="#" class="ClassPublic" obj="public"><span class="font-red"><?php echo __('実施'); ?></span></a></li>
	<li><a href="#" class="ClassPublic" obj="private"><span class="font-silver"><?php echo __('終了'); ?></span></a></li>
</ul>

