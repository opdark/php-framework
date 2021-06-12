<?php

namespace Core;

/**
 * Description of Middleware
 *
 * @author Thomasino
 */
abstract class Middleware {
    
    /**
     *
     * @var Application 
     */
    protected $app ;


    public function __construct(Application $app) {
        $this->app = $app ;
    }
    
    public abstract function run(); 
}
