<section class="pt0">
	<div class="info-box">
		<?php echo Form::open(array('action'=>'/adm/sale','method'=>'get')); ?>

		<h2>総合
			（
			<span class="font-green font-size-180"><?php echo number_format(count($aSale['credit'])+count($aSale['bank'])); ?>件</span> ／
			<span class="font-green font-size-180">&yen;<?php echo number_format($aSum['credit']['price']+$aSum['credit']['tax']+$aSum['bank']['price']+$aSum['bank']['tax']); ?></span>
<!-- 			<span class="font-green font-size-180"><?php echo number_format($aSum['credit']['point']+$aSum['bank']['point']); ?> Pt.</span> -->
			）
		</h2>

		<div class="mt16">年月選択
			<select name="year" class="dropdown">
			<?php for($i = 2015; $i <= (int)date('Y'); $i++): ?>
				<?php $sSelect = ($i == (int)$sY)? ' selected':''; ?>
				<option value="<?php echo $i; ?>"<?php echo $sSelect; ?>><?php echo $i; ?>年</option>
			<?php endfor; ?>
			</select>
			<select name="month" class="dropdown">
			<option value="0">全月</option>
			<?php for($i = 1; $i <= 12; $i++): ?>
				<?php $sSelect = ($i == (int)$sM)? ' selected':''; ?>
				<option value="<?php echo $i; ?>"<?php echo $sSelect; ?>><?php echo $i; ?>月</option>
			<?php endfor; ?>
			</select>
			<button type="submit" class="do button na sale-ymset" name="sub_state" value="1">実行</button>
		</div>

		<?php echo Form::close(); ?>
		<div class="info-box">
		<h2>クレジット / PayPal
			（
			<span class="font-blue font-size-140"><?php echo number_format(count($aSale['credit'])); ?>件</span> ／
			<span class="font-blue font-size-140">&yen;<?php echo number_format($aSum['credit']['price']+$aSum['credit']['tax']); ?></span>
<!-- 			<span class="font-blue font-size-140"><?php echo number_format($aSum['credit']['point'])?> Pt.</span> -->
			）
		</h2>
		<hr>
		<div class="info-box table-box record-table admin-table mt0">
		<table>
		<thead>
			<tr>
				<th>請求書番号</th>
				<th class="text-right">金額（税込）</th>
				<th class="text-center">決済日時</th>
				<th class="text-center">取引ID</th>
<!-- 				<th>付与ポイント</th>  -->
				<th>先生情報</th>
				<th>PDF</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (count($aSale['credit'])):
					foreach ($aSale['credit'] as $i => $aS):
						$sOdd = ($i % 2)? '':'odd';
						$sBN = $aS['bNO'];
						$sFilePath = CL_FILEPATH.DS.$aS['ttID'].DS.'payment_pdf'.DS.$sBN;

						$bB = (file_exists($sFilePath.'.pdf'))? true:false;
						$bR = (file_exists($sFilePath.'-R.pdf'))? true:false;
						$bL = (file_exists($sFilePath.'-L.pdf'))? true:false;
			?>
<tr class="<?php echo $sOdd; ?>">
<td class=""><?php echo $sBN; ?></td>
<td class="text-right">&yen;<?php echo number_format($aS['price']+$aS['tax']); ?></td>
<td class="text-center"><?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aS['bPayDate']); ?></td>
<td class="text-center"><?php echo ($aS['transactionID'])? $aS['transactionID']:'クレジット決済'; ?></td>
<!-- <td class="text-right"><?php echo number_format($aS['point']); ?> Pt.</td> -->
<td class=""><?php echo ($aS['ttName'])? $aS['ttName'].'（'.$aS['cmName'].'）':$aS['ttMail']; ?></td>

<td class="">
<a href="/adm/order/pdfview/<?php echo $aS['ttID']; ?>/e/<?php echo $aS['eNO']; ?>" target="pdfview" title="見積書" class="button na default width-auto">見</a>
<?php if ($bB): ?>
<a href="/adm/order/pdfview/<?php echo $aS['ttID']; ?>/b/<?php echo $aS['eNO']; ?>" target="pdfview" title="請求書" class="button na default width-auto">請</a>
<?php endif; ?>
<?php if ($bR): ?>
<a href="/adm/order/pdfview/<?php echo $aS['ttID']; ?>/r/<?php echo $aS['eNO']; ?>" target="pdfview" title="領収書" class="button na default width-auto">領</a>
<?php endif; ?>
<?php if ($bL): ?>
<a href="/adm/order/pdfview/<?php echo $aS['ttID']; ?>/l/<?php echo $aS['eNO']; ?>" target="pdfview" title="納品書" class="button na default width-auto">納</a>
<?php endif; ?>
</td>

</tr>
				<?php
						endforeach;
					endif;
				?>
				</tbody>
			</table>
		</div>
		</div>
		<div class="info-box">
		<h2>銀行振込
			（
			<span class="font-blue font-size-140"><?php echo number_format(count($aSale['bank'])); ?>件</span> ／
			<span class="font-blue font-size-140">&yen;<?php echo number_format($aSum['bank']['price']+$aSum['bank']['tax']); ?></span>
<!-- 			<span class="font-blue font-size-140"><?php echo number_format($aSum['bank']['point'])?> Pt.</span> -->
			）
		</h2>
		<hr>
		<div class="info-box table-box record-table admin-table mt0">
		<table>
		<thead>
			<tr>
				<th>請求書番号</th>
				<th class="text-right">金額（税込）</th>
				<th class="text-center">入金確認日</th>
<!-- 				<th>付与ポイント</th> -->
				<th>先生情報</th>
				<th>PDF</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (count($aSale['bank'])):
					foreach ($aSale['bank'] as $i => $aS):
						$sOdd = ($i % 2)? '':'odd';
						$sBN = $aS['bNO'];
						$sFilePath = CL_FILEPATH.DS.$aS['ttID'].DS.'payment_pdf'.DS.$sBN;

						$bB = (file_exists($sFilePath.'.pdf'))? true:false;
						$bR = (file_exists($sFilePath.'-R.pdf'))? true:false;
						$bL = (file_exists($sFilePath.'-L.pdf'))? true:false;
			?>
<tr class="<?php echo $sOdd; ?>">
<td class=""><?php echo $sBN; ?></td>
<td class="text-right">&yen;<?php echo number_format($aS['price']+$aS['tax']); ?></td>
<td class="text-center"><?php echo date('Y/m/d',strtotime($aS['bPayDate'])); ?></td>
<!-- <td class="text-right"><?php echo number_format($aS['point']); ?> Pt.</td> -->
<td class=""><?php echo ($aS['ttName'])? $aS['ttName'].'（'.$aS['cmName'].'）':$aS['ttMail']; ?></td>

<td class="">
<a href="/adm/order/pdfview/<?php echo $aS['ttID']; ?>/e/<?php echo $aS['eNO']; ?>" target="pdfview" title="見積書" class="button na default width-auto">見</a>
<?php if ($bB): ?>
<a href="/adm/order/pdfview/<?php echo $aS['ttID']; ?>/b/<?php echo $aS['eNO']; ?>" target="pdfview" title="請求書" class="button na default width-auto">請</a>
<?php endif; ?>
<?php if ($bR): ?>
<a href="/adm/order/pdfview/<?php echo $aS['ttID']; ?>/r/<?php echo $aS['eNO']; ?>" target="pdfview" title="領収書" class="button na default width-auto">領</a>
<?php endif; ?>
<?php if ($bL): ?>
<a href="/adm/order/pdfview/<?php echo $aS['ttID']; ?>/l/<?php echo $aS['eNO']; ?>" target="pdfview" title="納品書" class="button na default width-auto">納</a>
<?php endif; ?>
</td>

</tr>
				<?php
						endforeach;
					endif;
				?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</section>
