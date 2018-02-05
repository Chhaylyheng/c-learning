<div id="content-inner" class="login">

<div class="info-box">
<h2 class="font-size-160 font-red">エラーが発生しました！</h2>
<hr>
<p><?php echo $admmsg; ?></p>
<p><?php echo $tmsg; ?></p>
<p><?php echo $smsg; ?></p>
<p><?php echo $orgmsg; ?></p>
<?php if (isset($_SERVER['HTTP_REFERER'])): ?>
<div class="button-box mt16">
<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="button na cancel">戻る</a>
</div>
<?php endif; ?>
</div>

</div>
