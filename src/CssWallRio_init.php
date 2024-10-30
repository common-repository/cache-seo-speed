<?php
/**
 * Cache SEO Speed
 * by Wallace Rio -  wallrio.com
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR."CssWallRio_misc.php";
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."CssWallRio_wpload.php";
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."CssWallRio_minify.php";
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."CssWallRio_filter.php";


class CssWallRio_init {

    private $dir;
    public  $enabled = false,
            $theme,
            $timeToUpdate,
            $initialDate,$statusUpdate,
            $version,$statusUpdateForce = false;
    private static $pluginDir,$pluginName,$wpContentDir,$rootDir;    

    function __construct($dir){
        $this->dir = $dir;

        self::$pluginDir = dirname(dirname( __FILE__ )).DIRECTORY_SEPARATOR;
        self::$wpContentDir = dirname(dirname(dirname(dirname(__FILE__)))) .DIRECTORY_SEPARATOR;
        
        self::$rootDir = dirname(dirname(dirname(dirname((dirname( __FILE__) ))))).DIRECTORY_SEPARATOR;
        
        $pluginUrlArray = explode('/', self::$pluginDir);
        $pluginUrlArray = array_filter($pluginUrlArray);
        self::$pluginName = end($pluginUrlArray);

    }  

    public function defaultWordPress(){        
       
        define( 'ABSPATH', self::$rootDir . '/' );
        define('WP_USE_THEMES', true);    
        require( self::$rootDir . DIRECTORY_SEPARATOR . 'wp-blog-header.php' );        
    }

    public function loadPackage(){
       
        $packageFile = self::$pluginDir . DIRECTORY_SEPARATOR . 'package.json';        
        if(file_exists($packageFile)){
            $packageContent = file_get_contents($packageFile);
            $package = json_decode($packageContent);
            return $package;
        }
        return false;
    }


   
    public function checkPage(){            
        $REQUEST_URI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;
        $REQUEST_URIArray = explode('?',$REQUEST_URI);
        $page = $REQUEST_URIArray[0];
        
        $dir = self::$wpContentDir . 'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR .'objects'.DIRECTORY_SEPARATOR;        
        $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
        $single = 'default';
        if($this->singlecache == true)
        $single = str_replace('.', '_', $REMOTE_ADDR).'-'.md5($HTTP_USER_AGENT);
        $dirFinal = $dir.$single.$page;
        $dirFinal = str_replace('//','/',$dirFinal);
        $dirFinal = str_replace(':','_',$dirFinal);
    

        if(file_exists($dirFinal.'index.php'))return true;        
        return false;
    }

    public function savePage($content){
        $REQUEST_URI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;
        $REQUEST_URIArray = explode('?',$REQUEST_URI);
        $page = $REQUEST_URIArray[0];
        $dir = self::$wpContentDir . 'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR .'objects'.DIRECTORY_SEPARATOR;
        $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
        $single = 'default';
        if($this->singlecache == true) $single = str_replace('.', '_', $REMOTE_ADDR).'-'.md5($HTTP_USER_AGENT);
        $dirFinal = $dir.$single.$page;
        $dirFinal = str_replace('//','/',$dirFinal);
        $dirFinal = str_replace(':','_',$dirFinal);
        $single = str_replace(':','_',$single);


         

        if(!file_exists($dirFinal)) mkdir($dirFinal,0777,true);
        file_put_contents($dirFinal.DIRECTORY_SEPARATOR.'index.php', $content);
        file_put_contents($dir.$single.DIRECTORY_SEPARATOR.'request.json',json_encode($_SERVER));
    }

    public function getPage(){
        $REQUEST_URI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;
        $REQUEST_URIArray = explode('?',$REQUEST_URI);
        $page = $REQUEST_URIArray[0];
        $dir = self::$wpContentDir . 'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR .'objects'.DIRECTORY_SEPARATOR;        
        $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
        $single = 'default';
        if($this->singlecache == true)
        $single = str_replace('.', '_', $REMOTE_ADDR).'-'.md5($HTTP_USER_AGENT);
        $dirFinal = $dir.$single.$page;
        $dirFinal = str_replace('//','/',$dirFinal);
        $dirFinal = str_replace(':','_',$dirFinal);
        return file_get_contents($dirFinal.DIRECTORY_SEPARATOR.'index.php');
    }

    public function checkActive(){
        $REQUEST_URI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;
        $REQUEST_URIArray = explode('?',$REQUEST_URI);
        $page = $REQUEST_URIArray[0];

        $this->loadConfig();
    }

    

    public function optimize($content){
        require "CssWallRio_spider.php";
        $spider = new CssWallRio_spider($this->config);
        if( substr($this->theme, strlen($this->theme)-1, strlen($this->theme) ) == '/' ){
            $this->theme = substr($this->theme, 0,strlen($this->theme)-1 );
        }
        $themeName = $this->theme;
        $themeNameArray = explode('/',$themeName);
        $themeName = end($themeNameArray);
        
        $cacheDir = self::$wpContentDir . 'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR .'objects';
       
        $cacheDir_size = CssWallRio_misc::dirSize($cacheDir);
    
        $cacheDir_sizeFormat = number_format($cacheDir_size / 1048576, 1);

        $limitout = false;

        if($this->sizecachelimit == true)
        if($cacheDir_sizeFormat >= $this->sizecachelimitvalue)
            $limitout = true;

        
        if($this->minifyHtml === true)
            $content = CssWallRio_minify::html($content);

        $content = $spider->run($content);
        
        $content = $content."\n".'<!-- Cache SEO Speed - '.$this->version.' : '.date("Y/m/d H:i:s", time()).''.(($this->statusUpdate != null)?' | new update '.$this->statusUpdate:'')
            .(($this->statusUpdateForce===true)?' required':'' ). (( $limitout == true)?' | cache limit reached':'') .' -->';

 
        $content = str_replace('</head','<meta name="generator" content="Cache SEO Speed - '.$this->version.'" />'."\n".'</head',$content);

        $filters = isset($this->filters)?$this->filters:false;
        $content = CssWallRio_filter::init($filters,$content);

        if( $limitout != true)
        $this->savePage($content);
      

        return $content;
    }


    public function dirSize($dir){
        $dirSize = 0;
        if(!is_dir($dir)){return false;};
        $files = scandir($dir);if(!$files){return false;}
        $files = array_diff($files, array('.','..'));

        foreach ($files as $file) {
            if(is_dir("$dir/$file")){
                 $dirSize += self::dirSize("$dir/$file");
            }else{
                $dirSize += filesize("$dir/$file");
            }
        }
        return $dirSize;
    }

    public function runCache($content,$existCache){
     
        if( $existCache === true){   
            echo $this->getPage();
            return true;
        }

     
        $themeDir = $this->theme . DIRECTORY_SEPARATOR;
        $content = $this->optimize($content);
        echo $content;
        return $themeDir;
    }


    public function loadConfig(){
       
        $packageFile = self::$pluginDir . DIRECTORY_SEPARATOR . 'package.json';
        if(file_exists($packageFile)){
            $packageContent = file_get_contents($packageFile);
            $package = json_decode($packageContent);
            $this->package = $package;
            $this->version = isset($package->version)?$package->version:'';
        }

        $configFile = self::$wpContentDir.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR.'.css-config.dat';
        


        if(file_exists($configFile)){
            
            

            $configContent = file_get_contents($configFile);
            $config = json_decode($configContent);

            $this->config = $config;
            $this->theme = isset($config->theme)?$config->theme:null;
            $this->enabled = isset($config->enabled)?$config->enabled:true;
            $this->minifyCss = isset($config->minifyCss)?$config->minifyCss:false;
            $this->minifyJs = isset($config->minifyJs)?$config->minifyJs:false;
            $this->minifyHtml = isset($config->minifyHtml)?$config->minifyHtml:false;
            $this->showloading = isset($config->showloading)?$config->showloading:false;        
            $this->sizecachelimit = isset($config->sizecachelimit)?$config->sizecachelimit:false;
            $this->sizecachelimitvalue = isset($config->sizecachelimitvalue)?$config->sizecachelimitvalue:'';            
            $this->optimizebrowser = isset($config->optimizebrowser)?$config->optimizebrowser:false;
            $this->optimizegzip = isset($config->optimizegzip)?$config->optimizegzip:false;
            $this->mobileusage = isset($config->mobileusage)?$config->mobileusage:false;            
            $this->mobile_css_noload_external = isset($config->mobile_css_noload_external)?$config->mobile_css_noload_external:false;     
            
            $this->includeimports = isset($config->includeimports)?$config->includeimports:false;     

            $this->desktopusage = isset($config->desktopusage)?$config->desktopusage:false;
            $this->timeToUpdate = isset($config->timeToUpdate)?$config->timeToUpdate:5;
                
            $this->useragent_allow = isset($config->useragent_allow)?$config->useragent_allow:'';
            $this->useragent_deny = isset($config->useragent_deny)?$config->useragent_deny:'';        
            $this->pageListBlock = isset($config->pagesblock)?$config->pagesblock:array();
            $this->filters = isset($config->filters)?$config->filters:null;
            $this->singlecache = isset($config->singlecache)?$config->singlecache:false;
          
        }
        
        $this->wpload = new CssWallRio_wpload($this->theme);
        
        return $config;
    }

    public function run($content,$existCache){
        if(!isset($this->desktopusage)) $this->desktopusage = true;
        if(!isset($this->pageListBlock)) $this->pageListBlock = array();
        if(!isset($this->mobileusage)) $this->mobileusage = true;

         $disable_run = false;
         $disableAllow_run = false;
         $disableDeny_run = false;

        $useragent=$_SERVER['HTTP_USER_AGENT'];

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
            if($this->mobileusage === true){
                $disable_run = true;
            }
        }else{
            if($this->desktopusage === true){
                $disable_run = true;
            }
        }

        $checkDenyMatch = 0;
        if(!empty($this->useragent_deny)){
            $checkDenyMatch = @preg_match($this->useragent_deny, $useragent);
            if($checkDenyMatch !== false){
                if($checkDenyMatch > 0){
                    $disableDeny_run = true;
                }else{
                    $disableDeny_run = false;
                }
            }
        }else{
            $disableDeny_run = null;
        }

        $checkAllowMatch = 0;
        if(!empty($this->useragent_allow)){
            $checkAllowMatch = @preg_match($this->useragent_allow, $useragent);
            if($checkAllowMatch !== false){
                if($checkAllowMatch > 0){
                    $disableAllow_run = false;
                }else{
                    $disableAllow_run = true;
                }
            }
        }else{
            $disableAllow_run = null;
        }

        $REQUEST_URI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;      
        $SCRIPT_NAME = isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:null;      
        
        $pageDomain = dirname($SCRIPT_NAME);

        $page =str_replace($pageDomain.'/', '', $REQUEST_URI);
        if(substr($page, strlen($page)-1,strlen($page))=='/')
            $page = substr($page, 0,strlen($page)-1);
        
        if(substr($page, 0,1)=='/')$page = substr($page, 1);

        if($page == '')$page = 'home';
        
        $pageListBlock = $this->pageListBlock;

        foreach ($pageListBlock as $key => $value) {
                if(substr($value, strlen($value)-1,strlen($value))=='/')
                $value = substr($value, 0,strlen($value)-1);  
                if(substr($value, 0,1)=='/')
                $value = substr($value, 1);
                $pageListBlock[$key] = $value;            
        }


        if(
            in_array($page, $pageListBlock) ||
            $this->enabled === false  || $disable_run === true 
            || ( $disableDeny_run === null && $disableAllow_run === true ) 
            || ( $disableDeny_run === true && $disableAllow_run === null ) 
        )return false;
        
 

        if($this->runCache($content,$existCache) == false)return false;
        
        return true;

    }

}
