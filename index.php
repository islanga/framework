<?php
/**
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
	
/**
 * The full path to the directory which holds "app", WITHOUT a trailing DS.
 *
 */
if (!defined('ROOT')) {
	define('ROOT', dirname(__FILE__));
}

if (!defined('CORE_INCLUDE_PATH')) {
	define('CORE_INCLUDE_PATH', ROOT . DS . 'includes');
} 
  
# set to either true or false to display errors
define('DEVELOPMENT_ENVIRONMENT', false); 
require (CORE_INCLUDE_PATH . DS . 'bootstrap.php');