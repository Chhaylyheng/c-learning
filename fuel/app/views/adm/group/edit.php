<?php
	$errClass = array('gt_name'=>'','gt_prefix'=>'');
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' input-error';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '登録';
	$sAction = 'create';
	$bPrefix = true;
	if (isset($aGroup)):
		$sSubBtn = '更新';
		$sAction = 'edit/'.$aGroup['gtID'];

		if ($aGroup['gtCNum'] > 0):
			$bPrefix = false;
		endif;
	endif;
?>


<div class="info-box">
<form action="/adm/group/<?php echo $sAction; ?>" method="post">
	<p class="mt0 text-right"><sup>*</sup>は必須項目</p>
	<div class="formControl">
		<div class="formGroup">
			<div class="formLabel">団体名称<sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="gt_name" value="<?php echo $gt_name; ?>" maxlength="30" placeholder="団体名称を入力してください" class="width-24em text-left<?php echo $errClass['gt_name']; ?>">
				<?php echo $errMsg['gt_name']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">接頭辞<sup>*</sup></div>
			<div class="formContent inline-box">
<?php if ($bPrefix): ?>
				<input type="text" name="gt_prefix" value="<?php echo $gt_prefix; ?>" maxlength="3" placeholder="講義コードの接頭辞を入力してください。" class="width-24em text-left<?php echo $errClass['gt_prefix']; ?>">
				<p class="mt4 font-gray">※接頭辞は団体内で作成される講義コードに自動的に付与される3文字の識別文字です。半角の英数で指定可能です。</p>
				<?php echo $errMsg['gt_prefix']; ?>
<?php else: ?>
				<p class="font-blue font-size-120 font-bold"><?php echo $gt_prefix; ?></p>
				<p class="mt4 font-gray">※既に講義が登録されているため、接頭辞を変更することはできません。</p>
<?php endif; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel">LDAP連携<sup>*</sup></div>
			<div class="formContent inline-box">
				<?php $sDisp = ($gt_ldap)? 'block':'none'; ?>
				<p><label><input type="checkbox" name="gt_ldap" value="1" autocomplete="off"<?php echo ($gt_ldap)? ' checked':''?>>LDAP連携を利用する</label></p>
				<div class="LDAPSetting mt8" style="display: <?php echo $sDisp; ?>">
					<div class="mt4">
						<p class="font-bold">プロトコル</p>
						<select name="gt_l_protocol" class="dropdown font-default">
							<option value="LDAP"<?php echo ($gt_l_protocol == 'LDAP')? ' selected':''?>>LDAP</option>
							<option value="LDAPS"<?php echo ($gt_l_protocol == 'LDAPS')? ' selected':''?>>LDAPS</option>
						</select>
					</div>
					<div class="mt4">
						<p class="font-bold">サーバー（例：air.c-learning.jp）</p>
						<input type="text" name="gt_l_server" value="<?php echo $gt_l_server; ?>" class="width-24em">
					</div>
					<div class="mt4">
						<p class="font-bold">ポート番号（0 は 規定値）</p>
						<input type="text" name="gt_l_port" value="<?php echo $gt_l_port; ?>" maxlength="5" class="width-6em text-right" style="padding: 8px 8px;">
					</div>
					<div class="mt4">
						<p class="font-bold">バインドする識別名 (-D,binddn)</p>
						<input type="text" name="gt_l_dn" value="<?php echo $gt_l_dn; ?>" class="width-36em">
						<p class="mt4 font-gray font-size-80">例：[USER]@air.c-learning.jp、uid=[USER],dc=air,dc=c-learning,dc=jp</p>
						<p class="mt0 font-gray font-size-80">※[USER]にはログインIDが挿入されます。</p>
					</div>
					<div class="mt4">
						<p class="font-bold">検索の開始位置 (-b,searchbase)</p>
						<input type="text" name="gt_l_sb" value="<?php echo $gt_l_sb; ?>" class="width-36em">
						<p class="mt4 font-gray font-size-80">例：ou=KEIYAKU-KANRI,o=NETMAN,dc=air,dc=c-learning,dc=jp</p>
					</div>
					<div class="mt4">
						<p class="font-bold">個人関連情報属性名称</p>
						<select name="gt_l_uid" class="dropdown font-default">
							<option value="uid"<?php echo ($gt_l_uid == 'uid')? ' selected':''?>>uid</option>
							<option value="cn"<?php echo ($gt_l_uid == 'cn')? ' selected':''?>>cn</option>
						</select>
					</div>

					<div class="mt4">
						<p class="font-bold">ldapsearchコマンド</p>
						<p class="mt4 font-green LDAPCommand font-size-80">ldapsearch -x -LLL -H "<?php echo $gt_l_protocol; ?>://<?php echo $gt_l_server; ?><?php echo ($gt_l_port > 0)? ':'.$gt_l_port:''; ?>/" -D "<?php echo $gt_l_dn; ?>" -w [PASSWORD] -b "<?php echo $gt_l_sb; ?>" "<?php echo $gt_l_uid; ?>=[USER]"</p>
					</div>

				</div>
			</div>
		</div>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubBtn; ?></button>
	</div>
</form>
</div>
