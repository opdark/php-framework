<?php


namespace Core\Dto; 

/**
 * Short description of class Dto
 *
 * @access public
 * @author Thomas Darko,  
 */
class Dto implements \ArrayAccess
{
    
    use \Core\Utility\Hydration;

  
   /**
     * Short description of method __construct
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function __construct(array $attribs=[])
    {
        $this->hydrate($attribs);
    }
    
    /**
     * Short description of method hydrate
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
     public function hydrate(array $attribs) {
        foreach ($attribs as $attribute => $value) {
            $method = 'set' . ucfirst($attribute);
            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
    }


    

    /**
     * Short description of method toArray
     *
     * @access public
     * @author Thomas Darko,  
     * @return array
     */
    public function toArray() {
        return json_decode(json_encode($this), true);
    }
    
    /**
     * 
     * @param type $offset
     */
    public function offsetExists($offset) {
        
    }

    public function offsetGet($offset) {
        
    }

    public function offsetSet($offset, $value) {
        
    }

    public function offsetUnset($offset) {
        
    }

} 
