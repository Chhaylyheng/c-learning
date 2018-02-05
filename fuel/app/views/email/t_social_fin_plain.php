───────────────────
　ようこそ、<?php echo CL_SITENAME; ?> へ
───────────────────

先生アカウントの登録が完了しました！

<?php echo $aTeacher['ttName']; ?> 様、
この度は<?php echo CL_SITENAME; ?>へのご登録ありがとうございます。
先生アカウントの登録が完了いたしましたのでご連絡いたします。

************************************************************************
氏名：<?php echo $aTeacher['ttName']."\n"; ?>
メールアドレス：<?php echo $aTeacher['ttMail']."\n"; ?>
パスワード：（セキュリティのため、表示されません）
連携されたアカウント：<?php echo $aTeacher['provider']."\n"; ?>

電話番号：<?php echo ($aTeacher['ttSTel'])? $aTeacher['ttSTel']."\n":''; ?>
電話による初期サポート：<?php echo (($aTeacher['ttTelSupport'])? '希望する':'希望しない')."\n"; ?>

所属：<?php echo ($aTeacher['cmName'])? $aTeacher['cmName']."\n":''; ?>
<?php echo ($aTeacher['ttDept'])? '　　：'.$aTeacher['ttDept']."\n":''; ?>
<?php echo ($aTeacher['ttSubject'])? '　　：'.$aTeacher['ttSubject']."\n":''; ?>

講義名：<?php echo $aClass['ctName']."\n"; ?>
講義コード：<?php echo $aClass['ctCode']."\n"; ?>

************************************************************************

今すぐ<?php echo CL_SITENAME; ?>をご利用される方はこちらから。

■先生ログイン用URL
<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/t

連携されたアカウント以外にも、メールアドレスとご登録いただきましたパスワードを入力することで<?php echo CL_SITENAME; ?>をご利用いただけます。

ログインの方法、詳しい登録手順について、下記から手順書をダウンロードできます。
http://bit.ly/2ulBLiS

※本メールは、<?php echo CL_SITENAME; ?>で先生登録をしていただいた方にお送りしています。
※虚偽の申請や競合調査等の場合に関してはID発行を差し控えさせていただくことがありますことをご了承下さい。

ご不明な点、あるいは本メールにお心当たりのない方は、
大変お手数ですが、下記連絡先までご連絡いただきますようお願い申し上げます。
------------------------------------------------------------------------------------
MAIL：<?php echo CL_INFOMAIL; ?>

------------------------------------------------------------------------------------

※本メールの送信元は送信専用となっており、このメールに返信されてもメールは届きません。
<?php echo CL_MAILCOPY; ?>
