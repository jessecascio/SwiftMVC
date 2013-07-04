<?php

/**
 * This is the default Controller.  All controllers must extend the Controller parent class
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage application  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo      
 */

class IndexController extends core\Controller
{
    /**
     * Controllers have _init() functions instead of __construct()
     */
    public function _init()
    {
        // .js files to be added to the page
        $this->view->addScript('js/ajax_example.js');
    }

    public function indexAction()
    {
        // actions automatically display their views on destruction i.e. controller/action.phtml

        // to disable the auto rendering of the view
        //$this->view->noAutoRender(); 

        // if you want to render a different view then the default one, disable the auto rendering
        // and render the view that you want
        //$this->view->noAutoRender(); 
        //$this->view->render('index/other.phtml');

        // you can pass variables to the view scripts
        // $this->view->myVar = "MVCMasta";

        // you can access the request data for POST, GET, COOKIE, SESSION, and URL data
        // sample url: <public>/index/index/myVar/hello
        // or a get:   <public>?myVar=hello
        //$this->view->myVar = $this->request->myVar;

        // if you need to redirct to another controller's action get the Router,
        // you cannot use this technique to get to another action inside the same controller
        //$this->view->noAutoRender();
        //$Router = core\Router::getInstance();
        //$Router->redirect('sample/index');   
    }

    public function ajaxCallAction()
    {   
        // for ajax calls typically you will need to disable the auto render
        // and verify that the request is an ajax request so json doesnt output to browser
        $this->view->noAutoRender();   
        $this->request->requireAjax();

        // you have access to the passed vars via the Request object
        //$ajax_var = $this->request->myVar;

        // you can send back raw data or a view fragment
        $this->view->ajax_var = $this->request->myVar;
        $this->view->controller_var = "Result of Controller logic";
        $view = $this->view->getView('index/ajax_partial.phtml');
        echo json_encode(array('html_response' => $view));
    }
}

?>

