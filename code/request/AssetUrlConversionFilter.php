<?php

use \SilverStripeAustralia\ContentServiceAssets\ContentServiceAsset;

/**
 * @author marcus
 */
class AssetUrlConversionFilter implements RequestFilter {
    
    private $convertUrls = false;
    
    
    public function setConvertUrls($v) {
        $this->convertUrls = $v;
    }
    
    public function postRequest(\SS_HTTPRequest $request, \SS_HTTPResponse $response, \DataModel $model) {
        if ($this->convertUrls) {
            $body = $response->getBody();
            
//            if (preg_match_all('{(src|href)="/?(assets/.*?)"}', $body, $matches)) {
//                $names = $matches[2];
//                $files = 
//            }
            // find urls inserted in content
            if (preg_match_all('/data-cdnfileid="(\d+)"/', $body, $matches)) {
                $files = CdnImage::get()->filter('ID', $matches[1]);
                $fileIds = array();
                foreach ($files as $file) {
                    $url = $file->getUrl();
                    $filename = $file->Filename;
                    $body = str_replace("src=\"$filename\"", "src=\"$url\"", $body);
                    $fileIds[] = $file->ID;
                }
                
                $assets = ContentServiceAsset::get()->filter('SourceID', $matches[1]);
                foreach ($assets as $asset) {
                    $url = $asset->getUrl();
                    $filename = $asset->Filename;
                    // note the extra forward slash here, image_cached inserts it
                    $body = str_replace("src=\"/$filename\"", "src=\"$url\"", $body);
                }

                $response->setBody($body);
            }
        }
    }

    public function preRequest(\SS_HTTPRequest $request, \Session $session, \DataModel $model) {
        
    }

}
