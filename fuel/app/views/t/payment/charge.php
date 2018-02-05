<?php
	$errClass = array('t_name'=>'','t_school'=>'','t_mail'=>'','t_mail_chk'=>'','t_pass_now'=>'','t_pass_edit'=>'','t_pass_chk'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

?>

<?php /*
<section class="pt0">
	<h2><a class="link-out accordion acc-open" href="#">CLポイント残高</a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner">
		<p class="point-num"><span><?php echo number_format($aTeacher['ttPoint']); ?></span> CLポイント</p>
		<p>あなたの利用プランは「<span class="font-green"><?php echo $aTeacher['ptName']; ?></span>」です。</p>
		<p>あなたが現在実施中の講義は「<span class="font-green"><?php echo $iActCnt; ?>講義</span>」、終了した講義は「<?php echo $iClsCnt; ?>講義」です。</p>
		<p>毎月の消費CLポイントは、<span class="font-green"><?php echo number_format($iActPay); ?>（<?php echo number_format($aTeacher['ptPrice']).'×'.$iActCnt; ?>）</span>CLポイントです。</p>

		<?php if ($iLack): ?>
			<p>あなたが来月（<?php echo $sNextMonth; ?>）講義を継続するためには、あと <span class="font-red"><?php echo number_format($iLack); ?></span> CLポイントが必要です。</p>
			<p><?php echo $sNextMonth; ?>1日 0:00までにポイントのチャージが無い場合は、<span class="font-red">作成日の古い<?php echo $iStop; ?>講義
			<?php for($i = 0; $i < $iStop; $i++): ?>「<?php echo $aActClass[$i]['ctName']; ?>」<?php endfor; ?>
			</span>が「終了した講義」になります。</p>
			<p class="recital">※「終了した講義」は、学生からの閲覧ができなくなります。</p>
			<p class="recital">※ポイントをチャージすることで、いつでも「終了した講義」を「実施中の講義」にできます。</p>
		<?php else: ?>
			<p>あなたが来月（<?php echo $sNextMonth; ?>）講義を継続した場合、<span class="font-green"><?php echo number_format($iRest); ?></span> CLポイントが残ります。</p>
		<?php endif ?>
	</div>
	</div>
</section>
*/ ?>

<section class="pt0">
	<h2><a class="link-out accordion acc-open" href="#">契約情報の入力</a></h2>

	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner">
		<p class="error-box mb16" style="display: none;" id="mathPointErr"></p>
		<?php echo Form::open(array('action'=>'/t/payment/estimatecreate','method'=>'post','id'=>'charge-form','data'=>(int)$aTeacher['ptPrice']."|".(int)$aTeacher['ttPoint']."|".CL_TAX_RATE."|".CL_PT_BLINE)) ; ?>
		<p class="font-grey">① 契約する講義数を選択してください。</p>

		<p class="select-box select-box-advance-arrow select-box-na z-index-19" id="class-num" style="margin-left: 4px;">
			<select placeholder="0">
			<?php for($i = 0; $i <= 20; $i++): ?>
				<?php $sSelect = ($i == 0)? ' selected':''; ?>
				<option value="<?php echo $i; ?>"<?php echo $sSelect; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
			</select>
		</p>

		<div class="mt8" style="margin-left: 16px;">
		<p class="recital">※契約期間は半期（6ヶ月）となります。</p>
		<p class="recital mt0">※1講義 1,000円/月（税抜）として計算されます。</p>
		<p class="recital mt0">※講義数とは別にID基本使用料として、半期分 6,000円（税抜）がご購入金額に加算されます。</p>
		</div>

		<table class="ppframe mt16 mb16">
		<tr>
<?php /*
			<th>CLポイント</th>
			<th></th>
*/ ?>
			<th>ご購入金額</th>
		</tr>
		<tr>
<?php /*
			<td><input type="text" name="point" value="<?php echo $aPPSet['pt']; ?>" class="" maxlength="6"></td>
			<td class=""><i class="fa fa-exchange fa-3x"></i></td>
*/ ?>
			<td><input type="text" name="price" value="<?php echo $aPPSet['pr']; ?>" class="" maxlength="6" readonly="readonly"> <span>円（税抜）</span><br><span id="exp" class="font-grey">（0 × 1,000 × 6）＋ 6,000</span></td>
		</tr>
		</table>
		<input type="hidden" name="point" value="<?php echo $aPPSet['pt']; ?>" class="" maxlength="6">
<?php /*
		<p>チャージ後のCLポイント</p>
		<p class="point-num mb0"><span id="sum-pt"><?php echo number_format($aTeacher['ttPoint']); ?></span> CLポイント</p>
		<p class="adjust-style font-grey">※ <?php echo $aTeacher['ptName']; ?> で、</p>
		<p class="select-box select-box-advance-arrow select-box-na z-index-19">
			<select placeholder="<?php echo $iActCnt; ?>" id="class-num">
			<?php for($i = 1; $i <= 20; $i++): ?>
				<?php $sSelect = ($i == $iActCnt)? ' selected':''; ?>
				<option value="<?php echo $i; ?>"<?php echo $sSelect; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
			</select>
		</p>
		<p class="adjust-style font-grey"> 講義を、<span class="font-green" id="pay-range"><?php echo $sRange; ?></span>実施可能</p>
*/ ?>
		<p>ご請求金額</p>
		<p class="billing-num"><span id="sum-pay">0</span> 円（税込）</p>
		<hr class="mt32">
		<p class="font-grey">② お支払い方法を選択してください。</p>
		<div class="radio-lists mt16">
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
