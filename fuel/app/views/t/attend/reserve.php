<?php
	$sAction = 'reserve';
	$sButton = __('追加する');
	if ($no):
		$sAction = 'edit/'.$no;
		$sButton = __('更新する');
	endif;
?>



<div class="info-box">
	<form action="/t/attend/<?php echo $sAction; ?>" method="post">
	<?php if (isset($error['reserve'])): ?>
		<p class="error-box"><?php echo $error['reserve'] ?></p>
	<?php endif; ?>

	<?php
		$errClass = array('date'=>'','s_time'=>'','e_time'=>'','keycode'=>'');
		$errMsg = $errClass;

		foreach ($errClass as $c => $v):
			if (isset($error[$c])):
				$errClass[$c] = ' class="input-error"';
				$errMsg[$c] = '<p class="error-msg">'.$error[$c].'</p>';
			endif;
		endforeach;
	?>

		<p class="inline-box"><label><?php echo __('予約日'); ?>
		<?php if ($no): ?>
			<span class="font-blue font-size-140 ml16"><?php echo $date; ?></span>
		<?php else: ?>
			<input type="text" name="date" value="<?php echo $date; ?>" id="datepick" class="width-10em text-center ml16" readonly<?php echo $errClass['date']; ?>>
		<?php endif; ?>
		</label></p>
		<?php echo $errMsg['date']; ?>

		<p class="inline-box">
		<label><?php echo __('予約時刻'); ?>
			<input type="text" name="s_time" value="<?php echo $s_time; ?>" class="timepick width-8em text-center ml16" maxlength="5"<?php echo $errClass['s_time']; ?>>
		</label>
		～
		<label>
			<input type="text" name="e_time" value="<?php echo $e_time; ?>" class="timepick width-8em text-center" maxlength="5"<?php echo $errClass['e_time']; ?>>
		</label>
		</p>
		<?php echo $errMsg['s_time']; ?>
		<?php echo $errMsg['e_time']; ?>

		<p class="inline-box"><label><?php echo __('確認キー'); ?>
			<input type="text" name="keycode" value="<?php echo $keycode; ?>" class="keyfield width-8em text-center ml16" maxlength="4"<?php echo $errClass['keycode']; ?>>
		</label>
		<label>
			<button type="button" class="button na confirm keygen width-auto"><?php echo __('自動'); ?></button>
		</label>
		</p>
		<?php echo $errMsg['keycode']; ?>

		<p class="inline-box"><label><?php echo __('位置取得'); ?>
			<?php
				$sCheck = '';
				if ($geochk):
					$sCheck = ' checked';
				endif;
			?>
			<input type="checkbox" name="geochk" value="1" class="geochk ml16"<?php echo $sCheck; ?>><i class="fa fa-map-marker"></i> <?php echo __('出席時の位置情報を取得する'); ?>
		</label>
		</p>

		<div class="info-box mt0">
			<p class="font-green"><i class="fa fa-arrow-down"></i> <?php echo __('出席を取得する場所をクリックしてマーカーを設置してください。'); ?></p>
			<div id="map_canvas" style="width: 100%; height: 500px;" lat="<?php echo $latlon['lat']; ?>" lon="<?php echo $latlon['lon']; ?>">
				<p class="MapOption">
					<button type="button" class="CurrentPosition button na confirm"><i class="fa fa-map-marker"></i><span><?php echo __('現在地に移動'); ?></span></button>
					<input type="text" class="AddressPosition" id="MapSearchText" placeholder="<?php echo __('住所を入力'); ?>"><button type="button" class="AddressSubmit button na confirm width-auto"><?php echo __('移動'); ?></button>
				</p>
			</div>
		</div>

		<p class="button-box">
			<button type="submit" class="formSubmit button do"><?php echo $sButton; ?></button>
		</p>

		<input type="hidden" name="lat" value="<?php echo $latlon['lat']; ?>">
		<input type="hidden" name="lon" value="<?php echo $latlon['lon']; ?>">
	</form>
</div>

<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
		<tr>
			<th><?php echo __('日付'); ?></th>
			<th><?php echo __('受付期間'); ?></th>
			<th><?php echo __('確認キー'); ?></th>
			<th><?php echo __('位置取得'); ?></th>
			<th><?php echo __('操作'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
			$iNum = 0;
			if (!is_null($aAttendList)):
				$iNum = count($aAttendList);
				for ($i = 0; $i < $iNum; $i++):
					$aA = $aAttendList[$i];
					$sDate = ClFunc_Tz::tz('Y/m/d',$tz,$aA['acAStart']);
					$sSTime = ClFunc_Tz::tz('H:i',$tz,$aA['acAStart']);
					$sETime = ClFunc_Tz::tz('H:i',$tz,$aA['acAEnd']);
		?>
		<tr>
		<td><?php echo $sDate; ?></td>
		<td><?php echo $sSTime.' - '.$sETime; ?></td>
		<td><?php echo $aA['acKey']; ?></td>
		<td><?php if ($aA['acGIS']): ?><i class="fa fa-map-marker fa-lg"></i><?php endif; ?></td>
		<td>
			<?php if ((int)$aClass['ctStatus'] > 0): ?>
			<a href="/t/attend/edit/<?php echo $aA['no']; ?>" title="<?php echo __('編集'); ?>"><i class="fa fa-pencil-square-o fa-lg"></i></a>
			<a href="/t/attend/delete/<?php echo $aA['no']; ?>" title="<?php echo __('削除'); ?>" class="deleteBtn" data="t-attendreserve"><i class="fa fa-trash fa-lg"></i></a>
			<?php endif; ?>
		</td>
		</tr>
		<?php
				endfor;
			endif;
		?>
		</tbody>
		</table>
	</div>
</div>

<script type="text/javascript" src="<?php echo CL_MAP_URL; ?>"></script>
