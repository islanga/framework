<?php
#set include path
$path = array(ROOT . DS . "model" . DS,
    CORE_INCLUDE_PATH . DS. "classes" . DS,
    CORE_INCLUDE_PATH . DS); 
set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $path));
 
#Check if environment is development and display errors

function setReporting() 
{ 
  if (DEVELOPMENT_ENVIRONMENT == true) 
  {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
  } 
  else 
  {
    error_reporting(E_ALL);
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
    ini_set('error_log', ROOT . DS . 'tmp' . DS . 'logs' . DS . 'error.log');
  }
}

#Check for Magic Quotes and remove them * */

function stripSlashesDeep($value) 
{
  $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
  return $value;
}
  
function removeMagicQuotes() 
{
  if (get_magic_quotes_gpc()) 
  {
    $_GET = stripSlashesDeep($_GET);
    $_POST = stripSlashesDeep($_POST);
    $_COOKIE = stripSlashesDeep($_COOKIE);
  }
}

#Check register globals and remove them 

function unregisterGlobals() 
{
  if (ini_get('register_globals')) 
  {
    $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
    foreach ($array as $value) 
	{  
      foreach ($GLOBALS[$value] as $key => $var) 
	  {
        if ($var === $GLOBALS[$key]) 
		{
          unset($GLOBALS[$key]);
        }
      }
    }
  }
}

function initDatabase() 
{
  $config_file 		= CORE_INCLUDE_PATH . DS . "config.ini";
  $config_options 	= parse_ini_file($config_file, "database");

  $db = new MySQL($config_options["database"]);
  Database_Table::Singleton()->setDBO($db);
}

#Main call Function

function execute() 
{  
  $config_file 				= CORE_INCLUDE_PATH . DS . "config.ini";
  $general_config_options 	= parse_ini_file($config_file, "general");

  $errorFlag 				= true;
  
  $module 					= isset($_GET['module']) ? strtolower($_GET['module']) : 'index';
  $controller 				= isset($_GET['controller']) ? strtolower($_GET['controller']) : 'index';
  $action 					= isset($_GET['action']) ? strtolower($_GET['action']) : 'index';
  $controllerName 			= ucwords($controller);
  
  if ($module != 'index') 
  {
	$controllerName 		= ucwords($module) . "Controller";	
	$controllerPath 		= CORE_INCLUDE_PATH . DS . ucwords($module) . "Controller.php";
  } 
  else 
  {
   	$controllerPath 		= CORE_INCLUDE_PATH . DS . ucwords($controller) . "Controller.php";
    $controllerName 		= $controllerName . "Controller";
  }
  if (file_exists($controllerPath)) 
  {   
    require_once($controllerPath);
    $errorFlag = false;
  }
  if (!$errorFlag) 
  {
    $dispatch = new $controllerName;
	
    $dispatch->setLayout('main');
    if (method_exists($dispatch, $action . "Action")) 
	{
      call_user_func_array(array($dispatch, $action . "Action"), array());
    }
  }
}
 
/* * transform an associative array to an object * */

function arrayToObject($array) {
  $object = new stdClass();
  foreach ($array as $key => $value) {
    if (is_int($key))
      continue;
    $object->$key = $value;
  }
  return $object;
}

/** Autoload any classes that are required * */
function __autoload($className) 
{ 
  try 
  {
    if (file_exists( CORE_INCLUDE_PATH . DS. "classes" . DS ."{$className}.php") ||
	    file_exists( CORE_INCLUDE_PATH . DS. "{$className}.php") || file_exists( ROOT . DS . "model" . DS . "{$className}.php")) 
	{ 
      require_once "{$className}.php";
    }
  } catch (Exception $ex) {
    die($ex->getMessage());
  }
}

setReporting();
removeMagicQuotes();
unregisterGlobals();
initDatabase();
execute();