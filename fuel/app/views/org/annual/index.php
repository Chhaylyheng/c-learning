<?php $iNo = 1; ?>

<div class="mt0 info-box">
<h2><?php echo $iNo; ?>. <?php echo __('講義の終了'); ?></h2>
<hr>

<p class="line-height-13">
	<?php echo __('指定年度の講義を全て終了させます。'); ?><br>
	<?php echo __('「講義の次年度コピー」を選択して実行すると、指定年度の講義の内容をコピーした新しい講義が作成されます。'); ?><br>
	<span class="font-gray font-size-90"><?php echo __('※コピーされる内容は、アンケートや小テストの問題、教材倉庫の教材、協働板のカテゴリー、レポートのテーマとなります。'); ?></span><br>
</p>

<?php if (!is_null($aYears)): ?>
<form action="/org/annual/classclose" method="post" class="mt8" style="padding: 8px; overflow: hidden;">
<div class="va-bottom mt4 mr4" style="display: inline-block;">
<?php echo __('年度'); ?><br>
<select name="year" class="dropdown">
<?php foreach ($aYears as $iY): ?>
<option value="<?php echo $iY; ?>"><?php echo __(':year年度',array('year'=>$iY)); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="va-bottom mt4 mr4" style="display: inline-block;">
<?php echo __('講義の次年度コピー'); ?><br>
<select name="renew" class="dropdown text-left">
<option value="0" class="text-left"><?php echo __('コピーしない'); ?></option>
<option value="1" class="text-left"><?php echo __('コピーする'); ?></option>
<option value="2" class="text-left"><?php echo __('コピーする（先生の担当情報を含む）'); ?></option>
</select>
</div>
<div class="va-bottom mt4" style="display: inline-block;">
<button type="button" class="button do width-auto annualClass" style="padding: 8px;"><?php echo __('実行する'); ?></button>
</div>
</form>
<?php else: ?>
<p class="font-red"><?php echo __('登録されている講義がありません。'); ?></p>
<?php endif; ?>

</div>

<?php $iNo++; ?>

<div class="mt8 info-box">
<h2><?php echo $iNo; ?>. <?php echo __('学生の削除'); ?></h2>
<hr>

<p class="line-height-13"><?php echo __('次年度に利用しない学生を削除します。'); ?></p>

<?php if (CL_CAREERTASU_MODE): ?>

	<p class="line-height-13"><a href="/org/student"><?php echo __('学生一覧へ移動'); ?></a></p>

<?php else: ?>

	<p class="line-height-13"><?php echo __('「学年」を基準に対象学生を削除します。'); ?><br>
	<span class="font-gray font-size-90"><?php echo __('※個別に削除を行う場合は → '); ?><a href="/org/student"><?php echo __('学生一覧へ移動'); ?></a></span><br>
</p>

<?php if (!is_null($aStuYears)): ?>
<form action="/org/annual/studentdelete" method="post" class="mt8" style="padding: 8px; overflow: hidden;">
<div class="va-bottom mt4 mr4" style="display: inline-block;">
<?php echo __('学年（人数）'); ?><br>
<select name="year" class="dropdown">
<?php foreach ($aStuYears as $iY => $iNum): ?>
<option value="<?php echo $iY; ?>"><?php echo $iY.' ('.$iNum.')'; ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="va-bottom mt4" style="display: inline-block;">
<button type="button" class="button do width-auto annualStudentDelete" style="padding: 8px;"><?php echo __('実行する'); ?></button>
</div>
</form>
<?php else: ?>
<p class="font-red"><?php echo __('学年が設定されている学生が見つかりません'); ?></p>
<?php endif; ?>

	<?php endif; ?>

</div>

<?php $iNo++; ?>

<?php if (!CL_CAREERTASU_MODE): ?>

<div class="mt8 info-box">
<h2><?php echo $iNo; ?>. <?php echo __('学年の更新'); ?></h2>
<hr>

<p class="line-height-13"><?php echo __('既存学生の学年を一括で1年増加させます。'); ?></p>


<form action="/org/annual/studentyearincrement" method="post" class="mt8" style="padding: 8px; overflow: hidden;">
<div class="va-bottom mt4" style="display: inline-block;">
<button type="button" class="button do width-auto annualStudentYearIncrement" style="padding: 8px;"><?php echo __('実行する'); ?></button>
</div>
<input type="hidden" name="yearincrement" value="1">
</form>
</div>

<?php $iNo++; ?>

<?php endif; ?>

<div class="mt8 info-box">
<h2><?php echo $iNo; ?>. <?php echo __('その後の手順'); ?></h2>
<hr>

<p class="line-height-13 font-bold">■<?php echo __('新規学生の追加'); ?></p>
<p class="line-height-13 mt0">
　<?php echo __('次年度に新たに利用する学生をCSVで追加します。'); ?><br>
　<a href="/org/student/csv"><?php echo __('CSVから学生の登録'); ?></a>
</p>

<p class="line-height-13 font-bold mt8">■<?php echo __('新規先生の追加'); ?></p>
<p class="line-height-13 mt0">
　<?php echo __('次年度に新たに利用する先生をCSVで追加します。'); ?><br>
　<a href="/org/teacher/csv"><?php echo __('CSVから先生の登録'); ?></a>
</p>

<p class="line-height-13 font-bold mt8">■<?php echo __('新規講義の追加'); ?></p>
<p class="line-height-13 mt0">
　<?php echo __('次年度に新たに利用する講義をCSVで追加します。'); ?><br>
　<a href="/org/class/csv"><?php echo __('CSVから講義の登録'); ?></a>
</p>

<p class="line-height-13 font-bold mt8">■<?php echo __('担当先生の更新'); ?></p>
<p class="line-height-13 mt0">
　<?php echo __('新規講義や既存の講義などの担当先生を追加・更新します。'); ?><br>
　<a href="/org/class/csvteach"><?php echo __('CSVから担当の登録'); ?></a>
</p>

<p class="line-height-13 font-bold mt8">■<?php echo __('学生履修の更新'); ?></p>
<p class="line-height-13 mt0">
　<?php echo __('新規講義や既存の講義などの学生履修情報を追加・更新します。'); ?><br>
　<a href="/org/student/csvstady"><?php echo __('CSVから履修の登録'); ?></a>
</p>

</div>





