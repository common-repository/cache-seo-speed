<?php
/*
Plugin Name: Cache SEO Speed
Plugin URI: http://wallrio.com/wordpress/plugins/cache-seo-speed/
Description: Tool for optimization, caching and minification, accelerates the loading of pages with focus on performance analyzers.
Version: 0.1.1
Author: Wallace Rio
Author URI: http://wallrio.com
License: GPLv2

Copyright (C)2018 Wallace Rio - wallrio.com

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/



$dirPlugin = ((dirname(__FILE__))).DIRECTORY_SEPARATOR;
require_once $dirPlugin.'views'.DIRECTORY_SEPARATOR.'CssWallRioTranslation.php';
require_once $dirPlugin.'src'.DIRECTORY_SEPARATOR.'CssWallRio_misc.php';

if(get_locale() == 'en_US'){
    CssWallRioTranslation::setlocale('en');
}else if(get_locale() == 'pt_BR'){
    CssWallRioTranslation::setlocale('pt');
}


if(!class_exists('CssWallRioMain')){
    class CssWallRioMain{

        

        private static $statusUpdate,
                        $statusUpdateForce = false,
                        $updateLink,
                        $rootDir,
                        $pluginName,
                        $pluginUrl,
                        $pluginDir,
                        $backupDir,
                        $wpcontentDir,
                        $uploadDir,
                        $themesDir;

        function __construct($domain){

            register_activation_hook(__FILE__ , array($this, 'enable'));
            register_deactivation_hook(__FILE__ , array($this, 'disable'));

            self::$pluginDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
            
            $pluginUrlArray = explode('/', self::$pluginDir);
            $pluginUrlArray = array_filter($pluginUrlArray);
            self::$pluginName = end($pluginUrlArray);

            self::$pluginUrl = WP_CONTENT_URL.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.self::$pluginName.DIRECTORY_SEPARATOR;

            self::$rootDir = ((dirname(dirname(dirname(dirname(__FILE__)))))) . DIRECTORY_SEPARATOR;
            self::$wpcontentDir = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
            self::$uploadDir = self::$wpcontentDir . 'uploads' . DIRECTORY_SEPARATOR;
            self::$themesDir = self::$wpcontentDir . 'themes' . DIRECTORY_SEPARATOR;
            self::$backupDir = self::$pluginDir . 'backup' . DIRECTORY_SEPARATOR;
            $this->domain = $domain;

            add_action('admin_enqueue_scripts', array('CssWallRioMain','adminStyle') );
            add_action('admin_menu', array('CssWallRioMain','makeMenu'));

        }

        
        /**
         * load content package of plugin
         * @return [object] JSON
         */
        public static function loadPackage(){
            $packageFile = self::$pluginDir . DIRECTORY_SEPARATOR . 'package.json';
            
            if(file_exists($packageFile)){
                $packageContent = file_get_contents($packageFile);
                $package = json_decode($packageContent);
                return $package;
            }
            return false;
        }


        /**
         * load content settings of plugin defined by users
         * @return [object] JSON
         */
        public static function loadConfig(){
             $configFile = self::$wpcontentDir.'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR.'.css-config.dat';
           
            if(file_exists($configFile)){
                $configContent = file_get_contents($configFile);
                $config = json_decode($configContent);
                return  $config;
            }
            return false;
        }

     

    

        /**
         * make the menu in admin area
         * @return null
         */
        public static function makeMenu(){

            $plugin_icon_url = self::$pluginUrl.'icon.png';
            add_menu_page('Cache SEO Speed', 'Cache SEO Speed','manage_options', 'cache-seo-speed',array('CssWallRioMain','managerContent'),$plugin_icon_url);


    		add_submenu_page('cache-seo-speed', CssWallRioTranslation::text('about'), CssWallRioTranslation::text('about'), 'manage_options', 'CssWallRioMain_about',array('CssWallRioMain','aboutContent'));

            
        }

         public static function adminStyle( $hook ) {
            if ( get_option('cacha-seo-speed-start') === 'true' )  return;
            
            wp_register_style( 'custom_wp_admin_css', self::$pluginUrl . 'admin-style.css', false, '0.0.1' );
            wp_enqueue_style( 'custom_wp_admin_css' );
        }

        /**
         * make content page settings of plugin in admin area
         * @return null
         */
        public static function managerContent(){

            add_option('cacha-seo-speed-start','true');

            $viewsDir = self::$pluginDir.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR;
            $viewCurrentDir = $viewsDir.'settings'.DIRECTORY_SEPARATOR;
            ob_start();
            require $viewCurrentDir.'index.php';
            $content = ob_get_contents();
            ob_clean();
            echo $content;
        }

        /**
         * make content page about of plugin in admin area
         * @return null
         */
        public static function aboutContent(){

            $viewsDir = self::$pluginDir.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR;
            $viewCurrentDir = $viewsDir.'about'.DIRECTORY_SEPARATOR;

            
            ob_start();
            require $viewCurrentDir.'index.php';
            $content = ob_get_contents();
            ob_clean();
            echo $content;
        }
       



   

        /**
         * actions on enable plugin
         * @return null
         */
        public  function enable(){


            $WPIcache_loadfile = self::$pluginDir.'src'.DIRECTORY_SEPARATOR.'CssWallRio_load.php';
            $indexFile = self::$rootDir.'index.php';
            $indexContent = file_get_contents($indexFile);
            $current_theme = get_template_directory() . DIRECTORY_SEPARATOR;
            $package = self::loadPackage();
           
            $optionsCache = array(            
                'theme'=>$current_theme,
                'wpcontentdir'=>self::$wpcontentDir,
                'enabled' => false,
                'optimizebrowser' => false,
                'optimizegzip' => false,
                'minifyCss'=>false,
                'minifyJs'=>false,
                'minifyHtml'=>false,
                'timeToUpdate'=>5,
                'mobile_css_noload_external'=>false,
                'showloading'=>false,
                'desktopusage'=>false,
                'mobileusage'=>false,
                'singlecache'=>false,
                'useragent_allow'=>'',
                'useragent_deny'=>'',
                'pagesblock'=>array(),
                'initialDate'=>time()
            );

            $optionsCache = json_encode($optionsCache);
            $dirCache = self::$wpcontentDir.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR;
           
            if(!file_exists($dirCache)) mkdir($dirCache,0777,true);
            if( !file_exists($dirCache.'.css-config.dat') )
            file_put_contents($dirCache.'.css-config.dat',$optionsCache);
       
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
        


        /**
         * actions on disable plugin
         * @return [type] [description]
         */
    	public  function disable(){

            

            delete_option('cacha-seo-speed-start');
            
            $WPIcache_loadfile = self::$pluginDir.'src'.DIRECTORY_SEPARATOR.'CssWallRio_load.php';
            $indexFile = self::$rootDir.'index.php';
            $indexContent = file_get_contents($indexFile);

            $indexContentOut = '<?php';
            $indexContentOut .= "\n".'/*wp-cache-seo-speed[*/ '."\n";
            $indexContentOut .= 'if(file_exists("'.$WPIcache_loadfile.'"))';
            $indexContentOut .= 'require_once "'.$WPIcache_loadfile.'";';
            $indexContentOut .= "\n".'/*]wp-cache-seo-speed*/'."\n";
            $indexContentOut .= '?>'."";

           

            if(strpos($indexContent,$indexContentOut) !== false){
                $indexContent = str_replace($indexContentOut ,'', $indexContent);
                file_put_contents($indexFile,$indexContent);
            }else{

                $indexContentOut  = '/*wp-cache-seo-speed[*/ '."\n";
                $indexContentOut .= 'if(file_exists("'.$WPIcache_loadfile.'"))';
                $indexContentOut .= 'require_once "'.$WPIcache_loadfile.'";';
                $indexContentOut .= "\n".'/*]wp-cache-seo-speed*/';
                if(strpos($indexContent,$indexContentOut) !== false){
                    $indexContent = str_replace($indexContentOut ,'', $indexContent);
                    file_put_contents($indexFile,$indexContent);
                }
            }
    	}

    }

    new CssWallRioMain(get_site_url());
}


