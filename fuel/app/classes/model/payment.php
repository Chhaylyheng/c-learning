<?php
class Model_Payment extends \Model
{
	public static function getPlan($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('Plan_Master');
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

	public static function getCoupon($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('Coupon_Table');
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

	public static function getPointRate($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('PointRate_Master');
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


	public static function getPayDoc($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array()->from('PaymentDoc_View');
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


	public static function getPaymentHistory($aAndWhere = null,$aOrWhere = null,$aSort = null)
	{
		$query = DB::select_array(
			array(
				'ph.*',
				'tt.ttName','tt.cmName','tt.ttMail',
				'cp.cpName',
			)
		)
			->from(array('PaymentHistory_Table','ph'))
			->join(array('Teacher_View','tt'),'LEFT')
			->on('ph.ttID','=','tt.ttID')
			->join(array('Coupon_Table','cp'),'LEFT')
			->on('ph.cpNO','=','cp.no')
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


	public static function insertPayDoc($aInsert = null)
	{
		if (is_null($aInsert))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$result = DB::insert('PaymentDoc_Table')->set($aInsert)->execute();
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


	public static function updatePayDoc($aInsert =null,$aE = null,$bBill = false)
	{
		if (is_null($aInsert) || is_null($aE))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			if ($bBill)
			{
				$result = DB::select_array()
					->from('BillNumber_Table')
					->where('month','=',date('Ym'))
					->execute();

				if (count($result))
				{
					$aB = $result->current();
					$bNO = (int)$aB['no'] + 1;
					$result = DB::update('BillNumber_Table')
					->where('month','=',date('Ym'))
					->set(array('no'=>$bNO))
					->execute();
				}
				else
				{
					$bNO = 1;
					$result = DB::insert('BillNumber_Table')
					->set(array('month'=>date('Ym'),'no'=>$bNO))
					->execute();
				}
				$aBK = array(1=>'C',2=>'B');
				$aInsert['bNO'] = 'CL-'.date('Ym').'-'.$aBK[$aE['eBilling']].'-'.sprintf('%05d',$bNO);
			}

			$query = DB::update('PaymentDoc_Table');
			$query->where('eNO','=',$aE['eNO']);
			$query->set($aInsert);
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


	public static function deletePayDoc($sNO =null)
	{
		if (is_null($sNO))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();
			$query = DB::delete('PaymentDoc_Table');
			$query->where('eNO','=',$sNO);
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


	public static function setPaymentBank($sPayDate = null,$aBill = null)
	{
		if (is_null($sPayDate) || is_null($aBill))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			foreach ($aBill as $aB)
			{
				# 注文更新
				$aUpdate = array(
					'status'     => 2,
					'bPayDate'   => date('Ymd',strtotime($sPayDate)),
					'updateDate' => date('YmdHis'),
				);
				$result = DB::update('PaymentDoc_Table')
					->where('eNO','=',$aB['eNO'])
					->set($aUpdate)
					->execute();
				# 購入履歴更新
				$aInsert = array(
					'ttID'       => $aB['ttID'],
					'eNO'        => $aB['eNO'],
					'bNO'        => $aB['bNO'],
					'billing'    => 2,
					'point'      => $aB['point'],
					'price'      => $aB['ePrice'],
					'tax'        => ($aB['ePrice'] * $aB['eTax']),
					'bPayDate'   => date('Ymd',strtotime($sPayDate)),
					'createDate' => date('YmdHis'),
				);
				$result = DB::insert('PaymentHistory_Table')
					->set($aInsert)
					->execute();
				# ポイント付与履歴
				$aInsert = array(
					'ttID'       => $aB['ttID'],
					'phID'       => 1,
					'text'       => '銀行振込で購入',
					'prevP'      => $aB['ttPoint'],
					'plusP'      => $aB['point'],
					'nowP'       => $aB['ttPoint']+$aB['point'],
					'createDate' => date('YmdHis'),
				);
				$result = DB::insert('PointHistory_Table')
					->set($aInsert)
					->execute();
				# ポイント付与
				$result = DB::update('Teacher_Table')
					->where('ttID','=',$aB['ttID'])
					->value('ttPoint', DB::expr('`ttPoint`+'.$aB['point']))
					->execute();
			}
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

	public static function setPaymentCard($aE)
	{
		if (is_null($aE))
		{
			throw new Exception('処理に必要な情報がありません');
		}
		try
		{
			DB::start_transaction();

			# 請求番号生成
			$result = DB::select_array()
				->from('BillNumber_Table')
				->where('month','=',date('Ym'))
				->execute();

			if (count($result))
			{
				$aB = $result->current();
				$bNO = (int)$aB['no'] + 1;
				$result = DB::update('BillNumber_Table')
					->where('month','=',date('Ym'))
					->set(array('no'=>$bNO))
					->execute();
			}
			else
			{
				$bNO = 1;
				$result = DB::insert('BillNumber_Table')
					->set(array('month'=>date('Ym'),'no'=>$bNO))
					->execute();
			}
			$aBK = Clfunc_Flag::getPaymentCode();
			$sbNO = 'CL-'.date('Ym').'-'.$aBK[$aE['eBilling']].'-'.sprintf('%05d',$bNO);

			# 注文更新
			$aUpdate = array(
				'status'     => 2,
				'bNO'        => $sbNO,
				'bDate'      => date('YmdHis'),
				'bPayDate'   => date('Ymd'),
				'updateDate' => date('YmdHis'),
				'purchase'   => $aE['purchase'],
			);
			$result = DB::update('PaymentDoc_Table')
				->where('eNO','=',$aE['eNO'])
				->set($aUpdate)
				->execute();

			# 購入履歴更新
			$aInsert = array(
				'ttID'          => $aE['ttID'],
				'eNO'           => $aE['eNO'],
				'bNO'           => $sbNO,
				'billing'       => 1,
				'point'         => $aE['point'],
				'price'         => $aE['ePrice'],
				'tax'           => ($aE['ePrice'] * $aE['eTax']),
				'bPayDate'      => date('YmdHis'),
				'createDate'    => date('YmdHis'),
				'transactionID' => ((isset($aE['tranID']))? $aE['tranID']:''),
			);
			$result = DB::insert('PaymentHistory_Table')
				->set($aInsert)
				->execute();

			# ポイント付与履歴
			$aInsert = array(
				'ttID'       => $aE['ttID'],
				'phID'       => 1,
				'text'       => 'クレジットカード決済で購入',
				'prevP'      => $aE['ttPoint'],
				'plusP'      => $aE['point'],
				'nowP'       => $aE['ttPoint'] + $aE['point'],
				'createDate' => date('YmdHis'),
			);
			$result = DB::insert('PointHistory_Table')
				->set($aInsert)
				->execute();
			# ポイント付与
			$result = DB::update('Teacher_Table')
				->where('ttID','=',$aE['ttID'])
				->value('ttPoint', DB::expr('`ttPoint`+'.$aE['point']))
				->execute();

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

}
