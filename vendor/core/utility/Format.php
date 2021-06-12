<?php

namespace Core\Utility ;

class Format  {

    
    public static function array2xml($array, $xml = false) {
        if ($xml === false) {
            $xml = new \SimpleXMLElement('<root/>');
        }
        
        foreach ($array as $key => $value) {
            
            $key = is_int($key)? '_' .$key : $key;
            
            if (is_array($value)) {
                self::array2xml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
                
            }
        }
        
        return $xml->asXML();
    }

}

?>