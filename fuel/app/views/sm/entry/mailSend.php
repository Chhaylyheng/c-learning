<?php if ($sent_mail): ?>
<p><?php echo __('アカウント登録手続き完了のお知らせを <strong>:mail</strong> へメールで送信しました。', array('mail'=>$sent_mail)); ?></p>
<?php else: ?>
<p><?php echo __('アカウント登録手続きが完了しました。'); ?></p>
<?php endif; ?>

<?php echo Clfunc_Mobile::hr(); ?>

<p><a href="/s/index"><?php echo __('早速、:siteを使う', array('site'=>CL_SITENAME)); ?></a></p>
