<?php if (!is_null($aNews)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_news_sort';
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
			2: {sorter: false},
			5: {sorter: false},
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

<div class="info-box">
	<div class="info-box table-box record-table admin-table mt0">
		<p class="error-box mb16" style="display: none;" id="stErr"></p>
		<table class="table-sort">
		<thead>
			<tr>
				<th class="width-10"><?php echo __('状況'); ?></th>
				<th class="width-40"><?php echo __('ニュース内容'); ?></th>
				<th class="width-10"><?php echo __('掲載日時'); ?></th>
				<th class="width-10"><?php echo __('終了日時'); ?></th>
				<th class="width-10"><?php echo __('通知'); ?></th>
				<th class="width-10"><?php echo __('登録日時'); ?></th>
				<th class="width-10"><?php echo __('操作'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aNews)):
				foreach ($aNews as $aN):
					$sBHead = mb_strimwidth($aN['cnBody'],0,48,'…','UTF-8');
					$sNow = time();
					$aStatus = array('color' => 'font-silver', 'text' => __('掲載終了'), 'sort' => 2);
					if (strtotime($aN['cnEnd']) >= $sNow):
						if (strtotime($aN['cnStart']) <= $sNow):
							$aStatus['color'] = 'font-blue';
							$aStatus['text']  = '<a href="/t/news/finish/'.$aN['no'].'" class="NewsFinish button na default width-auto '.$aStatus['color'].'">'.__('掲載中').'</a>';
							$aStatus['sort'] = 0;
						else:
							$aStatus['color'] = 'font-green';
							$aStatus['text']  = __('掲載前');
							$aStatus['sort'] = 1;
						endif;
					endif;

					$sSend = ($aN['cnSend'])? '○':'×';
		?>
			<tr>
				<td class="width-10 <?php echo $aStatus['color']; ?>"><span style="display: none;"><?php echo $aStatus['sort']; ?></span><?php echo $aStatus['text']; ?></td>
				<td class="width-40">
					<p class="news-bhead link-style"><?php echo $sBHead; ?></p>
					<div class="news-body link-style" style="display: none;"><?php echo nl2br($aN['cnBody']); ?></div>
					<?php echo ($aN['cnChain'])? '<div class="font-blue mt4"><i class="fa fa-chain"></i> '.$aN['cnChain']['title'].'</div>':''; ?>
				</td>
				<td class="width-10"><?php echo ClFunc_Tz::tz('Y/m/d<\\b\\r>H:i',$tz,$aN['cnStart']); ?></td>
				<td class="width-10"><?php echo ClFunc_Tz::tz('Y/m/d<\\b\\r>H:i',$tz,$aN['cnEnd']); ?></td>

				<td class="width-10"><?php echo $sSend; ?></td>

				<td class="width-10"><?php echo ClFunc_Tz::tz('Y/m/d<\\b\\r>H:i',$tz,$aN['cnDate']); ?></td>

				<td class="width-10">
					<div class="dropdown inline-block">
						<button type="button" class="news-dropdown-toggle" id="<?php echo $aN['no']; ?>_edit"><div><?php echo __('管理'); ?></div></button>
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
</div>

<ul class="dropdown-list dropdown-list-edit" obj="">
	<li><a href="#" class="NewsEdit text-left"><span class="font-default"><?php echo __('ニュースの編集'); ?></span></a></li>
	<li><a href="#" class="NewsDelete text-left"><span class="font-default"><?php echo __('ニュースの削除'); ?></span></a></li>
</ul>
