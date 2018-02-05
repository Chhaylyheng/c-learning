<?php
	if (!is_null($aReport)):
		foreach ($aReport as $aR):
			$sLink = null;
			$sPath = '';
			$sSize = '';
			$sRPath = '';
			$sRSize = '';
			$aPub = array(__('締切'),'red');
			if ($aR['rbPublic'] == 1):
				$sLink = 'put';
				$aPub = array(__('公開中'),'blue');
				if ($aR['rbAutoCloseDate'] != CL_DATETIME_DEFAULT):
					$aPub[2] = '～ '.date('n/j H:i',strtotime($aR['rbAutoCloseDate']));
				endif;
			endif;

			if ($aR['baseFID'] != ''):
				$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aR['baseFID'],'mode'=>'e'));
				$sSize = \Clfunc_Common::FilesizeFormat($aR['baseFSize'],1);
				$sThumb = null;
				if ($aR['baseFFileType'] == 1):
					$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aR['baseFID'],'mode'=>'tm'));
				endif;
				$sFile = Clfunc_Mobile::emj('CLIP').'<a href="'.$sPath.'">'.$aR['baseFName'].'</a> ('.$sSize.')';
			endif;

			if ($aR['resultFID'] != ''):
				$sRPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aR['resultFID'],'mode'=>'e'));
				$sRSize = \Clfunc_Common::FilesizeFormat($aR['resultFSize'],1);
				$sRThumb = null;
				if ($aR['resultFFileType'] == 1):
					$sRThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aR['resultFID'],'mode'=>'tm'));
				endif;
				$sRFile = Clfunc_Mobile::emj('CLIP').'<a href="'.$sRPath.'">'.$aR['resultFName'].'</a> ('.$sRSize.')';
			endif;

			$sMine = '';
			$sShare = '';
			if ($aR['rbShare'] > 0):
				$sMine = '┣<a href="/s/report/shareboard/'.$aR['rbID'].DS.$aStudent['stID'].'/s'.Clfunc_Mobile::SesID().'">';
				if ($aR['rbShare'] == 2):
					$sMine .= '★'.((isset($aR['RPut']['rpAvgScore']))? $aR['RPut']['rpAvgScore']:0).' ';
				endif;
				$sMine .= Clfunc_Mobile::emj('SMILE').((isset($aR['RPut']['rpComNum']))? $aR['RPut']['rpComNum']:0);
				$sMine .= '</a><br>';

				$sShare .= '┣<a href="/s/report/list/'.$aR['rbID'].'">'.__('共有板').'</a><br>';
				$sSep = '┣';
			endif;

			$sIcon = '<span style="color: #CC0000;">'.Clfunc_Mobile::emj('PENCIL').'</span>';
			$pDate = '<span style="color: #CC0000;">'.__('未提出').'</span><br>';
			if (isset($aR['RPut'])):
				$aP = $aR['RPut'];
				$sIcon = '<span style="color: #008800;">'.__('[済]').'</span>';
				$pDate = '<span style="color: #008800;">'.__('提出済み').'('.date('Y/m/d H:i', strtotime($aP['rpDate'])).')</span><br>';
				$pText = ($aP['rpText'])? '┣'.__('提出テキスト').'<div style="color: #008800;">'.nl2br(\Clfunc_Common::url2link($aP['rpText'],0)).'</div>':'';
				$pFiles = '';
				for ($i = 1; $i <= 3; $i++):
					if (isset($aP['fID'.$i])):
						$pFiles .= Clfunc_Mobile::emj('CLIP').'<a href="'.\Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aP['fID'.$i],'mode'=>'e')).'">'.$aP['fName'.$i].'</a> ('.\Clfunc_Common::FilesizeFormat($aP['fSize'.$i],1).')<br>';
					endif;
				endfor;
				if ($pFiles):
					$pFiles = '┣'.__('提出ファイル').'<br>'.$pFiles;
				endif;

				$rScore = null;
				$rComment = null;
				$rFiles = null;

				if ($aR['rbRatePublic']):
					$rFiles = '';
					for ($i = 1; $i <= 3; $i++):
						if (isset($aP['rID'.$i])):
							$rFiles .= Clfunc_Mobile::emj('CLIP').'<a href="'.\Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aP['rID'.$i],'mode'=>'e')).'">'.$aP['rName'.$i].'</a> ('.\Clfunc_Common::FilesizeFormat($aP['rSize'.$i],1).')<br>';
						endif;
					endfor;
					if ($rFiles):
						$rFiles = '┣'.__('評価ファイル').'<br>'.$rFiles;
					endif;
					$rComment = ($aP['rpComment'])? '┣'.__('先生コメント').'<div style="color: #008800;">'.nl2br(\Clfunc_Common::url2link($aP['rpComment'],0)).'</div>':'';
					$rScore = ($aP['rpScore'])? '┣'.__('評価').' <span style="color: #008800;">'.$aRateMaster[$aP['rpScore']]['rrName'].'</span><br>':'┣'.__('評価').' <span style="color: #CC0000;">'.__('未評価').'</span><br>';
				endif;
			endif;

?>
<div>
	<?php echo $sIcon; ?><?php echo $aR['rbTitle']; ?><br>
	┣<span style="color: <?php echo $aPub[1]; ?>;"><?php echo $aPub[0]; ?></span>
	<?php if (isset($aPub[2])): ?> <?php echo Clfunc_Mobile::emj('CLOCK').$aPub[2]; ?><?php endif; ?>
	<br>
	┣<?php echo $pDate; ?>
	<?php if ($aR['rbPublic'] == 1 && (!isset($aR['RPut']) || $aR['RPut']['rpScore'] == 0)): ?>
	┣<a href="/s/report/put/<?php echo $aR['rbID'].Clfunc_Mobile::SesID(); ?>"><?php echo (isset($aR['RPut']))?  __('レポート再提出'):__('レポート提出'); ?></a><br>
	<?php endif; ?>
	<?php if (isset($aR['RPut'])): ?>
<?php
	echo $rScore;
	echo $pFiles;
	echo $pText;
	echo $rComment;
	echo $rFiles;
?>
	<?php endif; ?>
<?php
	echo $sMine;
	echo $sShare;
?>
■<?php echo __('内容/備考'); ?><br>
	<?php echo nl2br(\Clfunc_Common::url2link($aR['rbText'],400)); ?><br>
<?php
if ($sPath):
switch ($aR['baseFFileType']):
	case 1:
?>
<img style="max-width: 100%;" src="<?php echo $sThumb; ?>" alt="<?php echo $aR['baseFName'].' ('.$sSize.')'; ?>">
<?php
	break;
	default:
		echo $sFile;
	break;
endswitch;
?>
<br>
<?php endif; ?>

<?php if ($sRPath): ?>
■<?php echo __('結果ファイル'); ?><br>
<?php
switch ($aR['resultFFileType']):
	case 1:
?>
<img style="max-width: 100%;" src="<?php echo $sRThumb; ?>" alt="<?php echo $aR['baseFName'].' ('.$sRSize.')'; ?>">
<?php
	break;
	default:
		echo $sRFile;
	break;
endswitch;
?>
<br>
<?php endif; ?>

</div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php
		endforeach;
	endif;
?>
