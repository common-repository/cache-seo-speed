<?php
/**
 * Cache SEO Speed
 * by Wallace Rio -  wallrio.com
 */

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("display_startup_erros",true);


class CssWallRio_filter{
	
	public static function init($filters,$content = ''){

		if($filters == true){
			$filters = array(
				'GoogleAnalyticsRemoveOnPageSpeed',
				'GtmRemoveOnPageSpeed'
			);
		}else{
			return $content;
		}
		if(is_array($filters))
		foreach ($filters as $key => $value) {
			// if($value == 'GoogleAnalyticsRemoveOnPageSpeed')
				// $content = CssWallRio_filter::GoogleAnalyticsRemoveOnPageSpeed($content);

			if($value == 'GtmRemoveOnPageSpeed')
				$content = CssWallRio_filter::GtmRemoveOnPageSpeed($content);
		}
		return $content;
	}
/*
	public static  function GoogleAnalyticsRemoveOnPageSpeed($content = ''){	
        $sourceReplaceAnalit= '#\(function\(i,s,o,g,r,a,m\)\{i\[\'GoogleAnalyticsObject#ism';
        $targetReplaceAnalit = 'if(navigator.userAgent.indexOf("Speed Insights") == -1) { (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject';            
        $content = preg_replace($sourceReplaceAnalit, $targetReplaceAnalit, $content);
        $sourceReplaceAnalit= '#ga\(\'send\'\, \'pageview\'\)\;#ism';
        $targetReplaceAnalit = 'ga(\'send\', \'pageview\');}';         
        $content = preg_replace($sourceReplaceAnalit, $targetReplaceAnalit, $content);
        return $content;
	}

	public static  function GtmRemoveOnPageSpeed($content = ''){		
        $sourceReplaceAnalit = '#\(function\(w,d,s,l,i\)\{#ism';
        $targetReplaceAnalit = 'if(navigator.userAgent.indexOf("Speed Insights") == -1) { (function(w,d,s,l,i){';            

        $content = preg_replace($sourceReplaceAnalit, $targetReplaceAnalit, $content);

        $sourceReplaceAnalit= '#dataLayer\',\'(.*)\'\);\<#ism';
        $targetReplaceAnalit = 'dataLayer\',\'$1\');}<';         
        $content = preg_replace($sourceReplaceAnalit, $targetReplaceAnalit, $content);

        return $content;
	}*/

	public static  function GoogleAnalyticsRemoveOnPageSpeed($content = ''){
		// set GoogleAnalytics Remove On PageSpeed
            $sourceReplaceAnalit= '#\(function\(i,s,o,g,r,a,m\)\{i\[\'GoogleAnalyticsObject#im';
            // $targetReplaceAnalit = 'if(navigator.userAgent.indexOf("Speed Insights") == -1) { (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject';            
            $targetReplaceAnalit = 'window.addEventListener(\'mousemove\', function()  { (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject';            
            $content = preg_replace($sourceReplaceAnalit, $targetReplaceAnalit, $content);
            $sourceReplaceAnalit= '#ga\(\'send\'\, \'pageview\'\)\;#im';
            // $targetReplaceAnalit = 'ga(\'send\', \'pageview\');}';         
            $targetReplaceAnalit = 'ga(\'send\', \'pageview\');});';         
            $content = preg_replace($sourceReplaceAnalit, $targetReplaceAnalit, $content);

        return $content;
	}

	public static  function GtmRemoveOnPageSpeed($content = ''){		
        $sourceReplaceAnalit= '#\(function\(w,d,s,l,i\)\{#im';
        // $targetReplaceAnalit = 'if(navigator.userAgent.indexOf("Speed Insights") == -1) { (function(w,d,s,l,i){';            
        $targetReplaceAnalit = 'window.addEventListener("mousemove",function(){ (function(w,d,s,l,i){';            
        $content = preg_replace($sourceReplaceAnalit, $targetReplaceAnalit, $content,-1,$count);


        $sourceReplaceAnalit= '#dataLayer\',\'(.*)\'\);\<#im';
        // $targetReplaceAnalit = 'dataLayer\',\'$1\');}<';         
        $targetReplaceAnalit = 'dataLayer\',\'$1\');});<';         
        $content = preg_replace($sourceReplaceAnalit, $targetReplaceAnalit, $content);
        return $content;
	}

}