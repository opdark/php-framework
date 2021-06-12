<?php

namespace Constant;

/**
 * Description of FileType
 *
 * @author user
 */
class FileType {
    const DOC_INVOICE = 0;
   
    const DOC_WAYBILL = 1;
   
    const DOC_SMSCODE = 3;
   
    const DOC_PACKAGE = 4;
   
    const DOC_FIELD = 5;
    
    const DOC_VALUE = 6;
    
    const DOC_TEMP = 99;
   
    
    static function getFileFolder($type){
        return '_'.$type.'_';
    }
            
    
    const EXTENSION_PDF = ['pdf'];
   
    const EXTENSION_JSON = ['json'];
   
    const EXTENSION_XML = ['xml'];
   
    const EXTENSION_EXCEL = ['csv', 'xls', 'xlsx'];
   
    const EXTENSION_IMAGE = ['png', 'gif', 'jpeg','jpg', 'ico','jfif'];
    
    
}
