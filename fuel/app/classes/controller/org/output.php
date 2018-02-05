<?php
class Controller_Org_Output extends Controller_Restbase
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

	public function action_teacherlist()
	{
		$res = array(
			array(
				__('メールアドレス'),
				__('パスワード'),
				__('氏名'),
				__('学部'),
				__('学科'),
			)
		);
		if ($this->aGroup['gtLDAP'])
		{
			$res[0][5] = 'uid';
		}

		$res[0][] = __('ログイン回数');
		$res[0][] = __('最終ログイン日時');

		$result = Model_Group::getGroupTeachers(array(array('gtp.gtID','=',$this->aGroup['gtID'])),null,array('tv.ttName'=>'asc'));
		if (count($result))
		{
			foreach ($result as $i => $aR)
			{
				$res[($i+1)] = array(
					$aR['ttMail'],
					$aR['ttFirst'],
					$aR['ttName'],
					$aR['ttDept'],
					$aR['ttSubject'],
				);
				if ($this->aGroup['gtLDAP'])
				{
					$res[($i+1)][1] = '';
					$res[($i+1)][5] = $aR['ttLoginID'];
				}

				$res[($i+1)][] = $aR['ttLoginNum'];
				$res[($i+1)][] = ($aR['ttLoginDate'] != CL_DATETIME_DEFAULT)? $aR['ttLoginDate']:'';
			}
		}
		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_studentlist()
	{
		$res = array(
				array(
					__('ログインID'),
					__('パスワード'),
					__('氏名'),
					__('性別'),
			)
		);

		if (!CL_CAREERTASU_MODE)
		{
			$res[0][] = __('学籍番号');
		}
		if (CL_CAREERTASU_MODE)
		{
			$res[0][] = __('学校');
		}
		$res[0][] = __('学部');
		if (!CL_CAREERTASU_MODE)
		{
			$res[0][] = __('学科');
			$res[0][] = __('学年');
			$res[0][] = __('クラス');
			$res[0][] = __('コース');
		}

		$res[0][] = __('ログイン回数');
		$res[0][] = __('最終ログイン日時');

		$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$this->aGroup['gtID'])),null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			foreach ($result as $i => $aR)
			{
				$res[($i+1)] = array(
					$aR['stLogin'],
					$aR['stFirst'],
					$aR['stName'],
					(int)$aR['stSex'],
				);
				if ($this->aGroup['gtLDAP'])
				{
					$res[($i+1)][1] = '';
				}
				if (!CL_CAREERTASU_MODE)
				{
					$res[($i+1)][] = $aR['stNO'];
				}
				if (CL_CAREERTASU_MODE)
				{
					$res[($i+1)][] = $aR['stSchool'];
				}
				$res[($i+1)][] = $aR['stDept'];
				if (!CL_CAREERTASU_MODE)
				{
					$res[($i+1)][] = $aR['stSubject'];
					$res[($i+1)][] = ($aR['stYear'])? $aR['stYear']:'';
					$res[($i+1)][] = $aR['stClass'];
					$res[($i+1)][] = $aR['stCourse'];
				}

				$res[($i+1)][] = $aR['stLoginNum'];
				$res[($i+1)][] = ($aR['stLoginDate'] != CL_DATETIME_DEFAULT)? $aR['stLoginDate']:'';
			}
		}
		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_classlist()
	{
		$res = array(
			array(
				__('講義コード'),
				__('講義名'),
				__('年度'),
				__('期'),
				__('曜日'),
				__('時限'),
				__('実施状況'),
			)
		);

		$result = Model_Group::getGroupClasses(array(array('gtID','=',$this->aGroup['gtID'])),null,array('ctCode'=>'asc'));
		if (count($result))
		{
			foreach ($result as $i => $aR)
			{
				$res[($i+1)] = array(
					$aR['ctCode'],
					$aR['ctName'],
					$aR['ctYear'],
					$this->aPeriod[$aR['dpNO']],
					$this->aWeekday[$aR['ctWeekDay']],
					$aR['dhNO'],
					$aR['ctStatus'],
				);
			}
		}
		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_studylist()
	{
		$res = array(
			array(
				__('講義コード'),
				__('ログインID'),
			)
		);

		$result = Model_Group::getGroupStudentsClasses(array(array('gtID','=',$this->aGroup['gtID'])),null,array('ctCode'=>'asc','stLogin'=>'asc'));
		if (count($result))
		{
			foreach ($result as $i => $aR)
			{
				$res[($i+1)] = array(
					$aR['ctCode'],
					$aR['stLogin'],
				);
			}
		}
		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_inchargelist()
	{
		$res = array(
				array(
					__('講義コード'),
					__('メールアドレス'),
					__('主担当'),
				)
		);

		$result = Model_Group::getGroupTeachersClasses(array(array('gc.gtID','=',$this->aGroup['gtID'])),null,array('ct.ctCode'=>'asc','tt.ttMail'=>'asc'));
		if (count($result))
		{
			foreach ($result as $i => $aR)
			{
				$res[($i+1)] = array(
					$aR['ctCode'],
					$aR['ttMail'],
					(int)$aR['tpMaster'],
				);
			}
		}
		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

	public function action_studymatrix()
	{
		$aClasses = null;
		$aStudies = null;
		$aCtIDs = null;

		# 講義を取得
		$result = Model_Group::getGroupClasses2(array(array('gcp.gtID','=',$this->aGroup['gtID'])),null,array('ct.ctCode'=>'asc'));
		if (count($result))
		{
			$aClasses = $result->as_array('ctID');
			$aCtIDs = array_keys($aClasses);
		}

		# 講義を取得
		$result = Model_Student::getStudentPosition(array(array('ctID','IN',$aCtIDs)));
		if (count($result))
		{
			foreach ($result as $aSP)
			{
				if (isset($aClasses[$aSP['ctID']]))
				{
					$aStudies[$aSP['stID']][$aSP['ctID']] = true;
				}
			}
		}

		$res = array(
			array(
				'','','',
			),
			array(
				__('ログインID'),
			),
		);

		$res[1][] = __('氏名');
		$res[1][] = __('性別');
		if (!CL_CAREERTASU_MODE)
		{
			$res[0][] = '';
			$res[1][] = __('学籍番号');
		}
		if (CL_CAREERTASU_MODE)
		{
			$res[0][] = '';
			$res[1][] = __('学校');
		}
		$res[0][] = '';
		$res[1][] = __('学部');
		if (!CL_CAREERTASU_MODE)
		{
			$res[0][] = '';
			$res[0][] = '';
			$res[0][] = '';
			$res[0][] = '';
			$res[1][] = __('学科');
			$res[1][] = __('学年');
			$res[1][] = __('クラス');
			$res[1][] = __('コース');
		}

		$res[0][] = __('履修人数').' ->';
		$res[1][] = __('履修数');

		if (!is_null($aClasses))
		{
			foreach ($aClasses as $aC)
			{
				$res[0][] = (int)$aC['scNum'];
				$res[1][] = $aC['ctName']."\r\n".'['.$aC['ctCode'].']';
			}
		}

		$result = Model_Group::getGroupStudents(array(array('gsp.gtID','=',$this->aGroup['gtID'])),null,array('st.stNO'=>'asc'));
		if (count($result))
		{
			foreach ($result as $i => $aR)
			{
				$j = $i + 2;

				$aStudy = null;
				if (isset($aStudies[$aR['stID']]))
				{
					$aStudy = $aStudies[$aR['stID']];
				}

				$res[$j] = array(
					$aR['stLogin'],
				);
				$res[$j][] = $aR['stName'];
				$res[$j][] = $this->aSex[$aR['stSex']];
				if (!CL_CAREERTASU_MODE)
				{
					$res[$j][] = $aR['stNO'];
				}
				if (CL_CAREERTASU_MODE)
				{
					$res[$j][] = $aR['stSchool'];
				}
				$res[$j][] = $aR['stDept'];
				if (!CL_CAREERTASU_MODE)
				{
					$res[$j][] = $aR['stSubject'];
					$res[$j][] = ($aR['stYear'])? $aR['stYear']:'';
					$res[$j][] = $aR['stClass'];
					$res[$j][] = $aR['stCourse'];
				}

				$res[$j][] = count($aStudy);

				if (!is_null($aClasses))
				{
					foreach ($aClasses as $sCtID => $aC)
					{
						$res[$j][] = (isset($aStudy[$sCtID]))? '○':'';
					}
				}
			}
		}
		mb_convert_variables('sjis-win','UTF-8',$res);

		$this->response($res);
		return;
	}

}
