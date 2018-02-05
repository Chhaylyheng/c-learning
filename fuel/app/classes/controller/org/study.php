<?php
class Controller_Org_Study extends Controller_Org_Base
{
	private $bn = 'org/study';

	private $aStudents = null;
	private $aClasses = null;
	private $aStudies = null;
	private $aSearchCol = array(
		'st.stLogin','st.stName','st.stNO','st.stSchool','st.stDept','st.stSubject','st.stYear','st.stClass','st.stCourse'
	);

	public function action_index()
	{
		$sTitle = __('学生履修一覧');
		$aBreadCrumbs = array();

		$aWords = null;
		$sWords = null;
		$sW = Input::get('w',false);
		if ($sW)
		{
			$aW = \Clfunc_Common::getSearchWords($sW);
			$aWords = \Clfunc_Common::getSearchWhere($aW,$this->aSearchCol);
			$sWords = implode(' ', $aW);
		}

		# 学生を取得
		$aWhere = array(
			array('gsp.gtID','=',$this->aGroup['gtID'])
		);
		$result = Model_Group::getGroupStudents($aWhere,null,array('st.stNO'=>'asc'),$aWords);
		$iTotal = count($result);

		$config = array(
			'pagination_url' => CL_PROTOCOL.'://'.CL_DOMAIN.'/org/study/',
			'total_items'    => $iTotal,
			'per_page'       => 200,
			'num_links'      => 10,
			'show_first'     => true,
			'show_last'      => true,
			'uri_segment'    => 'p',
		);

		# ページング処理
		$oPagination = Pagination::forge('mypagination', $config);

		$aStIDs = null;
		$result = Model_Group::getGroupStudents($aWhere,null,array('st.stNO'=>'asc'),$aWords,$oPagination);
		if (count($result))
		{
			$this->aStudents = $result->as_array('stID');
			$aStIDs = array_keys($this->aStudents);
		}

		# 講義を取得
		$result = Model_Group::getGroupClasses2(array(array('gcp.gtID','=',$this->aGroup['gtID'])),null,array('ct.ctCode'=>'asc'));
		if (count($result))
		{
			$this->aClasses = $result->as_array('ctID');
		}

		# 講義を取得
		$result = Model_Student::getStudentPosition(array(array('stID','IN',$aStIDs)));
		if (count($result))
		{
			foreach ($result as $aSP)
			{
				if (isset($this->aClasses[$aSP['ctID']]))
				{
					$this->aStudies[$aSP['stID']][$aSP['ctID']] = true;
				}
			}
		}

		# パンくずリスト生成
		$aBreadCrumbs[] = array('name' => $sTitle);
		$this->template->set_global('breadcrumbs',$aBreadCrumbs);
		# ページタイトル生成
		$this->template->set_global('pagetitle',$sTitle);

		# カスタムボタン
		$aCustomMenu = array(
			array(
				'url'  => '/org/output/studymatrix.csv',
				'name' => __('一覧のCSVダウンロード'),
				'icon' => 'fa-download',
				'option' => array(
					'target' => '_blank',
				)
			),
		);
		$this->template->set_global('aCustomMenu',$aCustomMenu);

		$aSearchForm = array(
			'url' => '/org/study',
		);
		$this->template->set_global('aSearchForm',$aSearchForm);
		$this->template->set_global('sWords',$sWords);

		$this->template->content = View::forge($this->bn.DS.'index');
		$this->template->content->set('aStudents',$this->aStudents);
		$this->template->content->set_safe('oPagination',$oPagination);
		$this->template->content->set('aClasses',$this->aClasses);
		$this->template->content->set('aStudies',$this->aStudies);
		$this->template->javascript = array('cl.org.study.js');
		return $this->template;
	}

}


