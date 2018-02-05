<?php
$dir = (isset($dir))? $dir:'';
if (preg_match("/DoCoMo/", $_SERVER['HTTP_USER_AGENT']))
{
	header('Content-Type: application/xhtml+xml');
}
echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo View::forge('template_header_m'); ?>
</head>
<body>
<div style="font-size:x-small;">
<a name="top" id="top"></a>

<?php if (isset($pagetitle)): ?>
<?php if (isset($subtitle)): ?>
<?php
	elseif (!is_null($aClass)):
		$sPWH = '';
		$sSep = '';
		if ($aClass['dpNO'])
		{
			$sPWH .= $aPeriod[$aClass['dpNO']];
			$sSep = '/';
		}
		if ($aClass['ctWeekDay'])
		{
			$sPWH .= $sSep.$aWeekDay[$aClass['ctWeekDay']];
			$sSep = '/';
		}
		if ($aClass['dhNO'])
		{
			$sPWH .= $sSep.$aHour[$aClass['dhNO']];
		}
		if ($sPWH)
		{
			$sPWH = '（'.$sPWH.'）';
		}
?>
	<div style="text-align:center;" align="center"><?php echo $aClass['ctName'].$sPWH; ?></div>
	<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>


<div style="text-align:center;" align="center">
<?php if (isset($classtitle)): ?>
	<?php echo $classtitle; ?>
<?php else: ?>
	<?php echo $pagetitle; ?>
<?php endif; ?>
</div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<?php if (isset($ses['SES_S_ERROR_MSG'])): ?>
<div style="color: #0000CC; margin-top: 5px;"><?php echo nl2br($ses['SES_S_ERROR_MSG']); ?></div>
<?php Session::delete('SES_S_ERROR_MSG'); ?>
<?php endif; ?>
<?php if (isset($ses['SES_S_NOTICE_MSG'])): ?>
<div style="color: #00CC00; margin-top: 5px;"><?php echo nl2br($ses['SES_S_NOTICE_MSG']); ?></div>
<?php Session::delete('SES_S_NOTICE_MSG'); ?>
<?php endif; ?>

<div>
<?php echo $content; ?>
</div>

<?php if (isset($breadcrumbs)): ?>
<?php krsort($breadcrumbs); ?>
<?php echo Clfunc_Mobile::hr(); ?>
	<?php foreach ($breadcrumbs as $i => $bm): ?>
		<?php if (isset($bm['link'])): ?>
			<div><?php echo Clfunc_Mobile::emj($i+1); ?><a href="/g<?php echo $bm['link'].Clfunc_Mobile::SesID(); ?>" accesskey="<?php echo $i+1; ?>"><?php echo $bm['name']; ?></a></div>
		<?php endif;?>
	<?php endforeach; ?>
	<div><?php echo Clfunc_Mobile::emj('0'); ?><a href="/g/index<?php echo Clfunc_Mobile::SesID(); ?>" accesskey="0"><?php echo __('トップに戻る'); ?></a></div>
<?php endif; ?>

<?php echo $footer; ?>
</div>
</body>
</html>
