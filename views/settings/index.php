<?php
/**
 * Cache SEO Speed
 * by Wallace Rio -  wallrio.com
 */



$configPath = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR.'.css-config.dat';
$dirCache = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'cache/cache-seo-speed'.DIRECTORY_SEPARATOR;

$wpcontentDir = dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR;
$dirPlugin = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR;
$dirRoot = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).DIRECTORY_SEPARATOR;
$urlPlugin = plugins_url().'/';
// $domain = str_replace('wp-content/plugins/', '', $urlPlugin);

$pluginUrlArray = explode('/', $dirPlugin);
$pluginUrlArray = array_filter($pluginUrlArray);
$pluginName = end($pluginUrlArray);

$pluginUrl = plugins_url().'/'.$pluginName.'/';


require_once $dirPlugin.'src'.DIRECTORY_SEPARATOR."CssWallRio_misc.php";
require_once $dirPlugin.'src'.DIRECTORY_SEPARATOR."CssWallRio_wpload.php";
require_once $dirPlugin.'views'.DIRECTORY_SEPARATOR.'CssWallRioView.php';


CssWallRioView::$dirPlugin = $dirPlugin;

$package = CssWallRioView::package();
$version = isset($package->version)?$package->version:''; 
$messageAction = '';

$objectsDir = $wpcontentDir . 'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR .'objects';

$styleCleanBt = '';
if(!file_exists($objectsDir)){
    $styleCleanBt = 'disabled';
}
if(isset($_POST['clean'])){
    if(file_exists($objectsDir)){
        CssWallRio_misc::rrmdir($objectsDir);
        $messageAction = '<div style="display:table;margin-top:10px;margin-bottom:10px;background:#fff;color:green;padding:20px 40px">'.CssWallRioTranslation::text('alert-cache-clean').'!</div>';    
        CssWallRioView::saveConfig($_POST,true);
    }
}

$objectsDir_size = CssWallRio_misc::dirSize($objectsDir);
$objectsDir_size = number_format($objectsDir_size / 1048576, 1) . ' MB';
$server = json_encode($_SERVER);
$domain = get_site_url() ;
$domainUrl = urlencode( $domain );

    require_once $dirPlugin.'src'.DIRECTORY_SEPARATOR."CssWallRio_optimage.php"; 
  
if(isset($_POST['restoreoptimage'])){
    // require_once $dirPlugin.'src'.DIRECTORY_SEPARATOR."CssWallRio_optimage.php"; 
     CssWallRio_optimage::restoreImages(
        $dirCache.DIRECTORY_SEPARATOR."backup",
        $wpcontentDir,
        $domain.'/wp-content/'     
    );

    if(file_exists($dirCache.DIRECTORY_SEPARATOR."opt.zip")){
        unlink($dirCache.DIRECTORY_SEPARATOR."opt.zip");
    }

    CssWallRio_optimage::deleteExtractDir($dirCache.DIRECTORY_SEPARATOR."extract");

    $messageAction = '<div style="display:table;margin-top:10px;margin-bottom:10px;background:#fff;color:green;padding:20px 40px">'.CssWallRioTranslation::text('optimization-restored').'!</div>'; 

}

$pageOpt = '';
if(isset($_POST['optimizeimage'])){
    // require_once $dirPlugin.'src'.DIRECTORY_SEPARATOR."CssWallRio_optimage.php"; 
    if(file_exists($objectsDir)){
        CssWallRio_misc::rrmdir($objectsDir);
        
    }

       

 
    $optimizeimage_page = isset($_POST['optimizeimage_page'])?$_POST['optimizeimage_page']:'';
    if($optimizeimage_page == 'home'){
        $pageOpt = $domain; 
    }else{
        if(substr($optimizeimage_page, 0,1) == '/') $optimizeimage_page = substr($optimizeimage_page, 1);

        if(substr($domain, strlen($domain)-1) == '/')
            $domain = substr($domain, $domain,strlen($domain)-1);

        $pageOpt = $domain.'/'.$optimizeimage_page;
    }

    CssWallRio_optimage::deleteExtractDir($dirCache.DIRECTORY_SEPARATOR."extract");
    
    if(file_exists($dirCache.DIRECTORY_SEPARATOR."opt.zip")){
        unlink($dirCache.DIRECTORY_SEPARATOR."opt.zip");
    }

    CssWallRio_optimage::download(
        'https://www.googleapis.com/pagespeedonline/v3beta1/optimizeContents?url='.$pageOpt.'?strategy=desktop',
        $dirCache.DIRECTORY_SEPARATOR."opt.zip",
        $domain
    );

    CssWallRio_optimage::extract(
        $dirCache.DIRECTORY_SEPARATOR."opt.zip",
        $dirCache.DIRECTORY_SEPARATOR."extract".DIRECTORY_SEPARATOR
    );

    CssWallRio_optimage::createFileWithDirectory(
        $dirCache.DIRECTORY_SEPARATOR."extract",
        $wpcontentDir,
        $domain.'/wp-content/'     
    );



    // CssWallRio_optimage::deleteExtractDir($dirCache.DIRECTORY_SEPARATOR."extract");

    $messageAction = '<div style="display:table;margin-top:10px;margin-bottom:10px;background:#fff;color:green;padding:20px 40px">'.CssWallRioTranslation::text('optimization-image-finish').'!</div>';    

}
$restoreoptimage_visible = '';  
if(!file_exists($dirCache.DIRECTORY_SEPARATOR."backup"))
$restoreoptimage_visible = ' style="display:none" ';  


$optimizeimage_disabled = '';
if( $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1' ){
    $optimizeimage_disabled = 'disabled';
}


$optimizedImage = false;
if(file_exists($dirCache.DIRECTORY_SEPARATOR."opt.zip") === true){
    $optimizedImage = true;
}



if(isset($_POST['save'])){

    check_admin_referer( 'save-settings');

    $enabled = isset($_POST['enabled'])?$_POST['enabled']:false;
    if($enabled === true || $enabled === "on"){
        CssWallRioView::optimizeBrowserCache($dirRoot,$_POST);
    }else{
        CssWallRioView::removeOptimizeBrowserCache($dirRoot,$_POST);
    }
    CssWallRioView::saveConfig($_POST);
    $messageAction = '<div style="display:table;margin-top:10px;margin-bottom:10px;background:#fff;color:green;padding:20px 40px">'.CssWallRioTranslation::text('alert-saved-success').'!</div>';
}


$disabled_all = '';
       
if(file_exists($configPath)){
    $configContent = file_get_contents($configPath);
    $config = json_decode($configContent,true);
}else{
    $config = array();
}

       
$theme = isset($config['theme'])?$config['theme']:'';    
$enabled = isset($config['enabled'])?$config['enabled']:false;
$minifyCss = isset($config['minifyCss'])?$config['minifyCss']:false;
$minifyJs = isset($config['minifyJs'])?$config['minifyJs']:false;
$minifyHtml = isset($config['minifyHtml'])?$config['minifyHtml']:false;
$showloading = isset($config['showloading'])?$config['showloading']:false;        
$mobile_css_noload_external = isset($config['mobile_css_noload_external'])?$config['mobile_css_noload_external']:false;        
$includeimports = isset($config['includeimports'])?$config['includeimports']:false;        
$timeToUpdate = isset($config['timeToUpdate'])?$config['timeToUpdate']:5;
$activatekey = isset($config['activatekey'])?$config['activatekey']:'';        
$sizecachelimit = isset($config['sizecachelimit'])?$config['sizecachelimit']:false;
$sizecachelimitvalue = isset($config['sizecachelimitvalue'])?$config['sizecachelimitvalue']:'';
$optimizebrowser = isset($config['optimizebrowser'])?$config['optimizebrowser']:false;
$optimizegzip = isset($config['optimizegzip'])?$config['optimizegzip']:false;
$desktopusage = isset($config['desktopusage'])?$config['desktopusage']:false;
$mobileusage = isset($config['mobileusage'])?$config['mobileusage']:false;        
$useragent_allow = isset($config['useragent_allow'])?$config['useragent_allow']:'';
$useragent_deny = isset($config['useragent_deny'])?$config['useragent_deny']:'';        
$pagesblock = isset($config['pagesblock'])?$config['pagesblock']:array();
$pagesblockShow = implode(',', $pagesblock);
$filters = isset($config['filters'])?$config['filters']:false;
$singlecache = isset($config['singlecache'])?$config['singlecache']:false;
$useragent=$_SERVER['HTTP_USER_AGENT'];
$checkAllowMatch = null;
if(!empty($useragent_allow))
$checkAllowMatch = @preg_match($useragent_allow, $useragent);        
$checkDenyMatch = null;
if(!empty($useragent_deny))
$checkDenyMatch = @preg_match($useragent_deny, $useragent);
$key_email = isset($config['key_email'])?$config['key_email']:'';
$key_plan = isset($config['key_plan'])?$config['key_plan']:'';

$disabled_ifactive_all = '';
$hide_ifactive_all = '';
$disabled_all = '';
$themeName = explode('/', $theme);
$themeName = array_filter($themeName);
$themeName = end($themeName);
$enabled_checked = '';
$minifyCss_checked = '';
$minifyJs_checked = '';
$minifyHtml_checked = '';
$optimizebrowser_checked = '';
$sizecachelimitcheck = '';
$mobileusage_checked = '';
$mobile_css_noload_external_checked = '';
$includeimports_checked = '';
$desktopusage_checked = '';
$showloading_checked = '';
$activatekeyStyle = '';
$optimizegzip_checked = '';
$filters_checked = '';
$singlecache_checked = '';

if($enabled === true){
    $enabled_checked = ' checked ';
}
if($minifyCss === true){
    $minifyCss_checked = ' checked ';
}
if($minifyJs === true){
    $minifyJs_checked = ' checked ';
}

if($minifyHtml === true){
    $minifyHtml_checked = ' checked ';
}

if($optimizebrowser === true){
    $optimizebrowser_checked = ' checked ';
}

if($sizecachelimit === true){
    $sizecachelimitcheck = ' checked ';
}

if($optimizegzip === true){
    $optimizegzip_checked = ' checked ';
}
if($mobileusage === true){
    $mobileusage_checked = ' checked ';
}
if($includeimports === true){
    $includeimports_checked = ' checked ';
}

if($desktopusage === true){
    $desktopusage_checked = ' checked ';
}

if($showloading === true){
    $showloading_checked = ' checked ';
}

if($filters === true){
    $filters_checked = ' checked ';
}

if($singlecache === true){
    $singlecache_checked = ' checked ';
}

$wpload = new CssWallRio_wpload($theme);
$wpload->writeOnFunctions();

$countListhostsRequests = 0;
$listhostsRequests = '';
if($singlecache == true){
    
    $listhostsRequestsPre = CssWallRioView::getRequestSigleCache();

    $countListhostsRequests = count($listhostsRequestsPre);
    foreach ($listhostsRequestsPre as $key => $value) {
        $ip = isset($value->REMOTE_ADDR)?$value->REMOTE_ADDR:'';
        $server = isset($value->SERVER_NAME)?$value->SERVER_NAME:'';
        $time = date('m/d/Y h:i:s', isset($value->REQUEST_TIME)?$value->REQUEST_TIME:'' );
        $device = CssWallRioView::getDeviceByUserAgent( isset($value->HTTP_USER_AGENT)?$value->HTTP_USER_AGENT:'' );
        $listhostsRequests .= '<li>'.$time.' - '.$ip.' - '.$device->os.' - '.$server.' - '.$device->browser.'</li>';
    }

}

$serverName = isset($_SERVER['SERVER_SOFTWARE'])?$_SERVER['SERVER_SOFTWARE']:'unknown';
$serverConf = '';
if(strpos(strtolower($serverName), 'nginx') !== false){            
    if(file_exists('/etc/nginx/nginx.conf'))
    $serverConf = file_get_contents('/etc/nginx/nginx.conf');
}

$localIP = getHostByName(php_uname('n'));


if(!file_exists($dirCache))mkdir($dirCache,0777,true);
$is_writable = file_put_contents($dirCache.'testwrite.test', "writed");
if( $is_writable < 1 ){
    $messageAction .= '<div style="display:table;margin-top:10px;margin-bottom:10px;background:red;color:#fff;padding:20px 40px">'.CssWallRioTranslation::text('alert-permission-directory').' <strong>wp-content</strong> </div>';
}
unlink($dirCache.'testwrite.test');       


$is_writable = file_put_contents( $dirCache .'testwrite.test', "writed");        
if( $is_writable < 1 ){
    $messageAction .= '<div style="display:table;margin-top:10px;margin-bottom:10px;background:red;color:#fff;padding:20px 40px">'.CssWallRioTranslation::text('alert-permission-directory').' <strong>'.dirname(WP_CONTENT_DIR).'</strong> </div>';
}
unlink($dirCache.'testwrite.test');   

$baseRoot = dirname(dirname(dirname(dirname(dirname(( __FILE__ ))))));
$checkUpdateFile = $baseRoot.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'cache-seo-speed'.DIRECTORY_SEPARATOR.'.css-lastupdate.dat';
$checkUpdateDir = basename($checkUpdateFile);

if(CssWallRio_misc::checkWritable( dirname($checkUpdateFile) ) === false){
    $messageAction .= '<div style="display:table;margin-top:10px;margin-bottom:10px;background:red;color:#fff;padding:20px 40px">'.CssWallRioTranslation::text('alert-permission-directory').' <strong>'.dirname($checkUpdateFile).DIRECTORY_SEPARATOR.'</strong> </div>';
}   

?>

<style media="screen">

.CssWallrio_success{
    color: green;
}
@media (min-width:600px){
    .line{
        display: table;
    }.col{
        display: table-cell;
        width: 60%;
    }
}
.form-table{
}.form-table tr{
    border-bottom: 1px solid #ccc;
}.form-table tr th{
    padding: 10px;
    background: #fff;
    color: #777;
}.form-table tr td{
    background: #e1e1e1;
}
.buttonFat{
    padding: 10px 30px !important;
    height: auto !important;
}
.icon-text{
    display: table;
}.icon-text .icon{
    display: table-cell;
    width: 0.1px;
    vertical-align: middle;
}.icon-text .text{
    display: table-cell;
    vertical-align: middle;
}
.donation input{
    outline: 0;
}
h1,h2{
    padding: 0px !important;
    margin: 0px !important;
}
h1{
    color:#FF4900;
}
h1 small{
    font-weight: normal;
    font-size: 15px;
}h2{
    font-weight: normal;
    color:#444;
}

.link{
    background-color: transparent;
    padding: 10px 0px;
    margin: 0px;
    border:0px;
    color:blue;
    text-decoration: underline;
    cursor: pointer;
}.link:hover{
    text-decoration: underline;
}
</style>

<br>
<br>
<div class="wraps">

    <div class="icon-text">
        <div class="icon">
            <img src="<?php echo $pluginUrl; ?>icon-default.png" alt="Logo" style="height: 50px;display: inline-table;vertical-align: bottom;">
        </div>
        <div class="text">
            <h1 style="display: inline-table;vertical-align: bottom;padding-left:5px">Cache SEO Speed <small><?php echo $version; ?></small> </h1>
    <h2><?php echo CssWallRioTranslation::text('settings'); ?></h2>
        </div>
    </div>
    <br><br>
    <div class="line">
        <div class="col">


            <form method="post">
                
                <?php wp_nonce_field( 'save-settings'); ?>

                <table class="form-table">



                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('enable'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('enable'); ?></span></legend><label for="">
                                <input name="enabled" id="enabled"  type="checkbox" <?php echo $enabled_checked; ?> <?php echo $disabled_all; ?> >
                                </label>
                            </fieldset>
                        </td>
                    </tr>


                    <tr>
                        <th scope="row"><label for="blogname"><?php echo CssWallRioTranslation::text('theme'); ?></label></th>
                        <td>
                            <input type="hidden" name="theme" id="theme" value="<?php echo $theme; ?>" class="regular-text" type="text" readonly>

                            <?php echo $themeName; ?>
                        </td>
                    </tr>


                    <tr>
                        <th scope="row">CSS</th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span>CSS</span></legend><label for="">
                                <input name="minifyCss" id="minifyCss"  type="checkbox" <?php echo $minifyCss_checked; ?> <?php echo $disabled_all; ?> >
                                <?php echo CssWallRioTranslation::text('to-minify-css'); ?>
                                </label>
                            </fieldset>

                            <input name="includeimports" id="includeimports"  type="checkbox" <?php echo $includeimports_checked; ?> <?php echo $disabled_all; ?>> <?php echo CssWallRioTranslation::text('include-imports-css'); ?> 
                                </label>

                        </td>
                    </tr>


                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('to-minify-js'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('to-minify-js'); ?></span></legend><label for="">
                                <input name="minifyJs" id="minifyJs"  type="checkbox" <?php echo $minifyJs_checked; ?> <?php echo $disabled_all; ?>>
                                </label>
                            </fieldset>
                        </td>
                    </tr>


                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('to-minify-html'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('to-minify-html'); ?></span></legend><label for="">
                                <input name="minifyHtml" id="minifyHtml"  type="checkbox" <?php echo $minifyHtml_checked; ?> <?php echo $disabled_all; ?>>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('action-to-cache'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('action-to-cache'); ?></span></legend><label for="">
                                <input name="clean" id="clean" class="button button-primary " value="<?php echo CssWallRioTranslation::text('clean-cache'); ?>" type="submit" <?php echo $styleCleanBt; ?> <?php echo $disabled_all; ?>>
                                <p class="description" id="home-description"><?php echo CssWallRioTranslation::text('size-current'); ?>: <?php echo $objectsDir_size; ?></p>
                                </label>

                                <br>
                                <br>
                                 <input name="sizecachelimit" id="sizecachelimit"  type="checkbox" <?php echo $sizecachelimitcheck; ?> <?php echo $disabled_all; ?>> <?php echo CssWallRioTranslation::text('size-cache-limit'); ?>
                                 <br>
                                <input type="number" name="sizecachelimitvalue" min="0" value="<?php echo $sizecachelimitvalue; ?>" step="0.1"> MB
                                
                            </fieldset>
                        </td>
                    </tr>



                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('image-optimization-title'); ?>
                            <br>
                            <small style="font-weight: normal !important;"><?php echo CssWallRioTranslation::text('image-optimization-alert'); ?>
                                </small>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('image-optimization-title'); ?></span></legend>
                                <label for="">
                                   
                                    <input name="optimizeimage" id="optimizeimage" class="button button-primary " value="<?php echo CssWallRioTranslation::text('optimizate-image'); ?>" type="submit" <?php echo $optimizeimage_disabled; ?> <?php echo $disabled_all; ?>>
                                    <br>

                                    <button type="submit" name="restoreoptimage" id="restoreoptimage" class="link" <?php echo $restoreoptimage_visible; ?> <?php echo $disabled_all; ?> ><?php echo CssWallRioTranslation::text('optimization-restore-button'); ?></button>

                                    <p class="description" >
                                        <?php

                                            if($optimizedImage === true){
                                                echo '<span class="CssWallrio_success">Optimized</span>';
                                            }

                                        ?>
                                        <?php echo $pageOpt; ?>
                                    </p>

                                </label>

                                <br>
                                <br>
                                <i>Página</i>
                                <input type="text" name="optimizeimage_page" value="home" > 
                                
                            </fieldset>
                        </td>
                    </tr>




                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('optimize-cache'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('optimize-cache'); ?></span></legend>

                                <label for="optimizebrowser">
                                <input name="optimizebrowser" id="optimizebrowser"  type="checkbox" <?php echo $optimizebrowser_checked; ?> <?php echo $disabled_all; ?>> <?php echo CssWallRioTranslation::text('cache-browser'); ?>
                                </label>
                                <br>
                                <label for="optimizegzip">
                                <input name="optimizegzip" id="optimizegzip"  type="checkbox" <?php echo $optimizegzip_checked; ?> <?php echo $disabled_all; ?>> <?php echo CssWallRioTranslation::text('compression-gzip'); ?>
                                </label>

                                <p>
                                    <?php echo CssWallRioTranslation::text('optimize-cache-notice'); ?>
                                </p>

                            </fieldset>
                        </td>
                    </tr>


                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('allow-user-agent'); ?></th>
                        <td>
                            <fieldset>
                                <input name="useragent_allow" id="useragent_allow"  type="text" value="<?php echo $useragent_allow; ?>" <?php echo $disabled_all; ?> >
                                <p class="description" id="useragent_allow-example"><?php echo CssWallRioTranslation::text('usage'); ?> REGEX, Ex: /chrome/im </p>
                                </label>
                                <p style="color:red"><?php if($checkAllowMatch === false)echo 'Error on regex!'; ?></p>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('block-user-agent'); ?></th>
                        <td>
                            <fieldset>
                                <input name="useragent_deny" id="useragent_deny"  type="text" value="<?php echo $useragent_deny; ?>" <?php echo $disabled_all; ?> >
                                <p class="description" id="useragent_deny-example"><?php echo CssWallRioTranslation::text('usage'); ?> REGEX, Ex: /chrome/im </p>
                                <p style="color:red"><?php if($checkDenyMatch === false)echo 'Error on regex!'; ?></p>
                                </label>
                            </fieldset>
                        </td>
                    </tr>


                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('user-agent-current'); ?>:</th>
                        <td>
                            <fieldset>
                             <?php echo $_SERVER['HTTP_USER_AGENT']; ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                     <tr>
                        <th scope="row"><label for="blogname"><?php echo CssWallRioTranslation::text('server'); ?>:</label></th>
                        <td>                        
                            <?php echo $serverName; ?> - 
                            <?php echo $localIP; ?>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('on-mobile'); ?>:</th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('on-mobile'); ?></span></legend><label for="mobileusage">
                                <input name="mobileusage" id="mobileusage"  type="checkbox" <?php echo $mobileusage_checked; ?> <?php echo $disabled_all; ?>> 
                                <?php echo CssWallRioTranslation::text('mobile-disable-cache'); ?>
                                </label>
                                <br />

                                <input name="mobile_css_noload_external" id="mobile_css_noload_external"  type="checkbox" <?php echo $mobile_css_noload_external_checked; ?> <?php echo $disabled_all; ?>> <?php echo CssWallRioTranslation::text('mobile-no-load-external-css'); ?> 
                                </label>
                             

                                
                             

                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('on-desktop'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('on-desktop'); ?></span></legend>

                                <label for="desktopusage">
                                <input name="desktopusage" id="desktopusage"  type="checkbox" <?php echo $desktopusage_checked; ?> <?php echo $disabled_all; ?>> <?php echo CssWallRioTranslation::text('desktop-disable-cache'); ?>
                                </label>

                            </fieldset>
                        </td>
                    </tr>

                       
                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('no-use-cache-on-pages'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('no-use-cache-on-pages'); ?></span></legend>

                                <label for="pagesblock">
                                <input name="pagesblock" id="pagesblock"  type="text" value="<?php echo $pagesblockShow; ?>" <?php echo $disabled_all; ?>>
                                <p class="description" >
                                <?php echo CssWallRioTranslation::text('no-use-cache-on-pages-example'); ?>
                            </p>

                                </label>

                            </fieldset>
                        </td>
                    </tr>


                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('single-cache'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('single-cache'); ?></span></legend>

                                <label for="singlecache">
                                <input name="singlecache" id="singlecache"  type="checkbox" <?php echo $singlecache_checked; ?> <?php echo $disabled_all; ?>> Single Cache
                                </label>

                                <p class="description" >
                                    <?php echo CssWallRioTranslation::text('single-cache-resume'); ?>
                                     (<?php echo $countListhostsRequests; ?>)</p>
                                
                                <div style="overflow:auto;position: relative;width: 100%;height: 100px;border:1px solid #ccc;">
                                    
                                    <div style="position: absolute;white-space: nowrap;">
                                        <ul>                                        
                                           <?php echo $listhostsRequests; ?> 
                                        </ul>
                                    </div>

                                </div>


                            </fieldset>
                        </td>
                    </tr>


                    <tr>
                        <th scope="row"><?php echo CssWallRioTranslation::text('filters'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('filters'); ?></span></legend>

                                <label for="filters">
                                <input name="filters" id="filters"  type="checkbox" <?php echo $filters_checked; ?> <?php echo $disabled_all; ?>> <?php echo CssWallRioTranslation::text('filters-enable'); ?>
                                </label>

                                <p class="description" >
                                    <?php echo CssWallRioTranslation::text('filters-resume'); ?>:
                                    <ul>
                                        <li>GoogleAnalytics</li>
                                        <li>GTM</li>
                                    </ul>
                                </p>
                               

                            </fieldset>
                        </td>
                    </tr>


                    </tbody>
                </table>

                <br>
                <input name="save" id="save" class="button button-primary buttonFat" value="<?php echo CssWallRioTranslation::text('save'); ?>" type="submit" <?php echo $disabled_all; ?>>
            </form>

        </div>
        <div class="col" style="    padding: 0px 30px;">

            <?php echo $messageAction; ?>

            <h2><?php echo CssWallRioTranslation::text('donation'); ?></h2>
            <p><?php echo CssWallRioTranslation::text('donation-resume'); ?></p>
            <p></p>

            <h3><?php echo CssWallRioTranslation::text('donation-pagseguro'); ?></h3>

           <!-- INICIO FORMULARIO BOTAO PAGSEGURO -->
            <form class="donation" action="https://pagseguro.uol.com.br/checkout/v2/donation.html" method="post" target="_blank">
            <!-- NÃO EDITE OS COMANDOS DAS LINHAS ABAIXO -->
            <input type="hidden" name="currency" value="BRL" />
            <input type="hidden" name="receiverEmail" value="wallrio@gmail.com" />
            <input type="hidden" name="iot" value="button" />
            <input type="image" src="https://wallrio.com/wordpress/plugins/cache-seo-speed/pagseguro/donate-button.jpg" name="submit" alt="Pague com PagSeguro - é rápido, grátis e seguro!" />
            </form>
            <!-- FINAL FORMULARIO BOTAO PAGSEGURO -->

            <br>

            <h3><?php echo CssWallRioTranslation::text('donation-paypal'); ?></h3>

            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="LEQYPHXF26W66">
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/pt_BR/i/scr/pixel.gif" width="1" height="1">
</form>



            <br>
            <br>
            <h2><?php echo CssWallRioTranslation::text('notices'); ?></h2>            
            <p>1. <?php echo CssWallRioTranslation::text('notices-1'); ?></p>
            <p>2. <?php echo CssWallRioTranslation::text('notices-2'); ?> </p>

        </div>

    </div>
</div>

