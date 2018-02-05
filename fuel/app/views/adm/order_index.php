	<section class="pt0">
		<div class="info-box">
			<?php echo Form::open(array('action'=>'/adm/order/paymentcheck','method'=>'post','class'=>'paychk')); ?>

			<p>入金確認日
				<input type="text" name="paydate" size="30" maxlength="10" value="<?php echo date('Y/m/d') ?>" readonly id="payment-datepick" style="width: 10em; text-align: center; display: inline-block; ">
				<button type="submit" class="do button na" name="sub_state" value="1">実行</button>
			</p>
			<div class="info-box table-box record-table admin-table mt0">
			<table>
			<thead>
				<tr>
					<th class="text-center"><input type="checkbox" class="AllChk" value=""></th>
					<th>請求書番号</th>
					<th class="text-right">金額（税込）</th>
					<th class="text-center">注文日時</th>
<!-- 					<th>付与ポイント</th> -->
					<th>先生情報</th>
					<th>PDF</th>
					<th class="text-center">削除</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if (!is_null($aBill)):
						$iMax = count($aBill);
						foreach ($aBill as $i => $aB):
							$sOdd = ($i % 2)? '':'odd';
							$sBN = $aB['bNO'];

							$bBill = false;
							$sFilePath = CL_FILEPATH.DS.$aB['ttID'].DS.'payment_pdf'.DS.$sBN.'.pdf';
							if (file_exists($sFilePath))
							{
								$bBill = true;
							}
				?>
<tr class="<?php echo $sOdd; ?>">
<td class="text-center">
<input type="checkbox" name="chkB[]" class="Chk" value="<?php echo $sBN; ?>">
</td>
<td class=""><?php echo $sBN; ?></td>
<td class="text-right">&yen;<?php echo number_format($aB['ePrice']*(1+$aB['eTax'])); ?></td>
<td class="text-center"><?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aB['bDate']); ?></td>
<!-- <td class="text-right"><?php echo number_format($aB['point']); ?> Pt.</td>  -->
<td class=""><?php echo ($aB['ttName'])? $aB['ttName'].'（'.$aB['cmName'].'）':$aB['ttMail']; ?></td>

<td class="">
<a href="/adm/order/pdfview/<?php echo $aB['ttID']; ?>/e/<?php echo $aB['eNO']; ?>" target="pdfview" title="見積書" class="button na default width-auto">見</a>
<?php if ($bBill): ?>
<a href="/adm/order/pdfview/<?php echo $aB['ttID']; ?>/b/<?php echo $aB['eNO']; ?>" target="pdfview" title="請求書" class="button na default width-auto">請</a>
<?php endif; ?>
</td>

<td class="text-center"><a href="/adm/order/paymentremove/<?php echo $sBN; ?>" class="paymentremove button na default width-auto"><i class="fa fa-trash mr0"></i></a></td>
</tr>
					<?php
							endforeach;
						endif;
					?>
					</tbody>
				</table>
			</div>
			<?php echo Form::close(); ?>
		</div>
	</section>
