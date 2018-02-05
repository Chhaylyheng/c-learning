<?php
	$errClass = array('tt_csv'=>'');
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
	<?php echo Form::open(array('action'=>'/t/drill/csv/'.$aDrill['dcID'].DS.$aDrill['dbNO'],'method'=>'post','enctype'=>'multipart/form-data')) ; ?>
	<p><?php echo __('ドリル問題のCSVファイルを指定して「登録する」ボタンをクリックしてください。'); ?></p>
	<p class="mt4">
		<a href="/t/sample/DrillQueryCreate/drill_default_format.csv"><i class="fa fa-download"></i><?php echo __('フォーマットファイル'); ?></a>
	</p>
	<p><input type="file" name="tt_csv"<?php echo $errClass['tt_csv']; ?>></p>
	<p class="mt4"><?php echo __('※:sizeMBまでのCSVファイルが指定できます。',array('size'=>CL_FILESIZE)); ?></p>
	<?php echo $errMsg['tt_csv']; ?>
	<?php
		if (isset($error['valid'])):
			foreach ($error['valid'] as $sE):
	?>
	<p class="error-msg mt4 mb0"><?php echo $sE; ?></p>
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
		<p class="mt4 mb0"><?php echo __('※特殊な形式なためフォーマットファイルを利用して作成してください。'); ?></p>
		<p class="mt4 mb0"><?php echo __('※「正解」は、改行が削除され、全角英数字が半角に修正されます。'); ?></p>
		<p class="mt4 mb0"><?php echo __('※問題グループに指定した名前がない場合は、新規にグループが作成されます。'); ?></p>
		<p class="mt4 mb0"><?php echo __('※C-Learning ASPのドリルCSVを登録することも可能ですが、下記内容が変更となります。'); ?><br>
		<ul class="ml8">
			<li class="mt4"><?php echo __('「選択肢配列」の認識が変更されたため、指定列数が一部減少します。（三列 → 二列、二列 → 一列、一列 → 一列）'); ?></li>
			<li class="mt4"><?php echo __('「問題文」の色指定をされていても、無視されます。'); ?></li>
		</ul>
	</div>
</div>
