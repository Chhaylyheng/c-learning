<?php echo \View::forge('email/html_header', array('title'=>'[CL]副担当登録メール')); ?>

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
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="line-height: 16px; font-size: 14px;"><?php echo $aTeacher['ttName']; ?>さんにより<br><?php echo $aClass['ctName'].'【'.$aClass['ctCode'].'】'; ?>の副担当に設定されました。</span><br></p></div>
</div>
<!--[if mso]></td></tr></table><![endif]-->



<div style="font-size: 16px;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: center;">
<div class="our-class">
	<table cellpadding="4" style="margin: 5px auto; border-collapse: collapse; border: 1px solid #555; color: #444; font-size: 16px;">
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">氏名</th>
		<td style="text-align: left; border: 1px solid #555;"><?php echo $aAssist['atName']; ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">メールアドレス</th>
		<td style="text-align: left;border: 1px solid #555;"><?php echo $aAssist['atMail']; ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">パスワード</th>
		<td style="text-align: left;border: 1px solid #555;"><?php echo ($aAssist['atFirst'])? $aAssist['atFirst'].'（初期パスワードですので、初回ログイン時に変更してください）':'********（変更済みのパスワードでログインできます。）'; ?></td>
	</tr>
	<tr>
		<th style="text-align: center; color: #fff; background-color: #888; border: 1px solid #555;">担当する講義</th>
		<td style="text-align: left;border: 1px solid #555;"><?php echo $aClass['ctName']; ?>【<?php echo $aClass['ctCode']; ?>】</td>
	</tr>
	</table>
</div>
</div>


                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="line-height: 16px; font-size: 14px;">副担当機能を利用するには、<br>以下の「ログイン」ボタンよりログインしてください。<br>メールアドレスとパスワードを入力することで<br><?php echo CL_SITENAME; ?>をご利用いただけます。</span></p></div>
</div>
<!--[if mso]></td></tr></table><![endif]-->


<div align="center" class="button-container center" style="Margin-right: 10px;Margin-left: 10px;">
    <div style="line-height:10px;font-size:1px">&nbsp;</div>
  <a href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/a" target="_blank" style="color: #ffffff; text-decoration: none;">
    <!--[if mso]>
      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/a" style="height:50px; v-text-anchor:middle; width:314px;" arcsize="10%" strokecolor="#0E408A" fillcolor="#0E408A" >
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
  <a href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/a" target="_blank"><?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/a</a>
  </div>

    <div style="line-height:10px;font-size:1px">&nbsp;</div>
</div>


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