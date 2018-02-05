<?php
/**
 * The staging database settings. These get merged with the global settings.
 */

return array(
	'default' => array(
		'connection'  => array(
			'dsn'        => 'mysql:host=localhost;dbname=testing',
			'username'   => 'root',
			'password'   => '',
		),
	),
);
