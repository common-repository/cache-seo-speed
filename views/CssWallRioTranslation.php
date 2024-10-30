<?php
/**
 * Cache SEO Speed
 * by Wallace Rio -  wallrio.com
 */

class CssWallRioTranslation{

	public static $locale;
	public static function setlocale($locale){
		self::$locale = $locale;
	}

	public static function text($text){
		$sourceEN = array(
			'settings'=>'Settings',
			'enable'=>'Enable',
			'theme'=>'Theme',
			'to-minify-css'=>'To minify CSS',
			'to-minify-js'=>'To minify JS',
			'to-minify-html'=>'To minify HTML',
			'action-to-cache'=>'Action to cache',
			'size-current'=>'Size current',
			'size-cache-limit'=>'Limit cache size',
			'optimize-cache'=>'Cache optimization',
			'optimize-cache-notice'=>'The htaccess will be modified (apache)',
			'cache-browser'=>'Cache browser',
			'compression-gzip'=>'Compression Gzip',
			'clean-cache'=>'Clean cache',
			'allow-user-agent'=>'Allow User Agent',
			'block-user-agent'=>'Block User Agent',
			'usage'=>'Usage',
			'user-agent-current'=>'User Agent Current',
			'server'=>'Servidor',
			'on-mobile'=>'On mobile',
			'mobile-disable-cache'=>'Disable cache',
			'mobile-no-load-external-css'=>'No load external CSS',
			'on-desktop'=>'On desktop',
			'desktop-disable-cache'=>'Disable cache',
			'visualization'=>'Visualization',
			'no-use-cache-on-pages'=>'No use cache on pages',
			'no-use-cache-on-pages-example'=>'separate with comma, ex: home, product/itens, about',
			'single-cache'=>'Single Cache',
			'single-cache-resume'=>'Record cache by users separated',
			'filters'=>'Filters',
			'filters-enable'=>'Enable',
			'filters-resume'=>'Remove from PageSpeed',
			'save'=>'Save',
			'donation'=>'Donation',
			'donation-resume'=>'Help keep <strong>SEO Speed Cache</strong> active and free, make a donation of any value by clicking the button below..',
			'donation-pagseguro'=>'Donate with PagSeguro',
			'donation-paypal'=>'Donate with PayPal',
			'notices'=>'Notices',
			'notices-1'=>'The cache is to build in first access on page',
			'notices-2'=>'The cache is updated automatically with each change via administrative area',
			

			'notice-alert-not-running'=>'The cache is not running, please update the plugin.',
			'notice-alert-new-update'=>'new update',
			'notice-alert-updateto'=>'Update Now to',

			'about'=>'About',
			'author'=>'Author',
			'site'=>'Site',

			'alert-cache-clean'=>'Cache clean',
			'alert-saved-success'=>'Saved with success',
			'alert-permission-directory'=>'Set permission 755 to directory',
			
			'include-imports-css'=>'Concatenate @import',
			'image-optimization-title'=>'Image optimization',
			'optimizate-image'=>'Optimize Now',
			'optimization-image-finish'=>'Image optimized with success',
			'optimization-restore-button'=>'Restore images not optimized',
			'optimization-restored'=>'Images restored',
			'image-optimization-alert'=>'does not work on localhost'
			
		);

		$sourcePT = array(
			'settings'=>'Configurações',
			'enable'=>'Habilitar',
			'theme'=>'Tema',
			'to-minify-css'=>'Minificar CSS',
			'to-minify-js'=>'Minificar JS',
			'to-minify-html'=>'Minificar HTML',
			'action-to-cache'=>'Ação para o cache',
			'size-current'=>'Tamanho em disco',
			'size-cache-limit'=>'Limitar tamanho do cache',
			'optimize-cache'=>'Otimização do cache',
			'optimize-cache-notice'=>'O htaccess será modificado (apache)',
			'cache-browser'=>'Cache do navegador',
			'compression-gzip'=>'Compressão Gzip',
			'clean-cache'=>'Limpar cache',
			'allow-user-agent'=>'Permitir User Agent',
			'block-user-agent'=>'Bloquear User Agent',
			'usage'=>'Utilização',
			'user-agent-current'=>'User Agent Atual',
			'server'=>'Servidor',
			'on-mobile'=>'No celular',
			'mobile-disable-cache'=>'Desabilitar cache',
			'mobile-no-load-external-css'=>'Não carregar CSS externo',
			'on-desktop'=>'No desktop',
			'desktop-disable-cache'=>'Desabilitar cache',
			'no-use-cache-on-pages-example'=>'separe com virgula, ex: home, product/itens, about',
			'visualization'=>'Visualização',
			'no-use-cache-on-pages'=>'Não utilizar cache nas páginas',
			'single-cache'=>'Cache individual',
			'single-cache-resume'=>'Grava cache por separação de usuários',
			'filters'=>'Filtros',
			'filters-enable'=>'Habilitar',
			'filters-resume'=>'Remove no PageSpeed',
			'save'=>'Salvar',
			'donation'=>'Doação',
			'donation-resume'=>'Ajude a manter o <strong>Cache SEO Speed</strong> ativo e gratuito, faça uma doação de qualquer valor clicando no botão abaixo.',
			'donation-pagseguro'=>'Doar com PagSeguro',
			'donation-paypal'=>'Doar com PayPal (Internacional)',
			'notices'=>'Avisos',
			'notices-1'=>'O cache é criado no primeiro acesso na página',
			'notices-2'=>'O cache é atualizado automáticamente a cada alteração via área administrativa',

			'notice-alert-not-running'=>'O cache não está rodando, por favor, atualize o plugin.',
			'notice-alert-new-update'=>'nova atualização',
			'notice-alert-updateto'=>'Atualize para a versão ',

			'about'=>'Sobre',
			'author'=>'Autor',
			'site'=>'Site',
	
			'alert-cache-clean'=>'Cache limpo',
			'alert-saved-success'=>'Salvo com sucesso',
			'alert-permission-directory'=>'Altere para 755 a permissão do diretório',

			'include-imports-css'=>'Concatenar @import',
			'image-optimization-title'=>'Otimização de imagens',
			'optimizate-image'=>'Otimizar agora',
			'optimization-image-finish'=>'Image optimized with success',
			'optimization-restore-button'=>'Restaurar imagens não otimizadas',
			'optimization-restored'=>'Imagens restauradas',
			'image-optimization-alert'=>'Não funciona no localhost'
		);

		if(self::$locale == 'pt') return $sourcePT[$text];
		
		if(self::$locale == 'en' || self::$locale !== 'pt'){
			return $sourceEN[$text];
		}

		return $text;
	}
}