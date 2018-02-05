<?php if ($aReport['zipProgress'] == 1): ?>
<script type="text/javascript">
var intervalTime = 2000;
var timerID;

timerID = setInterval(function() { ArchiveDownload() },intervalTime);
</script>
<?php endif; ?>

<?php if (!is_null($aStudent)): ?>
<script type="text/javascript">
$(function() {
	var sskey = 'cl_t_reportput_sort';
	var defaultSort = [[1,0]];

	var currentSort = new Array();
	if(('sessionStorage' in window) && (window.sessionStorage !== null)) {
		store = sessionStorage.getItem(sskey);
		if (store) {
			store = store.split('|');
			for (i = 0; i < store.length; i++) {
				currentSort.push(store[i].split(','));
			}
		}
	}
	if (!currentSort || currentSort == null || currentSort.length == 0) {
		currentSort = defaultSort;
	}

	$('table.table-sort').tablesorter({
		cssHeader: 'headerSort',
		headers: {
			0: {sorter: false},
		},
		sortList: currentSort,
		widgets: ['zebra']
	}).bind("sortEnd", function(sorter) {
		currentSort = sorter.target.config.sortList;
		currentSort = currentSort.join('|');
		setSessionStorage(sskey, currentSort);
	});
});
</script>
<?php endif; ?>

<?php $sTableName = __('提出').'：<span class="font-red font-size-160">'.$aReport['rbPutNum'].'</span> / '.$aClass['scNum']; ?>

<div class="info-box mt16">
	<div class="info-box table-box record-table admin-table mt0">
		<h2 class="mb4">
			<?php echo $sTableName; ?>
			<button class="button na default width-auto ml16 CheckNotYet font-size-80 va-top" style="padding: 4px 8px;">
				<input type="checkbox" class="NotYet va-middle" style="font-size: 100%; min-height; auto; line-height: 1; display: inline; margin-right: 0;">
				<?php echo __('未提出者をチェック'); ?></button>
			<button class="button na default width-auto ml8  CheckSbmted font-size-80 va-top" style="padding: 4px 8px;">
				<input type="checkbox" class="Sbmted va-middle" style="font-size: 100%; min-height; auto; line-height: 1; display: inline; margin-right: 0;">
				<?php echo __('提出者をチェック'); ?></button>
		</h2>
		<form action="/t/mail/send" method="post" id="StudentCheckForm">
		<input type="hidden" name="mode" value="select">
		<input type="hidden" name="func" value="report">
		<input type="hidden" name="rb" value="<?php echo $aReport['rbID']; ?>">
		<table class="kreport-data table-sort">
		<thead>
			<tr>
				<th><input type="checkbox" class="AllChk" title="<?php echo __('全てをチェック'); ?>"></th>
				<th class="string-bottom"><?php echo __('学籍番号'); ?></th>
				<th><?php echo __('氏名'); ?></th>
				<th><?php echo __('クラス'); ?></th>
				<th><?php echo __('提出日時'); ?></th>
				<th style="max-width: 30%;"><?php echo __('提出ファイル'); ?></th>
				<th><?php echo __('評価'); ?></th>
				<?php if ($aReport['rbShare']): ?>
					<th><?php echo __('コメント数'); ?></th>
					<?php if ($aReport['rbShare'] == 2): ?>
						<th><?php echo __('平均点'); ?></th>
					<?php endif; ?>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!is_null($aStudent)):
				foreach ($aStudent as $sStID => $aS):
					$sJsKey = $aReport['rbID'].'_'.$sStID;
					$aM = array('no'=>'','name'=>'','date'=>'─','put'=>'','score'=>'─','share'=>'─','avg'=>'─');

					if (isset($aS['stu'])):
						$aM['no'] = $aS['stu']['stNO'];
						$aM['name'] = $aS['stu']['stName'];
						$aM['class'] = $aS['stu']['stClass'];
					endif;
					if (isset($aS['put'])):
						$aP = $aS['put'];

						$aM['no'] = ($aP['rpstNO'])? $aP['rpstNO']:$aM['no'];
						$aM['name'] = ($aP['rpstName'])? $aP['rpstName']:$aM['name'];
						$aM['class'] = ($aP['rpstClass'])? $aP['rpstClass']:$aM['class'];
						$sDate = ($aP['rpTeachPut'])? '<a href="/t/report/submit/'.$aReport['rbID'].'/'.$sStID.'/d" class="button na width-auto confirm font-size-90 ReportSubmitCancel" style="padding: 8px;">'.__('先生による提出').'</a>':(($aP['rpDate'] != CL_DATETIME_DEFAULT)? '<span class="font-bold font-blue">'.ClFunc_Tz::tz('Y/m/d<\b\r>H:i',$tz,$aP['rpDate']).'</span>':'<a href="/t/report/submit/'.$aReport['rbID'].'/'.$sStID.'" class="button na width-auto cancel font-size-90 ReportSubmit" style="padding: 8px;">'.__('未提出').'</a>');
						$aM['date'] = $sDate;
						$sCom = ($aP['rpComment'])? '<i class="fa fa-commenting mr0 ml4"></i>':'';
						$aM['score'] = '<a href="/t/report/detail/'.$aReport['rbID'].'/'.$sStID.'/d" class="button na width-auto do font-size-110">'.(($aP['rpScore'])? $aRateMaster[$aP['rpScore']]['rrName']:__('評価する')).$sCom.'</a>';
						$aM['share'] = '<a href="/t/report/detail/'.$aReport['rbID'].'/'.$sStID.'/s" class="button na width-auto default font-size-110"><i class="fa fa-comments mr4"></i>'.$aP['rpComNum'].'</a>';
						$aM['avg']   = '<a href="/t/report/detail/'.$aReport['rbID'].'/'.$sStID.'/r" class="button na width-auto default font-size-110"><i class="fa fa-star mr4"></i>'.$aP['rpAvgScore'].'</a>';

						for ($i = 1; $i <= 3; $i++):
							if (isset($aP['fID'.$i])):
								$aM['put'] .= '<i class="fa fa-paperclip mr4 ml2"></i><a href="'.\Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aP['fID'.$i],'mode'=>'e')).DS.$aP['fExt'.$i].'" target="_blank">'.$aP['fName'.$i].'</a> ('.\Clfunc_Common::FilesizeFormat($aP['fSize'.$i],1).')<br>';
							endif;
						endfor;

					else:
						$aM['date'] = '<a href="/t/report/submit/'.$aReport['rbID'].'/'.$sStID.'" class="button na width-auto cancel font-size-90 ReportSubmit" style="padding: 8px;">'.__('未提出').'</a>';
						$aM['score'] = '<a href="/t/report/detail/'.$aReport['rbID'].'/'.$sStID.'/d" class="button na width-auto do font-size-110">'.__('評価する').'</a>';
						$aM['share'] = '<a href="/t/report/detail/'.$aReport['rbID'].'/'.$sStID.'/s" class="button na width-auto default font-size-110"><i class="fa fa-comments mr4"></i>0</a>';
						$aM['avg']   = '<a href="/t/report/detail/'.$aReport['rbID'].'/'.$sStID.'/r" class="button na width-auto default font-size-110"><i class="fa fa-star mr4"></i>0</a>';
					endif;

					if (!$aM['put']):
						$aM['put'] = '─';
					endif;

					$bMail = false;
					if ($aS['stu']['stMail']):
						$bMail = true;
					endif;
					if ($aS['stu']['stSubMail']):
						$bMail = true;
					endif;
		?>
			<tr data="<?php echo $sJsKey; ?>">
				<td>
				<?php if($bMail): ?>
					<input type="checkbox" name="StuChk[]" class="Chk" value="<?php echo $aS['stu']['stID']?>">
				<?php endif; ?>
				</td>
				<td><?php echo $aM['no']; ?></td>
				<td><?php echo $aM['name']; ?></td>
				<td><?php echo $aM['class']; ?></td>
				<td class="sp-full"><?php echo $aM['date']; ?></td>
				<td class="sp-full"><?php echo $aM['put']; ?></td>
				<td><span class="sp-display-inline font-grey"><?php echo __('評価'); ?>:</span
					><?php echo $aM['score']; ?>
				</td>
				<?php if ($aReport['rbShare']): ?>
				<td><span class="sp-display-inline font-grey"><?php echo __('共有板'); ?></span
					><?php echo $aM['share']; ?>
				</td>
					<?php if ($aReport['rbShare'] == 2): ?>
					<td><span class="sp-display-inline font-grey"><?php echo __('平均'); ?></span
						><?php echo $aM['avg']; ?>
					</td>
					<?php endif; ?>
				<?php endif; ?>
			</tr>
		<?php
				endforeach;
			endif;
		?>
		</tbody>
		</table>
		</form>
	</div>
</div>

