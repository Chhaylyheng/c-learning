<style>
header {
	display: none;
}
#content {
	margin: 0!important;
	padding-top: 52px;
	background-color: transparent;
}
footer {
	background-color: transparent;
}
#note-header {
	position: fixed;
	background-color: #eee;
	width: 100%;
	padding: 8px;
	height: 52px;
	overflow: hidden;
	top: 0;
}
img.note {
	max-width: 100%;
	border: none;
}

</style>

<div id="note-header">
<div class="text-center">
	<a href="/t/payment/product" class="button do width-auto" style="padding: 10px 16px;">プラン選択へ</a>
</div>
<a href="<?php echo \Input::referrer(); ?>" class="page-close">×</a>
</div>


<div class="text-center font-size-180 pt32 line-height-14">
C-Learningにはクイックアンケート以外にも様々な便利な機能があります！<br>
ここで少しだけご紹介します。
</div>

<div class="text-center pt32" id="attend">

<?php echo Asset::img('note-attend.png',array('class'=>'note')); ?>

</div>

<div class="text-center pt32" id="quest">

<?php echo Asset::img('note-quest.png',array('class'=>'note')); ?>

</div>

<div class="text-center pt32" id="test">

<?php echo Asset::img('note-test.png',array('class'=>'note')); ?>

</div>

<div class="text-center pt32" id="material">

<?php echo Asset::img('note-material.png',array('class'=>'note')); ?>

</div>

<div class="text-center pt32" id="coop">

<?php echo Asset::img('note-coop.png',array('class'=>'note')); ?>

</div>

<div class="text-center pt32" id="report">

<?php echo Asset::img('note-report.png',array('class'=>'note')); ?>

</div>

<div class="text-center pt32" id="news">

<?php echo Asset::img('note-news.png',array('class'=>'note')); ?>

</div>
