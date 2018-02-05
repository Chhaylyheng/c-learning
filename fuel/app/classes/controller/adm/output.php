<?php
class Controller_Adm_Output extends Controller_Restbase
{
	public function before()
	{
		parent::before();

		$sHash = Cookie::get('CL_ADM_HASH',false);
		if (!$sHash)
		{
			Response::redirect('adm/AdminLogin/index/1');
		}
		$aLogin = unserialize(Crypt::decode($sHash));

		$result = Model_Admin::getAdminFromHash($aLogin['hash']);
		if (!count($result))
		{
			Response::redirect('adm/AdminLogin/index/1');
		}
		$this->aAdmin = $result->current();
	}

	public function action_teacherlist()
	{
		$res = array(
			array(
				'先生ID',
				'氏名',
				'メールアドレス',

				'契約',
				'契約期間',
				'実施中講義数',
				'契約講義数',
				'履修人数',
				'契約履修人数',
				'利用容量',
				'契約容量',

				'学校',
				'学部',
				'学科',
				'終了講義数',
				'ログイン回数',
				'最終ログイン日時',
			)
		);

		$result = Model_Teacher::getTeacher(null,null,array('ttID'=>'asc'));
		if (count($result))
		{
			foreach ($result as $aR)
			{
				$res[] = array(
					$aR['ttID'],
					$aR['ttName'],
					$aR['ttMail'],

					($aR['ptName'])? $aR['ptName']:'契約期限切れ',
					(is_null($aR['coTermDate']))? '─':(($aR['coTermDate'] != '0000-00-00')? date('Y/m/d',strtotime($aR['coTermDate'])):'─'),
					$aR['ttClassNum'],
					($aR['ptID'] != 99 && $aR['ptID'] != '')? (int)$aR['coClassNum']:'─',
					$aR['ttStuNum'],
					($aR['ptID'] != 99 && $aR['ptID'] != '')? (int)$aR['coStuNum']:'─',
					\Clfunc_Common::FilesizeFormat($aR['ttDiskUsed'],1),
					($aR['ptID'] != 99 && $aR['ptID'] != '')? (int)$aR['coCapacity'].'GB':'─',

					$aR['cmName'],
					$aR['ttDept'],
					$aR['ttSubject'],
					$aR['ttCloseNum'],
					(int)$aR['ttLoginNum'],
					(($aR['ttLoginDate'] != CL_DATETIME_DEFAULT)? date("Y/m/d H:i",strtotime($aR['ttLoginDate'])):'─'),
				);
			}
		}
		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

}
