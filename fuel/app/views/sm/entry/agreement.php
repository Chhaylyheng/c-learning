<div style="text-align:center;" align="center"><span style="font-size:medium;"><?php echo __('会員規約'); ?></span></div>

<form action="/s/entry/agreement<?php echo Clfunc_Mobile::SesID(); ?>" method="POST">
<?php echo Clfunc_Mobile::SesID('post'); ?>

<div style="margin-top: 5px;">

<?php echo nl2br($sTerm); ?>

</div>

<?php echo Clfunc_Mobile::hr(); ?>

<div style="color:#CC0000;"><?php echo __('上記規約に同意いただくことで登録が完了します。'); ?></div>

<div style="text-align: center; margin-top: 5px;"><input type="submit" name="agree" value="<?php echo __('同意する'); ?>"></div>
<div style="text-align: center; margin-top: 5px;"><input type="submit" namr="disagree"value="<?php echo __('同意しない'); ?>"></div>

<?php echo Form::close(); ?>
