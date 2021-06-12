<?php

namespace Core\UtilitY;

/**
 * Description of FileManager
 *
 * @author Thomas Darko
 */
class FileManager {
    
    
    /**
     * Description: the function takes an XML file path and returns an array from it
     * @param string $file
     * @return array
     */
    public static function xml2array($file) {
        
        if(file_exists($file)){
            
            $content = file_get_contents($file);
            $xml = simplexml_load_string($content);
            $json = json_encode($xml);
            $array_data = json_decode($json, 1);
            
            return $array_data;
        }
    }
}
