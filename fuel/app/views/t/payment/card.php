<?php if (isset($error['estimate_error'])): ?>
<p class="error-box"><?php echo $error['bill_error']; ?></p>
<?php endif; ?>

<?php
	$sTitle = "クレジットカード情報の新規登録";
	$sBtn = '登録';
	$aDisp = array("none","block");
	if (!is_null($aCardInfo)):
		$aDisp = array("block","none");
		$sTitle = "クレジットカード情報の更新";
		$sBtn = '更新';
	endif;
?>

<div class="info-box tmp mb16" style="display: none;" id="purchaseInfo">
	<p></p>
	<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png',array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
</div>

<div class="info-box">
	<h2>ご購入情報（見積番号：<?php echo $aE['eNO']; ?>）</h2>
	<hr class="mb0">
	<div class="info-box estimate-input mt0">
<?php /*
		<h2 class="mt0">ご購入CLポイント</h2>
		<p class="billing-num"><span><?php echo number_format($aE['point']); ?></span> ポイント</p>
*/ ?>
		<h2>お支払合計金額</h2>
		<p class="billing-num"><span id="sum-pay"><?php echo number_format(floor($aE['ePrice']*(1+$aE['eTax']))); ?></span> 円（税込）</p>
	</div>
	<hr class="mb0">

	<div class="info-box estimate-input mt0" style="display: <?php echo $aDisp[0]; ?>;" id="cardinfo">
		<p class="error-box mb16" style="display: none;" id="purchaseErr"></p>
		<div class="info-box tmp mb16 mt0" style="display: none;" id="cardInfo">
			<p></p>
			<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png',array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
		</div>
		<?php echo Form::open(array('action'=>'','method'=>'post','id'=>'Purchase','data'=>$aTeacher['ttID'])) ; ?>
		<h2>カード番号</h2>
		<p id="cardNumber" class="billing-num"><span style="color: #6485fa;"><?php echo wordwrap($aCardInfo['CardNo'],4,'-',true); ?></span></p>
		<h2>カード有効期限</h2>
		<p id="cardExpire" class="billing-num"><span style="color: #6485fa;"><?php echo substr($aCardInfo['Expire'],2).'/\''.substr($aCardInfo['Expire'],0,2); ?></span></p>
		<h2><?php echo CL_SITENAME; ?>のログインパスワード</h2>
		<p><input type="password" name="passwd" size="30" maxlength="32" value=""></p>
		<p class="subtxt mt0">※セキュリティのため、<?php echo CL_SITENAME; ?>のログインパスワードをご入力ください。</p>
		<p class="button-box mt32"><button type="submit" class="button register do" name="sub_state" value="1">このカードで購入する</button></p>
		<p class="button-box mt16"><button type="button" class="button na confirm CardEdit">カード情報を変更する</button></p>
		<input type="hidden" name="eNO" value="<?php echo $aE['eNO']; ?>">
		<?php echo Form::close(); ?>
	</div>

	<div class="info-box estimate-input mt0" style="display: <?php echo $aDisp[1]; ?>;" id="cardinput">
		<h2 class="mb16"><?php echo $sTitle; ?></h2>
		<p>※利用できるカード種別は以下となります。<br><?php echo Asset::img('card_logo.png'); ?></p>
		<p class="error-box mb16" style="display: none;" id="cardErr"></p>
		<?php echo Form::open(array('action'=>'','method'=>'post','id'=>'AddCard','data'=>$aTeacher['ttID'])); ?>
		<h2>カード番号</h2>
		<p><input type="text" name="card_number" size="30" maxlength="16" value=""></p>
		<p class="subtxt mt0">※スペース・ハイフンを入れずに半角数字でご入力ください。</p>
		<h2>カード有効期限</h2>
		<p class="select-box  select-box-advance-arrow select-box-na">
			<select name="card_month" placeholder="--">
				<option value="">--</option>
			<?php for($i=1;$i<=12;$i++): ?>
				<option value="<?php echo sprintf('%02d',$i); ?>"><?php echo sprintf('%02d',$i); ?></option>
			<?php endfor; ?>
			</select>
		</p>
		<p class="adjust-style"> 月 / </p>
		<p class="select-box select-box-advance-arrow select-box-na">
			<select name="card_year" placeholder="----">
				<option value="">----</option>
			<?php for($i=date('Y');$i<=(date('Y') + 6);$i++): ?>
				<option value="<?php echo substr($i,2); ?>"><?php echo $i; ?></option>
			<?php endfor; ?>
			</select>
		</p>
		<p class="adjust-style"> 年</p>
		<h2>セキュリティコード</h2>
		<p><input type="password" name="card_seqcode" size="30" maxlength="4" value="" style="width: 8em; text-align: center;"></p>
		<p class="subtxt mt0">※カード裏面に印字されている末尾3桁の数字がセキュリティコードです。</p>
		<p class="button-box" style="margin-top: 150px;"><button type="button" class="button register do CardSave">カード情報を<?php echo $sBtn; ?>する</button></p>
		<?php echo Form::close(); ?>
	</div>

	<div class="info-box estimate-input mt0" style="display: none;" id="purchaseinfo">
		<p class="button-box mt16"><a href="/t/payment" class="button register cancel">見積・購入履歴に戻る</a></p>
	</div>
</div>

