<?php
return array(
	'_root_'  => 'index/index',  // The default route
	'_404_'   => 'index/404',    // The main 404 route
	't'       => 't/login',      // t Directory Index
	's'       => 's/login',      // s Directory Index
	'sm'      => 'sm/login',     // sm Directory Index
	'org'     => 'org/login',    // org Directory Index
	'g'       => 'g/login',      // g Directory Index
	'g2'      => 'g/login',      // g2 Directory Index
	'adm'     => 'index/404',    // adm Directory Index
	'a'       => 'a/login',      // a Directory Index
	'(.{3})/t' => 't/login/auth/ad/$1', // LDAP Teacher $1 -> GroupPrefix
	'(.{3})/s' => 's/login/auth/ad/$1', // LDAP Student $1 -> GroupPrefix
	'g2/(:any)' => 'g/$1', // g2 alias
);
