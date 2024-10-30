<?php
/**
 * Cache SEO Speed
 * by Wallace Rio -  wallrio.com
 */

class CssWallRioView{

    public static $dirPlugin,$singlecache;

    public static function package(){
        $dirRoot = dirname(dirname( __FILE__ ));
        $packageFile = $dirRoot . DIRECTORY_SEPARATOR . 'package.json';
        if(file_exists($packageFile)){
            $packageContent = file_get_contents($packageFile);
            $package = json_decode($packageContent);
            return $package;
        }
        return false;
    }



    

    public static function getDeviceByUserAgent($agent){
        $os = 'unknown';
        $br = 'unknown';
        if(preg_match('/Linux/i',$agent)) $os = 'Linux';
          elseif(preg_match('/Mac/i',$agent)) $os = 'macOS'; 
          elseif(preg_match('/iPhone/i',$agent)) $os = 'iPhone'; 
          elseif(preg_match('/iPad/i',$agent)) $os = 'iPad'; 
          elseif(preg_match('/Droid/i',$agent)) $os = 'Droid'; 
          elseif(preg_match('/Unix/i',$agent)) $os = 'Unix'; 
          elseif(preg_match('/Windows/i',$agent)) $os = 'Windows';
          else $os = 'Unknown';         
        if(preg_match('/Firefox/i',$agent)) $br = 'Firefox'; 
          elseif(preg_match('/Mac/i',$agent)) $br = 'Mac';
          elseif(preg_match('/Chrome/i',$agent)) $br = 'Chrome'; 
          elseif(preg_match('/Opera/i',$agent)) $br = 'Opera'; 
          elseif(preg_match('/MSIE/i',$agent)) $br = 'IE'; 
          else $bs = 'Unknown';        
          return (object) array(
            'os'=>$os,
            'browser'=>$br
          );
    }

    public static function optimizeBrowserCache($dirRoot,$POST){
        $optimizebrowser = isset($POST['optimizebrowser'])?$POST['optimizebrowser']:false;
        $optimizegzip = isset($POST['optimizegzip'])?$POST['optimizegzip']:false;
        $htaccessFile = $dirRoot.'.htaccess';
        if(file_exists($htaccessFile)){
            $htaccessContentPre = file_get_contents($htaccessFile);
        }else{
            $htaccessContentPre = '';            
        }
        
    
        
        $insertion = '';

        if($optimizebrowser == true)
                    $insertion .= '<ifModule mod_headers.c>'."\n".'<FilesMatch "\.(ico|pdf|jpg|jpeg|png|gif|html|htm|xml|txt|xsl|js|css)$">'."\n".' Header set Cache-Control "max-age=31536050" '."\n".'</FilesMatch>'."\n".'</IfModule>';

        if($optimizegzip == true)
                    $insertion .= "\n".'<IfModule mod_deflate.c>
AddOutputFilterByType DEFLATE text/plain
AddOutputFilterByType DEFLATE text/xml
AddOutputFilterByType DEFLATE application/xhtml+xml
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE application/xml
AddOutputFilterByType DEFLATE image/svg+xml
AddOutputFilterByType DEFLATE application/rss+xml
AddOutputFilterByType DEFLATE application/atom_xml
AddOutputFilterByType DEFLATE application/x-javascript
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-httpd-php
AddOutputFilterByType DEFLATE application/x-httpd-fastphp
AddOutputFilterByType DEFLATE application/x-httpd-eruby
AddOutputFilterByType DEFLATE text/html
SetOutputFilter DEFLATE
BrowserMatch ^Mozilla/4 gzip-only-text/html
BrowserMatch ^Mozilla/4.0[678] no-gzip
BrowserMatch ^HMSI[E] !no-gzip !gzip-only-text/html
SetEnvIfNoCase Request_URI .(?:gif|jpe?g|png)$ no-gzip dont-vary
</IfModule>';

            $marker = 'cache-seo-speed';

            if($insertion == ''){
                CssWallRio_misc::remove_marker($htaccessFile, $marker);
                return false;
            }

            CssWallRio_misc::insert_with_markers( $htaccessFile, $marker, $insertion );
                       
         
    }

    
    public static function removeOptimizeBrowserCache($dirRoot){
        $htaccessFile = $dirRoot.'.htaccess';
        if(file_exists($htaccessFile)){
            $htaccessContent = file_get_contents($htaccessFile);
            if(strpos($htaccessContent,"# Wpicache Optimization ---- "."\n".'<FilesMatch "\.(ico|pdf|jpg|jpeg|png|gif|html|htm|xml|txt|xsl|js|css)$">'."\n".' Header set Cache-Control "max-age=31536050" '."\n".'</FilesMatch>') !== false){
                    $htaccessContent = str_replace("\n"."# Wpicache Optimization ---- "."\n".'<FilesMatch "\.(ico|pdf|jpg|jpeg|png|gif|html|htm|xml|txt|xsl|js|css)$">'."\n".' Header set Cache-Control "max-age=31536050" '."\n".'</FilesMatch>','',$htaccessContent);
                    file_put_contents($htaccessFile,$htaccessContent);
            }
        }
    }

    

    

    public static function removeConfig($request){
        $filename = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'cache/cache-seo-speed'.DIRECTORY_SEPARATOR.'.css-config.dat';
        unlink($filename);
    }

    public static function getRequestSigleCache(){
        $wpcontentDir = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR;
        $dir = $wpcontentDir.DIRECTORY_SEPARATOR . 'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR .'objects'.DIRECTORY_SEPARATOR;            
        
        $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
        $single = 'default';
        if(self::$singlecache == true) $single = str_replace('.', '_', $REMOTE_ADDR).'-'.md5($HTTP_USER_AGENT);

        $arrayNew = Array();
        if(file_exists($dir)){            
            $cacheItemArray = scandir($dir);
            foreach ($cacheItemArray as $key => $value) {
                if($value !== '.' && $value !== '..'){
                    $filename = $dir.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$value.DIRECTORY_SEPARATOR.'request.json';;


                   

                    $filename = str_replace('//', '/', $filename);
                     // echo $filename;

                    // $filename = str_replace(':', '_', $filename);
                    $content = @file_get_contents($filename);
                    if($content == false) continue;
                    $arrayNew[] = json_decode($content);
                }
            }
        }
        return ($arrayNew);
    }

    public static function saveConfig($request,$themechange = false){
        if(isset($request['enabled']))
            $enabled = true;
        else
            $enabled = false;

        if($themechange == true){
            $theme = get_template_directory() . DIRECTORY_SEPARATOR;
        }else{
            $theme = isset($request['theme'])?$request['theme']:'';
        }
        $wpcontentdir = isset($request['wpcontentdir'])?$request['wpcontentdir']:'';
        if(isset($request['minifyCss']))
            $minifyCss = true;
        else
            $minifyCss = false;

        if(isset($request['minifyJs']))
            $minifyJs = true;
        else
            $minifyJs = false;

        if(isset($request['minifyHtml']))
            $minifyHtml = true;
        else
            $minifyHtml = false;
    
        if(isset($request['sizecachelimit']))
            $sizecachelimit = true;
        else
            $sizecachelimit = false;

        if(isset($request['optimizebrowser']))
            $optimizebrowser = true;
        else
            $optimizebrowser = false;

        if(isset($request['optimizegzip']))
            $optimizegzip = true;
        else
            $optimizegzip = false;

        if(isset($request['mobileusage']))
            $mobileusage = true;
        else
            $mobileusage = false;
        
        if(isset($request['showloading']))
            $showloading = true;
        else
            $showloading = false;

        if(isset($request['mobile_css_noload_external']))
            $mobile_css_noload_external = true;
        else
            $mobile_css_noload_external = false;
        
        if(isset($request['includeimports']))
            $includeimports = true;
        else
            $includeimports = false;

        if(isset($request['desktopusage']))
            $desktopusage = true;
        else
            $desktopusage = false;


        $timeToUpdate = isset($request['timeToUpdate'])?$request['timeToUpdate']:5;
        $useragent_allow = isset($request['useragent_allow'])?$request['useragent_allow']:'';
        $useragent_deny = isset($request['useragent_deny'])?$request['useragent_deny']:'';        
        $pagesblock = isset($request['pagesblock'])?$request['pagesblock']:'';        
        $serverconfig = isset($request['serverconfig'])?$request['serverconfig']:null;
        $sizecachelimitvalue = isset($request['sizecachelimitvalue'])?$request['sizecachelimitvalue']:'';
        
        if(isset($request['filters']))
            $filters = true;
        else
            $filters = false;

        if(isset($request['singlecache']))
            $singlecache = true;
        else
            $singlecache = false;

        self::$singlecache = $singlecache;

        $dirCache = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'cache/cache-seo-speed'.DIRECTORY_SEPARATOR;
        $filename = $dirCache.'.css-config.dat';        
        self::saveIndexWp();
        if(file_exists($filename) ){
            $precontent = file_get_contents($filename);
            $precontent = json_decode($precontent);        
        }

        $pagesblockArray = explode(',', $pagesblock);
        if($wpcontentdir == '')$wpcontentdir = WP_CONTENT_DIR.DIRECTORY_SEPARATOR;
        
        $array = array(            
            'theme'=>$theme,
            'wpcontentdir'=>$wpcontentdir,
            'enabled'=>$enabled,
            'minifyCss'=>$minifyCss,
            'minifyJs'=>$minifyJs,
            'minifyHtml'=>$minifyHtml,
            'sizecachelimit'=>$sizecachelimit,
            'sizecachelimitvalue'=>$sizecachelimitvalue,
            'optimizebrowser'=>$optimizebrowser,
            'optimizegzip'=>$optimizegzip,
            'mobileusage'=>$mobileusage,
            'mobile_css_noload_external'=>$mobile_css_noload_external,
            'includeimports'=>$includeimports,
            'showloading'=>$showloading,
            'desktopusage'=>$desktopusage,
            'singlecache'=>$singlecache,
            'timeToUpdate'=>$timeToUpdate,
            'useragent_allow'=>$useragent_allow,
            'useragent_deny'=>$useragent_deny,
            'filters'=>$filters,
            'pagesblock'=>$pagesblockArray
        );

        $data = json_encode($array);    
        $dir = dirname($filename);
        if(!file_exists($dir)){
            if(!mkdir($dir,0777,true)){                
            }
        }
        file_put_contents($filename,$data);

    }

    public static function saveIndexWp(){
        $rootDir = (dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . DIRECTORY_SEPARATOR;
        $pluginDir = ((((dirname(dirname(__FILE__)))))).DIRECTORY_SEPARATOR;

        $WPIcache_loadfile = $pluginDir.'src'.DIRECTORY_SEPARATOR.'CssWallRio_load.php';
        $indexFile = $rootDir.'index.php';
        $indexContent = file_get_contents($indexFile);
        $indexContentOut = '<?php';
        $indexContentOut .= "\n".'/*wp-cache-seo-speed[*/ '."\n";
        $indexContentOut .= 'if(file_exists("'.$WPIcache_loadfile.'"))';
        $indexContentOut .= 'require_once "'.$WPIcache_loadfile.'";';
        $indexContentOut .= "\n".'/*]wp-cache-seo-speed*/'."\n";
        $indexContentOut .= '?>'."";

        if(strpos($indexContent,$indexContentOut) === false){
            $indexContentOut .= $indexContent;
            file_put_contents($indexFile,$indexContentOut);
        }
    }

}
