<?php
/**
 *  Autoload MBP classes
 *  
 */
if(!class_exists('MBP_Autoloader')){
	class MBP_Autoloader{
		/**
		 *  Registers the autoloader with php
		 *  
		 *  @param boolean [in] $prepend Will prepend the autoloader on the autoload queue instead of appending it. 
		 */
		public static function register($prepend = false){
			if(version_compare(phpversion(), '5.3.0', '>=')){
				spl_autoload_register(array(new self, 'autoload'), true, $prepend);
			}else{
				spl_autoload_register(array(new self, 'autoload'));
			}
		}
		/**
		 *  Autoload a class
		 *  
		 *  @param string [in] $class The class to be autoloaded
		 */
		public static function autoload($class){
			if(strpos($class, 'MBP') !== 0){
				return;
			}
			
			if(is_file($file = dirname(__FILE__).'/class-'.strtolower(str_replace(array('_', "\0"), array('-', ''), $class).'.php'))){
				require_once($file);
			}
		}
	}
}