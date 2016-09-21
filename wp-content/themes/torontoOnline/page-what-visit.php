<?php 
/* 
 * Template Name: What to Visit
 */
 ?>

<?php get_header(); ?>

<?php if ( has_post_thumbnail( )) { ?>
	<div class="featured">
		<?php the_post_thumbnail( 'featured' ); ?>
		<h2><?php the_title( ); ?></h2>

	</div>
<?php } else { ?>
	<h2 class="noimage"><?php the_title( ); ?></h2>
<?php } ?>

<div id="primary" class="primary no-sidebar post-<?php the_ID(); ?>"> 

	<?php 
		$args = array(
			'posts_per_page' => 5,
			'cat' => 6,
			'order' => 'DESC',
			'orderby' => 'date'
		);

		$visit = new WP_Query($args);
	?>
	<ul class="blog-visit">
		<?php  while ( $visit-> have_posts() ): $visit->the_post(); ?>
			<?php  //print_r($visit); ?>
		<li>
			<div class="featured blog-visit-featured">
				<a href="<?php the_permalink() ?>">
					<?php the_post_thumbnail( 'medium-blog'); ?>
				</a> 
				<div class="category blog-visit-category">
					<?php the_category(', '); ?>
				</div>
			</div>

			<div class="content">
				<h2><?php the_title(); ?></h2>
				<?php the_excerpt(); ?>
			</div>
			<div class="post-information">
				<div class="author">
					By: <span><?php the_author(); ?></span>
				</div>
				<div class="date">
					<?php the_time('F j, Y'); ?>
				</div>
			</div>
		</li>	

		<?php  endwhile; wp_reset_postdata();?>
	</ul>
	
		
		
	 

</div>


<?php get_footer(); ?>
