<?php
$sMyID = $aTeacher['ttID'];
$sMyName = $aTeacher['ttName'];

if (!is_null($aAssistant)):
	$sMyID = $aAssistant['atID'];
	$sMyName = $aAssistant['atName'];
endif;
?>
<div class="res-field"></div>
<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<table class="kreport-data">
		<thead>
			<tr>
				<th colspan="4"><?php echo __('タイトル'); ?></th>
				<th style="width: 30%;"><?php echo __('ファイル'); ?></th>
				<th><?php echo __('投稿者'); ?></th>
				<th><?php echo __('登録日時'); ?></th>
				<th><?php echo __('文字数'); ?></th>
				<th><?php echo __('コメント'); ?></th>
				<th><?php echo __('操作'); ?></th>
				<th><?php echo __('既読'); ?></th>
			</tr>
		</thead>
		<?php
			if (!is_null($aParents)):
				$iMax = count($aParents);
				foreach ($aParents as $aP):
					$bTeach = preg_match('/^[t|a]/', $aP['cID']);
					$cName = ($aP['atName'])? $aP['atName']:(($aP['ttName'])? $aP['ttName']:(($aP['stName'])? $aP['stName']:$aP['cName']));
					$cColor = ($bTeach)? 'font-red':'font-green';
					$sJsKey = $aP['ccID'].'_'.$aP['cNO'];
					$aFiles = null;
					for ($i = 1; $i <= 3; $i++):
						if ($aP['fID'.$i]):
							$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aP['fID'.$i]));
							$sSize = \Clfunc_Common::FilesizeFormat($aP['fSize'.$i],1);
							$aFiles[$i] = '<p class="mt0"><i class="fa fa-paperclip"></i> <a href="'.$sLink.'" target="_blank">'.$aP['fName'.$i].'</a>('.$sSize.')</p>';
						endif;
					endfor;
					$sDate = ($aP['cDate'] != '0000-00-00 00:00:00')? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aP['cDate']):'─';
					$sDate = str_replace(' ', '<br class="pc-display-inline">', $sDate);
					if ($aP['cID'] == $sMyID):
						$aWriter = array('font-red',$sMyName);
					else:
						switch ($aCCategory['ccAnonymous']):
							case 0:
								$aWriter = array('font-gray', __('匿名'));
							break;
							case 1:
								if ($bTeach):
									$aWriter = array($cColor, $cName);
								else:
									$aWriter = array('font-gray', __('匿名'));
								endif;
							break;
							case 2:
								$aWriter = array($cColor, $cName);
							break;
						endswitch;
					endif;
					$sNew = (!isset($aAlready[$aP['cNO']]))? '<span class="attention attn-emp">N</span>':'';
					$aSort = array(' ',' ');
					if ($aP['cSort'] == $iMax):
					$aSort[0] = ' disabled="disabled"';
					endif;
					if ($aP['cSort'] == 1):
					$aSort[1] = ' disabled="disabled"';
					endif;
					$sFA = (isset($aCoops[$aP['cNO']]))? 'fa-plus-square-o':'';
					$sDeleteBtnDisp = ($aP['cID'] == $sMyID || !$bTeach)? '':' display: none;';
		?>
		<tbody>
		<tr class="c<?php echo $aP['cNO']; ?>" id="c<?php echo $aP['cNO']; ?>">
			<td class="CoopListIcon CoopRootIcon font-size-120"><i class="fa <?php echo $sFA; ?>" data="0"></i></td>
			<td class="" colspan="3">
				<a href="/t/coop/thread/<?php echo $aP['ccID'].DS.$aP['cNO'].'#c'.$aP['cNO']; ?>" class="button na do width-auto" style="padding: 8px;"><?php echo $aP['cTitle']; ?></a><?php echo $sNew; ?>
			</td>
			<td class="sp-full">
				<?php
					if (!is_null($aFiles)):
						foreach ($aFiles as $sF):
							echo $sF;
						endforeach;
					endif;
				?>
			</td>
			<td class="" nowrap="nowrap">
				<span class="<?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span>
			</td>
			<td class="" nowrap="nowrap">
				<?php echo $sDate; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('文字数'); ?>:</span
				><?php echo $aP['cCharNum']; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('コメント'); ?>:</span
				><span class="thread-comnum"><?php echo (int)(isset($aCnt['r'.$aP['cNO']]))? $aCnt['r'.$aP['cNO']]:0; ?></span>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="quest-dropdown-toggle" id="<?php echo $sJsKey; ?>_pedit_list" style="<?php echo $sDeleteBtnDisp; ?>"><div><?php echo __('管理'); ?></div></button>
				</div>
				<button<?php echo $aSort[0]; ?> class="CoopSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
				<button<?php echo $aSort[1]; ?> class="CoopSort button na default width-auto text-center" style="padding: 6px 4px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('既読'); ?>:</span
				><a href="/t/coop/already/<?php echo $aP['ccID'].DS.$aP['cNO']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo $aP['cAlreadyNum']; ?></a>
			</td>
		</tr>
		<?php
			if (isset($aCoops[$aP['cNO']])):
				foreach ($aCoops[$aP['cNO']] as $iC => $aC):
					$bTeach = preg_match('/^[t|a]/', $aC['cID']);
					$cName = ($aC['atName'])? $aC['atName']:(($aC['ttName'])? $aC['ttName']:(($aC['stName'])? $aC['stName']:$aC['cName']));
					$cColor = ($bTeach)? 'font-red':'font-green';
					$sJsKey = $aC['ccID'].'_'.$aC['cNO'];
					$aFiles = null;
					for ($i = 1; $i <= 3; $i++):
						if ($aC['fID'.$i]):
							$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aC['fID'.$i]));
							$sSize = \Clfunc_Common::FilesizeFormat($aC['fSize'.$i],1);
							$aFiles[$i] = '<p class="mt0"><i class="fa fa-paperclip"></i> <a href="'.$sLink.'" target="_blank">'.$aC['fName'.$i].'</a>('.$sSize.')</p>';
						endif;
					endfor;
					$sDate = ($aC['cDate'] != '0000-00-00 00:00:00')? ClFunc_Tz::tz('Y/m/d\<\b\\r\>H:i',$tz,$aC['cDate']):'─';
					$sDate = str_replace(' ', '<br class="pc-display-inline">', $sDate);
					if ($aC['cID'] == $sMyID):
						$aWriter = array('font-red',$sMyName);
					else:
						switch ($aCCategory['ccAnonymous']):
							case 0:
								$aWriter = array('font-gray', __('匿名'));
							break;
							case 1:
								if ($bTeach):
									$aWriter = array($cColor, $cName);
								else:
									$aWriter = array('font-gray', __('匿名'));
								endif;
							break;
							case 2:
								$aWriter = array($cColor, $cName);
							break;
						endswitch;
					endif;
					$sNew = (!isset($aAlready[$aC['cNO']]))? '<span class="attention attn-emp">N</span>':'';
					$sDelete = ($aC['cID'] == $sMyID || !$bTeach)? '':'_nondel';
		?>
		<tr class="c<?php echo $aP['cNO']; ?> c<?php echo $aC['cNO']; ?>" id="c<?php echo $aC['cNO']; ?>" style="display: none;">
			<td class="CoopListIcon"></td>
			<td class="CoopListIcon"><span class="tree-line"></span></td>
			<td class="" colspan="2">
				<a href="/t/coop/thread/<?php echo $aP['ccID'].DS.$aP['cNO'].'#c'.$aC['cNO']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo ($aC['cText'])? mb_strimwidth($aC['cText'],0,30,'…'):'　'; ?></a><?php echo $sNew; ?>
			</td>
			<td class="sp-full">
				<?php
					if (!is_null($aFiles)):
						foreach ($aFiles as $sF):
							echo $sF;
						endforeach;
					endif;
				?>
			</td>
			<td class="" nowrap="nowrap">
				<span class="<?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span>
			</td>
			<td class="" nowrap="nowrap">
				<?php echo $sDate; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('文字数'); ?>:</span
				><?php echo $aC['cCharNum']; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('コメント'); ?>:</span
				><span class="thread-comnum"><?php echo (int)(isset($aCnt['p'.$aC['cNO']]))? $aCnt['p'.$aC['cNO']]:0; ?></span>
			</td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="quest-dropdown-toggle" id="<?php echo $sJsKey; ?>_cedit_list<?php echo $sDelete; ?>"><div><?php echo __('管理'); ?></div></button>
				</div>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('既読'); ?>:</span
				><a href="/t/coop/already/<?php echo $aC['ccID'].DS.$aC['cNO']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo $aC['cAlreadyNum']; ?></a>
			</td>
		</tr>
		<?php
			if (isset($aCoops[$aP['cNO']][$iC]['children'])):
				foreach ($aCoops[$aP['cNO']][$iC]['children'] as $iGC => $aGC):
					$bTeach = preg_match('/^[t|a]/', $aGC['cID']);
					$cName = ($aGC['atName'])? $aGC['atName']:(($aGC['ttName'])? $aGC['ttName']:(($aGC['stName'])? $aGC['stName']:$aGC['cName']));
					$cColor = ($bTeach)? 'font-red':'font-green';
					$sJsKey = $aGC['ccID'].'_'.$aGC['cNO'];
					$aFiles = null;
					for ($i = 1; $i <= 3; $i++):
						if ($aGC['fID'.$i]):
							$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aGC['fID'.$i]));
							$sSize = \Clfunc_Common::FilesizeFormat($aGC['fSize'.$i],1);
							$aFiles[$i] = '<p class="mt0"><i class="fa fa-paperclip"></i> <a href="'.$sLink.'" target="_blank">'.$aGC['fName'.$i].'</a>('.$sSize.')</p>';
						endif;
					endfor;
					$sDate = ($aGC['cDate'] != '0000-00-00 00:00:00')? ClFunc_Tz::tz('Y/m/d\<\b\\r\>H:i',$tz,$aGC['cDate']):'─';
					$sDate = str_replace(' ', '<br class="pc-display-inline">', $sDate);
					if ($aGC['cID'] == $sMyID):
						$aWriter = array('font-red',$sMyName);
					else:
						switch ($aCCategory['ccAnonymous']):
							case 0:
								$aWriter = array('font-gray', __('匿名'));
							break;
							case 1:
								if ($bTeach):
									$aWriter = array($cColor, $cName);
								else:
									$aWriter = array('font-gray', __('匿名'));
								endif;
							break;
							case 2:
								$aWriter = array($cColor, $cName);
							break;
						endswitch;
					endif;
					$sNew = (!isset($aAlready[$aGC['cNO']]))? '<span class="attention attn-emp">N</span>':'';
					$sDeleteBtnDisp = ($aGC['cID'] == $sMyID || !$bTeach)? '':' display: none;';
		?>
		<tr class="c<?php echo $aP['cNO']; ?> c<?php echo $aC['cNO']; ?> c<?php echo $aGC['cNO']; ?>" id="c<?php echo $aGC['cNO']; ?>" style="display: none;">
			<td class="CoopListIcon"></td>
			<td class="CoopListIcon"></td>
			<td class="CoopListIcon"><span class="tree-line"></span></td>
			<td class="">
				<a href="/t/coop/thread/<?php echo $aP['ccID'].DS.$aP['cNO'].'#c'.$aGC['cNO']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo ($aGC['cText'])? mb_strimwidth($aGC['cText'],0,30,'…'):'　'; ?></a><?php echo $sNew; ?>
			</td>
			<td class="sp-full">
				<?php
					if (!is_null($aFiles)):
						foreach ($aFiles as $sF):
							echo $sF;
						endforeach;
					endif;
				?>
			</td>
			<td class="" nowrap="nowrap">
				<span class="<?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span>
			</td>
			<td class="" nowrap="nowrap">
				<?php echo $sDate; ?>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('文字数'); ?>:</span
				><?php echo $aGC['cCharNum']; ?>
			</td>
			<td class="" nowrap="nowrap"></td>
			<td class="">
				<div class="dropdown inline-block">
					<button type="button" class="quest-dropdown-toggle" id="<?php echo $sJsKey; ?>_cedit_list_gc" style="<?php echo $sDeleteBtnDisp; ?>"><div><?php echo __('管理'); ?></div></button>
				</div>
			</td>
			<td class="" nowrap="nowrap"><span class="sp-display-inline font-grey"><?php echo __('既読'); ?>:</span
				><a href="/t/coop/already/<?php echo $aGC['ccID'].DS.$aGC['cNO']; ?>" class="button na default width-auto" style="padding: 8px;"><?php echo $aGC['cAlreadyNum']; ?></a>
			</td>
		</tr>
		<?php
			endforeach;
			endif;
		endforeach;
		endif;
		?>
		</tbody>
		<?php
	endforeach;
	endif;
		?>
		</table>
	</div>
</div>

<ul class="dropdown-list dropdown-list-pedit" obj="">
	<li><a href="#" class="CoopPDelete text-left"><span class="font-default"><?php echo __('スレッドの削除'); ?></span></a></li>
</ul>
<ul class="dropdown-list dropdown-list-cedit" obj="">
<?php if ($aClass['tpMaster']): ?>
	<li><a href="#" class="CoopSortTop text-left"><span class="font-default"><?php echo __('スレッドの一番上へ'); ?></span></a></li>
<?php endif; ?>
	<li><a href="#" class="CoopDelete text-left"><span class="font-default"><?php echo __('コメントの削除'); ?></span></a></li>
</ul>

<form action="/t/coop/res/<?php echo $aCCategory['ccID']; ?>" method="post" class="res-box width-100" style="display: none;">
	<input type="hidden" name="c_no" value="0">
	<input type="hidden" name="c_id" value="">
	<input type="hidden" name="mode" value="input">
	<input type="hidden" name="ct" value="<?php echo $aClass['ctID']; ?>">
	<div class="res-msg-box"></div>
	<div class="formControl font-size-90" style="margin: auto;">
		<div class="formGroup" style="display: none;">
			<div class="formLabel"><?php echo __('タイトル'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="c_title" value="" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('タイトルを入力してください'); ?>" class="width-100 text-left">
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><a href="#" class="upload-box-toggle"><i class="fa fa-plus-square-o"></i><i class="fa fa-minus-square-o" style="display: none;"></i> <?php echo __('ファイル選択'); ?></a></div>
			<div class="formContent inline-box">
				<ul class="file-uploader" style="display: none;">
<?php
	for ($i = 1; $i <= 3; $i++):
?>
					<li class="width-16em file-box">
						<div class="input-cover text-center" style="background-size: cover;">
							<i class="fa fa-plus fa-3x mt16"></i>
							<p><?php echo __('ファイルを選択'); ?></p>
							<div class="uploaded-file" style="display: none;">
								<p><i class="fa fa-paperclip"></i> <a href="" class="file" target="_blank"><span class="name"></span></a><br><span class="size"></span></p>
								<p class="remove"><i class="fa fa-times fa-2x"></i></p>
							</div>
							<div class="upload-progress"><div class="upload-progress-bar"></div></div>
						</div>
						<span class="hidden-file"><input type="file" name="file-input" autocomplete="off"></span>
						<input type="hidden" name="c_file<?php echo $i; ?>" value="">
					</li>
<?php
	endfor;
?>
				</ul>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('本文'); ?></div>
			<div class="formContent inline-box">
<?php if (!preg_match('/CL_AIR/i', $_SERVER['HTTP_USER_AGENT'])): ?>
				<div id="dbbtn" class="mb4"></div>
<?php endif; ?>
				<textarea name="c_text" class="width-100 text-left font-size-100" rows="6"></textarea>
			</div>
		</div>
<?php
	$sMNone = '';
	if ($aClass['tpNum'] == 0 && $aCCategory['ccStuNum'] == 0 && is_null($aAssistant) && is_null($aAssist)):
		$sMNone = 'display: none;';
	endif;
	$sTNone = '';
	if ($aClass['tpNum'] == 0 && is_null($aAssistant) && is_null($aAssist)):
		$sTNone = 'display: none;';
	endif;
	$sSNone = ($aCCategory['ccStuNum'] == 0)? 'display: none;':'';
?>
		<div class="formGroup" style="<?php echo $sMNone; ?>">
			<div class="formLabel"><?php echo __('メール通知'); ?></div>
			<div class="formContent inline-box">
				<label class="mr16" style="<?php echo $sTNone; ?>"><input type="checkbox" name="mail-teacher" value="1"><?php echo __('先生に通知'); ?></label>
				<label style="<?php echo $sSNone; ?>"><input type="checkbox" name="mail-student" value="1"><?php echo __('学生に通知'); ?></label>
			</div>
		</div>
	</div>
	<div class="res-button-box">
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 ThreadRegist" style="padding: 4px 8px; display: none;" name="sub_state" value="1"><?php echo __('スレッドを登録する'); ?></button>
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 toComment" style="padding: 4px 8px; display: none;"><?php echo __('コメントする'); ?></button>
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 toUpdate" style="padding: 4px 8px; display: none;"><?php echo __('更新する'); ?></button>
		<button type="button" class="button default na width-auto CoopReplyToQuote font-size-90" style="padding: 4px 8px;"><?php echo __('引用'); ?></button>
		<button type="button" class="button default na width-auto CoopReplyToCancel font-size-90" style="padding: 4px 8px;"><?php echo __('キャンセル'); ?></button>
	</div>
<?php
	\Clfunc_Common::DropboxChooseBtn();
?>
</form>

