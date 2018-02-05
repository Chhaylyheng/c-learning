<section class="pt0">
	<h2><a class="link-out accordion acc-open" href="#">現在の契約内容</a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner">

	<table class="payment-contract-table">
		<thead>
		<tr>
			<th>契約プラン</th>
			<th>開始日</th>
			<th>終了日</th>
			<th>契約講義数</th>
			<th>講義履修人数</th>
		</tr>
		</thead>
		<tbody>
<?php
if (!is_null($aContract)):
	foreach ($aContract as $aC):
		$sAct = ($aC['coNO'] == $aTeacher['coNO'])? 'active':'';
?>
	<tr class="<?php echo $sAct; ?>">
		<td><?php echo $aMPlan[$aC['ptID']]['ptName']; ?>プラン</td>
		<td><?php echo date('Y/m/d',strtotime($aC['coStartDate'])); ?></td>
		<td><?php echo date('Y/m/d',strtotime($aC['coTermDate'])); ?></td>
		<td><?php echo $aC['coClassNum']; ?>講義</td>
		<td><?php echo number_format($aC['coStuNum']); ?>名</td>
	</tr>
<?php
	endforeach;
endif;
?>
		</tbody>
	</table>

	<p>あなたが現在実施中の講義は「<span class="font-green"><?php echo $aTeacher['ttClassNum']; ?>講義</span>」、終了した講義は「<?php echo $aTeacher['ttCloseNum']; ?>講義」です。</p>

	</div>
	</div>
</section>

<?php if ($aTeacher['ptID'] == 2): ?>
<section class="pt16">
	<h2><a class="link-out accordion acc-open" href="#">プラン変更（現在の契約期間内の講義追加）</a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner">
		<ul class="PaymentProductList">
		<li class="mt12">
		<h3>Standardプラン</h3>
		<div class="mt16 mb16 line-height-16">現在契約中のLightプランをStandardプランに変更します。<br>
		<span class="font-green"><?php echo date('Y年n月j日',strtotime($aTeacher['coTermDate'])); ?></span>までの残り月数で金額が計算されます。</div>
		<a href="/t/payment/change" class="button do">ご購入はコチラから</a>
		</li>
		</ul>
	</div>
	</div>
</section>
<?php endif; ?>

<?php if ($aTeacher['ptID'] == 3): ?>
<section class="pt16">
	<h2><a class="link-out accordion acc-open" href="#">追加購入</a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner">
		<ul class="PaymentProductList">
		<li class="mt12">
		<h3>講義の追加購入</h3>
		<a href="/t/payment/add" class="button do">ご購入はコチラから</a>
		</li>
		</ul>
	</div>
	</div>
</section>
<?php endif; ?>

<?php
$sTitle = '継続契約（契約期間終了日以降のお申込み）';
if ($aTeacher['ptID'] == 1):
	$sTitle = '新規契約';
endif;
?>
<section class="pt16">
	<h2><a class="link-out accordion acc-open" href="#"><?php echo $sTitle; ?></a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner">
		<ul class="PaymentProductList">
		<li class="mt12">
		<h3>Standardプラン</h3>
		<p>1講義 2,000円/月<br>ディスク容量 20GB<br>1講義の履修人数 300名まで</p>
		<a href="/t/payment/contract/3" class="button do">ご購入はコチラから</a>
		</li>
		<li class="mt12">
		<h3>Lightプラン</h3>
		<p>1講義 1,000円/月<sup>※</sup><br>ディスク容量 1GB<br>1講義の履修人数 300名まで</p>
		<a href="/t/payment/contract/2" class="button confirm">ご購入はコチラから</a>
		<p class="note">※1講義のみ利用可能です。<br>複数講義を利用する場合は、Standardへのプラン変更が必要になります。</p>
		</li>
		</ul>
	</div>
	</div>
</section>

