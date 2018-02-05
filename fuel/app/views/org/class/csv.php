<?php
	$errClass = array('ct_csv'=>'');
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
	<?php echo Form::open(array('action'=>'/org/class/csv','method'=>'post','enctype'=>'multipart/form-data')) ; ?>
	<p><?php echo __('CSVファイルを指定して「登録する」ボタンをクリックしてください。'); ?></p>
	<p class="mt4">
		<a href="/org/sample/classadd.csv"><i class="fa fa-download"></i><?php echo __('サンプルファイルのダウンロード'); ?></a>
	</p>
	<p><input type="file" name="ct_csv"<?php echo $errClass['ct_csv']; ?>></p>
	<p class="mt4"><?php echo __('※:sizeMBまでのCSVファイルが指定できます。',array('size'=>CL_FILESIZE)); ?></p>
	<?php echo $errMsg['ct_csv']; ?>
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
				<td nowrap><?php echo __('講義コード'); ?></td>
				<td class="text-center"></td>
				<td class="text-center"></td>
				<td class="text-center">0</td>
				<td class="text-center">20</td>
				<td class="font-silver font-size-80 width-50 line-height-14">
					<?php echo __('半角大小英数字と一部記号【_（アンダースコア）-（ハイフン）】で入力。'); ?><br>
					<?php echo __('空の場合は、講義コードを自動生成します。'); ?><br>
					<?php echo __('既に登録されている場合は、内容を上書きします。'); ?><br>
				</td>
			</tr>
			<tr>
				<td nowrap><?php echo __('講義名'); ?></td>
				<td class="text-center"></td>
				<td class="text-center">○</td>
				<td class="text-center">1</td>
				<td class="text-center">50</td>
				<td class="font-silver font-size-80 width-50 line-height-14"></td>
			</tr>
			<tr>
				<td nowrap><?php echo __('年度'); ?></td>
				<td class="text-center"></td>
				<td class="text-center">○</td>
				<td class="text-center">4</td>
				<td class="text-center">4</td>
				<td class="font-silver font-size-80 width-50 line-height-14">
					<?php echo __('西暦で入力。'); ?>
				</td>
			</tr>
			<tr>
				<td nowrap><?php echo __('期'); ?></td>
				<td class="text-center"></td>
				<td class="text-center"></td>
				<td class="text-center">0</td>
				<td class="text-center">50</td>
				<td class="font-silver font-size-80 width-50 line-height-14">
				<?php foreach ($aPeriod as $sP): ?>
					<?php echo $sP; ?>
				<?php endforeach; ?>
				<br>
				<?php echo __('上記のいずれかを入力。省略または上記以外を入力した場合は 指定なし となります。'); ?>
				</td>
			</tr>
			<tr>
				<td nowrap><?php echo __('曜日'); ?></td>
				<td class="text-center"></td>
				<td class="text-center"></td>
				<td class="text-center">0</td>
				<td class="text-center">50</td>
				<td class="font-silver font-size-80 width-50 line-height-14">
				<?php foreach ($aWeekDay as $sW): ?>
					<?php echo $sW; ?>
				<?php endforeach; ?>
				<br>
				<?php echo __('上記のいずれかを入力。省略または上記以外を入力した場合は 指定なし となります。'); ?>
				</td>
			</tr>
			<tr>
				<td nowrap><?php echo __('時限'); ?></td>
				<td class="text-center"></td>
				<td class="text-center"></td>
				<td class="text-center">0</td>
				<td class="text-center">1</td>
				<td class="font-silver font-size-80 width-50 line-height-14">
					<?php echo '0 ～ '.(count($aHour) - 1); ?><br>
					<?php echo __('一桁の数値で入力。省略または誤った文字を入力した場合は 指定なし となります。'); ?>
				</td>
			</tr>
			<tr>
				<td nowrap><?php echo __('実施状況'); ?></td>
				<td class="text-center"></td>
				<td class="text-center">○</td>
				<td class="text-center">1</td>
				<td class="text-center">1</td>
				<td class="font-silver font-size-80 width-50 line-height-14">
					<?php echo __('0（終了）、1（実施中）として、数値で入力。省略または誤った文字を入力した場合は 終了 となります。'); ?>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
