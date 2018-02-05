<?php
	$errClass = array('at_csv'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;
?>

<div class="info-box">
	<h2><?php echo __('CSVファイルの選択'); ?></h2>
	<hr>
	<?php echo Form::open(array('action'=>'/t/attend/csv','method'=>'post','enctype'=>'multipart/form-data')) ; ?>
	<p>
		<?php echo __('出席予約登録のCSVファイルを指定して「登録する」ボタンをクリックしてください。'); ?><br>
		<a href="/t/sample/attendadd.csv"><i class="fa fa-download"></i><?php echo __('サンプルファイルのダウンロード'); ?></a>
	</p>
	<p><input type="file" name="at_csv"<?php echo $errClass['at_csv']; ?>></p>
	<p class="mt4"><?php echo __('※:sizeMBまでのCSVファイルが指定できます。',array('size'=>CL_FILESIZE)); ?></p>
	<?php echo $errMsg['at_csv']; ?>
	<?php
		if (isset($error['valid'])):
			foreach ($error['valid'] as $sE):
	?>
	<p class="error-msg mt4"><?php echo $sE; ?></p>
	<?php
			endforeach;
		endif;
	?>
	<p class="button-box mt16"><button type="submit" name="mode" value="photo" class="button do na"><?php echo __('登録する'); ?></button></p>
	<?php echo Form::close(); ?>
</div>

<div class="info-box">
	<h2><?php echo __('CSVの記入説明'); ?></h2>
	<hr>
	<div class="info-box table-box record-table admin-table mt0">
		<table>
		<thead>
			<tr>
				<th nowrap><?php echo __('列名'); ?></th>
				<th nowrap><?php echo __('ユニーク'); ?></th>
				<th nowrap><?php echo __('必須'); ?></th>
				<th nowrap><?php echo __('最小'); ?></th>
				<th nowrap><?php echo __('最大'); ?></th>
				<th><?php echo __('特記事項'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td nowrap><?php echo __('予約日'); ?></td>
				<td class="text-center"></td>
				<td class="text-center">○</td>
				<td class="text-center">8</td>
				<td class="text-center">10</td>
				<td class="font-silver font-size-80 width-50 line-height-14"><?php echo __('YYYY/MM/DD形式で入力（例：:year/10/06、:year/5/9、:year/03/4）',array('year'=>date('Y'))).'<br>'.__('過去の日付は指定できません。'); ?></td>
			</tr>
			<tr>
				<td nowrap><?php echo __('予約開始時刻'); ?></td>
				<td class="text-center"></td>
				<td class="text-center">○</td>
				<td class="text-center">3</td>
				<td class="text-center">5</td>
				<td class="font-silver font-size-80 width-50 line-height-14"><?php echo __('6:00～22:55の範囲内でMM:DD形式で入力（例：10:25、9:3、08:9）').'<br>'.__('システム上 5分単位で切り捨てるので、5分単位の入力をお勧めします（例：10:29→10:25、8:3→8:0）'); ?></td>
			</tr>
			<tr>
				<td nowrap><?php echo __('予約終了時刻'); ?></td>
				<td class="text-center"></td>
				<td class="text-center">○</td>
				<td class="text-center">3</td>
				<td class="text-center">5</td>
				<td class="font-silver font-size-80 width-50 line-height-14"><?php echo __('6:00～22:55の範囲内でMM:DD形式で入力（例：10:25、9:3、08:9）').'<br>'.__('システム上 5分単位で切り捨てるので、5分単位の入力をお勧めします（例：10:29→10:25、8:3→8:0）').'<br>'.__('予約開始時刻よりも後の時刻を指定します。'); ?></td>
			</tr>
			<tr>
				<td nowrap><?php echo __('確認キー'); ?></td>
				<td class="text-center"></td>
				<td class="text-center"></td>
				<td class="text-center">4</td>
				<td class="text-center">4</td>
				<td class="font-silver font-size-80 width-50 line-height-14"><?php echo __('半角大小英数字4文字で指定').'<br>'.__('省略すると確認キーなしで出席予約します'); ?></td>
			</tr>
			<tr>
				<td nowrap><?php echo __('位置情報取得'); ?></td>
				<td class="text-center"></td>
				<td class="text-center">○</td>
				<td class="text-center">1</td>
				<td class="text-center">1</td>
				<td class="font-silver font-size-80 width-50 line-height-14"><?php echo __('取得する場合は「1」取得しない場合は「0」を入力').'<br>'.__('基準座標は前回予約時指定のものを利用します（初めての場合は登録学校の住所から自動算出）'); ?></td>
			</tr>
		</tbody>
		</table>
	</div>
</div>
