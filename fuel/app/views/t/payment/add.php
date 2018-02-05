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

<section class="pt16">
	<h2><a class="link-out accordion acc-open" href="#">契約情報の入力</a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner">
		<p class="error-box mb16" style="display: none;" id="mathChangeErr"></p>
		<?php echo Form::open(array('action'=>'/t/payment/estimatecreate','method'=>'post','id'=>'add-form','data'=>CL_TAX_RATE."|".CL_PT_BLINE)) ; ?>
		<input type="hidden" name="product" value="add">
		<input type="hidden" name="pt" value="<?php echo $aPlan['ptID']; ?>">
		<input type="hidden" name="range" value="<?php echo $iRange; ?>">
		<h3 class="mt24 mb16 font-size-140">契約期間</h3>
		<div class="ml16">
			<p class="point-num con-range"><span><?php echo $iRange; ?></span>ヶ月（<?php echo date('Y年n月j日',strtotime($aTeacher['coTermDate'])); ?>まで）</p>
		</div>

		<h3 class="mt24 mb16 font-size-140">追加講義数を選択</h3>

		<div class="ml16">
			<select name="class" class="normal-select" id="class-num">
				<?php for($i = 1; $i <= (20 - $aTeacher['coClassNum']); $i++): ?>
					<?php $sSelect = ($i == 1)? ' selected':''; ?>
					<option value="<?php echo $i; ?>"<?php echo $sSelect; ?>><?php echo $i; ?>講義</option>
				<?php endfor; ?>
			</select>

			<div class="mt8" style="margin-left: 16px;">
				<p class="recital">
					※1講義 <span class="class-price"><?php echo number_format($aPlan['ptPriceCL']); ?></span>円/月（税抜）で計算されます。
				</p>
			</div>
		</div>

		<h3 class="mt24 mb16 font-size-140">クーポンコードの利用（10桁）</h3>
		<div class="ml16 coupon-input">
			<input type="text" name="coupon-code" id="coupon-code" value="" maxlength="10"><div class="coupon-check">
				<i class="fa fa-circle-o" style=""></i>
				<i class="fa fa-spinner fa-spin" style="display: none;"></i>
				<i class="fa fa-check-circle" style="display: none; color: green;"></i>
			</div>
			<div class="mt8" style="margin-left: 16px;">
				<p class="font-red coupon-text mt4"></p>
				<p class="recital mt4">※ケータイ活用教育研究会加入の方やキャンペーン等のクーポンをお持ちの方はこちらに入力して下さい。</p>
			</div>
		</div>

		<h3 class="mt24 mb16 font-size-120">ご購入金額</h3>
		<p class="billing-price ml16"><span id="sum-price"><?php echo number_format($iPrice); ?></span> 円（税抜）<br><span id="exp" class="font-grey"><?php echo $sExp; ?></span></p>

		<h3 class="mt24 mb16 font-size-120">ご請求金額</h3>
		<p class="billing-num ml16"><span id="sum-pay">0</span> 円（税込）</p>

		<hr class="mt32">

		<h3 class="mt24 mb16 font-size-120">お支払い方法を選択</h3>
		<div class="radio-lists mt16 ml16">
			<?php if (CL_ENV == 'DEVELOPMENT'): ?>
			<label><input type="radio" name="billing" value="4" class="billing-detail"><span>PayPal決済</span></label>
			<?php endif; ?>
			<label><input type="radio" name="billing" value="1" class="billing-detail"><span>クレジットカード決済</span></label>
			<label><input type="radio" name="billing" value="2" class="billing-detail"><span>銀行振込</span></label>
		<div class="billing-box">
			<ul>
			<li>購入手続き完了後に発行可能な請求書の内容に従ってお支払いください。</li>
			<li>振込手数料はお客さまでご負担ください。</li>
			<li>購入手続き完了日の翌月末までにご入金のない場合、ご購入はキャンセルとさせていただきます。</li>
			</ul>
		</div>

		</div>
		<hr class="mt32">
		<p class="button-box mt32"><button type="submit" name="mode" value="start" id="pay-submit" class="button cancel register" disabled="disabled">見積を作成する</button></p>

		<?php echo Form::close(); ?>
	</div>
	</div>
</section>
