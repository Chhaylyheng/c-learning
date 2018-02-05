<?php
	if (!is_null($aStu)):
		foreach ($aStu as $aS):
			$aM = array('no'=>'','name'=>'','ans'=>__('未回答'));
			if (isset($aS['put'])):
				$aM['no'] = $aS['put']['qpstNO'];
				$aM['name'] = $aS['put']['qpstName'];
				$sComIcon = null;
				if (($aQuest['qbComPublic'] == 2 || ($aQuest['qbComPublic'] == 1 && $aS['stu']['stID'] == $aStudent['stID'])) && $aS['put']['qpComment']):
					$sComIcon = Clfunc_Mobile::emj('SMILE');
				endif;
				$aM['ans'] = '<a href="/s/quest/ansdetail/'.$aQuest['qbID'].'/'.Crypt::encode($aS['stu']['stID']).Clfunc_Mobile::SesID().'">'.__('回答を見る').$sComIcon.'</a>';
			endif;
			if (isset($aS['stu'])):
				$aM['no'] = $aS['stu']['stNO'];
				$aM['name'] = $aS['stu']['stName'];
			endif;
			if ($aQuest['qbAnsPublic'] < 2):
				if ($aS['stu']['stID'] != $aStudent['stID']):
					$aM['name'] = __('匿名');
				endif;
			endif;
?>
	<div>
		<?php echo $aM['name']; ?><br>
		┗<?php echo $aM['ans']; ?><br>
	</div>
	<?php echo Clfunc_Mobile::hr(); ?>
<?php
		endforeach;
	endif;
?>
