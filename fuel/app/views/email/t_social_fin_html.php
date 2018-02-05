<?php echo \View::forge('email/html_header', array('title'=>'[CL]先生アカウント登録手続き完了のお知らせ')); ?>

    <div style="background-color:transparent;">
      <div style="Margin: 0 auto;min-width: 320px;max-width: 500px;width: 500px;width: calc(19000% - 98300px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid ">
        <div style="border-collapse: collapse;display: table;width: 100%;">
          <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 500px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

              <!--[if (mso)|(IE)]><td align="center" width="500" style=" width:500px; padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
            <div class="col num12" style="min-width: 320px;max-width: 500px;width: 500px;width: calc(18000% - 89500px);background-color: transparent;">
              <div style="background-color: transparent; width: 100% !important;">
              <!--[if (!mso)&(!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;"><!--<![endif]-->


                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center"><span style="font-size: 16px; line-height: 19px;"><strong><span style="color: rgb(51, 102, 255); line-height: 19px; font-size: 16px;">ようこそ、<?php echo CL_SITENAME; ?>へ</span></strong></span></p><p style="margin: 0;font-size: 14px;line-height: 16px;text-align: center"><span style="font-size: 16px; line-height: 19px;"><strong><span style="color: rgb(51, 102, 255); line-height: 19px; font-size: 16px;">先生アカウントの登録が完了しました。<span style="line-height: 19px; font-size: 16px;">﻿</span></span></strong></span><br></p></div>
</div>
<!--[if mso]></td></tr></table><![endif]-->



                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="line-height: 16px; font-size: 14px;">※<?php echo $aTeacher['ttName']; ?>様、</span></p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="line-height: 16px; font-size: 14px;">この度は<?php echo CL_SITENAME; ?>へのご登録ありがとうございます。</span></p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="line-height: 16px; font-size: 14px;">先生アカウントの登録が完了いたしましたのでご連絡いたします。</span><span style="font-size: 14px; line-height: 16px;"></span><br></p></div>
</div>
<!--[if mso]></td></tr></table><![endif]-->



<div style="font-size: 16px;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: center;">
<div class="our-class">
	<table cellpadding="4" style="margin: 5px auto; border-collapse: collapse; border: 1px solid #555; color: #444; font-size: 16px;">
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">氏名</th>
		<td style="text-align: left; border: 1px solid #555;"><?php echo $aTeacher['ttName']; ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">メールアドレス</th>
		<td style="text-align: left;border: 1px solid #555;"><?php echo $aTeacher['ttMail']; ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">パスワード</th>
		<td style="text-align: left;border: 1px solid #555;">（セキュリティのため、表示されません）</td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">連携されたアカウント</th>
		<td style="text-align: left;border: 1px solid #555;"><?php echo $aTeacher['provider']; ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">電話番号</th>
		<td style="text-align: left; border: 1px solid #555;"><?php echo $aTeacher['ttSTel']; ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">電話による初期サポート</th>
		<td style="text-align: left; border: 1px solid #555;"><?php echo (($aTeacher['ttTelSupport'])? '希望する':'希望しない'); ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">所属</th>
		<td style="text-align: left;border: 1px solid #555;">
			<?php echo ($aTeacher['cmName'])? $aTeacher['cmName'].'<br>':''; ?>
			<?php echo ($aTeacher['ttDept'])? $aTeacher['ttDept'].'<br>':''; ?>
			<?php echo ($aTeacher['ttSubject'])? $aTeacher['ttSubject'].'<br>':''; ?>
		</td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">講義名</th>
		<td style="text-align: left;border: 1px solid #555;"><?php echo $aClass['ctName']; ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">講義コード</th>
		<td style="text-align: left;border: 1px solid #555;"><?php echo $aClass['ctCode']; ?></td>
	</tr>
	</table>
</div>
</div>


<div align="center" class="button-container center" style="Margin-right: 10px;Margin-left: 10px;">
    <div style="line-height:10px;font-size:1px">&nbsp;</div>
  <a href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/t" target="_blank" style="color: #ffffff; text-decoration: none;">
    <!--[if mso]>
      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/t" style="height:50px; v-text-anchor:middle; width:314px;" arcsize="10%" strokecolor="#0E408A" fillcolor="#0E408A" >
      <w:anchorlock/><center style="color:#ffffff; font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size:20px;">
    <![endif]-->
    <!--[if !mso]><!-->
    <div style="color: #ffffff; background-color: #0E408A; border-radius: 5px; -webkit-border-radius: 5px; -moz-border-radius: 5px; max-width: 294px; width: 60%; border-top: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; padding-top: 5px; padding-right: 30px; padding-bottom: 5px; padding-left: 30px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: center;">
    <!--<![endif]-->
      <span style="font-size:16px;line-height:32px;"><strong><span style="font-size: 20px; line-height: 40px;" data-mce-style="font-size: 20px; line-height: 40px;">ログイン</span></strong></span>
    <!--[if !mso]><!-->
    </div>
    <!--<![endif]-->
    <!--[if mso]>
          </center>
      </v:roundrect>
    <![endif]-->
  </a>

  <div style="margin-top: 10px;font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
  ※ボタンが反応しない場合は以下のURLをクリックしてください。<br>
  <a href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/t" target="_blank"><?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/t</a>
  </div>

    <div style="line-height:10px;font-size:1px">&nbsp;</div>
</div>



                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><span style="line-height: 16px; font-size: 14px;">連携されたアカウント以外にも、メールアドレスとご登録いただきましたパスワードを入力することで<?php echo CL_SITENAME; ?>をご利用いただけます。</span></div>
</div>

<!--[if mso]></td></tr></table><![endif]-->


                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
	<p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center">
		<span style="line-height: 16px; font-size: 14px;">ログインの方法、詳しい登録手順について、下記から手順書をダウンロードできます。</span><br>
		<span style="line-height: 16px; font-size: 14px;"><a href="http://bit.ly/2ulBLiS">手順書のダウンロード</a></span><br>
	</p>
	</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->


                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="line-height: 16px; font-size: 14px;">※本メールは、<?php echo CL_SITENAME; ?>で先生登録をしていただいた方にお送りしています。</span><span style="font-size: 14px; line-height: 16px;"></span><br></p></div>
</div>

<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><span style="line-height: 16px; font-size: 14px;">※虚偽の申請や競合調査等の場合に関してはID発行を差し控えさせていただくことがありますことをご了承下さい。</span></div>
</div>

<!--[if mso]></td></tr></table><![endif]-->



                    <div style="padding-right: 0px; padding-left: 0px; padding-top: 5px; padding-bottom: 5px;">
  <!--[if (mso)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px;padding-left: 0px; padding-top: 5px; padding-bottom: 5px;"><table width="100%" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
  <div align="center"><div style="border-top: 1px solid #BBBBBB; width:100%;">&nbsp;</div></div>
  <!--[if (mso)]></td></tr></table></td></tr></table><![endif]-->
</div>

              <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
              </div>
            </div>
          <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
        </div>
      </div>
    </div>

<?php echo \View::forge('email/html_footer'); ?>
