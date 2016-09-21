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
		<article>
			<div class="written-info">
				<div class="column">
					<?php the_tags(__('Tags for this post: ', 'torontoOnline', ', ' , '<br/>') ); ?>
				</div>

				<div class="column">
					<?php _e('Category: ', 'torontoOnline') . the_category(', '); ?>
				</div>

				<div class="column">
					<?php _e('Written By: ', 'torontoOnline') . "<span>" . the_author() . "</span>" ; ?>
				</div>	
			</div>

			<?php the_content( ); ?>
			<?php comments_template( ); ?> <!-- add a comments section below the post -->
		</article>

		<?php edit_post_link( ); ?> <!-- for admin users, add "edit post link" -->

	<?php endwhile; ?>
</div>

<?php get_sidebar( ); ?>

<?php get_footer(); ?>