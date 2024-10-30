<?php
/**
 * Cache SEO Speed
 * by Wallace Rio -  wallrio.com
 */


class CssWallRio_wpload{

    private static $timeToUpdate,$theme,$pluginDir,$pluginName,$wpContentDir,$rootDir;    

    function __construct($theme){
        self::$theme = $theme;

        self::$pluginDir = dirname(dirname( __FILE__ )).DIRECTORY_SEPARATOR;
        self::$wpContentDir = dirname(dirname(dirname(dirname(( __FILE__ ))))).DIRECTORY_SEPARATOR;
        self::$rootDir = dirname(dirname(dirname(dirname((dirname( __FILE__) ))))).DIRECTORY_SEPARATOR;
        
        $pluginUrlArray = explode('/', self::$pluginDir);
        $pluginUrlArray = array_filter($pluginUrlArray);
        self::$pluginName = end($pluginUrlArray);

    }


    public static function updated(){

       
        $checkUpdateFile = self::$wpContentDir.'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR.'.css-lastupdate.dat';
        $fileUpdatedPath = $checkUpdateFile;
        $timeToUpdate = self::$timeToUpdate;
       
        if(file_exists($fileUpdatedPath)){
            $fileUpdated = file_get_contents($fileUpdatedPath);

         

            if( strtotime(Date('Y/m/d H:i:s')) < strtotime($fileUpdated)+ $timeToUpdate  ){
          
                date_default_timezone_set('Brazil/East');
                $content = date("Y/m/d H:i:s", strtotime($fileUpdated)- $timeToUpdate );                
                file_put_contents($fileUpdatedPath,$content);
                return true;
            }
        }
        return false;
    }

    public function setTimeToUpdate($timeToUpdate){
        self::$timeToUpdate = $timeToUpdate;
    }

    public function writeOnFunctions(){
        $directoryTheme = self::$theme;
        $directoryFunctions = $directoryTheme.DIRECTORY_SEPARATOR.'functions.php';

        $directoryFunctions = str_replace('//', '/', $directoryFunctions);

        if(file_exists($directoryFunctions)){
            $functionsContent = file_get_contents($directoryFunctions);
            if( strpos($functionsContent,'CssWallRioOnsave') === false ){

          
                $checkUpdateFile = self::$wpContentDir.'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR.'.css-lastupdate.dat';
                $checkUpdateDir = dirname($checkUpdateFile).DIRECTORY_SEPARATOR;

           

                    if ( ! file_exists( $checkUpdateDir ) ) {
                        if ( ! is_writable( dirname( $checkUpdateDir ) ) ) {
                            return false;
                        }
                        if ( ! touch( $checkUpdateDir ) ) {
                            return false;
                        }
                    } elseif ( ! is_writeable( $checkUpdateDir ) ) {
                        return false;
                    }


                if(!file_exists($checkUpdateDir))mkdir($checkUpdateDir,0777,true);


                $code = "<?php function CssWallRioOnsave(){date_default_timezone_set('Brazil/East');file_put_contents('".$checkUpdateFile."', Date('Y/m/d H:i:s'));}add_action( 'save_post', 'CssWallRioOnsave' ); ?>";
                $functionsContent = $code."\n".$functionsContent;

               


                file_put_contents($directoryFunctions,$functionsContent);
            }
        }
    }

    public  function restoresFunction(){

            $directoryTheme =  get_template_directory() . DIRECTORY_SEPARATOR;
            $directoryFunctions = $directoryTheme.DIRECTORY_SEPARATOR.'functions.php';

            $directoryFunctions = str_replace('//', '/', $directoryFunctions);

            if(file_exists($directoryFunctions)){

                $functionsContent = file_get_contents($directoryFunctions);
                if( strpos($functionsContent,'CssWallRioOnsave') !== -1 ){
                 
                 
                    $checkUpdateFile = self::$wpContentDir.'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR.'.css-lastupdate.dat';

                    $code = "<?php function CssWallRioOnsave(){date_default_timezone_set('Brazil/East');file_put_contents('".$checkUpdateFile."', Date('Y/m/d H:i:s'));}add_action( 'save_post', 'CssWallRioOnsave' ); ?>"."\n";

                    $functionsContent = str_replace($code, '', $functionsContent);
                    file_put_contents($directoryFunctions,$functionsContent);
                }
            }
        }
        

}
