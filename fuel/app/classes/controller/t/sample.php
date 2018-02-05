<?php
class Controller_T_Sample extends Controller_Restbase
{
	public function action_studentadd()
	{
		$res = array(
			array(__('ログインID'),__('パスワード'),__('氏名'),__('性別'),__('学籍番号'),__('学部'),__('学科'),__('学年'),__('クラス'),__('コース')),
			array('login01','','CL太郎','0','2015S-0801','CL学部','CL学科','4','CLクラス','CLコース'),
			array('login02','Password02','CL次郎','1','2015S-0802','CL学部','','3','3-A',''),
			array('login03','Password03','CL花子','2','','','','','',''),
		);

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_attendadd()
	{
		$res = array(
			array('予約日','予約開始時刻','予約終了時刻','確認キー','位置情報取得'),
			array(date('Y/n/j'),'9:15','10:45','','1'),
			array(date('Y/m/d',strtotime('+5 days')),'10:25','11:55','8754','0'),
			array(date('Y/n/d',strtotime('+8 days')),'09:00','10:30','ABCD','1'),
			array(date('Y/m/j',strtotime('+12 days')),'13:0','15:0','','0'),
			array(date('Y/m/d',strtotime('+13 days')),'09:15','10:45','H1Z1','1'),
		);

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_QuestCreate($sFName = null)
	{
		$bAnony = false;
		$sHash = Cookie::get('CL_TL_HASH',false);
		if ($sHash)
		{
			$aLogin = unserialize(Crypt::decode($sHash));
			$result = Model_Teacher::getTeacherFromHash($aLogin['hash']);
			if (count($result))
			{
				$aTeacher = $result->current();
				$bAnony = ($aTeacher['gtID'])? true:false;
			}
		}

		$res = array(
			array('アンケートタイトル','','20文字以上は切り捨てられます。[必須項目]'),
		);

		if ($sFName != 'quest_simple_format')
		{
			$res[] = array('公開予定日時(年/月/日 時:分)','','YYYY/MM/DD HH:mm形式で入力してください。分は5分単位で丸めます。（例 09:21 → 09:20、09:29 → 09:25）[省略時は自動公開を行いません]');
			$res[] = array('締切予定日時(年/月/日 時:分)','','YYYY/MM/DD HH:mm形式で入力してください。分は5分単位で丸めます。（例 09:21 → 09:20、09:29 → 09:25）[省略時は自動公開を行いません]');
			$res[] = array('選択肢の表示方法','1','1～3の数値で指定します。（三列 = 3、二列 = 2、一列 = 1）[省略時は一列になります]');
			$res[] = array('選択肢の並び順','0','0か1の数値で指定します。（昇順 = 0、降順 = 1）[省略時は昇順になります]');
			$res[] = array('答えなおし','0','0か1の数値で指定します。（不可 = 0、可 = 1）[省略時は不可になります]');
			$res[] = array('個人の回答内容の公開範囲','0','0～2の数値で指定します。（非公開 = 0、匿名で公開 = 1、公開 = 2）[省略時は非公開になります]');
			$res[] = array('個人宛の先生コメントの公開範囲','0','0～2の数値で指定します。（非公開 = 0、回答者本人のみに公開 = 1、回答内容を閲覧できる全ての人に公開 = 2）[省略時は非公開になります]');
			$res[] = array('ゲスト回答','0','0～2の数値で指定します。（許可しない = 0、許可する(匿名回答) = 1、許可する(記名回答) = 2）[省略時は許可しないになります]');
			if ($bAnony)
			{
				$res[] = array('匿名回答','0','0か1の数値で指定します。（記名 = 0、匿名 = 1）[省略時は記名になります]');
			}
		}

		for ($i = 1; $i <= 5; $i++)
		{
			$res[] = array();
			if ($i == 1)
			{
				$res[] = array('※設問は回答形式、必須回答、設問文、選択肢を1セットとして指定します。設問を増やす場合はセットを繰り返してください。');
				$res[] = array();
			}
			$res[] = array('回答形式','','択一形式はradio、複数選択形式はselect、テキスト形式はtextを入力してください。[必須項目]');
			$res[] = array('必須回答','','0か1の数値で指定します。（任意 = 0、必須 = 1）[省略時は択一・複数選択で必須、テキストで任意となります。]');
			$res[] = array('設問文','','[必須項目]');
			for ($j = 1; $j <= 5; $j++)
			{
				if ($j == 1)
				{
					$res[] = array('選択肢'.$j,'','選択肢は50件まで指定可能です（50件より多い場合は無視します）。内容が空文字の場合は無視されます。回答形式がtextの場合、選択肢は無視します。');
				}
				else
				{
					$res[] = array('選択肢'.$j,'');
				}
			}
		}

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_TestCreate($sFName = null)
	{
		$res = array(
			array('小テストタイトル','','20文字以上は切り捨てられます。[必須項目]'),
			array('合格点数','0','数値で指定します。問題の配点合計より小さい値で指定します。[省略時は0になります。]'),
			array('制限時間','0','数値（分）で指定します。0は無制限となります。[省略時は0になります]'),
		);

		if ($sFName != 'test_simple_format')
		{
			$res[] = array('公開予定日時(年/月/日 時:分)','','YYYY/MM/DD HH:mm形式で入力してください。分は5分単位で丸めます。（例 09:21 → 09:20、09:29 → 09:25）[省略時は自動公開を行いません]');
			$res[] = array('締切予定日時(年/月/日 時:分)','','YYYY/MM/DD HH:mm形式で入力してください。分は5分単位で丸めます。（例 09:21 → 09:20、09:29 → 09:25）[省略時は自動公開を行いません]');
			$res[] = array('選択肢の表示方法','1','1～3の数値で指定します。（三列 = 3、二列 = 2、一列 = 1）[省略時は一列になります]');
			$res[] = array('選択肢の並び順','0','0か1の数値で指定します。（標準 = 0、ランダム = 1）[省略時は標準になります]');
			$res[] = array('点数、解説の公開','0','0～3の数値で指定します。（非公開 = 0、点数公開 = 1、解説公開 = 2、両方公開 = 3）[省略時は非公開になります]');
			$res[] = array('小テストの全体的な解説','','[省略可]');
		}

		for ($i = 1; $i <= 5; $i++)
		{
			$res[] = array();
			if ($i == 1)
			{
				$res[] = array('※問題は回答形式、配点、問題文、正解、選択肢を1セットとして指定します。問題を増やす場合はセットを繰り返してください。');
				$res[] = array();
			}
			$res[] = array('回答形式','','択一形式はradio、複数選択形式はselect、テキスト形式はtextを入力してください。[必須項目]');
			$res[] = array('配点','0','数値で指定します。[必須項目]');
			$res[] = array('問題文','','[必須項目]');
			$res[] = array('解説文','','[省略可]');
			for ($j = 1; $j <= 5; $j++)
			{
				if ($j == 1)
				{
					$res[] = array('正解'.$j,'','択一形式は選択肢番号、複数選択形式は1|2|3と選択肢番号をバーティカルバー区切り、テキスト形式は正解のテキストを記入してください。[必須項目]');
				}
				else if ($j == 2)
				{
					$res[] = array('正解'.$j,'','正解2以降はテキスト形式のみで使用します。問題に複数の正解がある場合は、正解2～正解5に記入してください。');
				}
				else
				{
					$res[] = array('正解'.$j,'');
				}
			}
			for ($j = 1; $j <= 5; $j++)
			{
				if ($j == 1)
				{
					$res[] = array('選択肢'.$j,'','選択肢は50件まで指定可能です（50件より多い場合は無視します）。内容が空文字の場合は無視されます。回答形式がtextの場合、選択肢は無視します。');
				}
				else
				{
					$res[] = array('選択肢'.$j,'');
				}
			}
		}

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_DrillQueryCreate($sFName = null)
	{
		$res = array();

		for ($i = 1; $i <= 5; $i++)
		{
			$res[] = array();
			if ($i == 1)
			{
				$res[] = array('※問題は回答形式、問題グループ、問題文、正解、選択肢を1セットとして指定します。問題を増やす場合はセットを繰り返してください。');
				$res[] = array();
			}
			$res[] = array('回答形式','','択一形式はradio、複数選択形式はselect、テキスト形式はtextを入力してください。[必須項目]');
			$res[] = array('問題グループ','','所属する問題グループを記入してください。新しいグループ名の場合は新規で作成されます。[省略可]');
			$res[] = array('問題文','','[必須項目]');
			$res[] = array('解説文','','[省略可]');
			for ($j = 1; $j <= 5; $j++)
			{
				if ($j == 1)
				{
					$res[] = array('正解'.$j,'','択一形式は選択肢番号、複数選択形式は1|2|3と選択肢番号をバーティカルバー区切り、テキスト形式は正解のテキストを記入してください。[必須項目]');
				}
				else if ($j == 2)
				{
					$res[] = array('正解'.$j,'','正解2以降はテキスト形式のみで使用します。問題に複数の正解がある場合は、正解2～正解5に記入してください。');
				}
				else
				{
					$res[] = array('正解'.$j,'');
				}
			}
			for ($j = 1; $j <= 5; $j++)
			{
				if ($j == 1)
				{
					$res[] = array('選択肢'.$j,'','選択肢は50件まで指定可能です（50件より多い場合は無視します）。内容が空文字の場合は無視されます。回答形式がtextの場合、選択肢は無視します。');
				}
				else
				{
					$res[] = array('選択肢'.$j,'');
				}
			}
		}

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

}
