<?php

class ClFunc_Mobile
{
	public static function is_mobiledevice($sUA = null)
	{
		if (is_null($sUA)) {
			if (!isset($_SERVER['HTTP_USER_AGENT']))
			{
				return false;
			}
			$agent = $_SERVER['HTTP_USER_AGENT'];
		} else {
			$agent = $sUA;
		}

		if (preg_match("/^DoCoMo/i", $agent) ||
			preg_match("/^(J\-PHONE|Vodafone|MOT\-[CV]|SoftBank)/i", $agent) ||
			preg_match("/^KDDI\-/i", $agent) ||
			preg_match("/UP\.Browser/i", $agent) ||
			preg_match("/^PDXGW/i", $agent) ||
			preg_match("/DDIPOCKET/i", $agent) ||
			preg_match("/WILLCOM/i", $agent) ||
			preg_match("/WS0[0-9]{2}SH/i", $agent) ||
			preg_match("/^(ASTEL|L\-mode)/i", $agent)
		)
		{
			return true;
		}
		return false;
	}

	public static function iMobileCareer($sUA = null)
	{
		if (is_null($sUA))
		{
			$user_agent = explode("/", $_SERVER['HTTP_USER_AGENT']);
			$agent = $_SERVER['HTTP_USER_AGENT'];
		}
		else
		{
			$user_agent = explode("/", $sUA);
			$agent = $sUA;
		}

		if ((strpos($user_agent[0],"J-PHONE") !== false)
				|| (strpos($user_agent[0],"Vodafone") !== false)
				|| (strpos($user_agent[0],"SoftBank") !== false))
		{
			return CL_MD_SOFTBANK;
		}

		if(strpos($user_agent[0], "DoCoMo") !== false)
		{
			return CL_MD_DOCOMO;
		}

		if(strpos($user_agent[0], "UP.Browser") !== false)
		{
			return CL_MD_AU;
		}

		if(preg_match("/DDIPOCKET/i", $agent) ||
				preg_match("/WILLCOM/i", $agent) ||
				preg_match("/WS0[0-9]{2}SH/i", $agent)
		)
		{
			return CL_MD_ETC;
		}
		return CL_MD_PC;
	}

	public static function emj($sN)
	{
		# 絵文字変換テーブルを作成
		$aEmoji = array(
		"MEMO"   => array("i"=>"&#63722;", "iU"=>"&#xE689;", "au"=>"&#62309;", "sbU"=>"&#xE301;", "sb"=>pack("C*",0x1b,0x24,0x4f,0x21,0x0f), "pc"=>"■"),
		"NEW"    => array("i"=>"&#63874;", "iU"=>"&#xE6DD;", "au"=>"&#63461;", "sbU"=>"&#xE212;", "sb"=>pack("C*",0x1b,0x24,0x46,0x32,0x0f), "pc"=>"[NEW]"),
		"SOCCER" => array("i"=>"&#63671;", "iU"=>"&#xE656;", "au"=>"&#63119;", "sbU"=>"&#xE018;", "sb"=>pack("C*",0x1b,0x24,0x47,0x38,0x0f), "pc"=>"●"),
		"!"      => array("i"=>"&#63911;", "iU"=>"&#xE702;", "au"=>"&#63066;", "sbU"=>"&#xE021;", "sb"=>pack("C*",0x1b,0x24,0x47,0x41,0x0f), "pc"=>"!"),
		"!?"     => array("i"=>"&#63912;", "iU"=>"&#xE703;", "au"=>"&#62448;", "sbU"=>"!?"      , "sb"=>"!?"                               , "pc"=>"!?"),
		"!!"     => array("i"=>"&#63913;", "iU"=>"&#xE704;", "au"=>"&#62449;", "sbU"=>"&#xE337;", "sb"=>"!!"                               , "pc"=>"!!"),
		"PENCIL" => array("i"=>"&#xE719;", "iU"=>"&#xE719;", "au"=>"&#63097;", "sbU"=>"&#xE301;", "sb"=>pack("C*",0x1b,0x24,0x4f,0x21,0x0f), "pc"=>"▼"),
		"CROWN"  => array("i"=>"&#xE71A;", "iU"=>"&#xE71A;", "au"=>"&#63481;", "sbU"=>"&#xE10E;", "sb"=>pack("C*",0x1b,0x24,0x45,0x2e,0x0f), "pc"=>"◆"),
		"CAMERA" => array("i"=>"&#63714;", "iU"=>"&#xE681;", "au"=>"&#63214;", "sbU"=>"&#xE008;", "sb"=>pack("C*",0x1b,0x24,0x47,0x28,0x0f), "pc"=>"■"),
		"BUILD"  => array("i"=>"&#63685;", "iU"=>"&#xE664;", "au"=>"&#63110;", "sbU"=>"&#xE038;", "sb"=>pack("C*",0x1b,0x24,0x47,0x58,0x0f), "pc"=>"■"),
		"CANDLE" => array("i"=>"&#63719;", "iU"=>"&#xE686;", "au"=>"&#63421;", "sbU"=>"&#xE34B;", "sb"=>pack("C*",0x1b,0x24,0x4f,0x6b,0x0f), "pc"=>"●"),
		"HEART1" => array("i"=>"&#63889;", "iU"=>"&#xE6EC;", "au"=>"&#63410;", "sbU"=>"&#xE322;", "sb"=>pack("C*",0x1b,0x24,0x47,0x42,0x0f), "pc"=>"▼"),
		"HEART2" => array("i"=>"&#63890;", "iU"=>"&#xE6ED;", "au"=>"&#62585;", "sbU"=>"&#xE328;", "sb"=>pack("C*",0x1b,0x24,0x4f,0x47,0x0f), "pc"=>"▼"),
		"BHEART" => array("i"=>"&#63891;", "iU"=>"&#xE6EE;", "au"=>"&#63055;", "sbU"=>"&#xE023;", "sb"=>pack("C*",0x1b,0x24,0x47,0x43,0x0f), "pc"=>"▼"),
		"HEARTS" => array("i"=>"&#63892;", "iU"=>"&#xE6EF;", "au"=>"&#63056;", "sbU"=>"&#xE327;", "sb"=>pack("C*",0x1b,0x24,0x4f,0x47,0x0f), "pc"=>"▼"),
		"HOUSE"  => array("i"=>"&#63684;", "iU"=>"&#xE663;", "au"=>"&#63108;", "sbU"=>"&#xE036;", "sb"=>pack("C*",0x1b,0x24,0x47,0x56,0x0f), "pc"=>"■"),
		"SHOES"  => array("i"=>"&#63738;", "iU"=>"&#xE699;", "au"=>"&#62444;", "sbU"=>"&#xE007;", "sb"=>pack("C*",0x1b,0x24,0x47,0x27,0x0f), "pc"=>"▲"),
		"GOOD"   => array("i"=>"&#xE727;", "iU"=>"&#xE727;", "au"=>"&#63186;", "sbU"=>"&#xE00E;", "sb"=>pack("C*",0x1b,0x24,0x47,0x2e,0x0f), "pc"=>"◎"),
		"FOOT"   => array("i"=>"&#63737;", "iU"=>"&#xE698;", "au"=>"&#62443;", "sbU"=>"&#xE536;", "sb"=>pack("C*",0x1b,0x24,0x51,0x56,0x0f), "pc"=>"◆"),
		"CLOVER" => array("i"=>"&#xE741;", "iU"=>"&#xE741;", "au"=>"&#63212;", "sbU"=>"&#xE110;", "sb"=>pack("C*",0x1b,0x24,0x45,0x30,0x0f), "pc"=>"＋"),
		"MAILTO" => array("i"=>"&#63859;", "iU"=>"&#xE6CF;", "au"=>"&#62566;", "sbU"=>"&#xE103;", "sb"=>pack("C*",0x1b,0x24,0x45,0x23,0x0f), "pc"=>"◆"),
		"WARN"   => array("i"=>"&#xE737;", "iU"=>"&#xE737;", "au"=>"&#63065;", "sbU"=>"&#xE252;", "sb"=>pack("C*",0x1b,0x24,0x46,0x72,0x0f), "pc"=>"▲"),
		"SEARCH" => array("i"=>"&#63873;", "iU"=>"&#xE6DC;", "au"=>"&#63217;", "sbU"=>"&#xE114;", "sb"=>pack("C*",0x1b,0x24,0x45,0x34,0x0f), "pc"=>"○"),
		"BOOK"   => array("i"=>"&#63716;", "iU"=>"&#xE683;", "au"=>"&#63095;", "sbU"=>"&#xE148;", "sb"=>pack("C*",0x1b,0x24,0x45,0x68,0x0f), "pc"=>"□"),
		"MAIL"   => array("i"=>"&#63863;", "iU"=>"&#xE6D3;", "au"=>"&#63226;", "sbU"=>"&#xE103;", "sb"=>pack("C*",0x1b,0x24,0x45,0x23,0x0f), "pc"=>"□"),
		"SMILE"  => array("i"=>"&#63893;", "iU"=>"&#xE6F0;", "au"=>"&#63049;", "sbU"=>"&#xE057;", "sb"=>pack("C*",0x1b,0x24,0x47,0x77,0x0f), "pc"=>"○"),
		"CLOCK"  => array("i"=>"&#63838;", "iU"=>"&#xE6BA;", "au"=>"&#63409;", "sbU"=>"&#xE02D;", "sb"=>pack("C*",0x1b,0x24,0x47,0x4d,0x0f), "pc"=>"○"),
		"REVOLT" => array("i"=>"&#63905;", "iU"=>"&#xE6FC;", "au"=>"&#63166;", "sbU"=>"&#xE334;", "sb"=>pack("C*",0x1b,0x24,0x4f,0x54,0x0f), "pc"=>"※"),
		"SOON"   => array("i"=>"&#63835;", "iU"=>"&#xE6B7;", "au"=>"⇒"      , "sbU"=>"⇒"      , "sb"=>"⇒"                               , "pc"=>"⇒"),
		"CLIP"   => array("i"=>"&#xE730;", "iU"=>"&#xE730;", "au"=>"&#63096;", "sbU"=>"◇"      , "sb"=>"◇"                               , "pc"=>"◇"),
		"FREE"   => array("i"=>"&#63867;", "iU"=>"&#xE6D7;", "au"=>"&#63381;", "sbU"=>"&#xE216;", "sb"=>pack("C*",0x1b,0x24,0x46,0x36,0x0f), "pc"=>"[FREE]"),
		"UPPER"  => array("i"=>"&#63898;", "iU"=>"&#xE6F5;", "au"=>"&#62446;", "sbU"=>"&#xE236;", "sb"=>pack("C*",0x1b,0x24,0x46,0x56,0x0f), "pc"=>"↑"),
		"LOWER"  => array("i"=>"&#63909;", "iU"=>"&#xE700;", "au"=>"&#62447;", "sbU"=>"&#xE238;", "sb"=>pack("C*",0x1b,0x24,0x46,0x58,0x0f), "pc"=>"↓"),
		"LOOK"   => array("i"=>"&#63730;", "iU"=>"&#xE691;", "au"=>"&#63425;", "sbU"=>"&#xE419;", "sb"=>pack("C*",0x1b,0x24,0x50,0x39,0x0f), "pc"=>"◎"),
		"KEY"    => array("i"=>"&#63869;", "iU"=>"&#xE6D9;", "au"=>"&#63218;", "sbU"=>"&#xE03F;", "sb"=>pack("C*",0x1b,0x24,0x47,0x5f,0x0f), "pc"=>"＊"),
		"KEITAI" => array("i"=>"&#63721;", "iU"=>"&#xE688;", "au"=>"&#63397;", "sbU"=>"&#xE00A;", "sb"=>pack("C*",0x1b,0x24,0x47,0x2a,0x0f), "pc"=>"■"),
		"HGLASS" => array("i"=>"&#xE71C;", "iU"=>"&#xE71C;", "au"=>"&#63060;", "sbU"=>"&#xE02D;", "sb"=>pack("C*",0x1b,0x24,0x47,0x4d,0x0f), "pc"=>"[WAIT]"),
		"LOOP"   => array("i"=>"&#xE735;", "iU"=>"&#xE735;", "au"=>"&#62589;", "sbU"=>"＠"      , "sb"=>"＠",                                "pc"=>"＠"),
		"1"      => array("i"=>"&#63879;", "iU"=>"&#xE6E2;", "au"=>"&#63227;", "sbU"=>"&#xE21C;", "sb"=>pack("C*",0x1b,0x24,0x46,0x3c,0x0f), "pc"=>"[1]"),
		"2"      => array("i"=>"&#63880;", "iU"=>"&#xE6E3;", "au"=>"&#63228;", "sbU"=>"&#xE21D;", "sb"=>pack("C*",0x1b,0x24,0x46,0x3d,0x0f), "pc"=>"[2]"),
		"3"      => array("i"=>"&#63881;", "iU"=>"&#xE6E4;", "au"=>"&#63296;", "sbU"=>"&#xE21E;", "sb"=>pack("C*",0x1b,0x24,0x46,0x3e,0x0f), "pc"=>"[3]"),
		"4"      => array("i"=>"&#63882;", "iU"=>"&#xE6E5;", "au"=>"&#63297;", "sbU"=>"&#xE21F;", "sb"=>pack("C*",0x1b,0x24,0x46,0x3f,0x0f), "pc"=>"[4]"),
		"5"      => array("i"=>"&#63883;", "iU"=>"&#xE6E6;", "au"=>"&#63298;", "sbU"=>"&#xE220;", "sb"=>pack("C*",0x1b,0x24,0x46,0x40,0x0f), "pc"=>"[5]"),
		"6"      => array("i"=>"&#63884;", "iU"=>"&#xE6E7;", "au"=>"&#63299;", "sbU"=>"&#xE221;", "sb"=>pack("C*",0x1b,0x24,0x46,0x41,0x0f), "pc"=>"[6]"),
		"7"      => array("i"=>"&#63885;", "iU"=>"&#xE6E8;", "au"=>"&#63300;", "sbU"=>"&#xE222;", "sb"=>pack("C*",0x1b,0x24,0x46,0x42,0x0f), "pc"=>"[7]"),
		"8"      => array("i"=>"&#63886;", "iU"=>"&#xE6E9;", "au"=>"&#63301;", "sbU"=>"&#xE223;", "sb"=>pack("C*",0x1b,0x24,0x46,0x43,0x0f), "pc"=>"[8]"),
		"9"      => array("i"=>"&#63887;", "iU"=>"&#xE6EA;", "au"=>"&#63302;", "sbU"=>"&#xE224;", "sb"=>pack("C*",0x1b,0x24,0x46,0x44,0x0f), "pc"=>"[9]"),
		"0"      => array("i"=>"&#63888;", "iU"=>"&#xE6EB;", "au"=>"&#63433;", "sbU"=>"&#xE225;", "sb"=>pack("C*",0x1b,0x24,0x46,0x45,0x0f), "pc"=>"[0]"),
		);
		# キャリアと文字で返す値を変更する
		switch (self::iMobileCareer())
		{
			case CL_MD_SOFTBANK:
				$sE = $aEmoji[$sN]["sbU"];
				break;
			case CL_MD_DOCOMO:
				$sE = $aEmoji[$sN]["iU"];
				break;
			case CL_MD_AU:
				$sE = $aEmoji[$sN]["au"];
				break;
			default:
				$sE = $aEmoji[$sN]["pc"];
				break;
		}
		return $sE;
	}

	public static function spacer($iM = 4)
	{
		$sImgPath = Asset::find_file('spacer.gif', 'img');
		return '<div><img src="/'.$sImgPath.'" style="margin-top:'.($iM-1).'px;"></div>'."\n";
	}

	public static function hr($sC = 'gray')
	{
		return '<hr size="1" style="width: 100%; margin: 5px 0; padding: 0; color: '.$sC.'; background: '.$sC.'; border-top: 1px solid '.$sC.';" />'."\n";
	}

	public static function SesID($sM = null)
	{
		if (Cookie::get('CL_COOKIE_CHK',false))
#		if (Agent::property('cookies') === true)
		{
			return;
		}
		$sessionid = Crypt::encode(serialize(array(Session::key())));
		if ($sM == 'post')
		{
			return '<input type="hidden" name="'.Config::get('session.file.cookie_name').'" value="'.$sessionid.'">';
		}
		return '?'.Config::get('session.file.cookie_name').'='.$sessionid;
	}

}