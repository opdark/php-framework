<?php

class AutoloadNamespace {

   
    static $__folders = [
        'model' => ['entity', 'table'],
        'vendor' => ['core'],
        'shared' => ['constant', 'middleware', 'service', 'provider']
    ];

    
    
    
    
    
    static function getFile($class) {
        $folders = explode('\\', $class);
        $firsFolder = strtolower(trim($folders[0]));
        $className = end($folders);
        unset($folders[count($folders) - 1]);

        $file = '';
        $folder_path = strtolower(trim(implode('/', $folders)));


        $file = ROOT_DIR . '/vendor/' . $folder_path . '/' . $className . '.php';

        //loading all class from Packs
        if (strpos($firsFolder, 'pack') !== FALSE) {
            return $file = ROOT_DIR . '/pack/' . str_replace('pack', '', $folder_path) . '/' . $className . '.php';
        } else {

            foreach ( self::$__folders as $folder =>$firstFolders) {
                if (in_array($firsFolder, $firstFolders)) {
                    return $file = ROOT_DIR . '/' . $folder. '/' . $folder_path . '/' . $className . '.php';
                }
            }
        }
    }

}
