<?php

class FreeLunchLabs_CloudFront_Model_RefreshAdapters_Apache extends Varien_Object {
    
    public $filename = '.htaccess';

    function buildRules($keys) {
        $contents = "RewriteEngine on \n";
        
        foreach($keys as $key) {
            $contents .= "RewriteRule ".$key."/(.*) ../$1 [PT] \n";
        }
        
        return $contents;
    }
    
    function getAdapterFileName() {
       return $this->filename;
    }
    
}
