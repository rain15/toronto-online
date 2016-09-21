<?php
function torontoOnline_scripts() {
	wp_enqueue_style('style', get_stylesheet_uri() );
	wp_enqueue_style('bxslidercss', get_stylesheet_directory_uri() . '/css/jquery.bxslider.css');

	//wp_enqueue_script('jquery');
	wp_enqueue_script('bxsliderjs', get_stylesheet_directory_uri() . '/js/jquery.bxslider.min.js', array('jquery'), '1.0', true );
	wp_enqueue_script('custom_script', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery'), '4.1.2', true );
 	

}
add_action('wp_enqueue_scripts', 'torontoOnline_scripts');

/** NAVIGATION **/
//Create menu in Admin Dashboard/Appearance section 
register_nav_menus(array(
	'main_menu' => __('Main Menu', 'torontoOnline')

) );

/** Widget Zone **/

function theme_widgets() {
        register_sidebar( array(
                'name'           => __('Sidebar Testimonials'),
                'id'             => 'testimonials',
                'description'    => 'Testimonial Widgets',
                'before_widget'  => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'   => '</aside>',
                'before_title'   => '<h3 class="widget-title"  >',
                'after_title'    => '</h3>',
        ) );
		register_sidebar( array(
                'name'           => __('Image for the Front Page'),
                'id'             => 'front-page',
                'description'    => 'Widget for the front-page',
                'before_widget'  => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'   => '</aside>',
                'before_title'   => '<h3 class="widget-title"  >',
                'after_title'    => '</h3>',
        ) );        
}
add_action('widgets_init', 'theme_widgets');


/**  Add Featured Images **/
add_theme_support('post-thumbnails' );

add_image_size('featured', 1100, 418, true );
add_image_size('medium-blog', 358, 208, true);


// Remove WordPress Admin bar on top of page
add_filter('show_admin_bar', '__return_false');

	
