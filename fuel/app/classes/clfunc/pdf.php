<?php
class ClFunc_Pdf
{
	# 画像ファイルパス
	public static $sImg  = '/assets/img/';
	public static $sLogo = 'logo.png';
	public static $sSeal = 'pdf_seal.png';
	public static $sCopy = 'pdf_copy.png';

	private static function baseDocument(&$pdf,$title,$number,$date)
	{
		# セキュリティ
		$pdf->SetProtection(array('copy','modify'));

		# 基本情報
		$pdf->SetCreator('株式会社ネットマン');
		$pdf->SetAuthor(CL_SITENAME);
		$pdf->SetTitle($title);
		$pdf->SetSubject('');
		# ヘッダ
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		# マージン
		$pdf->SetMargins(0,0,0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);

		# ページブレイク
		$pdf->SetAutoPageBreak(false, 0);

		# フォント
		$pdf->SetFont('msgothic','',12);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetTextColor(0,0,0);

		# 色の設定
		$pdf->SetDrawColor(174,174,174);
		$pdf->SetFillColor(242,242,242);

		# ページ追加
		$pdf->AddPage();

		# 背景と基本情報設定
		$pdf->MultiCell(0,297,'',0,'L',1);
		$pdf->SetXY(10,33);
		$pdf->SetFillColor(255,255,255);
		$pdf->MultiCell(190,256,'',0,'L',1);
		$pdf->Image(self::$sImg.self::$sLogo,75,16,60,'','png');
		$pdf->Image(self::$sImg.self::$sCopy,18,273,29,'','png');
		$pdf->SetXY(184,279);
		$pdf->SetFontSize(10);
		$pdf->Cell(0,0,'以上',0,0,'L',false);

		# タイトル
		$pdf->SetXY(10,37);
		$pdf->SetFontSize(20);
		$pdf->MultiCell(190,0,$title,0,'C',0,0);

		# 見積番号と日付
		$pdf->SetXY(10,48);
		$pdf->SetFont('msgothic','B',12);
		$sNumDate = 'No：'.$number."\n".'日付：'.$date;
		$pdf->MultiCell(180,0,$sNumDate,0,'R',false,0);

		# 発行者
		$pdf->SetXY(10,61);
		$pdf->SetFont('msgothic','',10);
		$sAddress = '〒104-0032'."\n".'東京都中央区八丁堀2-2-4 第6高輪ビル'."\n".'TEL：03-3523-5100'."\n".'株式会社ネットマン';
		$pdf->MultiCell(180,0,$sAddress,0,'R',false,0);

		# 社判
		$pdf->Image(self::$sImg.self::$sSeal,170,79,20,'','png');
	}


	public static function createEstimatePDF($sTtID,$data,$price,$bNew = true,$bSave = true)
	{
		$iF = 0;
		global $gaBilling;

		# PDFオブジェクト
		$pdf = new Tcpdf_Tcpdf('P', 'mm', 'A4', true, 'UTF-8', false);
		# 基本構造生成
		self::baseDocument($pdf, CL_SITENAME.' お見積書', $data['number'], $data['pubdate']);

		$pdf->SetFillColor(255, 200, 200);

		# 宛名
		$pdf->SetXY(25,99);
		$pdf->SetLineStyle(array('width'=>0.4));
		$pdf->SetFont('msgothic','',18);
		$pdf->MultiCell(146,13,$data['sendto'],'B','C',$iF,0,'','',true,0,false,false,13,'M');
		$pdf->MultiCell(14,13,'様','B','C',$iF,1,'','',true,0,false,false,13,'M');

		# 下記の通り御見積申し上げます。
		$pdf->SetXY(10,119);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(190,0,'下記の通り御見積申し上げます。',0,'C',$iF,1);

		# 要件
		$pdf->SetXY(45,128);
		$pdf->SetLineStyle(array('width'=>0.2));
		$pdf->SetFont('msgothic','',9);
		# 支払方法
		$pdf->SetX(45);
		$pdf->MultiCell(50,0,'支払方法','TB','R',$iF,0);
		$pdf->MultiCell(5,0,'','TB','C',$iF,0);
		$sBilling = $gaBilling[$data['billing']];
		if ($data['billing'] == 2)
		{
			$sBilling .= '※1';
		}
		$pdf->MultiCell(65,0,$sBilling,'TB','L',$iF,1);
		# 支払期日
		$pdf->SetX(45);
		$pdf->MultiCell(50,0,'支払期日','B','R',$iF,0);
		$pdf->MultiCell(5,0,'','B','C',$iF,0);
		$pdf->MultiCell(65,0,'購入手続き完了日の翌月末まで','B','L',$iF,1);
		# 有効期限
		$pdf->SetX(45);
		$pdf->MultiCell(50,0,'有効期限','B','R',$iF,0);
		$pdf->MultiCell(5,0,'','B','C',$iF,0);
		$pdf->MultiCell(65,0,'発行日より30日間','B','L',$iF,1);

		$sum = number_format(floor($price*(1+$data['tax_rate'])));

		# 金額
		$pdf->SetXY(25,144);
		$pdf->SetLineStyle(array('width'=>0.4));
		$pdf->SetFont('msgothic','',14);
		$pdf->MultiCell(55,13,'御見積金額','B','R',$iF,0,'','',true,0,false,false,13,'M');
		$pdf->SetFont('mspgothic','',26);
		$pdf->MultiCell(60,13,'￥'.$sum,'B','C',$iF,0,'','',true,0,false,false,13,'M');
		$pdf->SetFont('msgothic','',14);
		$pdf->MultiCell(45,13,'（税込）','B','L',$iF,1,'','',true,0,false,false,13,'M');

		# 内訳
		$pdf->SetXY(20,164);
		$pdf->SetLineStyle(array('width'=>0.1));
		$pdf->SetFont('msgothic','',10);
		# 内訳ヘッダ
		$pdf->MultiCell(60,0,'品 名','TBR','C',$iF,0);
		$pdf->MultiCell(35,0,'単 価','TBR','C',$iF,0);
		$pdf->MultiCell(25,0,'数 量','TBR','C',$iF,0);
		$pdf->MultiCell(50,0,'金 額','TB','C',$iF,1);
		# 内容
		if (is_array($data['dname']))
		{
			$pdf->SetFont('msgothic','',10);
			foreach ($data['dname'] as $i => $v)
			{
				$pdf->SetX(20);
				$pdf->MultiCell(60,9,$v,0,'L',$iF,0,'','',true,0,false,false,9,'M');
				$pdf->MultiCell(35,9,'￥'.number_format($data['dprice'][$i]),0,'R',$iF,0,'','',true,0,false,false,9,'M');
				$pdf->MultiCell(25,9,$data['dnum'][$i].$data['dunit'][$i],0,'R',$iF,0,'','',true,0,false,false,9,'M');
				$pdf->MultiCell(50,9,'￥'.number_format($data['dprice'][$i]*$data['dnum'][$i]),0,'R',$iF,1,'','',true,0,false,false,9,'M');
			}
		}
		# 合計（税抜）
		$pdf->SetX(20);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(120,7,'合計金額（税抜）','TBR','R',$iF,0,'','',true,0,false,false,7,'M');
		$pdf->SetFont('msgothic','',12);
		$pdf->MultiCell(50,7,'￥'.number_format($price),'TB','R',$iF,1,'','',true,0,false,false,7,'M');
		# 合計（税込）
		$pdf->SetX(20);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(120,7,'合計金額（税込）','BR','R',$iF,0,'','',true,0,false,false,7,'M');
		$pdf->SetFont('msgothic','',12);
		$pdf->MultiCell(50,7,'￥'.$sum,'B','R',$iF,1,'','',true,0,false,false,7,'M');

		$sNote = null;
		if ($data['billing'] == 2)
		{
			$sNote = '※1 振込手数料はお客さまのご負担となります。';
		}

		if (!is_null($sNote))
		{
			$pdf->SetX(20);
			$pdf->SetFont('msgothic','',9);
			$pdf->MultiCell(0,8,$sNote,0,'L',$iF,1,'','',true,0,false,false,8,'M');
		}

		if ($bSave)
		{
			$sFileName = ($bNew)? $data['number'].'.pdf':$data['number'].'-T.pdf';
			$sFilePath = CL_FILEPATH.DS.$sTtID.DS.'payment_pdf';
			\Clfunc_Common::chkDir($sFilePath);
			$pdf->Output($sFilePath.DS.$sFileName,'F');
		}
		else
		{
			$pdf->Output('cl_estimate_temp.pdf', 'I');
			exit();
		}
		return;
	}


	public static function createBillPDF($sTtID,$sendto,$aE,$bSave = true)
	{
		$iF = 0;
		global $gaBilling;

		# PDFオブジェクト
		$pdf = new Tcpdf_Tcpdf('P', 'mm', 'A4', true, 'UTF-8', false);
		# 基本構造生成
		self::baseDocument($pdf, CL_SITENAME.' 御請求書', $aE['bNO'], date("Y年n月j日",strtotime($aE['bPubDate'])));

		$pdf->SetFillColor(255, 200, 200);

		# 宛名
		$pdf->SetXY(25,99);
		$pdf->SetLineStyle(array('width'=>0.4));
		$pdf->SetFont('msgothic','',18);
		$pdf->MultiCell(146,13,$sendto,'B','C',$iF,0,'','',true,0,false,false,13,'M');
		$pdf->MultiCell(14,13,'様','B','C',$iF,1,'','',true,0,false,false,13,'M');

		# 下記の通り御請求申し上げます。
		$pdf->SetXY(10,119);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(190,0,'下記の通り御請求申し上げます。',0,'C',$iF,1);

		# 要件
		$pdf->SetXY(45,128);
		$pdf->SetLineStyle(array('width'=>0.2));
		$pdf->SetFont('msgothic','',9);

		switch ($aE['eBilling'])
		{
			case 1:
			case 4:
				# 支払期日
				$pdf->SetX(45);
				$pdf->MultiCell(50,0,'支払方法','TB','R',$iF,0);
				$pdf->MultiCell(5,0,'','TB','C',$iF,0);
				$pdf->MultiCell(65,0,$gaBilling[$aE['eBilling']],'TB','L',$iF,1);

				$iY = 140;
			break;
			case 2:
				# 支払期日
				$pdf->SetX(45);
				$pdf->MultiCell(50,0,'支払期日','TB','R',$iF,0);
				$pdf->MultiCell(5,0,'','TB','C',$iF,0);
				$pdf->MultiCell(65,0,date('Y年n月t日',strtotime('+1month',strtotime($aE['bPubDate']))),'TB','L',$iF,1);
				# 口座情報
				$pdf->SetX(45);
				$iH = 13;
				$pdf->MultiCell(50,$iH,'お振込先','B','R',$iF,0,'','',true,0,false,true,$iH,'M');
				$pdf->MultiCell(5,$iH,'','B','C',$iF,0);
				$sText = '三菱東京UFJ銀行 八重洲通支店'."\n".'（普）0052840　株式会社ネットマン'."\n".'※振込手数料はご負担ください。';
				$pdf->MultiCell(65,$iH,$sText,'B','L',$iF,1,'','',true,0,false,true,$iH,'M');

				$iY = 150;
			break;
		}

		$sum = number_format(floor($aE['ePrice']*(1+$aE['eTax'])));

		# 金額
		$pdf->SetXY(25,$iY);
		$pdf->SetLineStyle(array('width'=>0.4));
		$pdf->SetFont('msgothic','',14);
		$pdf->MultiCell(55,13,'御請求金額','B','R',$iF,0,'','',true,0,false,false,13,'M');
		$pdf->SetFont('mspgothic','',26);
		$pdf->MultiCell(60,13,'￥'.$sum,'B','C',$iF,0,'','',true,0,false,false,13,'M');
		$pdf->SetFont('msgothic','',14);
		$pdf->MultiCell(45,13,'（税込）','B','L',$iF,1,'','',true,0,false,false,13,'M');

		$data = unserialize($aE['eDetail']);
		# 内訳
		$pdf->SetXY(20,$iY+20);
		$pdf->SetLineStyle(array('width'=>0.1));
		$pdf->SetFont('msgothic','',10);
		# 内訳ヘッダ
		$pdf->MultiCell(60,0,'品 名','TBR','C',$iF,0);
		$pdf->MultiCell(35,0,'単 価','TBR','C',$iF,0);
		$pdf->MultiCell(25,0,'数 量','TBR','C',$iF,0);
		$pdf->MultiCell(50,0,'金 額','TB','C',$iF,1);
		# 内容
		if (is_array($data['dname']))
		{
			$pdf->SetFont('msgothic','',10);
			foreach ($data['dname'] as $i => $v)
			{
				$pdf->SetX(20);
				$pdf->MultiCell(60,9,$v,0,'L',$iF,0,'','',true,0,false,false,9,'M');
				$pdf->MultiCell(35,9,'￥'.number_format($data['dprice'][$i]),0,'R',$iF,0,'','',true,0,false,false,9,'M');
				$pdf->MultiCell(25,9,$data['dnum'][$i].$data['dunit'][$i],0,'R',$iF,0,'','',true,0,false,false,9,'M');
				$pdf->MultiCell(50,9,'￥'.number_format($data['dprice'][$i]*$data['dnum'][$i]),0,'R',$iF,1,'','',true,0,false,false,9,'M');
			}
		}
		# 合計（税抜）
		$pdf->SetX(20);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(120,7,'合計金額（税抜）','TBR','R',$iF,0,'','',true,0,false,false,7,'M');
		$pdf->SetFont('msgothic','',12);
		$pdf->MultiCell(50,7,'￥'.number_format($aE['ePrice']),'TB','R',$iF,1,'','',true,0,false,false,7,'M');
		# 合計（税込）
		$pdf->SetX(20);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(120,7,'合計金額（税込）','BR','R',$iF,0,'','',true,0,false,false,7,'M');
		$pdf->SetFont('msgothic','',12);
		$pdf->MultiCell(50,7,'￥'.$sum,'B','R',$iF,1,'','',true,0,false,false,7,'M');

		if ($bSave)
		{
			$sFileName = $aE['bNO'].'.pdf';
			$sFilePath = CL_FILEPATH.DS.$sTtID.DS.'payment_pdf';
			\Clfunc_Common::chkDir($sFilePath);
			$pdf->Output($sFilePath.DS.$sFileName,'F');
		}
		else
		{
			$pdf->Output('cl_bill_temp.pdf', 'I');
			exit();
		}
		return;
	}


	public static function createReceiptPDF($sTtID,$aIn,$aE,$bSave = true)
	{
		$iF = 0;
		global $gaBilling;

		$sTitle = ($aE['rNum'] == 2)? '領収書':'領収書（再発行）';

		# PDFオブジェクト
		$pdf = new Tcpdf_Tcpdf('P', 'mm', 'A4', true, 'UTF-8', false);
		# 基本構造生成
		self::baseDocument($pdf,$sTitle,$aE['bNO'],date("Y年n月j日",strtotime($aE['bPayDate'])));

		$pdf->SetFillColor(255, 200, 200);

		# 下記金額正に領収しました。
		$pdf->SetXY(10,110);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(190,0,'下記金額正に領収しました。',0,'C',$iF,1);

		$sum = number_format(floor($aE['ePrice']*(1+$aE['eTax'])));
		$tax = number_format(floor($aE['ePrice']*$aE['eTax']));

		$iH = 20;
		# 宛名
		$pdf->SetXY(25,127);
		$pdf->SetLineStyle(array('width'=>0.4));
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(14,$iH,'宛名','TB','C',$iF,0,'','',true,0,false,false,$iH,'M');
		$pdf->SetFont('msgothic','',14);
		$pdf->MultiCell(132,$iH,$aIn['sendto'],'TB','C',$iF,0,'','',true,0,false,false,$iH,'M');
		$pdf->SetFont('msgothic','',14);
		$pdf->MultiCell(14,$iH,'様','TB','C',$iF,1,'','',true,0,false,false,$iH,'M');
		# 金額
		$pdf->SetX(25);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(14,$iH+5,'金額','','C',$iF,1,'','',true,0,false,false,$iH+5,'M');
		# 但書
		$pdf->SetX(25);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(14,$iH,'但し','TB','C',$iF,0,'','',true,0,false,false,$iH,'M');
		$pdf->SetFont('msgothic','',14);
		$pdf->MultiCell(132,$iH,$aIn['note'],'TB','C',$iF,0,'','',true,0,false,false,$iH,'M');
		$pdf->MultiCell(14,$iH,'','TB','C',$iF,1,'','',true,0,false,false,$iH,'M');
		# 金額
		$pdf->SetXY(25+14,153);
		$pdf->SetFont('msgothic','',18);
		$pdf->MultiCell(132,0,'￥'.$sum,'','C',$iF,0,'','',true,0,false,false,0,'M');
		# 消費税
		$pdf->SetXY(25+14,162);
		$pdf->SetFont('msgothic','',10);
		$pdf->MultiCell(132,0,'（内、消費税等　￥'.$tax.'）','','C',$iF,0,'','',true,0,false,false,0,'M');

		# 本紙は電子的に保持している領収データをPDFファイルとして出力したものです
		$pdf->SetXY(20,270);
		$pdf->SetFont('msgothic','',6);
		$pdf->MultiCell(180,0,'本紙は電子的に保持している領収データをPDFファイルとして出力したものです',0,'L',$iF,1);

		if ($bSave)
		{
			$sFileName = $aE['bNO'].'-R.pdf';
			$sFilePath = CL_FILEPATH.DS.$sTtID.DS.'payment_pdf';
			\Clfunc_Common::chkDir($sFilePath);
			$pdf->Output($sFilePath.DS.$sFileName,'F');
		}
		else
		{
			$pdf->Output('cl_receipt_temp.pdf', 'I');
			exit();
		}
		return;
	}


	public static function createLicensePDF($sTtID,$aIn,$aE,$aCon,$aPlan,$bSave = true)
	{
		$fY = 0;
		$iF = 0;
		global $gaBilling;
		$aP = unserialize($aE['purchase']);

		$sTitle = '『'.CL_SITENAME.'』ライセンス証明書(納品書)';

		# PDFオブジェクト
		$pdf = new Tcpdf_Tcpdf('P', 'mm', 'A4', true, 'UTF-8', false);

		# セキュリティ
		$pdf->SetProtection(array('copy','modify'));

		# 基本情報
		$pdf->SetCreator('株式会社ネットマン');
		$pdf->SetAuthor(CL_SITENAME);
		$pdf->SetTitle($sTitle);
		$pdf->SetSubject('');
		# ヘッダ
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		# マージン
		$pdf->SetMargins(20,0,20);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);

		# ページブレイク
		$pdf->SetAutoPageBreak(false, 0);

		# フォント
		$pdf->SetFont('msgothic','',12);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetTextColor(0,0,0);

		# ページ追加
		$pdf->AddPage();

		$pdf->SetFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFontSize(20);

		# タイトル設定
		$pdf->SetY(8);
		$pdf->MultiCell(170,16,$sTitle,0,'C',1,1,'','',true,0,false,false,16,'M');

		$fY = $pdf->GetY();
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('msgothic','B',14);

		# 宛名
		$pdf->Text(20,$fY+3,$aIn['org'],false,false,true,0,1,'L');

		$fY = $pdf->GetY();

		$pdf->Text(25,$fY,$aIn['name'].'　様',false,false,true,0,1,'L');

		$fY = $pdf->GetY();

		$pdf->SetFont('msmincho','',10);

		# 日付
		$pdf->SetXY(150,$fY-6);
		$pdf->MultiCell(40,0,'日付：'.$aIn['pubdate'],'B','C',0,1);

		$fY = $pdf->GetY();

		# 発行者
		$pdf->SetXY(20,$fY+5);
		$pdf->SetFont('msmincho','',10);
		$sAddress = "〒104-0032\n東京都中央区八丁堀2-2-4 第6高輪ビル\nTEL：03-3523-5100/FAX:03-3523-5200";
		$pdf->MultiCell(170,0,$sAddress,0,'R',0,1);

		$pdf->SetFont('msmincho','B',13);
		$sCorpName = '株式会社ネットマン';
		$pdf->MultiCell(170,0,$sCorpName,0,'R',0,1);

		$fY = $pdf->GetY();

		# 社判
		$pdf->Image(self::$sImg.self::$sSeal,170,$fY,20,'','png');

		$fY = $pdf->GetY();

		$pdf->SetFont('msgothic','',10.5);

		# 文章
		$pdf->SetXY(40,$fY+9);
		$sText = "このたびは".CL_SITENAME."をお申込いただき、\n誠にありがとうございます。\n株式会社ネットマンは、お客様に対し、「".CL_SITENAME."利用規約」に基づき\n「".CL_SITENAME."」の使用を許諾します。\nこの開始通知書の内容は重要な情報です。紛失なさらぬように保管いただきますよう、\nよろしくお願いいたします。";
		$pdf->MultiCell(170,0,$sText,0,'L',0,1);

		$fY = $pdf->GetY();

		$pdf->SetFillColor(0,0,128);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetDrawColor(0,0,128);
		$pdf->SetFont('msgothic','B',14);

		# サービス情報表
		$pdf->SetXY(20,$fY+4);

		$pdf->MultiCell(170,0,'サービス情報',1,'C',1,1);

		$pdf->SetFillColor(0,0,0);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('msgothic','',11);

		# 団体名
		$pdf->MultiCell(45,9,'団体名',1,'C',0,0,'','',true,0,false,false,9,'M');
		$pdf->MultiCell(125,9,$aIn['org'],1,'C',0,1,'','',true,0,false,false,9,'M');

		# 団体名
		$pdf->MultiCell(45,9,'契約者名',1,'C',0,0,'','',true,0,false,false,9,'M');
		$pdf->MultiCell(125,9,$aIn['name'],1,'C',0,1,'','',true,0,false,false,9,'M');

		# サービス名称
		$pdf->MultiCell(45,9,'サービス名称',1,'C',0,0,'','',true,0,false,false,9,'M');
		$pdf->MultiCell(125,9,CL_SITENAME.'（'.$aPlan['ptName'].'）',1,'C',0,1,'','',true,0,false,false,9,'M');

		# 登録学生上限
		$pdf->MultiCell(45,9,'登録学生上限',1,'C',0,0,'','',true,0,false,false,9,'M');
		$pdf->MultiCell(125,9,$aCon['coStuNum'].'人/1講義',1,'C',0,1,'','',true,0,false,false,9,'M');

		# 初期ディスク容量上限
		$pdf->MultiCell(45,9,'ディスク容量上限',1,'C',0,0,'','',true,0,false,false,9,'M');
		$pdf->MultiCell(125,9,$aCon['coCapacity'].'GB',1,'C',0,1,'','',true,0,false,false,9,'M');

		# 講義数
		$pdf->MultiCell(45,9,'講義数',1,'C',0,0,'','',true,0,false,false,9,'M');
		$pdf->MultiCell(125,9,$aCon['coClassNum'].'講義',1,'C',0,1,'','',true,0,false,false,9,'M');

		# ご利用開始日
		$pdf->MultiCell(45,9,'契約期間',1,'C',0,0,'','',true,0,false,false,9,'M');
		$pdf->MultiCell(125,9,date('Y年n月j日',strtotime($aCon['coStartDate'])).' ～ '.date('Y年n月j日',strtotime($aCon['coTermDate'])),1,'C',0,1,'','',true,0,false,false,9,'M');

		# ご利用URL
		$pdf->MultiCell(45,9,'管理用URL',1,'C',0,0,'','',true,0,false,false,9,'M');
		$pdf->MultiCell(125,9,'https://'.CL_DOMAIN.'/t',1,'C',0,1,'','',true,0,false,false,9,'M');

		$fY = $pdf->GetY();

		# 文章
		if ($aP['coupon-code'] == 'KTAICLLABO')
		{
			$pdf->SetFont('msgothic','',9);
			$pdf->SetXY(30,$fY+5);
			$sText = "株式会社ネットマンは、ケータイ活用教育研究会から委託を受け、研究会ツールを提供しています。\nパスワードの調査はできかねますのでご注意下さい。\nこのサービスは製品の性質上、契約者の利用のみ許諾されるものです。第三者に譲渡、貸与はできません。";
			$pdf->MultiCell(160,0,$sText,0,'L',0,1);

			$fY = $pdf->GetY();
		}

		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFont('msgothic','',11);

		# 文章
		$pdf->SetXY(20,$fY+5);
		$sText = "　≪ サポート連絡先 ≫
　　●ご利用に関するお問い合わせ
　　　株式会社ネットマン ".CL_SITENAME." サービスサポートセンター
　　　　　　　　　　メールアドレス：air-support@c-learning.jp
　　●契約・お支払いに関するお問い合わせ
　　　　株式会社ネットマン　契約管理センター
　　　　　　　　　　メールアドレス：keiyaku@netman.co.jp";
		$pdf->MultiCell(170,46,$sText,1,'L',0,1,'','',true,0,false,false,46,'M');

		if ($bSave)
		{
			$sFileName = $aE['bNO'].'-L.pdf';
			$sFilePath = CL_FILEPATH.DS.$sTtID.DS.'payment_pdf';
			\Clfunc_Common::chkDir($sFilePath);
			$pdf->Output($sFilePath.DS.$sFileName,'F');
		}
		else
		{
			$pdf->Output('cl_license_temp.pdf', 'I');
			exit();
		}
		return;
	}
}
