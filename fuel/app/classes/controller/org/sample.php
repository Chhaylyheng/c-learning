<?php
class Controller_Org_Sample extends Controller_Restbase
{
	public $aAdmin = null;
	public $aGroup = null;

	public function before()
	{
		parent::before();

		$sHash = Cookie::get('CL_ORG_HASH',false);
		if (!$sHash)
		{
			Response::redirect('org/login/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Group::getGroupAdminFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('org/login/index/1');
		}
		$this->aAdmin = $result->current();

		$result = Model_Group::getGroup(array(array('gb.gtID','=',$this->aAdmin['gtID'])));
		if (!count($result))
		{
			Response::redirect('org/login/index/1');
		}
		$this->aGroup = $result->current();
	}

	public function action_teacheradd()
	{
		$res = array(
			array(__('メールアドレス'),__('パスワード'),__('氏名'),__('学部'),__('学科')),
			array('user01@c-learning.jp','','CL太郎','テスト学部','テスト学科'),
			array('user02@c-learning.jp','Password02','CL次郎','',''),
			array('user03@c-learning.jp','Password03','CL三郎','',''),
		);

		if ($this->aGroup['gtLDAP'])
		{
			$res[1][1] = '';
			$res[2][1] = '';
			$res[3][1] = '';
			$res[0][] = 'uid';
			$res[1][] = 'uid01';
			$res[2][] = 'uid02';
			$res[3][] = 'uid03';
		}

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_classadd()
	{
		$res = array(
			array(__('講義コード'),__('講義名'),__('年度'),__('期'),__('曜日'),__('時限'),__('実施状況')),
			array('','法学基礎I','2016','前期','水曜','2','1'),
			array('448591','心理行動科学研究','2017','','','','1'),
			array('105483','英語（コミュニケーション）','2017','通期','月曜','4','0'),
		);

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_classtake()
	{
		$res = array(
			array(__('講義コード'),__('メールアドレス'),__('主担当')),
			array('105483','user01@c-learning.jp',''),
			array('105483','user02@c-learning.jp','1'),
			array('105483','user03@c-learning.jp',''),
			array('448591','user01@c-learning.jp',''),
			array('448591','user03@c-learning.jp','1'),
			array('827011','user02@c-learning.jp',''),
			array('827011','user01@c-learning.jp','1'),
		);

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_studentadd()
	{
		$res = array(
			array(__('ログインID'),__('パスワード'),__('氏名'),__('性別'),__('学籍番号'),__('学部'),__('学科'),__('学年'),__('クラス'),__('コース')),
			array('login01','','CL太郎','0','2015S-0801','CL学部','CL学科','4','CLクラス','CLコース'),
			array('login02','Password02','CL次郎','1','2015S-0802','CL学部','','3','3-A',''),
			array('login03','Password03','CL花子','2','','','','','',''),
		);
		if ($this->aGroup['gtLDAP'])
		{
			$res[1][1] = '';
			$res[2][1] = '';
			$res[3][1] = '';
		}

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_classstady()
	{
		$res = array(
				array(__('講義コード'),__('ログインID')),
				array('105483','login01'),
				array('105483','login02'),
				array('105483','login03'),
				array('448591','login01'),
				array('448591','login03'),
				array('827011','login01'),
				array('827011','login02'),
		);

		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

}
