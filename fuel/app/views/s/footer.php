<footer class="content-padding">
	<p><?php echo CL_COPYRIGHT; ?></p>
	<?php if (CL_ENV == 'DEVELOPMENT'): ?>
		<p class="font-size-80 font-silver"><?php echo $_SERVER['HTTP_USER_AGENT']; ?></p>
	<?php endif; ?>
</footer>