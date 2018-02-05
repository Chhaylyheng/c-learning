<?php if (!is_null($aActive)): ?>
<div class="info-box">
	<?php if ($aActive['acKey']): ?>
	<p class="font-size-260 font-blue line-height-12"><i class="fa fa-key fa-fw"></i><span class="font-size-300"><?php echo $aActive['acKey']; ?></span></p>
	<?php endif; ?>
	<p><?php echo __('現在、出席受付中です。'); ?>（<span class="font-red"><strong><?php echo __(':timeまで',array('time'=>ClFunc_Tz::tz('H:i',$tz,$aActive['acAEnd']))); ?></strong></span>）</p>
	<p><a href="/t/attend/detail/<?php echo $aActive['no']; ?>" class="button confirm na"><?php echo __('出席状況の確認'); ?>
	<?php if ($aActive['acGIS']): ?>
	<i class="fa fa-map-marker"></i>
	<?php endif; ?>
	</a>
	<a href="/t/attend/stop/<?php echo $aActive['no']; ?>" class="button cancel na"><?php echo __('出席受付を停止'); ?></a></p>
</div>
<?php elseif ($aClass['ctStatus']): ?>
<div class="mt16">
	<h2><a href="#" class="link-out accordion"><?php echo __('今すぐ出席を開始'); ?></a></h2>
	<div class="accordion-content acc-content-open" style="display: none;">
	<div class="accordion-content-inner pt8">
	<form action="/t/attend/start" method="post">
		<p class="adjust-style"><?php echo __('終了時刻'); ?></p>
		<p class="adjust-style">
		<select name="e_time" class="dropdown">
			<?php for($i = 10; $i <= 120; $i+=5): ?>
				<?php $sSel = ($i == 90)? ' selected':''; ?>
				<?php if ($sTime = Clfunc_Common::endTime($i)): ?>
				<option value="<?php echo $sTime; ?>"<?php echo $sSel; ?>><?php echo ClFunc_Tz::tz('H:i',$tz,date('Y-m-d ').$sTime.':00'); ?></option>
				<?php endif; ?>
			<?php endfor; ?>
		</select>
		</p>
		<p class="adjust-style ml16"><?php echo __('確認キー'); ?></p>
		<p class="adjust-style inline-box"><input type="text" name="keycode" value="" class="keyfield text-center width-6em" maxlength="4"></p>
		<p class="adjust-style inline-box"><button type="button" class="button na confirm keygen width-auto"><?php echo __('自動'); ?></button></p>
		<p class="adjust-style inline-box ml16"><button type="submit" class="formSubmit button na do width-auto"><?php echo __('出席を開始'); ?></button></p>
		<p class="inline-box"><label><input type="checkbox" name="geochk" class="geochk" value="1"><i class="fa fa-map-marker"></i> <?php echo __('出席時の位置情報を取得する'); ?></label></p>
		<div class="info-box mt0">
			<p class="font-green"><i class="fa fa-arrow-down"></i> <?php echo __('出席を取得する場所をクリックしてマーカーを設置してください。'); ?></p>
			<div id="map_canvas" style="width: 100%; height: 500px;" lat="<?php echo $aLatLon['lat']; ?>" lon="<?php echo $aLatLon['lon']; ?>">
				<p class="MapOption">
					<button type="button" class="CurrentPosition button na confirm"><i class="fa fa-map-marker"></i><span><?php echo __('現在地に移動'); ?></span></button>
					<input type="text" class="AddressPosition" id="MapSearchText" placeholder="<?php echo __('住所を入力'); ?>"><button type="button" class="AddressSubmit button na confirm width-auto"><?php echo __('移動'); ?></button>
				</p>
			</div>
			<input type="hidden" name="lat" value="<?php echo $aLatLon['lat']; ?>">
			<input type="hidden" name="lon" value="<?php echo $aLatLon['lon']; ?>">
		</div>
	</form>
	</div>
	</div>
</div>
<?php endif; ?>

<div class="info-box mt16">
<?php if ($aClass['ctStatus']): ?>
	<form action="/t/attend/add" method="post">
		<p class="adjust-style inline-box"><label><?php echo __('日付'); ?>
			<input type="text" name="date" value="" id="datepick2" class="width-10em text-center ml4" readonly>
		</label></p>
		<p class="adjust-style inline-box ml16"><button type="submit" class="formSubmit button na do width-auto"><?php echo __('出席列の追加'); ?></button></p>
	</form>
<?php endif; ?>

<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_attend_sort';
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
			<?php $i = 0;?>
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 学籍番号
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 氏名
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// クラス
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 出席回数
		},
		sortList: currentSort
	}).bind("sortEnd", function(sorter) {
		currentSort = sorter.target.config.sortList;
		currentSort = currentSort.join('|');
		setSessionStorage(sskey, currentSort);
	});
});
</script>
<?php endif; ?>
<?php if (!is_null($aAttendList) && !is_null($aStudent)): ?>
<?php endif; ?>

	<div class="info-box table-box record-table admin-table scroll-box mt0">
		<p class="error-box mb16" style="display: none;" id="stErr"></p>
		<table class="kreport-data table-sort" id="attend-mode" val="history">
		<thead>
			<tr>
				<td colspan="4" class="text-right"><?php echo __('履修人数'); ?>：<?php echo __('<span class="stNum">:num</span>名',array('num'=>$aClass['scNum'])); ?></td>
				<?php
					$sNums = null;
					$iANum = 0;
					if (!is_null($aAttendList)):
						$iANum = count($aAttendList);
						foreach ($aAttendList as $aA):
							$sDate = ($aA['acStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('\'y/n/j',$tz,$aA['acStart']):((($aA['acAStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('\'y/n/j',$tz,$aA['acAStart']):date('\'y/n/j',strtotime($aA['abDate']))));
							$sTime = ($aA['acStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$tz,$aA['acStart']):((($aA['acAStart'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('H:i',$tz,$aA['acAStart']):''));
							$sPer = ($aClass['scNum'] > 0)? round(($aA['abNum']/$aClass['scNum'])*100,1):0;
							$sNums .= '<th class="text-center {sorter: false}"><span id="'.$aA['abDate'].'_'.$aA['acNO'].'_Num">'.$aA['abNum'].'</span>'.__(':num名',array('num'=>'')).'<br><span>'.$sPer.'</span>%</th>';
							$sGIS = ($aA['acGIS'])? ' <i class="fa fa-map-marker"></i>':'';
							$sDel = ($aActive['no'] != $aA['no'])? ' <a href="/t/attend/delete/'.$aA['no'].'" class="deleteBtn" data="t-attenddelete"><i class="fa fa-trash-o"></i></a>':'';
				?>
				<td class="text-center" nowrap="nowrap"><a href="/t/attend/detail/<?php echo $aA['no']; ?>"><?php echo $sDate.'<br>'.$sTime.$sGIS; ?></a><?php echo $sDel; ?></td>
				<?php
						endforeach;
					endif;
				?>
			</tr>
			<tr>
				<th nowrap="nowrap" class="string-bottom"><?php echo __('学籍番号'); ?></th>
				<th nowrap="nowrap"><?php echo __('氏名'); ?></th>
				<th nowrap="nowrap"><?php echo __('クラス'); ?></th>
				<th nowrap="nowrap" class="{sorter:'metadata'}"><?php echo __('出席数'); ?><br>
				<?php echo __('全<span class="aNum">:num</span>回',array('num'=>$iANum)); ?></th>
				<?php echo $sNums; ?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $aS):
		?>
			<tr>
				<td nowrap="nowrap"><?php echo $aS['stNO']; ?></td>
				<td class="width-10em"><?php echo $aS['stName']; ?></td>
				<td class="width-10em"><?php echo $aS['stClass']; ?></td>
				<?php $sPer = ($iANum > 0)? round(($aS['abNum']/$iANum)*100,1):'0'; ?>
				<td id="<?php echo $aS['stID']; ?>_Num" nowrap="nowrap" class="{sortValue: '<?php echo $aS['abNum']; ?>'}"><?php echo __('<span>:num</span>回',array('num'=>$aS['abNum'])); ?><br><span><?php echo $sPer; ?></span>%</td>
				<?php
					if (!is_null($aAttendList)):
						foreach ($aAttendList as $aA):
							if (!isset($aS['attend'][$aA['abDate']][$aA['acNO']]))
							{
								$aSA = array(
									'amAbsence' => 1,
									'amTime' => 0,
									'amShort' => $aAttendMaster[0]['amShort'],
								);
							}
							else
							{
								$aSA = $aS['attend'][$aA['abDate']][$aA['acNO']];
							}
							$sStyle = ($aSA['amAbsence'])? 'font-red':(($aSA['amTime'])? 'font-green':'font-blue');
				?>
					<td class="text-center" nowrap="nowrap">
						<div class="dropdown">
						<button type="button" class="attendstate-dropdown-toggle <?php echo $sStyle; ?>" id="<?php echo $aS['stID'].'_'.$aA['abDate'].'_'.$aA['acNO']; ?>"><div><?php echo $aSA['amShort']; ?></div></button>
						</div>
					</td>
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
	</div>
</div>

<ul class="dropdown-list dropdown-list-attendstate" obj="">
	<?php
		if (!is_null($aAttendMaster)):
			foreach ($aAttendMaster as $aM):
				$sObj = $aClass['ctID'].'_'.$aM['amAttendState'];
				$sStyle = ($aM['amAbsence'])? 'font-red':(($aM['amTime'])? 'font-green':'font-blue');
	?>
	<li><a href="#" obj="<?php echo $sObj; ?>" class="SwitchAttendState"><span class="<?php echo $sStyle; ?>"><?php echo $aM['amName'].'（'.$aM['amShort'].'）'; ?></span></a></li>
	<?php
			endforeach;
		endif;
	?>
</ul>

<script type="text/javascript" src="<?php echo CL_MAP_URL; ?>"></script>