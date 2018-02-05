<?php echo \View::forge('email/html_header', array('title'=>'[CL]副担当パスワードの再設定')); ?>

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
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center"><?php echo CL_SITENAME; ?>をご利用いただきありがとうございます。<br>以下のボタンを押して、パスワードを再設定してください。</p></div>
</div>
<!--[if mso]></td></tr></table><![endif]-->



<div align="center" class="button-container center" style="Margin-right: 10px;Margin-left: 10px;">
    <div style="line-height:10px;font-size:1px">&nbsp;</div>
  <a href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/a/password/reset/<?php echo $reset_hash; ?>" target="_blank" style="color: #ffffff; text-decoration: none;">
    <!--[if mso]>
      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/a/password/reset/<?php echo $reset_hash; ?>" style="height:50px; v-text-anchor:middle; width:314px;" arcsize="10%" strokecolor="#0E408A" fillcolor="#0E408A" >
      <w:anchorlock/><center style="color:#ffffff; font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size:20px;">
    <![endif]-->
    <!--[if !mso]><!-->
    <div style="color: #ffffff; background-color: #0E408A; border-radius: 5px; -webkit-border-radius: 5px; -moz-border-radius: 5px; max-width: 294px; width: 60%; border-top: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; padding-top: 5px; padding-right: 30px; padding-bottom: 5px; padding-left: 30px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: center;">
    <!--<![endif]-->
      <span style="font-size:16px;line-height:32px;"><strong><span style="font-size: 20px; line-height: 40px;" data-mce-style="font-size: 20px; line-height: 40px;">パスワードの再設定</span></strong></span>
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
  <a href="<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/a/password/reset/<?php echo $reset_hash; ?>" target="_blank"><?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/a/password/reset/<?php echo $reset_hash; ?></a>
  </div>

    <div style="line-height:10px;font-size:1px">&nbsp;</div>
</div>



                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center">上記の再設定ページへアクセスしていただくことにより、このメールがお客様に到達したとみなさせていただきます。</p></div>
</div>
<!--[if mso]></td></tr></table><![endif]-->



                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
<div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center">再設定ページはメールの送信から24時間有効です。<br>期限を過ぎた場合は、改めて再設定メールの送信を行ってください。</p></div>
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
