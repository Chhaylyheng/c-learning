<?php
	$errClass = array('te_csv'=>'');
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
	<?php echo Form::open(array('action'=>'/org/class/csvteach','method'=>'post','enctype'=>'multipart/form-data')) ; ?>
	<p><?php echo __('CSVファイルを指定して「登録する」ボタンをクリックしてください。'); ?></p>
	<p class="mt4">
		<a href="/org/sample/classtake.csv"><i class="fa fa-download"></i><?php echo __('サンプルファイルのダウンロード'); ?></a>
	</p>
	<p><input type="file" name="te_csv"<?php echo $errClass['te_csv']; ?>></p>
	<p class="mt4"><?php echo __('※:sizeMBまでのCSVファイルが指定できます。',array('size'=>CL_FILESIZE)); ?></p>
	<?php echo $errMsg['te_csv']; ?>
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
				<td class="text-center">○</td>
				<td class="text-center">1</td>
				<td class="text-center">20</td>
				<td class="font-silver font-size-80 width-50 line-height-14"><?php echo __('登録済みの講義コードを入力。'); ?></td>
			</tr>
			<tr>
				<td nowrap><?php echo __('メールアドレス'); ?></td>
				<td class="text-center"></td>
				<td class="text-center">○</td>
				<td class="text-center">1</td>
				<td class="text-center">200</td>
				<td class="font-silver font-size-80 width-50 line-height-14"><?php echo __('登録済みの先生メールアドレスを入力。'); ?></td>
			</tr>
			<tr>
				<td nowrap><?php echo __('主担当'); ?></td>
				<td class="text-center"></td>
				<td class="text-center"></td>
				<td class="text-center">0</td>
				<td class="text-center">1</td>
				<td class="font-silver font-size-80 width-50 line-height-14"><?php echo __('主担当とする場合は 1 を入力。'); ?></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
