<div class="slider">
		<ul class="bxslider">
			<?php $args = array(
				'posts_per_page' => 4,
				'orderby' => 'date',
				'order' => 'DESC',
				'post_type' => 'post'
			); ?>
			<?php $slider = new WP_Query($args); ?>
			<?php while ($slider->have_posts()) : $slider->the_post(); ?>
				<li>
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail('featured'); ?>
					</a>
				</li>
			<?php endwhile; wp_reset_postdata(); ?>
		</ul>
	</div>