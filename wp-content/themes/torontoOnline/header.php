<!DOCTYPE html>
<html <?php language_attributes( ); ?>>
<head>
	<meta charset="UTF-8">
	<meta name=viewport content="width=device-width, initial-scale=1">
	<title><?php wp_title( '' ); ?><?php if(wp_title('', false)) { echo ':'; } ?><?php bloginfo('name'); ?></title>
	<?php wp_head(); ?>

</head>
<body <?php body_class( ); ?>>
	<div id="page">
		<header id="master" class="site-header" role="banner">
			<div class="container">
				<div class="logo">
					<a href="<?php echo esc_url(home_url('/')); ?>"> 
						<img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="Logo"> 
					</a>
				</div> <!-- .logo -->
				<nav id="site-navigation" class="main-navigation" role="navigation">
					<?php wp_nav_menu(array('theme_location' => 'main_menu')); ?>
				</nav>
			</div> <!-- .container -->
		</header>
		<div class="container content">
			
	
