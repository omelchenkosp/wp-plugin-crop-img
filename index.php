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
    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST["file"]));
    $upload_dir = wp_upload_dir();

    // @new
    $upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

    $decoded = $image;
    $filename = 'my-base64-image.png';

    $hashed_filename = md5( $filename . microtime() ) . '_' . $filename;

    // @new
    $image_upload = file_put_contents( $upload_path . $hashed_filename, $decoded );

    //HANDLE UPLOADED FILE
    if( !function_exists( 'wp_handle_sideload' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    // Without that I'm getting a debug error!?
    if( !function_exists( 'wp_get_current_user' ) ) {
      require_once( ABSPATH . 'wp-includes/pluggable.php' );
    }
    
    // require_once ABSPATH . 'wp-admin/includes/image.php';
    // require_once ABSPATH . 'wp-admin/includes/file.php';
    // require_once ABSPATH . 'wp-admin/includes/media.php';

    // @new
    $file             = array();
    $file['error']    = '';
    $file['tmp_name'] = $upload_path . $hashed_filename;
    $file['name']     = $hashed_filename;
    $file['type']     = 'image/png';
    $file['size']     = filesize( $upload_path . $hashed_filename );

    // upload file to server
    // @new use $file instead of $image_upload
    $file_return = wp_handle_sideload( $file, array( 'test_form' => false ) );

    $filename = $file_return['file'];
    $attachment = array(
        'post_mime_type' => $file_return['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
        'post_content' => '',
        'post_status' => 'inherit',
        'guid' => wp_upload_dir()['url'] . basename($filename)
    );
    $attach_id = wp_insert_attachment( $attachment, $filename );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
    wp_update_attachment_metadata( $attach_id, $attach_data );
}

function update_business_gallery3() {
    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST["file"]));
    require_once(ABSPATH.'wp-admin/includes/file.php');
    $upload_dir = wp_upload_dir();
    $uploadedfile = file_put_contents(str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR.'/image.png', $image);
    $movefile = wp_handle_upload($uploadedfile, array('test_form' => false));
    //On sauvegarde la photo dans le média library
    if ($movefile) {
        $wp_upload_dir = wp_upload_dir();
        $guid = $wp_upload_dir['url'].'/'.basename($movefile['file']);
        $attachment = array(
            'guid'              => $guid,
            'post_mime_type'    => $movefile['type'],
            'post_title'        => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
            'post_content'      => '',
            'post_status'       => 'inherit'
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