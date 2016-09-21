<?php get_header(); ?>

<?php if ( has_post_thumbnail( )) { ?>
	<div class="featured">
		<?php the_post_thumbnail( 'featured' ); ?>	
		<h2><?php the_title( ); ?></h2>

	</div>
<?php } else { ?>
	<h2 class="noimage"><?php the_title( ); ?></h2>
<?php } ?>

<div id="primary" class="primary post-<?php the_ID(); ?>"> 

	<?php while( have_posts() ) : the_post();  ?>
		<p><?php the_content( ); ?></p>
	<?php endwhile; ?>
</div>

<?php get_sidebar( ); ?>

<?php get_footer(); ?>