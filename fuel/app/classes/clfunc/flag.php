<?php
class Clfunc_Flag
{
	# 先生プロフィールの制限
	const T_PROF_NONE    = 0b0000000000; # 制限なし
	const T_PROF_NAME    = 0b0000000001; # 氏名
	const T_PROF_SCHOOL  = 0b0000000010; # 学校名
	const T_PROF_DEPT    = 0b0000000100; # 学部名
	const T_PROF_SUBJECT = 0b0000001000; # 学科名
	const T_PROF_PHOTO   = 0b0000010000; # 写真
	const T_PROF_MAIL    = 0b0000100000; # メールアドレス

	# 先生機能の制限
	const T_AUTH_NONE    = 0b0000000000; # 制限なし
	const T_AUTH_CLASS   = 0b0000000001; # 講義 作成/変更/終了/削除
	const T_AUTH_STUDENT = 0b0000010000; # 学生 作成/変更
	const T_AUTH_STUDY   = 0b0001000000; # 履修 追加/削除

	# 学生プロフィールの制限
	const S_PROF_NONE    = 0b0000000000; # 制限なし
	const S_PROF_NAME    = 0b0000000001; # 氏名
	const S_PROF_NO      = 0b0000000010; # 学籍番号
	const S_PROF_SEX     = 0b0000000100; # 性別
	const S_PROF_DEPT    = 0b0000001000; # 学部名
	const S_PROF_SUBJECT = 0b0000010000; # 学科名
	const S_PROF_YEAR    = 0b0000100000; # 学年
	const S_PROF_CLASS   = 0b0001000000; # クラス
	const S_PROF_COURSE  = 0b0010000000; # コース
	const S_PROF_SCHOOL  = 0b1000000000; # 学校名

	# 学生機能の制限
	const S_AUTH_NONE    = 0b0000000000; # 制限なし
	const S_AUTH_STADY   = 0b0000000001; # 履修登録

	# 学生からのログイン強制取得
	const S_GET_NONE    = 0b0000000000; # 制限なし
	const S_GET_NO      = 0b0000000010; # 学籍番号
	const S_GET_SEX     = 0b0000000100; # 性別
	const S_GET_DEPT    = 0b0000001000; # 学部名
	const S_GET_SUBJECT = 0b0000010000; # 学科名
	const S_GET_YEAR    = 0b0000100000; # 学年
	const S_GET_CLASS   = 0b0001000000; # クラス
	const S_GET_COURSE  = 0b0010000000; # コース
	const S_GET_MAIL    = 0b0100000000; # メールアドレス
	const S_GET_SCHOOL  = 0b1000000000; # 学校名

	# 講義機能の有効化
	const C_FUNC_NONE     = 0b0000000000; # 全て無効
	const C_FUNC_ATTEND   = 0b0000000001; # 出席
	const C_FUNC_QUEST    = 0b0000000010; # アンケート
	const C_FUNC_TEST     = 0b0000000100; # 小テスト
	const C_FUNC_DRILL    = 0b0000001000; # ドリル
	const C_FUNC_MATERIAL = 0b0000010000; # 教材倉庫
	const C_FUNC_COOP     = 0b0000100000; # 協働板
	const C_FUNC_REPORT   = 0b0001000000; # レポート
	const C_FUNC_CONTACT  = 0b0010000000; # 連絡・相談
	const C_FUNC_NEWS     = 0b0100000000; # ニュース
	const C_FUNC_ALOG     = 0b1000000000; # 活動履歴

	# お支払い種別
	const P_TYPE_NONE   = 0b0000000000; # なし
	const P_TYPE_CARD   = 0b0000000001; # クレジットカード
	const P_TYPE_BANK   = 0b0000000010; # 銀行振込
	const P_TYPE_PAYPAL = 0b0000000100; # PayPal

	public static function getOrgFlag()
	{
		$aOrgFlag = array(
			'T_PROF' => array(
				1  => __('氏名'),
//				2  => __('所属学校名'),
				4  => __('学部名'),
				8  => __('学科名'),
				16 => __('写真'),
				32 => __('メールアドレス'),
			),
			'T_AUTH' => array(
				1  => __('講義の作成/変更/終了/削除'),
				16 => __('学生の作成/変更'),
				64 => __('履修の追加/変更'),
			),
			'S_PROF' => array(
				1   => __('氏名'),
				2   => __('学籍番号'),
				4   => __('性別'),
				512 => __('学校名'),
				8   => __('学部名'),
				16  => __('学科名'),
				32  => __('学年'),
				64  => __('クラス'),
				128 => __('コース'),
			),
			'S_AUTH' => array(
				1  => __('履修登録'),
			),
			'S_GET' => array(
				2   => __('学籍番号'),
				4   => __('性別'),
				512 => __('学校名'),
				8   => __('学部名'),
				16  => __('学科名'),
				32  => __('学年'),
				64  => __('クラス'),
				128 => __('コース'),
				256 => __('メールアドレス'),
			),
		);
		return $aOrgFlag;
	}

	public static function getClassFlag()
	{
		$aClassFlag = array(
			'C_FUNC' => array(
				1   => __('出席'),
				2   => __('アンケート'),
				4   => __('小テスト'),
				8   => __('ドリル'),
				16  => __('教材倉庫'),
				32  => __('協働板'),
				64  => __('レポート'),
				512 => __('活動履歴'),
				128 => __('連絡・相談'),
				256 => __('ニュース'),
			),
			'S_GET' => array(
				2   => __('学籍番号'),
				4   => __('性別'),
				512 => __('学校名'),
				8   => __('学部名'),
				16  => __('学科名'),
				32  => __('学年'),
				64  => __('クラス'),
				128 => __('コース'),
				256 => __('メールアドレス'),
			),
		);
		return $aClassFlag;
	}


	public static function getStuGetFlag()
	{
		$aStuGetFlag = array(
			2   => 'stNO',
			4   => 'stSex',
			512 => 'cmKCode',
			8   => 'stDept',
			16  => 'stSubject',
			32  => 'stYear',
			64  => 'stClass',
			128 => 'stCourse',
			256 => 'stMail',
		);
		return $aStuGetFlag;
	}

	public static function getFuncFlag()
	{
		$aClassFuncFlag = array(
				1   => 'attend',
				2   => 'quest',
				4   => 'test',
				8   => 'drill',
				16  => 'material',
				32  => 'coop',
				64  => 'report',
				512 => 'alog',
				128 => 'contact',
				256 => 'news',
		);
		return $aStuGetFlag;
	}

	public static function getPaymentFlag()
	{
		$aPaymentFlag = array(
			1 => 'クレジットカード',
			2 => '銀行振込',
			4 => 'PayPal',
		);
		return $aPaymentFlag;
	}

	public static function getPaymentCode()
	{
		$aPaymentCode = array(
			1=>'C',	# クレジットカード
			2=>'B',	# 銀行振込
			4=>'P',	# PayPal
		);
		return $aPaymentCode;
	}




}