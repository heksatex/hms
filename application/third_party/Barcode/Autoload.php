<?php
/**
 * Users who do not have 'composer' to manage dependencies, include this
 * file to provide auto-loading of the classes in this library. 
 */
spl_autoload_register ( function ($class) {
	/*
	 * PSR-4 autoloader, based on PHP Framework Interop Group snippet (Under MIT License.)
	 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
	 */
	$prefix = "Code\\";
	$base_dir = __DIR__ . "/Code/";
	
	/* Only continue for classes in this namespace */
	$len = strlen ( $prefix );
	if (strncmp ( $prefix, $class, $len ) !== 0) {
		return;
	}
	
	/* Require the file if it exists */
	$relative_class = substr ( $class, $len );
	$file = $base_dir . str_replace ( '\\', '/', $relative_class ) . '.php';
	if (file_exists ( $file )) {
		require $file;
	}
} );
