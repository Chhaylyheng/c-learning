<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('初期設定'); ?></h1>

	<ul class="init-process mt16">
		<li><?php echo __('①先生設定'); ?><i class="fa fa-angle-right"></i></li
		><li><?php echo __('②講義登録'); ?><i class="fa fa-angle-right"></i></li
		><li><?php echo __('③学生履修登録'); ?><i class="fa fa-angle-right"></i></li
		><li class="active"><?php echo __('④完了'); ?></li>
	</ul>

	<div class="info-box mt8">

		<p class="text-center font-green font-size-160 mt8 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-check-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('ご登録ありがとうございました'); ?></p>

		<form action="/t/init/finish" method="post" class="mt16">

		<hr>
		<div class="button-box mt8" style="overflow: hidden;">
			<button type="submit" name="back" class="button default na width-auto" style="float: left;" value="1"><i class="fa fa-chevron-left ml0 mr16"></i><?php echo __('戻る'); ?></button>
			<button type="submit" name="next" class="button do na width-auto" style="float: right;" value="1"><?php echo __('利用を開始する'); ?><i class="fa fa-chevron-right ml16 mr0"></i></button>
		</div>

		</form>
	</div>
</div>
