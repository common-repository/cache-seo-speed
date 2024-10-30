<?php
/**
 * Cache SEO Speed
 * by Wallace Rio -  wallrio.com
 */


    $wpcontentDir = (dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR;
    $pluginDir = dirname(__FILE__).DIRECTORY_SEPARATOR;
	
	require $pluginDir."CssWallRio_init.php";

	$wpicache = new CssWallRio_init(dirname(__FILE__));
	$content = null;
	$wpicache->loadConfig();
	$wpicache->wpload->setTimeToUpdate($wpicache->timeToUpdate);
	$existCache = true;	

	// check update in admin area
	if(CssWallRio_wpload::updated() === true){
        $cacheDir = $wpcontentDir . 'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR .'objects'.DIRECTORY_SEPARATOR;
		
         CssWallRio_misc::rrmdir($cacheDir);
       
    }
   
    if($wpicache->checkPage() != true ){

    	$existCache = false;    
		ob_start();
			$dirRoot = dirname(dirname(dirname(dirname(dirname( __FILE__ )))));
		    define( 'ABSPATH', $dirRoot . '/' );
		    define('WP_USE_THEMES', true);    
		    require( $dirRoot . DIRECTORY_SEPARATOR . 'wp-blog-header.php' );
	    $content = ob_get_contents();
	    ob_clean();

	}
	
	if($wpicache->run($content,$existCache) === false){		
		if($content == ''){		
			$dirRoot = dirname(dirname(dirname(dirname(dirname( __FILE__ )))));
		    define( 'ABSPATH', $dirRoot . '/' );
		    define('WP_USE_THEMES', true);    		   
		    require( $dirRoot . DIRECTORY_SEPARATOR . 'wp-blog-header.php' );
	    }else{
	    	echo $content;
	    }	   
	}
	exit;

