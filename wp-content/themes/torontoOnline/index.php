<?php get_header(); ?>

<div id="primary" class="primary post-<?php the_ID(); ?>"> 

	<?php while( have_posts() ) : the_post();  ?>
		<article>
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'medium-blog'); ?>
			</a> 
			<div class="content-post">
				<h2><?php the_title(); ?></h2>
				<div class="category-wrapper">
					<?php the_category(); ?>	

				</div>
				
				<div class="post-info-wrapper">
					<div class="post-information">
						
						<div class="date">
							<strong>Published: </strong><?php the_time('F j, Y'); ?>
						</div>
					</div>
				</div>
				<div class="clear"></div>
				<?php the_excerpt( ); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php get_sidebar( ); ?>

<?php get_footer(); ?>