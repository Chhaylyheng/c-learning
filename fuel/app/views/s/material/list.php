<div id="thread-group" data="/s/material/listpiece/<?php echo $aMCategory['mcID']; ?>/">
<?php
if (!is_null($aMaterial)):
	foreach ($aMaterial as $iNO => $aM):
?>
<div class="info-box mt16 mat-list" id="m<?php echo $iNO; ?>"><div class="text-center mt16 mb16 font-silver"><i class="fa fa-spinner fa-pulse fa-2x"></i></div></div>
<?php
endforeach;
else:
?>
<p><?php echo __('教材はありません'); ?></p>
<?php
endif;
?>
</div>
