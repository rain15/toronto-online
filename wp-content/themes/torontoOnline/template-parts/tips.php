<div class="blog-tips">
			<h2>Tips to travel to Toronto</h2>
			<?php $args = array(
				'cat' => 4,
				'posts_per_page' => 2,
				'order' => 'DESC',
				'orderby' => 'date'

			); ?>
			<?php $travelTips = new WP_Query($args); ?>
			<ul>
			<?php while($travelTips->have_posts()): $travelTips->the_post(); ?>
				<li>
					<a href="<?php the_permalink(); ?>"> 
						<?php the_post_thumbnail('medium-blog' ); ?>	
					</a>
					<h3><?php the_title(); ?></h3>
					<?php the_excerpt(); ?>
					<a href="<?php the_permalink(); ?>" class="read-more">continue reading</a>
				</li>	
			<?php endwhile; wp_reset_postdata(); ?>
			</ul>
		</div>
		