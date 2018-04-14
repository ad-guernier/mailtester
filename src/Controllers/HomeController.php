<?php
namespace Guern\Controllers;

use Guern\Controllers\FrontController;

class HomeController extends FrontController {

    public function view() {
        echo $this->twig->render('index.html.twig');
    }
}
