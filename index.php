<?php
/*
Plugin name: WP CropImage
Version: 0.0.1
Author: Andrew Omelchenko
Description: Simple plugin for preview and croping images before uploading.
*/
function bye1() {
	$mmm = 'Plugin activated';
	$fp = fopen("roooo-plug.txt", "w");
	fwrite($fp, $mmm);
	fclose($fp);
}
register_activation_hook(__FILE__, 'bye1');