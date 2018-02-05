<div id="content-inner" class="login">

	<h1 class="mt8"><?php echo __('初期設定'); ?></h1>

	<ul class="init-process mt16">
		<li><?php echo __('①先生設定'); ?><i class="fa fa-angle-right"></i></li
		><li><?php echo __('②講義登録'); ?><i class="fa fa-angle-right"></i></li
		><li class="active"><?php echo __('③完了'); ?></li>
	</ul>


	<div class="info-box mt8">

		<p class="text-center font-blue font-size-160 mt8 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-info-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('ご登録ありがとうございました'); ?></p>

		<p class="mb16 text-center font-size-120"><?php echo __(':siteに下記講義が作成されました', array('site'=>CL_SITENAME)); ?></p>

		<p class="mb16 text-center"><?php echo __('講義名'); ?><span class="font-red font-size-200 pl16"><?php echo $aClass['ctName']; ?></span></p>
		<p class="mb16 text-center"><?php echo __('講義コード'); ?><span class="font-red font-size-200 pl16"><?php echo \Clfunc_Common::getCode($aClass['ctCode']); ?></span></p>

		<form action="/t/init/student" method="post" class="mt16">

		<hr>
		<div class="button-box mt8" style="overflow: hidden;">
			<button type="submit" name="back" class="button default na width-auto" style="float: left;" value="1"><i class="fa fa-chevron-left ml0 mr16"></i><?php echo __('戻る'); ?></button>
			<button type="submit" name="next" class="button do na width-auto" value="1"><?php echo __('はじめる'); ?><i class="fa fa-check ml16 mr0"></i></button>
		</div>

		</form>
	</div>
</div>
