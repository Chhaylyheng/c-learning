<?php if (!is_null($aTeachers)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_adm_target_sort';
	var defaultSort = [[2,0],[3,0],[4,0]];

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
			2: {sorter: "text"},
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
		<form action="/adm/kreport/target/<?php echo $aReport['no']; ?>" method="post">
		<table class="kreport-data table-sort">
		<thead>
			<tr><th><input type="checkbox" class="AllChk" value=""></th><th>先生ID</th><th>契約</th><th>学校</th><th>氏名</th><th>メールアドレス</th><th>実施中講義</th><th>終了講義</th><th>最終ログイン</th></tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aTeachers)):
					$iMax = count($aTeachers);
					foreach ($aTeachers as $i => $aT):
						$sOdd = ($i % 2)? '':'odd';
						$sTtID = $aT['ttID'];
						$sCheck = (isset($aTarget[$sTtID]))? ' checked':'';
			?>
<tr class="<?php echo $sOdd; ?>">
<td class="">
<input type="checkbox" name="chkT[]" class="Chk" value="<?php echo $sTtID; ?>"<?php echo $sCheck; ?>>
</td>
<td class="">
<?php echo $aT['ttID']; ?>
</td>
<td class="">
<span style="display: none;"><?php echo ($aT['ptID'])? $aT['ptID']:98; ?></span>
<a href="/adm/teacher/contract/<?php echo $aT['ttID']; ?>">
<?php echo $aT['ptName']; ?>
<?php echo (is_null($aT['coTermDate']))? '契約期限切れ':(($aT['coTermDate'] != '0000-00-00')? '<br>～ '.date('Y/m/d',strtotime($aT['coTermDate'])):''); ?>
</a>
</td>
<td class="">
<?php echo $aT['cmName']; ?>
</td>
<td class="">
<?php echo ($aT['ttName'])? $aT['ttName']:$aT['ttMail']; ?>
</td>
<td class="">
<?php echo $aT['ttMail']; ?>
</td>
<td class="">
<?php echo $aT['ttClassNum']; ?>
</td>
<td class="">
<?php echo $aT['ttCloseNum']; ?>
</td>
<td class="">
<?php echo ($aT['ttLoginNum'])? nl2br(date("Y/m/d\nH:i",strtotime($aT['ttLoginDate']))).'（'.$aT['ttLoginNum'].'）':'未ログイン'; ?>
</td>
</tr>
					<?php
							endforeach;
						endif;
					?>
				</tbody>
			</table>
			<p class="button-box mt16"><button type="submit" class="button do" name="sub_state" value="1">回答対象者の変更</button></p>
		</form>
		</div>
	</section>
