<?php
abstract  class BaseController
{
  /**
   *
   * @var BaseController
   */
  protected $_controller;
  /**
   *
   * @var BaseView
   */
  protected $_view;
  /**
   *
   * @var string
   */
  protected $layout;
  /**
   *
   * @var BaseHelper
   */
  protected $_helper;
 
  /**
   *
   * @param BaseController $controller
   */
  public function __construct($controller) {
    $this->_controller = $controller;
    $this->_view = new BaseView($controller);
    $this->_helper = new BaseHelper($controller);
  }

  /**
   *
   * @param Object $name
   * @param Object $value
   */
  public function assign($name, $value) {
    $this->_view->set($name, $value);
  }

  public function getLayout() {
    return $this->layout;
  }

  public function setLayout($layout) {
    $this->layout = $layout;
  }

  public function getView(){
    return $this->_view;
  }

  public function setView(BaseView $view){
     $this->_view = $view;
  }

  public function getHelper($helper){
    return $this->_helper->$helper;
  }

  public function setHelper(BaseHelper $helper){
    $this->_helper = $helper;
  }

  /**
   * Displays the template
   * @param string $controller
   * @param string $action
   */
  public function render($controller=null, $action='') {
    $this->_view->render($action, $controller);
  }

  /**
   *
   * @param array $params
   */
  public function _forward( $params=array()) {/* action,controller */
    switch (sizeof($params)) {
      case '1'://action in same controller
        $this->render($this, $params[0]);
        break;
      case '2': // action in a different controller of the same module
        $module = isset($_GET['module']) ? strval($_GET['module']) : '';
        $controller = $params[1] . "Controller";
        if ($module)
          $controller = $module . "_" . $controller;
        $dispacth = new $controller;
        call_user_func_array(array($controller, $params[0]), '');
        break;
    }
  }

	/**
	 *
	 * @param string $action
	 * @param array $params
	 */
	public function _redirect($action,$params=array()) {
		$controller = isset($_GET['controller']) ? strval($_GET['controller']) : "index";
		$module = strval($_GET['module']);
		$view = new BaseView($this);
		$link = $view->baseUrl() . $module . "/" . $controller . "/" . $action;
		if (count($params)) {
			$parts = '';
			foreach ($params as $key => $value) {
				$parts .= "&$key=$value";
			}
		}

		header("location:$link"."$parts");
	}

  public abstract  function  __toString();
}
?>
