<?php echo $aT['cmName']; ?>

<?php echo ($aT['ttName'] != '')? $aT['ttName']:$aT['ttMail']; ?> 様

この度は<?php echo CL_SITENAME; ?>をご利用いただきまして、誠にありがとうございます。

銀行振込による購入手続きが完了しました。
購入履歴より、請求書、見積書、納品書を発行することが可能です。
郵送をご希望の場合は、事務手数料（1,080円）が発生しますが、
契約管理センターにて承りますので、keiyaku@netman.co.jp にご連絡下さい。

請求金額：<?php echo number_format($aE['ePrice']*(1+$aE['eTax'])); ?>円（税込）
支払期日：<?php echo date('Y年n月t日',strtotime('+1month')); ?>


<?php echo $sMailOpt; ?>


銀行振込における情報は以下になります。
************************************************************************
三菱東京UFJ銀行　八重洲通支店
（普）0052840
株式会社ネットマン
************************************************************************
※振込手数料はご負担下さい。

ご不明な点、あるいは本メールにお心当たりのない方は、
大変お手数ですが、下記連絡先までご連絡いただきますようお願い申し上げます。
------------------------------------------------------------------------------------
MAIL：<?php echo CL_INFOMAIL; ?>

------------------------------------------------------------------------------------

※本メールの送信元は送信専用となっており、このメールに返信されてもメールは届きません。
<?php echo CL_MAILCOPY; ?>
