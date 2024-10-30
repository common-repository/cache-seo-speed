<?php
/**
 * Cache SEO Speed
 * by Wallace Rio -  wallrio.com
 */

require_once "CssWallRio_minify.php";

class CssWallRio_spider{

	private static $dirTheme;
	private static $url,$minifyCss,$minifyJs,$mobile_css_noload_external,$includeimports;

	function __construct($config){
		self::$minifyCss = $config->minifyCss;
		self::$minifyJs = $config->minifyJs;
		self::$mobile_css_noload_external = $config->mobile_css_noload_external;
		self::$includeimports = $config->includeimports;
	}

	public function run($content){

		if(function_exists('create_function')){
			$callbackRep = create_function('$match', 'return CssWallRio_spider::linkAdjust($match);');
		}else{
			eval('$callbackRep = function($match){
				return CssWallRio_spider::linkAdjust($match);			
			};');


		}
		$content = preg_replace_callback('/<link[^>]*?rel=["|\']stylesheet["|\']*?[^>]*>/is', $callbackRep, $content);


		if(function_exists('create_function')){
			$callbackRep = create_function('$match', 'return CssWallRio_spider::styleAdjust($match);');
		}else{
			eval('$callbackRep = function($match){
				return CssWallRio_spider::styleAdjust($match);
			};');
		}
		$content = preg_replace_callback('#<script[^<]+?>(.*?)</script>#is', $callbackRep, $content);

		return $content;

	}
	
	


	public static function styleAdjust($match){
		$value = $match[0];
		$valueNew = preg_match_all('/src=["|\'](.*?)["|\']/is',$value,$newMatch);
		$attrStringsPre = preg_replace('/src=["|\'](.*?)["|\']/is','',$value);
		$valueNew = preg_match_all('/<script\b(.*?)*>/is',$attrStringsPre,$newMatch2);
		$attrStrings = str_replace(array('<script','/>','>'),'',$newMatch2[0][0]);

		if(count($newMatch[0]) > 0 ){
			$url = $newMatch[1][0];

			$resultRequest = wp_remote_get($url);
			if(is_array($resultRequest))
            	$contentFile = isset($resultRequest['body'])?$resultRequest['body']:'';
			else if(is_object($resultRequest))
           		$contentFile = isset($resultRequest->body)?$resultRequest->body:'';

						$type = 'js-link';
		}else{
			$url = '';
			$type = 'js-inline';
			$contentFile = $match[1];
		}

		$contentFile = str_replace('<iframe','&lt;iframe',$contentFile);
		$contentFile = str_replace('</script','&lt;/',$contentFile);
		
		$ifMinify = '';
		if(self::$minifyJs === true){
			$ifMinify = 'data-minify="true"';
			$contentFile = CssWallRio_minify::js($contentFile);
		}

		if(strpos($attrStrings, 'data-nowpcss')!== false)return $value;
		

		$valueResult = '<script '.$attrStrings .' '.$ifMinify.' data-css-type="'.$type.'" data-url="'.$url.'" >';
		$valueResult .= $contentFile;
		$valueResult .= '</script>';

		return $valueResult; 
	}

	public static function linkAdjust($match){
		$value = $match[0];
		$attrStringsPre = preg_replace('/href=["|\'](.*?)["|\']/is','',$value);
		$attrStrings = str_replace(array('<link','/>','>'),'',$attrStringsPre);	
		$valueNew = preg_match_all('/href=["|\'](.*?)["|\']/is',$value,$newMatch);
		$url = $newMatch[1][0];

		$resultRequest = wp_remote_get($url);

		if(is_array($resultRequest))
        $contentFile = isset($resultRequest['body'])?$resultRequest['body']:'';
		else if(is_object($resultRequest))
        $contentFile = isset($resultRequest->body)?$resultRequest->body:'';

		
		$urlAdjust = $url;
		$urlAdjust = explode('?', $urlAdjust);
		$urlAdjust = $urlAdjust[0];
		$urlAdjust = dirname($urlAdjust);
	
	
		self::$dirTheme = $urlAdjust;

		if(function_exists('create_function')){
			$callbackRep = create_function('$match', 'return CssWallRio_spider::changeUrlOnStyle($match,"'.$urlAdjust.'");');
		}else{
			eval('$callbackRep = function($match){
				return self::changeUrlOnStyle($match,"'.$urlAdjust.'");
			};');
		}
		$contentFile  = preg_replace_callback('/url\((.*?)\)/is',$callbackRep,$contentFile);


		// replace @import-----------------------------
		if(function_exists('create_function')){
			$callbackRep = create_function('$match', 'return CssWallRio_spider::changeImportStyle($match,"'.$urlAdjust.'");');
		}else{
			eval('$callbackRep = function($match){
				return self::changeImportStyle($match,"'.$urlAdjust.'");
			};');
		}

		$contentFile  = preg_replace_callback('/@import [\'|"](.*?)[\'|"];/is',$callbackRep,$contentFile);
		
		if(function_exists('create_function')){
			$callbackRep = create_function('$match', 'return CssWallRio_spider::changeImportStyle($match,"'.$urlAdjust.'",true);');
		}else{
			eval('$callbackRep = function($match){
				return self::changeImportStyle($match,"'.$urlAdjust.'",true);
			};');
		}
		$contentFile  = preg_replace_callback('/@import url\([\'|"]?(.*?)[\'|"]?\);/is',$callbackRep,$contentFile);





			$ifMinify = '';
		if(self::$minifyCss === true){
			$ifMinify = 'data-minify="true"';
			$contentFile = CssWallRio_minify::css($contentFile);
		}
		
		if(strpos($attrStrings, 'data-nowpcss')!== false)return $value;
		
		$html_style = '<style '.$attrStrings .' '.$ifMinify.' data-css-type="css-link" data-url="'.$url.'" >';
		$html_style .= $contentFile;
		$html_style .= '</style>';

		

		return $html_style; 
	}
	
	public static function changeImportStyle($matches,$url,$modeUrl = null){
		
		$attach_start = '"';
		$attach_end = '"';

		if($modeUrl == true){
			$attach_start = 'url(';
			$attach_end = ')';
		}

		$val = $matches[1];
			if(strpos($val, 'http://') !== false || strpos($val, 'https://') !== false || strpos($val, 'data:') !== false){
				
				if(self::$mobile_css_noload_external === true)
					return '';

				if(self::$includeimports === true){
					$resultRequest = wp_remote_get($val);
					if(is_array($resultRequest))
			        $contentFile = isset($resultRequest['body'])?$resultRequest['body']:'';
					else if(is_object($resultRequest))
			        $contentFile = isset($resultRequest->body)?$resultRequest->body:'';

					return "\n\n".$contentFile."\n\n";
				}
				
				return '@import '.$attach_start.''.$val.''.$attach_end.';';
			}

		$urlAdjust = $url;
		$urlAdjust = explode('?', $urlAdjust);
		$urlAdjust = $urlAdjust[0];
	
		$dirThemeNew = $urlAdjust;
		
		$val = str_replace(array('"','\''), '', $val);
		$val = $dirThemeNew.'/'.$val;

		if(self::$includeimports === true){
			$resultRequest = wp_remote_get($val);
			if(is_array($resultRequest))
	        $contentFile = isset($resultRequest['body'])?$resultRequest['body']:'';
			else if(is_object($resultRequest))
	        $contentFile = isset($resultRequest->body)?$resultRequest->body:'';

			return "\n\n".$contentFile."\n\n";
		}

		
		return '@import '.$attach_start.''.$val.''.$attach_end.';';
	}

	public static function changeUrlOnStyle($matches,$url){
		$val = $matches[1];
		if(strpos($val, 'http://') !== false || strpos($val, 'https://') !== false || strpos($val, 'data:') !== false){
			
			if(self::$mobile_css_noload_external === true)
				$val = '';

			return 'url('.$val.')';
		}
		if(substr($val, 0,1)=='"'){
			$signal = '"';
		}else if(substr($val, 0,1)=='\''){
			$signal = '\'';
		}else{
			$signal = '';
		}
		$val = str_replace(array('"','\''), '', $val);
		$val = $url.'/'.$val;
		return 'url('.$signal.$val.$signal.')';	
	}
}
