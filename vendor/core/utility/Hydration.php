<?php

namespace Core\Utility;

/**
 * Description of Hydration
 *
 * @author Thomas Darko
 */
trait Hydration {
   
    public function hydarate(Array $param) {
        
        foreach ($param as $key => $value) {
            $method = 'set' .ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    
}
