<?php

/*
Plugin Name: Schema JSON-LD
Plugin URI:  https://www.joellisenby.com
Description: Add Schema.org JSON-LD to WordPress
Version:     1.0
Author:      Joel Lisenby
Author URI:  https://www.joellisenby.com
*/

namespace schema;

class Schema {
	
	public function __construct() {
		
		add_action ('wp_footer', array( $this, 'wp_footer' ) );
		
	}
	
	public function wp_footer() {

		if(is_single()) {	
			echo '<script type="application/ld+json">'. $this->get_schema_json_article() .'</script>';
		}

	}
	
	public function get_schema_json_article() {
		
		$organization = array (
			'@type' => 'Organization',
			'name'  => get_bloginfo(),
			'url' => get_site_url()
		);
		
		if( !empty( get_theme_mod( 'custom_logo' ) ) ) {
			$logo_image = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ) , 'full' );
			$organization['logo'] = array(
				'@context' => 'http://schema.org',
				'@type' => 'ImageObject',
				'url' => $logo_image[0]
			);
		}
		
		$schema = array(
			'@context' => 'http://schema.org',
			'@type' => 'Article',
			'headline' => get_the_title(),
			'author' => array(
				'@type' => 'person',
				'name' => get_the_author()
			),
			'datePublished' => get_the_date('c'),
			'dateModified' => get_the_modified_date('c'),
			'articleSection' => get_the_category()[0]->cat_name,
			'mainEntityOfPage' => array (
				'@type' => 'WebPage',
				'@id' => get_permalink()
			),
			'url' => get_permalink(),
			'publisher' => $organization
		);
		
		if( has_post_thumbnail() ) {
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
			$schema['image'] = array(
				'@context' => 'http://schema.org',
				'@type' => 'ImageObject',
				'url' => $featured_image[0],
				'width' => $featured_image[1],
				'height' => $featured_image[2]
			);
		}
 
		return json_encode( $schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT );
		
	}
	
}

new Schema();

?>