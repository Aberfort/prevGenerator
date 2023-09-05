<?php

add_action( 'save_post', function ( $post_ID, $post ) {
	// Если это ревизия, то не отправляем картинку
	if (
		defined( 'STOP_ESSAY_PREV_GEN' )
		|| wp_is_post_revision( $post_ID )
		|| $post->post_status != 'publish'
		|| $post->post_type != 'post'
	) {
		return;
	}
	$string = $post->post_content;
	$string = strip_tags( $string );
	$string = substr( $string, 0, 3500 );
	$string = rtrim( $string, "!,.-" );
	$string = substr( $string, 0, strrpos( $string, ' ' ) );

	$title         = $post->post_title;
	$imageName     = $post->ID;
	$imageTitleUrl = $post->post_name;
	$imageTitleUrl = $imageTitleUrl . '-' . $imageName;
	$content       = $string;
	$link          = get_permalink( $post->ID );

	if(!class_exists('EssayPreviewGenerator')){
		require_once(__DIR__.'/class.EssayPreviewGenerator.php');
	}
	$generator = new EssayPreviewGenerator();
	$generator->createCustomImage( $title, $link, $content, $imageTitleUrl );

}, 10, 2 );
