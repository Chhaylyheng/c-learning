<?php
/*------------------------------------------------------------------------------------------------------
 * ※メンテナンスモード
 *
 * 「CL_MAINTE」を「true」にするとメンテナンスモードが有効になります。
 * メンテナンスモード有効中は全てのページへのアクセスがメンテ画面にリダイレクトされます。
 *
 * 「$sMainte = <<< EOM」から「EOM;」までの間に記入したテキストがメンテナンス画面に表示されます。
 * テキストにはHTMLが入力可能です。
 *
 * メンテナンスモードで表示されるテキストを通常時に確認したい場合は、以下のURLにアクセスしてください。
 * /mainte/preview
 *------------------------------------------------------------------------------------------------------*/
define('CL_MAINTE',false);

$sMainte = <<< EOM
<div>
メンテナンス終了日時は<span style="font-size: 120%; color: #CC0000">3月18日 21:30頃</span>の予定です。
</div>
EOM;

define('CL_MAINTE_MSG',$sMainte);
/*------------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------------
 * ※トラブルニュース
 *
 * 「CL_TROUBLE」を未来に設定するとトラブルニュースが有効になります。（YYYY-MM-DD HH:MM）
 * 指定の時間を過ぎるとニュースは消えます。
 *
 * 「$sTrouble = <<< EOM」から「EOM;」までの間に記入したテキストが先生トップ画面と事務局トップ画面に表示されます。
 *------------------------------------------------------------------------------------------------------*/
define('CL_TROUBLE','2017-10-10 18:00');

$sTrouble = <<< EOM
本日1時26分よりハードウェアの故障に伴いC-Learningのシステムが停止しておりましたが 7時37分に復旧いたしました。ご迷惑をお掛けしました。
EOM;

define('CL_TROUBLE_NEWS',$sTrouble);
/*------------------------------------------------------------------------------------------------------*/

define('CL_ENV','PRODUCTION');

define('CL_DOMAIN','kit.c-learning.jp');
define('CL_MAIL_FROM','noreply@' . CL_DOMAIN);
define('CL_SITENAME','C-Learning');
define('CL_MAIL_SENDER','C-Learning');
define('CL_INFOMAIL','air-support@c-learning.jp');
define('CL_KEIYAKUMAIL','keiyaku@netman.co.jp');
define('CL_MIYATAMAIL','wkomatsu@netman.co.jp');

define('CL_OILPATH', '/home/clkit/oil');

define('CL_PDF_PREFIX','cl');

# バージョン情報
define('CL_V','v1.540');
define('CL_VERSION',CL_SITENAME.' '.CL_V);
define('CL_MAILCOPY','Copyright NETMAN Co.,Ltd. All rights reserved.');
define('CL_COPYRIGHT','<span>'.CL_VERSION.'</span><span>'.CL_MAILCOPY.'</span>');
define('CL_MB_COPYRIGHT',CL_VERSION.'<br />(C)NETMAN');

define('CL_SEP','|+_*|');
define('CL_PROTOCOL','https');
define('CL_MBPROTOCOL','http');

define('CL_URL',CL_PROTOCOL.'://'.CL_DOMAIN);
define('CL_MBURL',CL_MBPROTOCOL.'://'.CL_DOMAIN);
define('CL_ENC','UTF-8');

define('CL_DEV_PC',3);
define('CL_DEV_SP',2);
define('CL_DEV_MB',1);

define('CL_MAPS_KEY','AIzaSyAD4qV-14Q5QzcD7iDHkGF22_ljwYxrB20');
define('CL_MAP_URL',CL_PROTOCOL.'://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&language=ja');

define('CL_DATETIME_DEFAULT','0000-00-00 00:00:00');

define('CL_FILESIZE',2048);
define('CL_IMGSIZE',3);
define('CL_EXT_PTN','/^(jpe?g|png|gif)$/i');
define('CL_WHITE_TRIM_PTN','/^[ 　]*(.*?)[ 　]*$/u');

define('CL_UPDIR','upload');
define('CL_UPPATH',DOCROOT.CL_UPDIR);

define('CL_FILEDIR','file');
define('CL_FILEPATH',DOCROOT.CL_FILEDIR);

define('CL_Q_SMALL_PREFIX','small_');
define('CL_Q_IMG_QUALITY',92);
define('CL_Q_SMALL_QUALITY',85);
define('CL_Q_IMG_SIZE',800);
define('CL_Q_SMALL_SIZE',240);

define('CL_TITLE_LENGTH', 40);
define('CL_FREE_DAYS', 30);

define('CL_PRE_TIME', 12);

define('CL_FA_VERSION', '4.7.0');

define('CL_ANDROID_API_KEY', 'AIzaSyB45WpIXVMOhfHStcEQrjtKFo9QxYJbQWc');
define('CL_ANDROID_API_KEY_T', 'AIzaSyB45WpIXVMOhfHStcEQrjtKFo9QxYJbQWc');
define('CL_APPLE_PUSH_MODE', 0); # 0:本番、1:テスト

define('CL_CAREERTASU_MODE', false);
define('CL_CORPORATE_MODE', true);

define('CL_CLASSCODE',6);

$gaFunction = array(
	 1 => '出席管理',
	 2 => 'アンケート',
	 4 => '協働板',
	 8 => '教材倉庫',
	16 => '小テスト',
	32 => 'ドリル',
);

$gaBilling = array(
	1 => 'クレジットカード',
	2 => '銀行振込',
);

$gaQuickTitle = array(
	'22' => 'はい/いいえ',
	'23' => 'はい/いいえ※',
	'24' => '賛成/反対',
	'25' => '賛成/反対※',
	'20' => '二択',
	'21' => '二択※',
	'30' => '三択',
	'31' => '三択※',
	'40' => '四択',
	'41' => '四択※',
	'50' => '五択',
	'51' => '五択※',
	'1'  => 'コメントのみ',
);
if (CL_CAREERTASU_MODE)
{
	$gaQuickTitle['100'] = '満足度アンケート';
}

$gaReportRate = array(
	'1' => 'A',
	'2' => 'B',
	'3' => 'C',
	'4' => 'D',
	'5' => 'F',
);

define('CL_TAX_RATE', 0.08);
define('CL_PT_BLINE', 100);

# 決済エラー系（会員＆取引）
define('CL_PG_ERR_EXIST',1);				# データが存在している
define('CL_PG_ERR_NOMEMBER',2);			# 会員が存在しない
define('CL_PG_ERR_UNSETMEMBER',4);	# 会員IDが指定してない
define('CL_PG_ERR_NOCARD',8);				# カードが存在していない
define('CL_PG_ERR_NOENTRY',16);			# 取引情報が存在しない
# 決済エラー系（カード決済）
define('CL_PG_ERR_C_LACK',32);			# カード残高不足
define('CL_PG_ERR_C_LIMIT',64);			# カード限度額オーバー
define('CL_PG_ERR_C_NUMBER',128);		# カード番号不備
define('CL_PG_ERR_C_TIME',256);			# カード有効期限不備
define('CL_PG_ERR_C_SEQCODE',512);	# カードセキュリティコード不備
define('CL_PG_ERR_C_FAILD',1024);		# カード利用不可
# 決済エラー系（システム）
define('CL_PG_ERR_S_PCODE',2048);		# 商品コード不備
define('CL_PG_ERR_S_PRICE',4096);		# 金額不備
define('CL_PG_ERR_S_TAX',8192);			# 税送料不備
define('CL_PG_ERR_S_ETC',16384);		# その他エラー

# モバイルデバイス
define('CL_MD_PC',0);
define('CL_MD_DOCOMO',1);
define('CL_MD_SOFTBANK',2);
define('CL_MD_AU',3);
define('CL_MD_ETC',4);

# AWS(S3,Elastic Transcoder)
define('CL_AWS_REGION','ap-northeast-1');
define('CL_AWS_KEY','AKIAJGZZPYJZYQ2J2X4Q');
define('CL_AWS_SECRET','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');
define('CL_AWS_BUCKET','cl-airtest');
define('CL_AWS_SYSPATH','kit'.DS);
define('CL_AWS_PIPELINE','1464918984117-zp517k');
#define('CL_AWS_PRESETID','1351620000001-000010'); # Generic 720p (mp4)
define('CL_AWS_PRESETID','1472611764718-ieyxd1'); # Generic 720p br500kbps(mp4)
define('CL_AWS_ENCEXT','.mp4');

# S3(Direct Link)
define('CL_AWS_DIRECT', true);

# S3 File Prefix
define('CL_PREFIX_THUMBNAIL','_thumb_');
define('CL_PREFIX_THUMBNAIL2','_thumb2_');
define('CL_PREFIX_ENCODE','_encode_');

# DROPBOX連携関連
define("CL_DROPBOX_URL","https://www.dropbox.com/static/api/2/dropins.js");
define("CL_DROPBOX_APPKEY","sfbl7bdz0o2nu4z");

# Asset追加パス
Asset::add_path('assets/docs/', 'docs');

# トラッキングタグ（2017/03/24）
define('CL_TRACKING_TAG',true);

