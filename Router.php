<?php

namespace thecodeholic\phpmvc;

use thecodeholic\phpmvc\exception\ForbiddenException;
use thecodeholic\phpmvc\exception\NotFoundException;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];



    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }
    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {

            throw new NotFoundException();
            return $this->renderView("_404");
        }
        if (is_string($callback)) {
            return Application::$app->view->renderView($callback);
        }
        if (is_array($callback)) {
            /** @var \thecodeholic\phpmvc\Controller $controller  */
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddlewares() as $middleware)
                $middleware->execute();
        }
        return  call_user_func($callback, $this->request, $this->response);
    }
}
