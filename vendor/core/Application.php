<?php

namespace Core;

use Core\Service\Container;
use Core\Http\Response;
use Core\Dto\User;

/**
 * Description of Application
 *
 * @author Thomas Darko
 */
class Application {

    /**
     *
     * @var \Core\Service\Container 
     */
    private $container;

    /**
     *
     * @var \Core\Http\Response 
     */
    private $response;

    /**
     * Short description of attribute $currentRoute
     *
     * @access private
     * @var Routing\Route
     */
    private $currentRoute;

    /**
     * Short description of attribute user
     *
     * @access private
     * @var User
     */
    private $user = null;

    /**
     * 
     */
    public function __construct() {

        $this->user = new User();

        // initialization of the response to return by the application
        $this->response = new Response($this);

        // initialization of the service container to be used by the application
        $this->container = Container::getInstance($this);
    }

    /**
     * 
     * @return Response
     */
    public function run() {

        //retrieving of the request from the container
        $request = $this->container->loadDefaultServices()->getRequest();

        // getting the route that matches the request url
        $uri = $request->getDirectUri();


        //update of the response format if we are making api call 
        $this->currentRoute = $this->container->getRouter()->getMatched($uri);
//       var_dump($uri);
//       var_dump($this->currentRoute);
//        exit;
        //case the route does not exist, we check if the request is viewing the routes of the application
        if (!$this->currentRoute) {
            $this->manageNotFound($uri);
        }


        // control if the params are valid
        $params = $request->getQueryString();
        if ($this->currentRoute->hasParams() && !$this->currentRoute->matchesQueryString($params)) {
            $this->response->halt('The params are invalid !');
        }

        //forcing to pass through authentication, if the route is secured
        if ($this->currentRoute->isSecured() && !$this->user->isAuthenticated()) {
            $loginRoute = $this->getContainer()->getConfig()->getDefaultRouteName('_login');
            
            ($loginRoute)? $this->redirect($loginRoute) 
                    : $this->response
                    ->halt('User not Authenticated...,  Kindly specify the name of the route leading to the login page as value of the attibute:  "_login"  '
                            . ' <br/> in the config.xml file.  Hierrachy of tags: app-routes-default > route-names !');
        }

        // runing of the  middlewares 
        $this->runMiddlewares();



        // runing of the action
        $this->runAction($request);


        return $this->response;
    }

    /**
     * 
     */
    private function runMiddlewares() {

        // getting the middle ware define on the route
        foreach ($this->currentRoute->getMiddlewares() as $middlewareClass) {

            if (class_exists($middlewareClass)) {
                $middleware = new $middlewareClass($this);
                $middleware->run();
            }
        }
    }

    /**
     * 
     */
    private function runAction(Http\Request $request) {

        // getting the instance of controller set in the route  that matches the uri
        $controller = $this->currentRoute->getController($this);

        if (!$controller) {
            $this->response->halt('The Controller * ' . $this->currentRoute->getControllerName() . ' * defined in the routing file does not exist !');
        }

        $method = $this->currentRoute->getAction();

        // running of the action inside the controller
        if ($controller->hasMethod($method)) {
//            $controller->setCurrentRoute($this->currentRoute);
            $controller->$method($request);
        }

        // case the method defined in the routing file does not exist in the controller
        else {
            $this->response->halt('The method * ' . $method . ' * defined in the routing file does not exist !');
        }
    }

    /**
     * 
     */
    private function manageNotFound($uri) {

        // case the uri contains api the we asume its a api call so we set the format to json
        (strpos($uri, '/api') != false) ? $this->response->setFormat('json') : '';
        $configroute = $this->getContainer()->getConfig()->getAllRoutes();
 
        // case it is urls displaying all app routes 
        if ($configroute && ($configroute['prefix'] == $uri) && ($configroute['active'] == 'true')) {
            $routes = $this->getContainer()->getRouter()->getRoutes();
            foreach ($routes as $route) {
                $list[] = $route->toArray();
            }
            $this->getResponse()->setData($list)->setFormat('xml')->addMessage(' ----THE ROUTES ----- ')->send();
        }
        
        // case route is also different from all routes of config, the we redirect to notfound page
        else {
            
            $notFoundRoute = $this->getContainer()->getConfig()->getDefaultRouteName('_404');
            
            var_dump($notFoundRoute);exit;
            
            // if a route is customized then we redirect the user to that route
            ($notFoundRoute)? $this->redirect($notFoundRoute) : $this->response->halt('URI is incorrect !') ;
        }
    }

    /**
     * 
     * @return Container
     */
    public function getContainer() {

        return $this->container;
    }

    /**
     * 
     * @return Response
     */
    public function getResponse() {

        return $this->response;
    }

    /**
     * 
     * @return Routing\Route
     */
    public function getCurrentRoute() {
        return $this->currentRoute;
    }

    /**
     * 
     * @return Pack
     */
    public function getCurrentPack() {
        $name = $this->getCurrentRoute()->getPackName();
        return $this->getContainer()->getConfig()->getPackByName($name);
    }

    /**
     * 
     * @return Dto\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * 
     * @return Application
     */
    public function getApp() {
        return $this;
    }

    public function redirect($routeName, $params = []) {
        $url = $this->getContainer()->getRouter()->getUriByRouteName($routeName, $params);
        return ($url) ?
                $this->response->redirect($url) :
                $this->response->halt(" The route '" . $routeName . "' with its params is not a valid route");
    }

}
