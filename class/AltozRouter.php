<?php
namespace Wof;


class AltozRouter extends \AltoRouter
{

    private static $instance;


    protected $basePath;


    /**
     * get the main router instance
     *
     * @return AltozRouter
     */
    public static function getInstance()
    {
        if(!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }



    public function __construct()
    {
        parent::__construct();
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $this->basePath = $basePath;
        $this->setBasePath($basePath);
        static::$instance = $this;
    }

    /**
     * launch the routing
     *
     * @return mixed
     */
    public function run()
    {
        $match = $this->match();

        if($match['target']) {
            $closure = $match['target'];
            return call_user_func_array($closure, $match['params']);
        }
        else {
            throw new \Exception('No route found');
        }
    }
}