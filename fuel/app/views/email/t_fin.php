───────────────────
　ようこそ、<?php echo CL_SITENAME; ?> へ
───────────────────

先生アカウントの登録が完了しました！

※本メールは、<?php echo CL_SITENAME; ?>で先生登録をしていただいた方にお送りしています。

<?php echo $aTeacher['ttName']; ?> 様、
この度は<?php echo CL_SITENAME; ?>へのご登録ありがとうございます。
先生アカウントの登録が完了いたしましたのでご連絡いたします。

************************************************************************
氏名：<?php echo $aTeacher['ttName']."\n"; ?>
メールアドレス：<?php echo $aTeacher['ttMail']."\n"; ?>
パスワード：（セキュリティのために表示されません）

所属：<?php echo $aTeacher['cmName']."\n"; ?>
　　：<?php echo $aTeacher['ttDept']."\n"; ?>
　　：<?php echo $aTeacher['ttSubject']."\n"; ?>

講義名：<?php echo $aClass['ctName']."\n"; ?>
講義コード：<?php echo $aClass['ctCode']."\n"; ?>

************************************************************************

メールアドレスとご登録いただきましたパスワードを入力することで<?php echo CL_SITENAME; ?>をご利用いただけます。

今すぐ<?php echo CL_SITENAME; ?>をご利用される方はこちらから

■<?php echo CL_SITENAME; ?> 先生ログイン用URL
<?php echo CL_PROTOCOL; ?>://<?php echo CL_DOMAIN; ?>/t

※本メールは、<?php echo CL_SITENAME; ?>で先生登録をしていただいた方にお送りしています。

ご不明な点、あるいは本メールにお心当たりのない方は、
大変お手数ですが、下記連絡先までご連絡いただきますようお願い申し上げます。
------------------------------------------------------------------------------------
MAIL：<?php echo CL_INFOMAIL; ?>

------------------------------------------------------------------------------------

※本メールの送信元は送信専用となっており、このメールに返信されてもメールは届きません。
<?php echo CL_MAILCOPY; ?>
