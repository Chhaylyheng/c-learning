<?php
	$errClass = array('coTermDate'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' input-error';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '更新';
	$sAction = 'contract/'.$aTeacher['ttID'];
?>


<div class="info-box">
<form action="/adm/teacher/<?php echo $sAction; ?>" method="post">
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel">契約形態</div>
			<div class="formContent inline-box">
			<?php if ($aTeacher['ptID'] != 99): ?>
				<select class="dropdown font-default" name="ptID">
				<?php foreach ($aPlan as $aP): ?>
				<?php $sSel = ($aP['ptID'] == $ptID)? ' selected':''; ?>
				<option value="<?php echo $aP['ptID']; ?>"<?php echo $sSel; ?>><?php echo $aP['ptName']; ?></option>
				<?php endforeach; ?>
				</select>
			<?php else: ?>
				<span class="font-blue"><?php echo $aTeacher['ptName'].'（'.$aTeacher['gtName'].'）'; ?></span>
			<?php endif; ?>
			</div>
			<div class="formContent inline-box">
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">契約満了日</div>
			<div class="formContent inline-box">
			<?php if ($aTeacher['ptID'] != 99): ?>
				<input type="text" name="coTermDate" value="<?php echo date('Y/m/d',strtotime($coTermDate)); ?>" id="contract-datepick" class="width-10em text-center<?php echo $errClass['coTermDate']; ?>">
				<?php echo $errMsg['coTermDate']; ?>
			<?php else: ?>
				<span class="font-blue">─</span>
			<?php endif; ?>
			</div>
			<div class="formContent inline-box">
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">契約講義数</div>
			<div class="formContent inline-box">
			<?php if ($aTeacher['ptID'] != 99): ?>
				<select class="dropdown font-default" name="coClassNum">
				<?php for ($i = 0; $i <= 30; $i++): ?>
				<?php if ($i < $aTeacher['ttClassNum']):?>
				<?php continue; ?>
				<?php endif; ?>
				<?php $sSel = ($i == $coClassNum)? ' selected':''; ?>
				<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $i; ?></option>
				<?php endfor; ?>
				</select>
				<br><span class="font-gray">※実施中講義は <?php echo $aTeacher['ttClassNum']; ?></span>
			<?php else: ?>
				<span class="font-blue">─</span>
			<?php endif; ?>
			</div>
			<div class="formContent inline-box">
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">履修人数/1講義</div>
			<div class="formContent inline-box">
			<?php if ($aTeacher['ptID'] != 99): ?>
				<select class="dropdown font-default" name="coStuNum">
				<option value="50">50</option>
				<?php for ($i = 300; $i <= 1000; $i += 100): ?>
				<?php $sSel = ($i == $coStuNum)? ' selected':''; ?>
				<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $i; ?></option>
				<?php endfor; ?>
				</select>
				<br><span class="font-gray">※履修人数の最大は <?php echo (int)$aTeacher['ttStuNum']; ?>名</span>
			<?php else: ?>
				<span class="font-blue">─</span>
			<?php endif; ?>
			</div>
			<div class="formContent inline-box">
			<?php foreach ($aPlan as $aP): ?>
			<?php echo $aP['ptName']; ?>のデフォルトは<?php echo $aP['ptStuNum']; ?>名<br>
			<?php endforeach; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">ディスク容量</div>
			<div class="formContent inline-box">
			<?php if ($aTeacher['ptID'] != 99): ?>
				<select class="dropdown font-default" name="coCapacity">
				<?php for ($i = 0; $i <= 50; $i+=5): ?>
				<?php if ($i == 0):?>
				<?php $sSel = ($coCapacity == 1)? ' selected':''; ?>
				<option value="1"<?php echo $sSel; ?>>1GB</option>
				<?php continue; ?>
				<?php endif; ?>
				<?php $sSel = ($i == $coCapacity)? ' selected':''; ?>
				<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $i; ?>GB</option>
				<?php endfor; ?>
				</select>
				<br><span class="font-gray">※利用中容量は <?php echo \Clfunc_Common::FileSizeFormat($aTeacher['ttDiskUsed'], 1); ?></span>
			<?php else: ?>
				<span class="font-blue">─</span>
			<?php endif; ?>
			</div>
			<div class="formContent inline-box">
			<?php foreach ($aPlan as $aP): ?>
			<?php echo $aP['ptName']; ?>のデフォルトは<?php echo $aP['ptCapacity']; ?>GB<br>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubBtn; ?></button>
	</div>
</form>
</div>

<div class="info-box mt16 table-box record-table admin-table">
<h2>契約履歴</h2>

<table class="mt8">

<tr>
	<th>契約期間</th>
	<th>契約形態</th>
	<th>契約講義数</th>
	<th>履修人数</th>
	<th>ディスク容量</th>
</tr>

<?php if (!is_null($aContract)): ?>
<?php foreach ($aContract as $aC): ?>

<tr>

<td><?php echo date('Y/m/d',strtotime($aC['coStartDate'])); ?> ～ <?php echo date('Y/m/d',strtotime($aC['coTermDate'])); ?></td>
<td><?php echo $aPlan[$aC['ptID']]['ptName']; ?></td>
<td><?php echo $aC['coClassNum']; ?></td>
<td><?php echo $aC['coStuNum']; ?>名</td>
<td><?php echo $aC['coCapacity']; ?>GB</td>

</tr>

<?php endforeach; ?>
<?php endif; ?>

</table>

</div>


