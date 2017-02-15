<?php
class BaseView {

  /**
   *
   * @var struct
   */
  protected $variables = array();
  /**
   *
   * @var BaseController
   */
  protected $_controller;

  /**
   *
   * @param BaseController $controller
   */
  public function __construct(BaseController $controller) 
  {
    $this->_controller = $controller;
  }

  public function set($name, $value) {
    $this->variables[$name] = $value;
  }
  /**
   *
   * @param BaseController $controller
   * @param string $action
   */
  public function render($action='', $controller=null) 
  {
    extract($this->variables);

    $my = null;
           
    if ($controller instanceof BaseController) 
	{
      $controllerName = explode("controller", strtolower($controller->__toString()));
    } 
	else 
	{
      $controllerName = explode("controller", strtolower($this->_controller->__toString()));
    }
	
    $controllerName = $controllerName[0];
    if (preg_match('/_/', $controllerName)) 
	{
      $controllerName = explode("_", $controllerName);
      $controllerName = $controllerName[1];
    }
    if ($action == '') 
	{
      $action = isset($_GET['action']) && $_GET['action'] != "index" ? strtolower($_GET['action']) : 'index';
    }
    $module = (isset($_GET['module']) && $_GET['module'] != "index") ? $_GET['module'] : "index";
    
    $templatePath = ROOT . DS;
    if ($module != "index") 
	{
      $templatePath .= $module . ".php";
    } 
	else 
	{
      $templatePath .= "student_reg.php";
    }

	ob_start();
    if (file_exists($templatePath))
      require_once($templatePath);
    $content = ob_get_contents();
    ob_end_clean();
	
    $layoutPath = ROOT . DS  . 'layouts';   
	
    include_once $layoutPath . DS . "layout.php";

  }

   public function url() 
  {
    $args = func_get_args();
    $parts = "";
    
    $server_path = $_SERVER['SERVER_NAME'] == "localhost" ? "http://{$_SERVER['SERVER_NAME']}{$this->baseUrl()}":$this->baseUrl();

    if (isset($args[1]) && is_array($args[1])) 
	{
      foreach ($args[1] as $key => $value) 
	  {
        $parts .= "&$key=$value";
      }
    }
    if (!is_array($args[0])) 
	{
      return $args[0] . $parts;
    } 
	else 
	{
      $module = isset($_GET['module']) ? strval($_GET['module']) : "index";

	//  echo $url;
      if (count($args[0]) < 3) 
	  {
        $controllerName = $args[0][0];
        $actionName = sizeof($args[0]) == 2 ? $args[0][1] : '';
        $moduleName = $_GET['module'];
      } 
	  else 
	  {
        $moduleName = isset($args[0][0]) ? $args[0][0] : "";
        $controllerName = isset($args[0][1]) ? $args[0][1] : "";
        $actionName = isset($args[0][2]) ? $args[0][2] : "";
      }
      // if($module != "index"){
      if (isset($_GET['module'])) 
	  {
        if ($moduleName == "index") 
		{
          $uri =  $server_path."$actionName.html" ;
         
        } 
		else 
		{
          $uri = $moduleName != "admin" ? $server_path."$moduleName/$controllerName/$actionName.html":$server_path."$moduleName/$controllerName/$actionName";
        }
      } 
	  else 
	  {
        if ($module == "index") 
		{
           $uri = $actionName != "index" ? $server_path."$moduleName/$actionName.html": $server_path."$moduleName.html" ;
        } 
		else 
		{
          $uri = $moduleName != "admin" ? $server_path."$moduleName/$controllerName/$actionName.html":$server_path."$moduleName/$controllerName/$actionName";
        }
      }
      return $uri . $parts;
    }
  }

  function baseUrl() {
    if ($_SERVER['SERVER_NAME'] == "localhost") 
	{
      $url = $_SERVER['REQUEST_URI'];
      $url = explode("/", $url);
      $url = "/" . $url[1] . "/";
    } 
	else 
	{
      $url = "/";
    }
    return $url;
  }


  public function getRequestParam() {
    $args = func_get_args();
    $args = $args[0];
    if (isset($_REQUEST[$args]) && !is_array($_REQUEST[$args])) {
      return strip_tags(htmlentities($_REQUEST[$args]));
    }
    if (isset($_REQUEST[$args]) && is_array($_REQUEST[$args])) {
//return strip_tags(htmlentities($_REQUEST[$args[0]]));
      $tmp = array();
      foreach ($_REQUEST[$args] as $key => $val) {
        $tmp[$args] = strip_tags(htmlentities($val));
      }
      return $tmp;
    }

    return "";
  }

  public function getRequestParams() {
    $request = array();
    if (isset($_REQUEST)) {
      foreach ($_REQUEST as $key => $values) {
//   if (isset($request[$key])) {
        if (!is_array($values)) {
          $request[$key] = strip_tags(htmlentities($values));
        } else {
          $v = array();
          foreach ($values as $val) {
            $v[] = strip_tags(htmlentities($val));
          }
          $request[$key] = $v;
        }
      }
// }
//}
      return $request;
    }
  }

  public function getController() {
    return $this->_controller;
  }

}

