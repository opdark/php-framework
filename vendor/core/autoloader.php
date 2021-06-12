<?php

// we define a constant 'ROOT_DIR' = the project folder
define('ROOT_DIR', dirname(__DIR__,2));
$array = explode('\\', ROOT_DIR) ;
define('ROOT_DIR_NAME',end($array));


include ROOT_DIR.'/vendor/core/autoload_namespaces.php';
include ROOT_DIR.'/vendor/autoload_libraries.php';

spl_autoload_register('autoload');
spl_autoload_register('autoload_library');


function autoload($class) {
    $file = AutoloadNamespace::getFile($class);
    if (is_file($file)) {
        require $file;
    }
}

function autoload_library($class){
    foreach (AutoLoadLibrary::$__files as $className => $path) {
        $file = ROOT_DIR . '/vendor/' . trim($path);
        if ((trim($class) == trim($className)) && is_file($file)) {
            require $file;
        }
    }
}
