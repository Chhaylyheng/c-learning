<?php
	$sPPay = '';
	if (Session::get('SES_T_PURCHASE_BANK',false)):
		Session::delete('SES_T_PURCHASE_BANK');
		$sPPay = 'pbank';
	endif;
?>
<section class="pt0" id="<?php echo $sPPay; ?>">
	<h2><a class="link-out accordion acc-open" href="#">見積履歴</a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner pt0">

		<div class="info-box table-box no-pad record-table mt16">
		<table>
			<thead>
				<tr>
					<th>見積番号</th>
					<th>見積発行日</th>
<?php /*					<th>CLポイント</th> */ ?>
					<th>金額</th>
					<th>支払い方法</th>
					<th>見積書</th>
					<th>購入手続</th>
					<th>削除</th>
				</tr>
			</thead>
			<tbody>
<?php
global $gaBilling;
if (isset($aBill[0])):
	foreach ($aBill[0] as $i => $aE):
		$sOdd = ($i % 2)? '':' class="odd"';
		$bOut = false;
		if ($aE['eDate'] < date('Y-m-d',strtotime('-30days'))):
			$bOut = true;
		endif;
?>
<tr<?php echo $sOdd; ?>>
	<td><span class="sp-display font-grey">見積番号</span><?php echo $aE['eNO']; ?></td>
	<td><span class="sp-display font-grey">見積発行日</span><?php echo date('Y年n月j日',strtotime($aE['eDate'])); ?></td>
<?php /*	<td><span class="sp-display font-grey">CLポイント</span><?php echo number_format($aE['point']); ?></td> */ ?>
	<td><span class="sp-display font-grey">金額</span><?php echo number_format($aE['ePrice']*(1+$aE['eTax'])); ?>円（税込）</td>
	<td><span class="sp-display font-grey">支払い方法</span><?php echo $gaBilling[$aE['eBilling']]; ?></td>
	<td><span class="sp-display font-grey">見積書</span><a href="/t/payment/pdfview/e/<?php echo $aE['eNO']; ?>" target="pdfview"><?php echo Asset::img('icon_pdf.png'); ?></a>
	<?php if (!$bOut): ?>
	/ <a href="/t/payment/estimateedit/<?php echo $aE['eNO']; ?>">情報変更</a></td>
	<?php endif; ?>
	<td class="pc-center-sp-left">
		<?php if (!$bOut): ?>
		<span class="sp-display"><a href="/t/payment/purchase/<?php echo $aE['eNO']; ?>" class="purchase" billing="<?php echo $aE['eBilling']; ?>">購入手続</a></span>
		<span class="pc-display"><a href="/t/payment/purchase/<?php echo $aE['eNO']; ?>" class="purchase" billing="<?php echo $aE['eBilling']; ?>"><i class="fa fa-shopping-cart fa-lg"></i></a></span>
		<?php else: ?>
		<span class="sp-display font-red">期限切れ</span>
		<span class="pc-display font-red">期限切れ</span>
		<?php endif; ?>
	</td>
	<td class="pc-center-sp-left">
		<span class="sp-display"><a href="/t/payment/estimatedelete/<?php echo $aE['eNO']; ?>" class="estimatedelete">削除</a></span>
		<span class="pc-display"><a href="/t/payment/estimatedelete/<?php echo $aE['eNO']; ?>" class="estimatedelete"><i class="fa fa-trash-o fa-lg"></i></a></span>
	</td>
</tr>
<?php
	endforeach;
	unset($aBill[0]);
endif;
?>
			</tbody>
		</table>
		</div>

	</div>
	</div>
</section>

<section>
	<h2><a class="link-out accordion acc-open" href="#">購入履歴</a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner pt0">

		<div class="info-box table-box no-pad record-table mt16">
		<table>
			<thead>
				<tr>
					<th>購入番号</th>
					<th>購入確定日</th>
					<th>入金確認日</th>
<?php /*					<th>CLポイント</th> */ ?>
					<th>金額</th>
					<th>支払い方法</th>
					<th class="pc-center-sp-left">見積書</th>
					<th class="pc-center-sp-left">請求書</th>
					<th class="pc-center-sp-left">領収書</th>
					<th class="pc-center-sp-left">納品書</th>
				</tr>
			</thead>
			<tbody>
<?php
if (!is_null($aBill)):
	foreach ($aBill as $aBs):
		foreach ($aBs as $i => $aE):
			$sOdd = ($i % 2)? '':' class="odd"';
?>
<tr<?php echo $sOdd; ?>>
	<td><span class="sp-display font-grey">購入番号</span><?php echo $aE['bNO']; ?></td>
	<td><span class="sp-display font-grey">購入確定日</span><?php echo date('Y年n月j日',strtotime($aE['bDate'])); ?></td>
	<td><span class="sp-display font-grey">入金確認日</span><?php echo ($aE['bPayDate'] != '0000-00-00')? date('Y年n月j日',strtotime($aE['bPayDate'])):'<span class="font-red">入金未確認</span>'; ?></td>
<?php /*	<td><span class="sp-display font-grey">CLポイント</span><?php echo number_format($aE['point']); ?></td> */ ?>
	<td><span class="sp-display font-grey">金額</span><?php echo number_format($aE['ePrice']*(1+$aE['eTax'])); ?>円（税込）</td>
	<td><span class="sp-display font-grey">支払い方法</span><?php echo $gaBilling[$aE['eBilling']]; ?></td>
	<td class="pc-center-sp-left"><span class="sp-display font-grey">見積書</span><a href="/t/payment/pdfview/e/<?php echo $aE['eNO']; ?>" target="pdfview"><?php echo Asset::img('icon_pdf.png'); ?></a></td>
	<td class="pc-center-sp-left"><span class="sp-display font-grey">請求書</span>
	<?php if ($aE['bNum'] > 0): ?>
		<a href="/t/payment/billpublish/<?php echo $aE['eNO']; ?>"><?php echo Asset::img('icon_pdf.png'); ?>(<?php echo $aE['bNum']; ?>)</a>
	<?php else: ?>
		済
	<?php endif; ?>
	</td>
	<td class="pc-center-sp-left"><span class="sp-display font-grey">領収書</span>
	<?php if ($aE['status'] == 2 && $aE['rNum'] > 0): ?>
		<a href="/t/payment/receiptpublish/<?php echo $aE['eNO']; ?>"><?php echo Asset::img('icon_pdf.png'); ?>(<?php echo $aE['rNum']; ?>)</a>
	<?php elseif ($aE['status'] < 2): ?>
		未
	<?php else: ?>
		済
	<?php endif; ?>
	</td>
	<td class="pc-center-sp-left"><span class="sp-display font-grey">納品書</span>
	<?php if ($aE['lNum'] > 0 && $aE['purchase']): ?>
		<a href="/t/payment/licensepublish/<?php echo $aE['eNO']; ?>"><?php echo Asset::img('icon_pdf.png'); ?>(<?php echo $aE['lNum']; ?>)</a>
	<?php else: ?>
		済
	<?php endif; ?>
	</td>
</tr>
<?php
		endforeach;
	endforeach;
endif;
?>
			</tbody>
		</table>
		</div>

	</div>
	</div>
</section>
