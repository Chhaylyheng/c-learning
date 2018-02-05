<?php

$dbname = 'cl_asp';
$msg = null;

$msg .= "--DF--------\n".shell_exec('df')."\n------------\n";
$msg .= "--MySQL-----\n".shell_exec('echo "SELECT * FROM Org_Table LIMIT 1;exit;" | mysql -ucluser -pclearningad-// '.$dbname)."\n------------\n";

echo $msg;
