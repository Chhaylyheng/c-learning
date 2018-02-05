<div class="info-box pt0">
<form action="" method="post">

<ul style="display: table; padding: 16px 8px;" class="width-100" obj="<?php echo $aClass['ctID']; ?>">
<li style="display: table-cell;" class="width-45">
<h2>先生（主担当：<span class="master-teacher"><?php echo $aClass['ttName']; ?></span>）</h2>
<select name="add" multiple size="20" class="width-100" style="background-color: #fcfcfc; border: 1px solid #ccc; padding: 4px;">
<?php
	if (!is_null($aCTeachers)):
		foreach ($aCTeachers as $sTtID => $aT):
			if (!$aT['tpMaster']):
?>
	<option value="<?php echo $sTtID; ?>"><?php echo $aT['ttMail'].'（'.$aT['ttName'].'）'; ?></option>
<?php
			endif;
		endforeach;
	endif;
?>
</select>
</li>
<li style="display: table-cell; vertical-align: middle;" class="width-10 text-center">
	<button type="button" class="button na width-auto confirm TeacherClassAdd" style="padding: 8px;"><i class="fa fa-arrow-circle-left mr0"></i> 先生追加</button>
	<button type="button" class="button na width-auto cancel TeacherClassRemove mt16" style="padding: 8px;">先生削除 <i class="fa fa-arrow-circle-right mr0"></i></button>
</li>
<li style="display: table-cell;" class="width-45">
<h2>未登録先生</h2>
<select name="remove" multiple size="20" class="width-100" style="background-color: #fcfcfc; border: 1px solid #ccc; padding: 4px;">
<?php
	if (!is_null($aTeachers)):
		foreach ($aTeachers as $sTtID => $aT):
			if (!isset($aCTeachers[$sTtID])):
?>
	<option value="<?php echo $sTtID; ?>"><?php echo $aT['ttMail'].'（'.$aT['ttName'].'）'; ?></option>
<?php
			endif;
		endforeach;
	endif;
?>
</select>
</li>
</ul>

</form>
</div>
