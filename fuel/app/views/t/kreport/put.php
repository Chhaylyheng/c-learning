<section class="info-box">
	<p>
		<?php if ($aReport['krPublic'] == 1): ?>
		<a href="/t/kreport/ans/<?php echo $aReport['krYear'].DS.$aReport['krPeriod']; ?>" class="button do na" style="text-align: center;">レポートを提出する</a>
		<?php endif; ?>
		<a href="/t/kreport/all/<?php echo $aReport['krYear'].DS.$aReport['krPeriod']; ?>" class="button do na" style="text-align: center;">回答全表示</a>
		<a href="" class="button do na scrollBottom" style="text-align: center;">コメント</a>
	</p>
	<div class="info-box table-box record-table admin-table mt0">
	<p class="error-box mb16" style="display: none;" id="krErr"></p>
	<table class="kreport-data" data="<?php echo $aReport['krYear'].'|'.$aReport['krPeriod'].'|ALL|'.$aTeacher['ttID'].'|1'; ?>">
	<thead>
		<tr><th>氏名</th><th>学校</th><th>No.</th><th>提出日</th><th>ファイル</th><th>既読</th><th>コメント</th><th>参考になった</th></tr>
	</thead>
	<tbody>
		<?php
			if (!is_null($aTeachers)):
				$i = 0;
				foreach ($aTeachers as $sTtID => $aTeach):
					$i++;
					$aT = $aTeach['teach'];
					$aPuts = $aTeach['put'];
					$iPuts = count($aPuts);
					if ($sTtID == $aTeacher['ttID'] && $aReport['krPublic'] == 1):
						$iPuts++;
					endif;
					$sOdd = ($i % 2)? '':'odd';
					?>
<tr class="<?php echo $sOdd; ?>" data="<?php echo $sTtID; ?>">
<td class="" rowspan="<?php echo ($iPuts)? $iPuts:1; ?>">
<span class="sp-display font-grey">氏名</span>
<?php echo ($aT['ttName'])? $aT['ttName']:$aT['ttMail']; ?>
</td>
<td class="" rowspan="<?php echo ($iPuts)? $iPuts:1; ?>">
<span class="sp-display font-grey">学校名</span>
<?php echo $aT['cmName']; ?>
</td>
					<?php
					if (!count($aPuts)):
					?>
<td class="text-center">
<span class="sp-display font-grey">No.</span>
</td>
<td class="">
<span class="sp-display font-grey">提出日</span>
<?php
$sPut = '─';
if ($sTtID == $aTeacher['ttID'] && $aReport['krPublic'] == 1):
	$sPut = '<a href="/t/kreport/ans/'.$aReport['krYear'].DS.$aReport['krPeriod'].'">レポートを提出する</a>';
endif;
echo $sPut;
?>
</td>
<td class="">
<span class="sp-display font-grey">ファイル</span>
─
</td>
<td class="">
<span class="sp-display font-grey">既読</span>
─
</td>
<td class="">
<span class="sp-display font-grey">コメント</span>
─
</td>
<td class="">
<span class="sp-display font-grey">参考になった</span>
─
</td>
</tr>
					<?php
						continue;
					endif;
					foreach ($aPuts as $j => $aP):
						$sSub = $aP['krSub'];
						if ($aP['krStatus'] == 0 && $sTtID == $aTeacher['ttID']):
							$sPut = '<a href="/t/kreport/ans/'.$aReport['krYear'].DS.$aReport['krPeriod'].DS.$aP['krSub'].'">一時保存<span class="font-size-80">（'.ClFunc_Tz::tz('Y/m/d H:i',$tz,$aP['krDate']).'）</span></a>';
						else:
							$sPut = '<a href="/t/kreport/ansdetail/'.$aReport['krYear'].DS.$aReport['krPeriod'].DS.$sTtID.DS.$aP['krSub'].'">'.ClFunc_Tz::tz('Y/m/d H:i',$tz,$aP['krDate']).'</a>';
						endif;
						$sKA = (int)$aP['krAlready'];
						$sKL = (int)$aP['krLike'];
						$sKC = (int)$aP['krCom'];
						$sNew = '<span class="attention attn-emp">NEW</span>';
						if ((isset($aAlready[$sTtID][$sSub]['kaAlready']) && $aAlready[$sTtID][$sSub]['kaAlready'] == 1) || $aP['krStatus'] == 0)
						{
							$sNew = '';
						}
						if ($aP['krStatus'] == 1)
						{
							if (!isset($aAlready[$sTtID][$sSub]['kaLike']) || $aAlready[$sTtID][$sSub]['kaLike'] == 0)
							{
								$sKL = '<a href="#" class="KRLikeUP" data="'.$aP['krSub'].'"><i class="fa fa-thumbs-o-up fa-lg" style="vertical-align: baseline;"></i></a> <span>'.$sKL.'</span>';
							}
							else
							{
								$sKL = '<i class="fa fa-check fa-lg" style="vertical-align: baseline;"></i> '.$sKL;
							}
						}
						$sFiles = null;
						for ($k = 1; $k <= 5; $k++):
							if ($aP['krFile'.$k.'Name']):
								$sName = $aP['krFile'.$k.'Name'];
								$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>'kreport', 'file'=>$aP['krFile'.$k.'File'], 'name'=>$sName));
								$sFiles .= '<a href="'.$sFile.'" target="_blank" title="'.$sName.'"><i class="fa fa-paperclip"></i></a> ';
							endif;
						endfor;
						$sFiles = (is_null($sFiles))? '─':$sFiles;

						if ($j):
							$i++;
							$sOdd = ($i % 2)? '':'odd';
?>
<tr class="<?php echo $sOdd; ?>" data="<?php echo $sTtID; ?>">
<?php
						endif;
?>
<td class="text-center">
<?php echo $sSub; ?>
</td>
<td class="">
<?php echo $sPut.$sNew; ?>
</td>
<td class="">
<?php echo $sFiles; ?>
</td>
<td class="">
<?php echo $sKA; ?>
</td>
<td class="">
<?php echo $sKC; ?>
</td>
<td class="">
<?php echo $sKL; ?>
</td>
</tr>
<?php
					endforeach;
					if ($sTtID == $aTeacher['ttID'] && $aReport['krPublic'] == 1):
						$i++;
						$sOdd = ($i % 2)? '':'odd';
?>
<tr class="<?php echo $sOdd; ?>">
<td class="text-center">
<span class="sp-display font-grey">No.</span>
</td>
<td class="">
<span class="sp-display font-grey">提出日</span>
<?php
$sPut = '<a href="/t/kreport/ans/'.$aReport['krYear'].DS.$aReport['krPeriod'].'">別のレポートを提出する</a>';
echo $sPut;
?>
</td>
<td class="">
<span class="sp-display font-grey">ファイル</span>
─
</td>
<td class="">
<span class="sp-display font-grey">既読</span>
─
</td>
<td class="">
<span class="sp-display font-grey">コメント</span>
─
</td>
<td class="">
<span class="sp-display font-grey">参考になった</span>
─
</td>
</tr>
<?php
					endif;
				endforeach;
			endif;
		?>
		</tbody>
	</table>
	</div>
</section>

<section class="info-box">
	<h2>コメント</h2>
	<hr>
	<p class="comment-more-show" style="display: none;"><span>もっと見る <i class="fa fa-angle-up"></i></span></p>
	<ul class="comment-show-box" cnt="0">
	</ul>
</section>

<div class="comment-write-box">
<table>
<tr>
	<td><i class="fa fa-comment-o fa-2x fa-flip-horizontal"></i></td>
	<td><textarea class="comment-write-text" rows="1"></textarea></td>
	<td><button type="button" class="button na do comment-write-button" style="min-width: 1em;">送信</button></td>
</tr>
</table>
</div>

<ul class="comment-item-template">
<li class="comment-item comment-item-other" no="" style="display: none;">
	<img style="width: 50px; height: 50px;" src="">
	<p class="name"><span>大学 氏名</span>　<span><i class="fa fa-share fa-fw"></i></span></p>
	<p class="comment">コメント</p>
	<p class="time">MM/DD<br>HH:mm</p>
</li>
<li class="comment-item comment-item-mine" no="" style="display: none;">
	<p class="name"><span><i class="fa fa-reply fa-fw"></i></span>　</p>
	<p class="time">MM/DD<br>HH:mm</p>
	<p class="comment">コメント</p>
</li>
</ul>