<?php
	$errClass = array('st_csv'=>'');
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
	<?php echo Form::open(array('action'=>'/org/student/csv','method'=>'post','enctype'=>'multipart/form-data')) ; ?>
	<p><?php echo __('CSVファイルを指定して「登録する」ボタンをクリックしてください。'); ?></p>
	<p class="mt4">
		<a href="/org/sample/studentadd.csv"><i class="fa fa-download"></i><?php echo __('サンプルファイルのダウンロード'); ?></a>
	</p>
	<p><input type="file" name="st_csv"<?php echo $errClass['st_csv']; ?>></p>
	<p class="mt4"><?php echo __('※:sizeMBまでのCSVファイルが指定できます。',array('size'=>CL_FILESIZE)); ?></p>
	<?php echo $errMsg['st_csv']; ?>
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
			<td nowrap><?php echo __('ログインID'); ?></td>
			<td class="text-center">○</td>
			<td class="text-center">○</td>
			<td class="text-center">4</td>
			<td class="text-center">20</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
				<?php echo __('半角大小英数字と一部記号【.（ドット）_（アンダースコア）-（ハイフン）】が利用可能'); ?><br>
				<?php echo __('既に登録されている場合は、内容を上書きします。'); ?>
			</td>
		</tr>
		<tr>
			<td nowrap><?php echo __('パスワード'); ?></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center">8</td>
			<td class="text-center">32</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
			<?php if ($aGroup['gtLDAP']): ?>
				<span class="font-red"><?php echo __('LDAP連携によるログインでは利用しないため、空白のまま登録してください。'); ?></span>
			<?php else: ?>
				<?php echo __('半角大小英数字と一部記号【.（ドット）_（アンダースコア）-（ハイフン）/（スラッシュ）】で、文字種別を2種類以上組み合わせて入力。省略すると自動生成します。'); ?><br>
				<?php echo __('上書き時に省略すると、既存のパスワードをそのまま利用します。'); ?>
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td nowrap><?php echo __('氏名'); ?></td>
			<td class="text-center"></td>
			<td class="text-center">○</td>
			<td class="text-center">1</td>
			<td class="text-center">50</td>
			<td class="font-silver font-size-80 width-50 line-height-14"></td>
		</tr>
		<tr>
			<td nowrap><?php echo __('性別'); ?></td>
			<td class="text-center"></td>
			<td class="text-center">○</td>
			<td class="text-center">1</td>
			<td class="text-center">1</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
				<?php echo __('数値で 0:指定なし、1:男性、2:女性 で指定'); ?>
			</td>
		</tr>
		<tr>
			<td nowrap><?php echo __('学籍番号'); ?></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center">0</td>
			<td class="text-center">20</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
				<?php echo __('半角大小英数字と一部記号【.（ドット）_（アンダースコア）-（ハイフン）】が利用可能'); ?><br>
				<?php echo __('上書き時に空白の場合は値を削除します。'); ?>
			</td>
		</tr>
		<tr>
			<td nowrap><?php echo __('学部'); ?></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center">0</td>
			<td class="text-center">50</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
				<?php echo __('上書き時に空白の場合は値を削除します。'); ?>
			</td>
		</tr>
		<tr>
			<td nowrap><?php echo __('学科'); ?></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center">0</td>
			<td class="text-center">50</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
				<?php echo __('上書き時に空白の場合は値を削除します。'); ?>
			</td>
		</tr>
		<tr>
			<td nowrap><?php echo __('学年'); ?></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center">0</td>
			<td class="text-center">10</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
				<?php echo __('数値で入力します。'); ?><br>
				<?php echo __('上書き時に空白の場合は値を削除します。'); ?>
			</td>
		</tr>
		<tr>
			<td nowrap><?php echo __('クラス'); ?></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center">0</td>
			<td class="text-center">50</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
				<?php echo __('上書き時に空白の場合は値を削除します。'); ?>
			</td>
		</tr>
		<tr>
			<td nowrap><?php echo __('コース'); ?></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center">0</td>
			<td class="text-center">50</td>
			<td class="font-silver font-size-80 width-50 line-height-14">
				<?php echo __('上書き時に空白の場合は値を削除します。'); ?>
			</td>
		</tr>
		</tbody>
		</table>
	</div>
</div>
