<?php
namespace Wof\Controllers;

use Wof\AltozRouter;

class CoreController
{

    protected $router;

    public function __construct()
    {
        $this->router = AltozRouter::getInstance();
    }

    public function show($templateName, $viewVars = [])
    {

        // ajout du router aux variables envoyées à la vue
        $viewVars['router'] = $this->router;


        $templatePath = locate_template($templateName);
        // https://developer.wordpress.org/reference/functions/load_template/
        load_template($templatePath, true, $viewVars);
    }


    // redirection simple ; nous pourrions gérer
    public function redirect($location, $status = 302, $x_redirect_by = 'Wof')
    {
        wp_redirect($location, $status, $x_redirect_by);
    }

}
