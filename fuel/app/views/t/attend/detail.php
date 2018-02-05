<div class="info-box table-box record-table admin-table">
<p class="error-box mb16" style="display: none;" id="stErr"></p>
<table class="kreport-data" id="attend-mode" val="detail">
<thead>
	<tr>
		<th><?php echo __('学籍番号'); ?></th>
		<th><?php echo __('氏名'); ?></th>
		<th><?php echo __('出席内容'); ?></th>
		<th><?php echo __('出席時刻'); ?></th>
		<th><?php echo __('変更日時'); ?></th>
		<?php if ($aAttend['acGIS']):?>
		<th><a href="#" class="MarkerJump" lat="<?php echo $aAttend['agLat']; ?>" lon="<?php echo $aAttend['agLon']; ?>"><?php echo __('位置情報'); ?> <i class="fa fa-map-marker"></i></a></th>
		<?php endif; ?>
	</tr>
</thead>
<tbody>
<?php
	$aMarker = null;
	if (!is_null($aStudent)):
		foreach ($aStudent as $aS):
?>
	<tr>
		<td><?php echo $aS['stNO']; ?></td>
		<td><?php echo $aS['stName']; ?></td>
		<?php
			if (!isset($aS['attend'][$aAttend['abDate']][$aAttend['acNO']]))
			{
				$aSA = array(
					'amAbsence' => 1,
					'amTime' => 0,
					'agLat' => '',
					'agLon' => '',
					'amName' => $aAttendMaster[0]['amName'],
					'amShort' => $aAttendMaster[0]['amShort'],
					'abAttendDate' => CL_DATETIME_DEFAULT,
					'abModifyDate' => CL_DATETIME_DEFAULT,
				);
			}
			else
			{
				$aSA = $aS['attend'][$aAttend['abDate']][$aAttend['acNO']];
			}
			$bGIS = false;
			if ($aSA['agLat'] && $aSA['agLon'])
			{
				$aMarker[] = array('lat'=>$aSA['agLat'],'lon'=>$aSA['agLon']);
				$bGIS = true;
			}
			$sStyle = ($aSA['amAbsence'])? 'font-red':(($aSA['amTime'])? 'font-green':'font-blue');
		?>
		<td>
			<div class="dropdown">
				<button type="button" class="attendstate-dropdown-toggle <?php echo $sStyle; ?>" id="<?php echo $aS['stID'].'_'.$aAttend['abDate'].'_'.$aAttend['acNO']; ?>"><div><?php echo $aSA['amName']; ?></div></button>
			</div>
		</td>
		<td><?php echo ($aSA['abAttendDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('G:i',$tz,$aSA['abAttendDate']):''; ?></td>
		<td id="<?php echo $aS['stID']; ?>_Date"><?php echo ($aSA['abModifyDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d G:i',$tz,$aSA['abModifyDate']):''; ?></td>
		<?php if ($aAttend['acGIS']):?>
		<td>
			<?php if ($bGIS): ?>
				<?php
					$sLen = $aSA['agLength'].'m';
					$sIcon = 'fa-map-marker';
					$sCCls = '';
					if ($aSA['agLength'] > 1000):
						$sLen = round($aSA['agLength']/1000,2).'km';
						$sIcon = 'fa-exclamation-circle';
						$sCCls = ' font-red2';
					endif;
				?>
			<a href="#" class="MarkerJump<?php echo $sCCls; ?>" lat="<?php echo $aSA['agLat']; ?>" lon="<?php echo $aSA['agLon']; ?>">
				<i class="fa <?php echo $sIcon; ?> fa-fw"></i><?php echo $sLen; ?>
			</a>
			<?php endif; ?>
		</td>
		<?php endif; ?>
	</tr>
<?php
		endforeach;
	endif;
?>
</tbody>
</table>
</div>

<?php if ($aAttend['acGIS']): ?>
<div class="info-box mt16 static-box">
<div id="map_canvas" style="width: 100%; height: 500px;" lat="<?php echo $aAttend['agLat']; ?>" lon="<?php echo $aAttend['agLon']; ?>"></div>
<script type="text/javascript">
function GMAP_MarkerSet(Map) {
	<?php
		if (!is_null($aMarker)):
			foreach ($aMarker as $i => $aM):
	?>
	var Pos<?php echo $i; ?> = new google.maps.LatLng(<?php echo $aM['lat'].','.$aM['lon']; ?>);
	var Marker<?php echo $i; ?> = new google.maps.Marker({ position: Pos<?php echo $i; ?>, map: Map, icon: 'http://maps.google.co.jp/mapfiles/ms/icons/red-dot.png',});
	<?php
			endforeach;
		endif;
	?>
}
</script>
</div>
<?php endif; ?>

<ul class="dropdown-list dropdown-list-attendstate" obj="">
	<?php
		if (!is_null($aAttendMaster)):
			foreach ($aAttendMaster as $aM):
				$sObj = $aClass['ctID'].'_'.$aM['amAttendState'];
				$sStyle = ($aM['amAbsence'])? 'font-red':(($aM['amTime'])? 'font-green':'font-blue');
	?>
	<li><a href="#" obj="<?php echo $sObj; ?>" class="SwitchAttendState"><span class="<?php echo $sStyle; ?>"><?php echo $aM['amName'].'（'.$aM['amShort'].'）'; ?></span></a></li>
	<?php
			endforeach;
		endif;
	?>
</ul>

<script type="text/javascript" src="<?php echo CL_MAP_URL; ?>"></script>