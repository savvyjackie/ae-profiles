<?php
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
remove_action('genesis_before_loop', 'genesis_do_breadcrumbs');
remove_action( 'genesis_loop', 'genesis_do_loop');
add_action( 'genesis_loop' , 'agent_directory_archive_loop' );
add_filter( 'body_class', 'add_body_class' );
function add_body_class( $classes ) {
   $classes[] = 'archive-aeprofiles';
   return $classes;
}

function agent_directory_archive_loop() {

	$class = '';

	if ( have_posts() ) : while ( have_posts() ) : the_post();

	// starting at 0
	$class = ( $class == 'even agent-wrap' ) ? 'odd agent-wrap' : 'even agent-wrap';

	$thumb_id = get_post_thumbnail_id();
	$thumb_url = wp_get_attachment_image_src($thumb_id, 'agent-profile-photo', true);

	?>

	<div <?php post_class($class); ?> itemscope itemtype="http://schema.org/Person">
		<?php
			$thumb_id = get_post_thumbnail_id();
			$thumb_url = wp_get_attachment_image_src($thumb_id, 'agent-profile-photo', true);
			echo '<a href="' . get_permalink() . '"><img src="' . $thumb_url[0] . '" alt="' . get_the_title() . ' photo" class="attachment-agent-profile-photo wp-post-image" itemprop="image" /></a>';
		?>
		<div class="agent-details vcard">
		<?php

		printf('<p><a class="fn" href="%s" itemprop="name">%s</a></p>', get_permalink(), get_the_title() );

		echo do_agent_details();
		
		echo do_agent_social();

		?>
		</div><!-- .agent-details -->
	</div> <!-- .agent-wrap -->

	<?php endwhile;
	
	genesis_posts_nav();

	else: ?>
	
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif;
	
}

genesis();