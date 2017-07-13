<?php

class FreeLunchLabs_CloudFront_Model_Refresh extends Varien_Object {

    public $cdn_rewrite_directory = "cdn";
    private $cdn_directories = array('media', 'skin', 'js');
    
    function addRefreshSupport() {
        return $this->writeRewriteFile();
    }

    function refreshDirectory($directory = null) {
        if (is_null($directory)) {
            $directories = $this->cdn_directories;
        } else {
            $directories = array(0 => $directory);
        }

        foreach ($directories as $cdn_directory) {
            $this->setKey($cdn_directory);
        }

        $this->writeRewriteFile();
        Mage::getModel('freelunchlabs_cloudfront/config')->setCloudFrontConfig();
    }

    function writeRewriteFile() {
        $adapter = Mage::getModel('freelunchlabs_cloudfront/refreshadapters_apache');
        $base_dir = Mage::getBaseDir() . DS;
        $file = new Varien_Io_File();

        if ($file->cd($base_dir) && $file->checkAndCreateFolder($this->cdn_rewrite_directory, 0755)) {
            if ($file->cd($base_dir . $this->cdn_rewrite_directory)) {
                $file->write($adapter->filename, $adapter->buildRules($this->getAllKeys()), 0644);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getRefreshURL($directory) {
        if (Mage::getStoreConfig('cloudfront/general/refresh') == 1) {
            return $this->cdn_rewrite_directory . "/" . $this->getKey($directory) . "/";
        } else {
            return "";
        }
    }

    function getAllKeys() {
        $keys = array();

        foreach ($this->cdn_directories as $directory) {
            $keys[$directory] = $this->getKey($directory);
        }

        return $keys;
    }

    function getKey($directory) {
        Mage::app()->getStore()->resetConfig();
        $key = Mage::getStoreConfig('cloudfront/general/refresh_' . $directory . '_key');

        if ($key) {
            return $key;
        } else {
            return $this->setKey($directory);
        }
    }

    function setKey($directory) {
        $key = rand(0, 999999);
        Mage::getModel('core/config')->saveConfig('cloudfront/general/refresh_' . $directory . '_key', $key);
        return $key;
    }

}