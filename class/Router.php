<?php
namespace Wof;

class Router
{

    const FRONT_CONTROLLER_FILEPATH = WOF_FILEPATH . '/front-controller.php';
    const WOF_ROUTE_PARAMETER = 'wof-custom-route';

    protected $routes = [];

    public function __construct()
    {

    }

    public function addRoute($url) {
        $this->routes[] = $url;
        return $this;
    }


    public function registerRoutes()
    {

        foreach($this->routes as $url) {
            // https://developer.wordpress.org/reference/functions/add_rewrite_rule/
            // DOC regexp http://www.expreg.com/presentation.php
            add_rewrite_rule(
                $url,
                'index.php?'. static::WOF_ROUTE_PARAMETER . '=1',  // vers quel "format virtuel" wordpress va transformer l'url demandée
                'top'   // la route se mettra en haut de la pile de priorités des routes enregistrées par wordpress
            );
        }

        // WARNING penser à retirer ceci dans la vrai vie
        $this->flushRoutes();

        // ce hook permet à wordpress de savoir quel fichier il va utiliser en tant que template
        add_action('template_include', [$this, 'displayTemplate']);
    }

    // le paramètre $template est le template que wordpress compte utiliser
    public function displayTemplate($template)
    {
        // récupération de la variable "vituelle get" enregistrée par wordpress
        // DOC https://developer.wordpress.org/reference/functions/get_query_var/
        // équivalent à $_GET['custom-route'];
        $customRoute = get_query_var(static::WOF_ROUTE_PARAMETER);


        // si le paramètre $customRouteName vaut test; nous décidons d'afficher le template page-test
        if(!empty($customRoute)) {
            return static::FRONT_CONTROLLER_FILEPATH;
        }
        // sinon ; on affiche le template que wordpress comptait utiliser
        return $template;
    }


    public function flushRoutes()
    {
        // nous demandons à wp de supprimer le cache des routes. Wordpress gère les routes en base de donnée. Attention ici le flush_rewrite_rules est "bourrin" ; il faudrait "casser le cache des routes" L'endoit moment idéal  serait au moment de l'activation du plugin
        flush_rewrite_rules();
    }

    // ==============================================================
    public function register()
    {
        // nous demandons à wordpress d'enregistrer dans les paramètre envoyés, la "fausse variable GET" custom-route
        add_filter('query_vars', function ($query_vars) {
            $query_vars[] = static::WOF_ROUTE_PARAMETER;
            return $query_vars;
        });

        add_action('init', [$this, 'registerRoutes']);
    }

}
