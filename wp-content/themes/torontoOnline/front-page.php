<?php get_header(); ?>

	<?php get_template_part('template-parts/slider' ); ?>

	<div class="what-to-visit">
		<?php dynamic_sidebar('front-page'); ?>
	</div>

	<div class="bottom-front-page">
		<?php get_template_part('template-parts/aboutus') ?>
		<?php get_template_part('template-parts/tips') ?>
	</div>

<?php get_footer(); ?>
