<?php if (!$aStudent['stMailAuth']): ?>
<div style="background-color: #aa0000; color: white; padding: 5px 0; font-size: 80%; margin-bottom: 5px;">
<?php echo Clfunc_Mobile::emj('WARN').__('メールアドレスの認証が完了していません。'); ?><br>
<div style="padding: 3px 0; text-align: center;"><?php echo Clfunc_Mobile::emj('MAIL'); ?><a href="/s/profile/mailauth/<?php echo Clfunc_Mobile::SesID(); ?>" style="color: white;"><?php echo __('メールアドレス認証メールを再送信する'); ?></a></div>
</div>
<?php endif; ?>

<div style="text-align:center; font-size: 80%;" align="center">
	<?php echo $sTitle; ?><br>
	<?php echo $sLogined; ?>
</div>

<?php echo Clfunc_Mobile::hr(); ?>

<div style="text-align:center; font-size: 80%;" align="center"><?php echo __('履修中の講義'); ?></div>
<?php echo Clfunc_Mobile::hr(); ?>

<?php
if (!is_null($aActClass)):
	$sWD = null;
	$iToday = strtotime(date('Y/m/d'));
	foreach ($aActClass as $aC):
		if (CL_CAREERTASU_MODE && !isset($aCTeach[$aC['ctID']])):
			continue;
		endif;
		if (CL_CAREERTASU_MODE && isset($aCTeach[$aC['ctID']]) && (strtotime($aCTeach[$aC['ctID']]['ttCTStart']) > $iToday || strtotime($aCTeach[$aC['ctID']]['ttCTEnd']) < $iToday)):
			continue;
		endif;
		$iUn = 0;
		if (isset($aUnread[$aC['ctID']])):
			$iUn = (int)$aUnread[$aC['ctID']];
		endif;

		if ($aWeekDay[$aC['ctWeekDay']] != $sWD):
?>
		<div style="margin-top: 5px; font-size: 80%;"><?php echo ($sWD = $aWeekDay[$aC['ctWeekDay']]); ?></div>
	<?php endif; ?>
	<div style="font-size: 80%;">
		<a href="/s/class/index/<?php echo $aC['ctID'].Clfunc_Mobile::SesID(); ?>"><?php echo ($aC['dhNO'])? $aHour[$aC['dhNO']]:'　 '; ?>　<?php echo $aC['ctName'].(($iUn)? '<span style="color: red;">['.$iUn.']</span>':''); ?></a>
	</div>

<?php if ($aC['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_NEWS): ?>
<?php if (isset($aNewsList[$aC['ctID']])): ?>
<?php foreach ($aNewsList[$aC['ctID']] as $aN): ?>
<?php
$sLink = null;
if ($aN['cnChain']):
	$aU = $aN['cnChain'];

	$aAnc = explode('#', $aU['url']);
	$sURL = $aAnc[0];
	$sAnc = (isset($aAnc[1]))? '#'.$aAnc[1]:null;

	$sPut = ($aU['put'])? __('[済]'):'';
	if ($aU['public'] == 0):
		$sLink = '<span>'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</span>';
	else:
		if ($aU['public'] == 1):
			$sLink = '<a href="'.$sURL.DS.'top'.DS.Clfunc_Mobile::SesID().'&ct='.$aC['ctID'].$sAnc.'">'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</a>';
		else:
			if ($sPut):
				$sLink = '<a href="'.$sURL.DS.'top'.DS.Clfunc_Mobile::SesID().'&ct='.$aC['ctID'].$sAnc.'">'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</a>';
			else:
				$sLink = '<span>'.\Clfunc_Mobile::emj('CLIP').$aU['title'].$sPut.'</span>';
			endif;
		endif;
	endif;
endif;
?>

<?php $sBHead = mb_strimwidth($aN['cnBody'],0,60,'…','UTF-8'); ?>
<marquee behavior="scroll" scrolldelay="30" style="background-color: #cccccc; margin-bottom: 1px; padding: 2px 0; font-size: 80%; white-space: norap; width: 100%; text-overflow: ellipsis;"><a href="/s/news/detail/<?php echo $aN['no'].DS.Clfunc_Mobile::SesID().'&ct='.$aC['ctID']; ?>" style="color: black;"><?php echo $sBHead; ?></a><?php echo $sLink; ?></marquee>
<?php endforeach; ?>
<div style="margin-bottom: 5px;"></div>
<?php endif; ?>
<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>

<?php if (!CL_CAREERTASU_MODE && !is_null($aActClass)): ?>

<?php echo Clfunc_Mobile::hr(); ?>
<div style="margin-bottom: 5px; text-align: center; font-size: 80%;"><a href="/s/attend/history/1<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('出席履歴'); ?></a></div>

<?php endif; ?>

<?php if (is_null($aGroup) || !($aGroup['gtStudentAuthFlag'] & \Clfunc_Flag::S_AUTH_STADY)): ?>

<?php echo Clfunc_Mobile::hr(); ?>
<div style="text-align:center; font-size: 80%;" align="center"><?php echo __('履修登録'); ?></div>
<?php echo Form::open(array('action'=>'/s/class/entry'.Clfunc_Mobile::SesID(),'method'=>'post')) ; ?>
	<?php echo Clfunc_Mobile::SesID('post'); ?>
	<div style="font-size: 80%;">
		<label><?php echo __('履修する講義の講義コード'); ?><br>
			<?php echo Form::input('c_code','',array('maxlength'=>'20')); ?>
		</label>
	</div>
	<div style="text-align: center; font-size: 80%;" align="center"><?php echo Form::submit('c_submit',__('講義の確認')); ?></div>
<?php Form::close(); ?>

<?php endif; ?>

<?php echo Clfunc_Mobile::hr(); ?>

<div style=" font-size: 80%;"><a href="/s/profile<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('アカウント設定'); ?></a></div>
<div style=" font-size: 80%;"><a href="/s/index/logout<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('ログアウト'); ?></a></div>

