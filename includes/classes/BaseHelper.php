<?php
class BaseHelper {
  //put your code here
  /**
   *
   * @var BaseController
   */
  protected $controller;
  
  public function __construct(BaseController $controller){
    $this->controller = $controller;
  }
  public function getController(){
    return $this->controller;
  }  
  public function __get($name){
    if(class_exists($name)) {
       return new $name();
    }
  } 
}
?>
