<?php echo ($cUnknown)? $cUnknown:$cName; ?>さんが
<?php echo CL_SITENAME; ?>の協働板に投稿しました。
ご確認ください。

講義名：<?php echo $ctName; ?>

協働板名：<?php echo $ccName; ?>

<?php if ($sWriter): ?>
宛先：<?php echo ($rName)? $rName:$sWriter; ?>
<?php endif; ?>

<?php if ($cTitle): ?>
タイトル：<?php echo $cTitle; ?>
<?php endif; ?>

本文：
<?php echo $cText; ?>

<?php if (count($files)): ?>
ファイル：
<?php
foreach ($files as $f):
if ($f['id']):
?>
<?php echo $f['name']?>（<?php echo \Clfunc_Common::FilesizeFormat($f['size'],1); ?>）
<?php
endif;
endforeach;
endif;
?>

<?php echo CL_SITENAME; ?>へのログインはコチラから
<?php echo CL_URL.DS.$mode ?>


------------------------------------------------------------------------------------
※本メールにお心当たりのない方は、このメールを破棄してください。
※本メールの送信元は送信専用となっており、このメールに返信されてもメールは届きません。
<?php echo CL_MAILCOPY; ?>
