<?php
namespace Guern\Controllers;

use Guern\Interfaces\ControllerInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;

class FrontController implements ControllerInterface {
    protected $twig;
    protected $request;
    private $controllerName;
    private $action;

    public function __construct() {
        $request = $_SERVER['REQUEST_URI'];

        if ($request === '/') {
            $this->controllerName = 'Home';
            $this->action = 'view';
        } else {
            $request = explode('/', $request);
            $this->controllerName = $request[1];
            $this->action = isset($request[2]) ? $request[2] : 'view';
        }

        $loader = new FilesystemLoader('views'); // Dossier contenant les templates
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);
        $this->twig->addExtension(new DebugExtension());

        $this->controllerName = 'Guern\Controllers\\' . $this->controllerName . 'Controller';
    }

    public function run() {
        $controller = new $this->controllerName();
        $action = $this->action;
        $controller->$action();
    }

    public function view(){}
}
