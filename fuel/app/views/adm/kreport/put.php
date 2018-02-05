<section class="pt0">
	<div class="info-box table-box record-table admin-table">
	<table>
	<thead>
		<tr><th>氏名</th><th>学校</th><th>レポート番号</th><th>提出日</th><th>一時保存</th><th>既読</th><th>コメント</th><th>参考になった</th></tr>
	</thead>
	<tbody>
		<?php
			if (!is_null($aTeachers)):
				$iMax = count($aTeachers);
				$i = 0;
				foreach ($aTeachers as $sTtID => $aTeach):
					$i++;
					$aT = $aTeach['teach'];
					$aPuts = $aTeach['put'];
					$iPuts = count($aPuts);

					$sOdd = ($i % 2)? '':'odd';

?>
<tr class="<?php echo $sOdd; ?>">
<td class="" rowspan="<?php echo ($iPuts)? $iPuts:1; ?>">
<?php echo ($aT['ttName'])? $aT['ttName']:$aT['ttMail']; ?>
</td>
<td class="" rowspan="<?php echo ($iPuts)? $iPuts:1; ?>">
<?php echo $aT['cmName']; ?>
</td>
<?php
					if (!$iPuts):
?>
<td class="">─</td>
<td class="">─</td>
<td class="">─</td>
<td class="">─</td>
<td class="">─</td>
<td class="">─</td>
</tr>
<?php
						continue;
					endif;
					foreach ($aPuts as $j => $aP):
						$i += $j;
						$sSub = $aP['krSub'];
						$sPut =($aP['krStatus'])? '<a href="/adm/kreport/ansdetail/'.$aReport['no'].DS.$sTtID.DS.$aP['krSub'].'">'.date('Y/m/d H:i',strtotime($aP['krDate'])).'</a>':'未提出';
						$sTmp =($aP['krStatus'])? '─':date('Y/m/d H:i',strtotime($aP['krDate']));
						$sKA = (int)$aP['krAlready'];
						$sKL = (int)$aP['krLike'];
						$sKC = (int)$aP['krCom'];
						if ($j):
							$sOdd = ($i % 2)? '':'odd';
?>
<tr class="<?php echo $sOdd; ?>">
<?php
						endif;
?>
<td class="text-center">
<?php echo $sSub; ?>
</td>
<td class="">
<?php echo $sPut; ?>
</td>
<td class="">
<?php echo $sTmp; ?>
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
				endforeach;
			endif;
?>
		</tbody>
	</table>
	</div>
</section>

<section class="info-box kreport-data" data="<?php echo $aReport['krYear'].'|'.$aReport['krPeriod'].'|ALL|Admin|1'; ?>">
	<h2>コメント</h2>
	<hr>
	<p class="comment-more-show" style="display: none;"><span>もっと見る <i class="fa fa-angle-up"></i></span></p>
	<ul class="comment-show-box" cnt="0">
	</ul>
</section>

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