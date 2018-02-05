<!-- hs[<?php echo Cookie::get('CL_TL_HASH'); ?>] -->

<h1>
	<i class="fa fa-sign-in fa-fw"></i> <?php echo $sTitle; ?>
	<p class="font-size-60 mt8"><?php echo $sLogined; ?></p>
</h1>

<?php
/*
	if ($aTeacher['gtID'] == ''):
		$diff = \Clfunc_Common::dateDiff($aTeacher['coTermDate'], date('Y-m-d'));
		if ($diff >= -10 || !$aTeacher['coTermDate']):
			if (!$aTeacher['coTermDate']):
				$err = __('現在契約されていません。:siteを利用する場合は、「購入手続き」より契約を行ってください。', array('site'=>CL_SITENAME));
			elseif ($diff == 0):
				$err = __('本日が契約終了日です。明日以降も:siteを利用する場合は、「購入手続き」より契約を行ってください。', array('site'=>CL_SITENAME));
			else:
				$err = __('契約終了まで、あと:num日です。継続して:siteを利用する場合は、契約終了までに「購入手続き」より契約を行ってください。', array('num'=>($diff * -1),'site'=>CL_SITENAME));
			endif;
?>

<div class="mt16 info-box font-white" style="background-color: #880000; border: 3px solid #cc0000;">
<p class="font-size-120"><i class="fa fa-exclamation-circle"></i> <?php echo $err; ?></p>
<p class="mt4"><a href="/t/payment/product" class="button na default width-auto"><?php echo __('購入手続き'); ?></a></p>
</div>

<?php endif; ?>
<?php endif; */?>

<?php if (!$aTeacher['ttMailAuth'] && $bT): ?>

<div class="mt16 info-box font-white" style="background-color: #880000; border: 3px solid #cc0000;">
<p class="font-size-120"><i class="fa fa-exclamation-circle"></i> <?php echo __('メールアドレスの認証が完了していません。'); ?></p>
<p class="mt4"><a href="/t/profile/mailauth" class="button na default width-auto"><?php echo __('メールアドレス認証メールを再送信する'); ?></a></p>
</div>

<?php endif; ?>

<?php if (!is_null($aReport) && $bT): ?>
<div class="mt16">
	<div class="info-box">
	<?php foreach ($aReport as $aR): ?>
		<?php
			$sRName = '（'.__(':year年度',array('year'=>$aR['krYear'])).' '.(($aR['krPeriod'] == 1)? __('4～9月期'):__('10～3月期')).'）';
			$sNew = ($aR['new'])? '<span class="attention attn-emp">NEW</span>':'';
			$sPutTitle = __('ケータイ研レポート').$sRName.__('回答一覧');
			if ($aR['krPublic'] == 1):
		?>
		<p><a href="/t/kreport/put/<?php echo $aR['krYear'].DS.$aR['krPeriod']; ?>" class="link-out" style="padding: 12px;"><?php echo $sPutTitle; ?><?php echo $sNew; ?></a></p>
		<?php else: ?>
		<div class="mt8" style="display: inline-block;"><a href="/t/kreport/put/<?php echo $aR['krYear'].DS.$aR['krPeriod']; ?>" class="button default na width-auto font-size-90" style="padding: 4px 8px;"><?php echo $sPutTitle; ?><?php echo $sNew; ?></a></div>
		<?php endif; ?>
	<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<?php $bAssist = (!$aTeacher['gtID'] || ($aTeacher['gtID'] && (!$aGroup['gtLDAP'] || $aGroup['gtLAssistant'])))? true:false; ?>
<?php $iColW = ($aTeacher['gtID'] && (!$aGroup['gtLDAP'] || $aGroup['gtLAssistant']))? '30':'40'; ?>

<?php $iColW = ($bT)? $iColW:'50'; ?>

<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr>
				<th class="width-<?php echo $iColW; ?>"><?php echo __('講義名'); ?></th>
				<th class="width-10"><?php echo __('講義コード'); ?></th>
				<th class="width-20"><?php echo __('期/曜日/時限'); ?></th>
				<th class="width-10"><?php echo __('履修人数'); ?></th>
				<?php if ($bT): ?>
					<?php if ($aTeacher['gtID']): ?>
					<th class="width-10"><?php echo __('先生'); ?></th>
					<?php endif; ?>
					<?php if ($bAssist): ?>
					<th class="width-10"><?php echo __('副担当'); ?></th>
					<?php endif; ?>
				<?php endif; ?>
				<th class="width-10"><?php echo __('操作'); ?></th></tr>
		</thead>
		<tbody>
			<?php
				if (!is_null($aActClass)):
					$iMax = count($aActClass);
					foreach ($aActClass as $i => $aC):
						$sMaster = '';
						$aSort = array(' ',' ');

						if ($bT):
							$sColor = ($aC['tpMaster'])? 'do':'confirm';
							if ($aC['tpSort'] == $iMax):
								$aSort[0] = ' disabled="disabled"';
							endif;
							if ($aC['tpSort'] == 1):
								$aSort[1] = ' disabled="disabled"';
							endif;
						else:
							$sColor = 'do';
							if ($aC['apSort'] == $iMax):
								$aSort[0] = ' disabled="disabled"';
							endif;
							if ($aC['apSort'] == 1):
								$aSort[1] = ' disabled="disabled"';
							endif;
						endif;
						if ($aTeacher['gtID'] && $bT)
						{
							$sTpNum = ($aC['tpNum'])? ' ['.__('他:num名',array('num'=>$aC['tpNum'])).']':'';
							if (isset($aMasters[$aC['ctID']]))
							{
								$sMaster = $aMasters[$aC['ctID']]['ttName'];
							}
						}
						$sJsKey = $aC['ctID'];
						$sPeriod = __(':year年度',array('year'=>$aC['ctYear']));
						$sPeriod .= ($aC['dpNO'])? ' '.$aPeriod[$aC['dpNO']]:'';
						$sPeriod .= ($aC['ctWeekDay'])? '/'.$aWeekDay[$aC['ctWeekDay']]:'';
						$sPeriod .= ($aC['dhNO'])? '/'.$aHour[$aC['dhNO']]:'';

						$iUn = 0;
						if (isset($aUnread[$aC['ctID']])):
							$iUn = (int)$aUnread[$aC['ctID']];
						endif;
				?>
				<tr>
					<td>
						<a href="/t/class/index/<?php echo $aC['ctID']; ?>" class="text-left button na width-100 font-size-110 <?php echo $sColor; ?>" style="padding: 8px;" id="<?php echo (!$i)? 'FirstCourse':''; ?>"><?php echo $aC['ctName'].(($iUn)? '<span class="attention  attn-emp">'.$iUn.'</span>':''); ?></a>
					</td>
					<td>
						<?php echo \Clfunc_Common::getCode($aC['ctCode']); ?>
					</td>

					<td class="">
						<?php echo $sPeriod; ?>
					</td>

					<td class="">
						<a href="/t/class/index/<?php echo $aC['ctID']; ?>/student" class="button na default width-auto" style="padding: 8px;"><?php echo __(':num名',array('num'=>$aC['scNum'])); ?></a>
					</td>

					<?php if ($bT): ?>
						<?php if ($aTeacher['gtID']): ?>
							<td class="">
							<?php if ($aC['tpNum']): ?>
							<a href="/t/class/teacher/<?php echo $aC['ctID']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo $sMaster.$sTpNum; ?></a>
							<?php else: ?>
							<?php echo $sMaster; ?>
							<?php endif; ?>
							</td>
						<?php endif; ?>
						<?php if ($bAssist): ?>
							<td class="">
							<?php $sAs = (isset($aAPos[$aC['ctID']]))? $aAssists[$aAPos[$aC['ctID']]['atID']]['atName']:__('未設定'); ?>
							<?php if ($aC['tpMaster']): ?>
							<button class="button na default width-auto AssistantDialogShow" style="padding: 8px;" id="<?php echo $aC['ctID']; ?>_assist" state="<?php echo (int)(isset($aAPos[$aC['ctID']])); ?>"><?php echo $sAs; ?></button>
							<?php else: ?>
							<span><?php echo $sAs; ?></span>
							<?php endif; ?>
							</td>
						<?php endif; ?>
					<?php endif; ?>

					<td class="">
						<button<?php echo $aSort[0]; ?> class="ClassSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_active_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
						<button<?php echo $aSort[1]; ?> class="ClassSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_active_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
					</td>

				</tr>
				<?php endforeach;?>
			<?php endif; ?>
		</tbody>
		</table>
	</div>
	<p class="mb4 ml8">
<?php if ((is_null($aGroup) || !($aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_CLASS)) && $bT): ?>
		<a href="/t/class/create" class="button na default width-auto" style="padding: 8px;"><?php echo __('新しい講義を作成'); ?></a>
<?php endif; ?>
<?php if ((!is_null($aGroup)) || $aTeacher['ptID'] >= 0): ?>
		<a href="/t/index/close" class="button na default width-auto" style="padding: 8px;"><?php echo __('終了した講義'); ?>
<?php endif; ?>
<?php if (!$bAssist): ?>
			<span class="attention attn-default font-size-80" style="padding: 2px 6px;"><?php echo $aTeacher['ttCloseNum']; ?></span>
<?php endif; ?>
		</a>
	</p>
</div>

<?php if ($aCreateKey): ?>
<script type="text/javascript">
$(function() {
	$(window).on('load', function() {
		shadowMask('on');
		var ccf = $('#ClassCreateFinish');
		$('body').append(ccf);
		var ccfTop = (($(window).height()-ccf.outerHeight())/2 - FixedSize);
		var ccfLeft = (($(window).width()-ccf.outerWidth())/2);

		ccfTop = (ccfTop < (FixedSize * -1))? (FixedSize - 8) * -1:ccfTop;
		ccfLeft = (ccfLeft < 0)? 0:ccfLeft;

		ccf.css({
			'top': ccfTop+'px',
			'left': ccfLeft+'px'
		});
		ccf.show();
	});

	$('.CCFClose').on('click', function() {
		var ccf = $('#ClassCreateFinish');
		ccf.hide();
		shadowMask('off');
		ccf.remove();
	});
});


</script>

<div id="ClassCreateFinish" style="position: absolute; z-index: 160; display: none;">

<?php if (is_null($aGroup) || !($aGroup['gtTeacherAuthFlag'] & \Clfunc_Flag::T_AUTH_STUDY)): ?>
<div class="info-box">
	<h2 class="text-center font-blue font-size-140 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-check-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('講義を作成しました！'); ?></h2>

	<div class="mt16 text-center">
		<?php echo __('講義コード'); ?> <span class="font-red font-size-240">【<?php echo \Clfunc_Common::getCode($aCreateKey[1]); ?>】</span>
	</div>

	<div class="mt16 text-center font-size-140">
		<?php echo __('学生に講義コードを伝え、履修登録させてください。'); ?>
	</div>

<?php if (!$aGroup['gtLDAP']): ?>
	<div class="button-box mt16 text-center">
		<a class="button confirm width-auto" href="<?php echo Asset::get_file(CL_PDF_PREFIX.'_student_entry_manual.pdf', 'docs'); ?>" target="_blank"><?php echo __('学生に配付する資料をダウンロード'); ?></a>
	</div>

	<p class="mt16 font-green text-center"><i class="fa fa-question-circle"></i> <?php echo __('資料のダウンロードや学生の履修は後からでもできます。'); ?></p>

	<hr>

	<h3 class="font-size-120 ml8 mt16"><i class="fa fa-caret-down"></i> <?php echo __('学生の履修方法'); ?></h3>
	<ol class="ml28 mt4 mb8">
		<li><?php echo __('配布用資料をダウンロードする。'); ?></li>
		<li><?php echo __('印刷して、学生に配布する。'); ?></li>
		<li><?php echo __('講義コード【:code】を学生に伝える。', array('code'=>\Clfunc_Common::getCode($aCreateKey[1]))); ?></li>
		<li><?php echo __('学生は手持ちのスマートフォンや携帯電話で、:siteに登録して講義に履修する。', array('site'=>CL_SITENAME)); ?></li>
	</ol>
<?php endif; ?>
</div>
<?php endif; ?>

<div class="info-box mt4">

	<h2 class="text-center font-green font-size-140 mb8 font-bold" style="line-height: 50px;"><i class="fa fa-info-circle fa-2x va-top" style="line-height: 50px;"></i> <?php echo __('学生登録なしで、すぐにアンケートをとるならコチラ！'); ?></h2>

	<div class="button-box mt16 text-center">
		<a class="button confirm width-auto" href="/print/t/GuestLogin/<?php echo $aCreateKey[0]; ?>" target="_blank"><?php echo __('ゲスト回答で配布する資料をダウンロード'); ?></a>
	</div>

	<hr>

	<h3 class="font-size-120 ml8 mt16"><i class="fa fa-caret-down"></i> <?php echo __('アンケートでゲスト回答を行う方法'); ?></h3>

	<p style="overflow: hidden;"><img src="<?php echo Asset::get_file((($sLang == 'en')? 'guest_dropdown_en.png':'guest_dropdown.png'), 'img'); ?>" style="float: left; margin-right: 1em;"><?php echo __('TutoText07'); ?></p>

</div>

<div class="info-box text-center mt4" style="overflow: hidden;">
	<button type="button" class="button do na width-auto CCFClose" style="padding: 9px 16px;"><?php echo __('閉じる'); ?></button>
</div>

</div>

<?php endif; ?>


<div id="AssistantDialog" style="position: absolute; z-index: 160; display: none;">
<div class="info-box">

<h2><?php echo __('副担当設定'); ?><span class="cTitle"></span></h2>

<div class="mt16 mb16">
<form action="#" method="post" id="AssistNewRegist">
	<input type="hidden" name="ctid" value="">
	<p class="bold"><?php echo __('新規登録'); ?></p>
	<p class="error-msg line-height-13 mt4 mb4" style="display: none;"></p>
	<div class="formControl font-size-90" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel" style="border: none;"><?php echo __('氏名'); ?></div>
			<div class="formContent inline-box" style="border: none;">
				<input type="text" name="a_name" value="" maxlength="20" class="width-100 text-left">
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('メールアドレス'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="a_mail" value="" maxlength="255" class="width-100 text-left">
				<p class="mt4" style="max-width: 320px;"><?php echo __('指定のメールアドレス宛てに、副担当ログイン用パスワードを送付します。'); ?></p>
			</div>
		</div>
	</div>
	<div class="text-center mt8">
		<button type="submit" class="button na do width-auto" style="padding: 8px;" name="sub_state" value="1"><?php echo __('登録する'); ?></button>
	</div>
</form>
</div>

<div class="mt16 mb16 AssistListBox" style="display: block;">
<hr>
<form action="#" method="post" id="AssistListSet">
	<input type="hidden" name="ctid" value="">
	<p class="bold"><?php echo __('リストから選択'); ?></p>
	<select name="a_list" class="dropdown">
<option value="0"><?php echo __('副担当を選択してください'); ?></option>
<?php if (!is_null($aAssists)): ?>
<?php foreach ($aAssists as $sA => $aA): ?>
<option value="<?php echo $sA; ?>"><?php echo $aA['atName'].'（'.$aA['atMail'].'）'; ?></option>
<?php endforeach; ?>
<?php endif; ?>
	</select>
	<div class="text-center mt8">
		<button type="submit" class="button na do width-auto" style="padding: 8px;" name="sub_state" value="1"><?php echo __('設定する'); ?></button>
	</div>
</form>
</div>

<div class="mt16 mb16 AssistDeleteBox" style="display: block;">
<hr>
<form action="#" method="post" id="AssistDelete">
	<input type="hidden" name="ctid" value="">
	<p class="bold"><?php echo __('講義から削除'); ?></p>
	<div class="text-center mt8">
		<button type="submit" class="button na cancel width-auto" style="padding: 8px;" name="sub_state" value="1"><?php echo __('副担当を削除する'); ?></button>
	</div>
</form>
</div>

</div>

<div class="info-box text-center mt4" style="overflow: hidden;">
	<button type="button" class="button default na width-auto AssistDialogClose" style="padding: 8px;"><?php echo __('閉じる'); ?></button>
</div>

</div>


<?php if ($aTeacher['ttMail'] == 'rito@netman.co.jp' || $aTeacher['ttMail'] == 'todokanai09@rsn.ne.jp'): ?>
<div class="font-size-90"><?php echo nl2br(print_r(Cookie::get(),true)); ?></div>
<div>
<button type="button" class="button na default width-auto" onClick="jumpLang('ja');return false;">日本語へ</button>
<button type="button" class="button na default width-auto" onClick="jumpLang('en');return false;">英語へ</button>
</div>
<?php endif; ?>
