<form action="/org/flag" method="post">

<section class="pt0">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('【先生】プロフィール変更の制限'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner">
	<p class="mt8 mb8"><?php echo __('先生自身に変更させたくない項目にチェックを入れてください。'); ?></p>

	<ul class="QuestAnsChoice">
<?php
	$aChoice = array();
	$aStyle = array('square-o','check-square-o');

	foreach ($aOrgFlag['T_PROF'] as $i => $v):
		$sCheck = (array_search($i, $T_PROF) !== false)? ' checked':'';
		$sIcon  = ($sCheck)? $aStyle[1]:$aStyle[0];
		$sLabel = ($sCheck)? 'check':'default';
?><li class="width-auto" style="margin-left: 6px!important; margin-right: 6px!important;"><label class="QueryChoice text-left <?php echo $sLabel; ?>"
	><input type="checkbox" name="T_PROF[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>
	><p><i class="fa fa-<?php echo $sIcon; ?> fa-fw"></i><?php echo $v; ?></p></label></li><?php
	endforeach;
?>
	</ul>

</div>
</div>
</section>


<section class="pt16">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('【先生】講義操作の制限'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner">
	<p class="mt8 mb8"><?php echo __('先生に操作させたくない項目にチェックを入れてください。'); ?></p>

	<ul class="QuestAnsChoice">
<?php
	$aChoice = array();
	$aStyle = array('square-o','check-square-o');

	foreach ($aOrgFlag['T_AUTH'] as $i => $v):
		$sCheck = (array_search($i, $T_AUTH) !== false)? ' checked':'';
		$sIcon  = ($sCheck)? $aStyle[1]:$aStyle[0];
		$sLabel = ($sCheck)? 'check':'default';
?><li class="width-auto" style="margin-left: 6px!important; margin-right: 6px!important;"><label class="QueryChoice text-left <?php echo $sLabel; ?>"
	><input type="checkbox" name="T_AUTH[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>
	><p><i class="fa fa-<?php echo $sIcon; ?> fa-fw"></i><?php echo $v; ?></p></label></li><?php
	endforeach;
?>
	</ul>

</div>
</div>
</section>


<section class="pt16">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('【学生】プロフィール変更の制限'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner">
	<p class="mt8 mb8"><?php echo __('学生自身に変更させたくない項目にチェックを入れてください。'); ?></p>

	<ul class="QuestAnsChoice">
<?php
	$aChoice = array();
	$aStyle = array('square-o','check-square-o');

	foreach ($aOrgFlag['S_PROF'] as $i => $v):
		if (CL_CAREERTASU_MODE && ($i == 2 || $i == 16 || $i == 32 || $i == 64 || $i == 128)):
?>
<input type="hidden" name="S_PROF[]" value="<?php echo $i; ?>">
<?php
			continue;
		endif;
		if (!CL_CAREERTASU_MODE && $i == 512):
			continue;
		endif;
		$sCheck = (array_search($i, $S_PROF) !== false)? ' checked':'';
		$sIcon  = ($sCheck)? $aStyle[1]:$aStyle[0];
		$sLabel = ($sCheck)? 'check':'default';
?><li class="width-auto" style="margin-left: 6px!important; margin-right: 6px!important;"><label class="QueryChoice text-left <?php echo $sLabel; ?>"
	><input type="checkbox" name="S_PROF[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>
	><p><i class="fa fa-<?php echo $sIcon; ?> fa-fw"></i><?php echo $v; ?></p></label></li><?php
	endforeach;
?>
	</ul>

</div>
</div>
</section>


<section class="pt16">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('【学生】講義履修の制限'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner">
	<p class="mt8 mb8"><?php echo __('学生からの講義履修をさせたくない場合にチェックを入れてください。'); ?></p>

	<ul class="QuestAnsChoice">
<?php
	$aChoice = array();
	$aStyle = array('square-o','check-square-o');

	foreach ($aOrgFlag['S_AUTH'] as $i => $v):
		$sCheck = (array_search($i, $S_AUTH) !== false)? ' checked':'';
		$sIcon  = ($sCheck)? $aStyle[1]:$aStyle[0];
		$sLabel = ($sCheck)? 'check':'default';
?><li class="width-auto" style="margin-left: 6px!important; margin-right: 6px!important;"><label class="QueryChoice text-left <?php echo $sLabel; ?>"
	><input type="checkbox" name="S_AUTH[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>
	><p><i class="fa fa-<?php echo $sIcon; ?> fa-fw"></i><?php echo $v; ?></p></label></li><?php
	endforeach;
?>
	</ul>

</div>
</div>
</section>


<section class="pt16">
<h2><a class="link-out accordion acc-open" href="#"><?php echo __('【学生】ログイン時の強制取得'); ?></a></h2>
<div class="accordion-content acc-content-open">
<div class="accordion-content-inner">
	<p class="mt8"><?php echo __('学生がログインした際、強制的に取得したい項目にチェックを入れてください。'); ?></p>
	<p class="mt4"><?php echo __('チェックした項目が未設定の場合、その項目の入力欄が学生ログイン時に表示されます。'); ?></p>
	<p class="mt4 mb8"><?php echo __('この設定は「【学生】プロフィール変更の制限」よりも優先されます。'); ?></p>

	<ul class="QuestAnsChoice">
<?php
	$aChoice = array();
	$aStyle = array('square-o','check-square-o');

	foreach ($aOrgFlag['S_GET'] as $i => $v):
		if (CL_CAREERTASU_MODE && ($i == 2 || $i == 16 || $i == 32 || $i == 64 || $i == 128)):
			continue;
		endif;
		if (!CL_CAREERTASU_MODE && $i == 512):
			continue;
		endif;
		$sCheck = (array_search($i, $S_GET) !== false)? ' checked':'';
		$sIcon  = ($sCheck)? $aStyle[1]:$aStyle[0];
		$sLabel = ($sCheck)? 'check':'default';
?><li class="width-auto" style="margin-left: 6px!important; margin-right: 6px!important;"><label class="QueryChoice text-left <?php echo $sLabel; ?>"
	><input type="checkbox" name="S_GET[]" value="<?php echo $i; ?>" autocomplete="off"<?php echo $sCheck; ?>
	><p><i class="fa fa-<?php echo $sIcon; ?> fa-fw"></i><?php echo $v; ?></p></label></li><?php
	endforeach;
?>
	</ul>

</div>
</div>
</section>

<p class="button-box mt16 text-center"><button type="submit" name="state" value="1" class="button do"><?php echo __('変更する'); ?></button></p>

</form>
