<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_testput_sort';
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
			<?php $i = 0; ?>
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 学籍番号
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// 氏名
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 学年
			<?php echo $i; $i++; ?>: {sorter: 'text'},	// クラス
			<?php echo $i; $i++; ?>: {sorter: 'digit'},	// 提出数
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

<div class="mt0">
	<h2><a href="#" class="link-out accordion" style="padding: 6px 0 6px 30px; background-position: 8px center;"><?php echo __('表示条件設定'); ?></a></h2>
	<div class="accordion-content acc-content-open" style="display: none;">
	<div class="accordion-content-inner pt8">
<?php echo Form::open(array('action'=>'/t/alog/list/'.$aALTheme['altID'],'method'=>'get','role'=>'form','class'=>'form-inline')) ; ?>
<div class="form-group">
	<?php echo __('期間'); ?>
	<input type="text" name="sd" value="<?php echo date('Y/m/d',strtotime($aY[0])); ?>" id="datepick1" class="width-10em text-center inline-block"> ～
	<input type="text" name="ed" value="<?php echo date('Y/m/d',strtotime($aY[1])); ?>" id="datepick2" class="width-10em text-center inline-block">
</div>
<div class="form-group mt8 button-box text-left">
	<button type="submit" class="button na do width-auto" style="padding: 8px;" name="sub_state" value="1"><?php echo __('表示条件設定'); ?></button>
</div>
<?php echo Form::close(); ?>
	</div>
	</div>
</div>

<div class="info-box mt16" style="z-index: 1;">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th class="string-bottom"><?php echo __('学籍番号'); ?></th>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('学年'); ?></th>
				<th><?php echo __('クラス'); ?></th>
				<th><?php echo __('入力数'); ?></th>
				<?php
					if (!is_null($aDays)):
						foreach ($aDays as $sD => $sDay):
				?>
				<th><?php echo $sDay; ?></th>
				<?php
						endforeach;
					endif;
				?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$aLog = (isset($aS['alog']))? $aS['alog']:null;
		?>
			<tr>
				<td><?php echo $aS['stNO']; ?></td>
				<td><?php echo $aS['stName']; ?></td>
				<td><?php echo $aS['stYear']; ?></td>
				<td><?php echo $aS['stClass']; ?></td>
				<td><?php echo $aS['alogNum']; ?></td>
				<?php
					if (!is_null($aDays)):
						foreach ($aDays as $sD => $sDay):
							if (!isset($aLog[$sD])):
				?>
				<td></td>
				<?php
								continue;
							endif;
				?>
				<td class="line-height-13" style="vertical-align: top;">
				<?php
							foreach ($aLog[$sD] as $aL):
								$sTitle = '<span class="font-size-80">'.Clfunc_Tz::tz('H:i',$tz,$aL['alDate']).'</span> '.(($aL['alTitle'])? $aL['alTitle']:mb_strimwidth($aL['alText'], 0, 20, '…'));
								$sCom = ($aL['alCom'])? '<i class="fa fa-commenting mr0 ml4"></i>':'';
				?>
					<p class="mt4"><a href="/t/alog/detail/<?php echo $aALTheme['altID'].DS.$aL['no']; ?>"><?php echo $sTitle.$sCom; ?></a></p>
				<?php
							endforeach;
				?>
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

