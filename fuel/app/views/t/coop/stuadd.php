<div class="info-box pt8">
<p class="mt0 mb0"><a href="/t/coop" class="button na do width-auto"><?php echo __('学生選択完了'); ?></a></p>
<form action="" method="post">
<ul style="display: table; padding: 8px;" class="width-100" obj="<?php echo $aCCategory['ctID'].'_'.$aCCategory['ccID']; ?>">
<li style="display: table-cell;" class="width-45">
<h2><?php echo __('対象学生'); ?></h2>
<select name="add" multiple size="20" class="width-100" style="background-color: #fcfcfc; border: 1px solid #ccc; padding: 4px;">
<?php
	if (!is_null($aCStu)):
		foreach ($aCStu as $sStID => $aS):
			$sAttr = ($aS['stClass'] != '')? $aS['stClass']:'';
			$sAttr .= (($sAttr != '')? ',':'').(($aS['stNO'] != '')? $aS['stNO']:'');
?>
	<option value="<?php echo $sStID; ?>"><?php echo $aS['stName'].(($sAttr != '')? '（'.$sAttr.'）':''); ?></option>
<?php
		endforeach;
	endif;
?>
</select>
</li>
<li style="display: table-cell; vertical-align: middle;" class="width-10 text-center">
	<button type="button" class="button na width-auto confirm StudentCoopAdd" style="padding: 8px;"><i class="fa fa-arrow-circle-left mr0"></i> <?php echo __('学生追加'); ?></button>
	<button type="button" class="button na width-auto cancel StudentCoopRemove mt16" style="padding: 8px;"><?php echo __('学生削除'); ?> <i class="fa fa-arrow-circle-right mr0"></i></button>
</li>
<li style="display: table-cell;" class="width-45">
<h2><?php echo __('対象外学生'); ?></h2>
<select name="remove" multiple size="20" class="width-100" style="background-color: #fcfcfc; border: 1px solid #ccc; padding: 4px;">
<?php
	if (!is_null($aStu)):
		foreach ($aStu as $sStID => $aS):
			$sAttr = ($aS['stClass'] != '')? $aS['stClass']:'';
			$sAttr .= (($sAttr != '')? ',':'').(($aS['stNO'] != '')? $aS['stNO']:'');
?>
	<option value="<?php echo $sStID; ?>"><?php echo $aS['stName'].(($sAttr != '')? '（'.$sAttr.'）':''); ?></option>
<?php
		endforeach;
	endif;
?>
</select>
</li>
</ul>

</form>
</div>
