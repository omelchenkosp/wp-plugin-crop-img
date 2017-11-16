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
wp_deregister_script('jquery');
wp_enqueue_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js', array(), true );

wp_enqueue_script( 'cropit', plugins_url() . '/wp-plugin-crop-img/jquery.cropit.js', array(), true );


add_action( 'wp_ajax_nopriv_update_business_gallery',  'update_business_gallery' );
add_action( 'wp_ajax_update_business_gallery', 'update_business_gallery' );
function update_business_gallery() {
    //header("Content-type:application/json"); 
    // $photo_id = $_POST["photo_id"];
    $photo_id = 2;
    // $business_id = $_POST["business_id"];
    $business_id = 4;
    
    //Upload la photo dans le dossier
    require_once(ABSPATH.'wp-admin/includes/file.php');
    $uploadedfile = $_FILES["file"];
    $movefile = wp_handle_upload($uploadedfile, array('test_form' => false));

    //On sauvegarde la photo dans le média library
    if ($movefile) {
        $wp_upload_dir = wp_upload_dir();
        $guid = $wp_upload_dir['url'].'/'.basename($movefile['file']);
        $attachment = array(
	        'guid' => $guid,
	        'post_mime_type' => $movefile['type'],
	        'post_title' => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
	        'post_content' => '',
	        'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $movefile['file']);
        // generate the attachment metadata
        $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
        // update the attachment metadata
        wp_update_attachment_metadata( $attach_id,  $attach_data );
        if (update_field($photo_id, $attach_id, $business_id)) {
            echo $guid;
            die();
        } else {
            echo 'error';
            die();
        }
    } else {
        echo 'error';
        die();
    }
}