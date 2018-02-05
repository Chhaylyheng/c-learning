<?php
class Model_Assistant extends \Model
{
	public static function getAssistantFromPostLogin($sMail = null,$sPass = null)
	{
		if (is_null($sMail) || is_null($sPass))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$query = DB::select_array()
				->from('Assistant_Table')
				->where('atMail',$sMail)
				->where('atPass',sha1($sPass))
			;
			$result = $query->execute();

			if (!count($result))
			{
				DB::start_transaction();
				$query = DB::update('Assistant_Table');
				$query->value('atPassMiss', DB::expr('`atPassMiss`+1'));
				$query->where('atMail',$sMail);
				$result2 = $query->execute();
				DB::commit_transaction();
			}
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function setLoginUpdate($aUser = null,$bTZ = false)
	{
		if (is_null($aUser))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::update('Assistant_Table');
			$query->value('atLoginNum', DB::expr('`atLoginNum`+1'));
			$query->value('atLastLoginDate', DB::expr('`atLoginDate`'));
			$query->value('atLoginDate', DB::expr('NOW()'));
			$query->value('atPassMiss', 0);
			$query->value('atUA', Input::user_agent());
			$query->value('atHash', sha1($aUser['atMail'].$aUser['atPass']));
			if ($bTZ)
			{
				$query->value('atTimeZone', $aUser['atTimeZone']);
			}
			$query->where('atID',$aUser['atID']);
			$result = $query->execute();
			DB::commit_transaction();

			$result = self::getAssistantFromID($aUser['atID']);
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function getAssistant($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('Assistant_Table');
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}

	public static function getAssistantPosition($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{

		$aNSubWhere = array('ct.ctID' => DB::expr('sp.ctID'), 'sp.spAuth' => 1);
		$subquery = DB::select(DB::expr('count(sp.stID)'))
			->from(array('StudentPosition_Table','sp'))
			->where($aNSubWhere)
			->compile()
		;
		$sNSub = '('.$subquery.') AS `scNum`';

		$aWSubWhere = array('ct.ctID' => DB::expr('sp.ctID'), 'sp.spAuth' => 0);
		$subquery = DB::select(DB::expr('count(sp.stID)'))
			->from(array('StudentPosition_Table','sp'))
			->where($aWSubWhere)
			->compile()
		;
		$sWSub = '('.$subquery.') AS `scWaitNum`';

		$query = DB::select_array(
				array(
					'ap.*',
					'ct.*',
					DB::expr($sNSub),
					DB::expr($sWSub),
				)
			)
			->from(array('AssistantPosition_Table','ap'))
			->join(array('Class_Table','ct'),'LEFT')
			->on('ap.ctID','=','ct.ctID')
		;
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			foreach ($aSort as $sC => $sS)
			{
				$query->order_by($sC,$sS);
			}
		}
		$result = $query->execute();
		return $result;
	}

	public static function getAssistantFromMail($sMail = null, $sAtID = null)
	{
		$query = DB::select_array()
			->from('Assistant_Table')
			->where('atMail',$sMail)
		;
		if (!is_null($sAtID))
		{
			$query->where('atID','!=',$sAtID);
		}
		$result = $query->execute();

		return $result;
	}
	public static function getAssistantFromHash($sHash = null)
	{
		$query = DB::select_array()
			->from('Assistant_Table')
			->where('atHash',$sHash)
		;
		$result = $query->execute();

		return $result;
	}
	public static function getAssistantFromID($sID = null)
	{
		$result = DB::select_array()
			->from('Assistant_Table')
			->where('atID',$sID)
			->execute()
		;
		return $result;
	}

	public static function insertAssistant($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			// IDを生成
			$sAtID = self::getAssistantID();
			$aInsert['atID'] = $sAtID;
			$aInsert['atFirst'] = strtolower(Str::random('distinct', 8));
			$aInsert['atPass'] = sha1($aInsert['atFirst']);
			$aInsert['atHash'] = sha1($aInsert['atMail'].$aInsert['atPass']);
			$aInsert['atDate'] = date('YmdHis');

			$result = DB::insert('Assistant_Table')
				->set($aInsert)
				->execute()
			;

			DB::commit_transaction();
			// クエリの結果を返す
			return $sAtID;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}


	public static function getAssistantFromClass($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array(
			array(
				'at.*','ap.*',
			)
		)
		->from(array('AssistantPosition_Table','ap'))
		->join(array('Assistant_Table','at'),'LEFT')
		->on('ap.atID','=','at.atID')
		;
		if (!is_null($aAndWhere))
		{
			foreach ($aAndWhere as $aW)
			{
				$query->where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aOrWhere))
		{
			foreach ($aOrWhere as $aW)
			{
				$query->or_where($aW[0],$aW[1],$aW[2]);
			}
		}
		if (!is_null($aSort))
		{
			if ($aSort !== false)
			{
				foreach ($aSort as $sC => $sS)
				{
					$query->order_by($sC,$sS);
				}
			}
		}

		$result = $query->execute();
		return $result;
	}

	public static function updateAssistant($sAtID = null,$aUpdate = null)
	{
		if (is_null($sAtID) || is_null($aUpdate))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$result = DB::update('Assistant_Table')
				->where('atID',$sAtID)
				->set($aUpdate)
				->execute()
			;

			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function setAssistant($sCtID = null, $sAtID =null)
	{
		if (is_null($sAtID) || is_null($sCtID))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			$aInsert = array(
				'ctID' => $sCtID,
				'atID' => $sAtID,
				'apSort' => self::getAClassSort($sAtID),
				'apDate' => date('YmdHis'),
			);

			DB::start_transaction();

			$result = DB::insert('AssistantPosition_Table')
				->set($aInsert)
				->execute()
			;

			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}

	}

	public static function removeAssistant($aAndWhere = null)
	{
		if (is_null($aAndWhere))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			$query = DB::delete('AssistantPosition_Table');

			if (!is_null($aAndWhere))
			{
				foreach ($aAndWhere as $aW)
				{
					$query->where($aW[0],$aW[1],$aW[2]);
				}
			}

			$result = $query->execute();

			DB::commit_transaction();
			// クエリの結果を返す
			return $result;
		}
		catch (Exception $e)
		{
			// 未決のトランザクションクエリをロールバックする
			DB::rollback_transaction();
			throw $e;
		}
	}

	public static function resetAssistantClassSort($aAIDs)
	{
		$result = self::getAssistantPosition(array(array('ap.atID','IN',$aAIDs),array('ct.ctStatus','=',1)),null,array('ap.atID'=>'asc','ap.apSort'=>'asc'));
		if (!count($result))
		{
			return;
		}

		try
		{
			$sAtID = null;
			$iSort = 1;
			foreach ($result as $aC)
			{
				if ($sAtID !== $aC['atID'])
				{
					$iSort = 1;
				}

				$res = DB::update('AssistantPosition_Table')
					->where('atID','=',$aC['atID'])
					->where('ctID','=',$aC['ctID'])
					->set(array('apSort'=>$iSort))
					->execute()
				;

				$iSort++;
				$sAtID = $aC['atID'];
			}
			return;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getAssistantID($aIDs = null)
	{
		try
		{
			while (true)
			{
				$sAtID = 'a'.Str::random('numeric',9);
				if (!is_null($aIDs))
				{
					if (array_search($sAtID, $aIDs) !== false)
					{
						continue;
					}
				}
				$result1 = DB::select()->from('Assistant_Table')->where('atID',$sAtID)->execute()->as_array();
				$result2 = DB::select()->from('AssistantMissingID_Table')->where('atID',$sAtID)->execute()->as_array();
				if (empty($result1) && empty($result2))
				{
					break;
				}
			}
			return $sAtID;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	private static function getAClassSort($sAtID = null)
	{
		$result = self::getAssistantPosition(array(array('ap.atID','=',$sAtID),array('ct.ctStatus','=',1)),null,array('ap.apSort'=>'desc'));
		if (!count($result))
		{
			return 1;
		}
		$aClass = $result->as_array();

		return ($aClass[0]['apSort'] + 1);
	}

}

