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

$pluginUrlArray = explode('/', $dirPlugin);
$pluginUrlArray = array_filter($pluginUrlArray);
$pluginName = end($pluginUrlArray);

$pluginUrl = plugins_url().'/'.$pluginName.'/';



require_once $dirPlugin.'views'.DIRECTORY_SEPARATOR.'CssWallRioView.php';
CssWallRioView::$dirPlugin = $dirPlugin;
$package = CssWallRioView::package();
$version = isset($package->version)?$package->version:''; 

?>

<style media="screen">
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
</style>

<div class="wraps">
    <br>
    <br>
    <div class="icon-text">
        <div class="icon">
            <img src="<?php echo $pluginUrl; ?>icon-default.png" alt="Logo" style="height: 50px;display: inline-table;vertical-align: bottom;">
        </div>
        <div class="text">
            <h1 style="display: inline-table;vertical-align: bottom;padding-left:5px">Cache SEO Speed <small><?php echo $version; ?></small> </h1>
            <h2><?php echo CssWallRioTranslation::text('about'); ?></h2>
        </div>
    </div>
    <br><br>

    <table class="form-table">
        <tr>
            <th scope="row"><?php echo CssWallRioTranslation::text('author'); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('author'); ?></span></legend><label for="">
                    Wallace Rio
                    </label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo CssWallRioTranslation::text('site'); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo CssWallRioTranslation::text('site'); ?></span></legend><label for="">
                    <a href="http://wallrio.com/wordpress/plugins/cache-seo-speed/" target="_blank">Web Site</a>
                    </label>
                </fieldset>
            </td>
        </tr>
    </table>
</div>
