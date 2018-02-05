<?php

require_once(dirname(__FILE__).'/common.php');

require_once(dirname(__FILE__).'/entry.php');
$i18n = array_merge($i18n,$i18n_Entry);
unset($i18n_Entry);

require_once(dirname(__FILE__).'/password.php');
$i18n = array_merge($i18n,$i18n_Password);
unset($i18n_Password);

require_once(dirname(__FILE__).'/top.php');
$i18n = array_merge($i18n,$i18n_Top);
unset($i18n_Top);

require_once(dirname(__FILE__).'/profile.php');
$i18n = array_merge($i18n,$i18n_Profile);
unset($i18n_Profile);

require_once(dirname(__FILE__).'/class.php');
$i18n = array_merge($i18n,$i18n_Class);
unset($i18n_Class);

require_once(dirname(__FILE__).'/student.php');
$i18n = array_merge($i18n,$i18n_Student);
unset($i18n_Student);

require_once(dirname(__FILE__).'/attend.php');
$i18n = array_merge($i18n,$i18n_Attend);
unset($i18n_Attend);

require_once(dirname(__FILE__).'/quest.php');
$i18n = array_merge($i18n,$i18n_Quest);
unset($i18n_Quest);

require_once(dirname(__FILE__).'/output.php');
$i18n = array_merge($i18n,$i18n_Output);
unset($i18n_Output);

require_once(dirname(__FILE__).'/init.php');
$i18n = array_merge($i18n,$i18n_Init);
unset($i18n_Init);

require_once(dirname(__FILE__).'/org.php');
$i18n = array_merge($i18n,$i18n_Org);
unset($i18n_Org);

require_once(dirname(__FILE__).'/news.php');
$i18n = array_merge($i18n,$i18n_News);
unset($i18n_News);

require_once(dirname(__FILE__).'/test.php');
$i18n = array_merge($i18n,$i18n_Test);
unset($i18n_Test);

require_once(dirname(__FILE__).'/tutorial.php');
$i18n = array_merge($i18n,$i18n_Tutorial);
unset($i18n_Tutorial);

require_once(dirname(__FILE__).'/report.php');
$i18n = array_merge($i18n,$i18n_Report);
unset($i18n_Report);

require_once(dirname(__FILE__).'/assistant.php');
$i18n = array_merge($i18n,$i18n_Assistant);
unset($i18n_Assistant);

require_once(dirname(__FILE__).'/material.php');
$i18n = array_merge($i18n,$i18n_Material);
unset($i18n_Material);

require_once(dirname(__FILE__).'/coop.php');
$i18n = array_merge($i18n,$i18n_Coop);
unset($i18n_Coop);

require_once(dirname(__FILE__).'/contact.php');
$i18n = array_merge($i18n,$i18n_Contact);
unset($i18n_Contact);

require_once(dirname(__FILE__).'/drill.php');
$i18n = array_merge($i18n,$i18n_Drill);
unset($i18n_Drill);

require_once(dirname(__FILE__).'/alog.php');
$i18n = array_merge($i18n,$i18n_Alog);
unset($i18n_Alog);

require_once(dirname(__FILE__).'/manual.php');
$i18n = array_merge($i18n,$i18n_Manual);
unset($i18n_Manual);

return $i18n;


