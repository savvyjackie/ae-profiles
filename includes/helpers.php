<?php

/**
 * Lists all the terms of a given taxonomy
 *
 * Adds the taxonomy title and a list of the terms associated with that taxonomy
 * used in custom post type templates.
 */
function agentevo_list_terms($taxonomy) {
	$the_tax_object = get_taxonomy($taxonomy);
	$terms = get_terms($taxonomy);
	$term_list = '';

	$count = count($terms); $i=0;
	if ($count > 0) {
	    foreach ($terms as $term) {
	        $i++;
	    	$term_list .= '<li><a href="' . site_url($taxonomy . '/' . $term->slug) . '" title="' . sprintf(__('View all post filed under %s', 'gbd'), $term->name) . '">' . $term->name . ' (' . $term->count . ')</a></li>';
	    }
		echo '<div class="' . $taxonomy . ' term-list-container">';
		echo '<h3 class="taxonomy-name">' . $the_tax_object->label . '</h3>';
		echo "<ul class=\"term-list\">{$term_list}</ul>";
		echo '</div> <!-- .' . $taxonomy . ' .term-list-container -->';
	}
}

/**
 * Shows default image if genesis_image() returns false
 */
function agentevo_image($size="thumbnail") {

	if ($size == "medium") {
		$dimensions = "-300x300";
	} elseif ($size == "large") {
		$dimensions = "-640x640";
	} else {
		$dimensions = "-150x150";
	}

	if ( genesis_get_image( array( 'size' => $size ) ) ) {
		return genesis_get_image( array( 'size' => $size, 'attr' => array('class' => 'size-' . $size) ) );
	} else {
		return '<img class="thumbnail" src="http://agentevolution.com/ae-framework-images/default-thumb' . $dimensions  . '.png" alt="no preview available" />';
	}
}


/**
 * Shows default image if get_the_post_thumnail() returns false
 *
 * If no post thumbnail is found the default thumbnail will be
 * resized to match the dimensions of the $size parameter
 * using inline CSS.
 */
function agentevo_get_image($pid, $size="thumbnail") {

	if ($size == "medium") {
		$dimensions = "-300x300";
	} elseif ($size == "large") {
		$dimensions = "-640x640";
	} else {
		$dimensions = "-150x150";
	}

	$sizes = genesis_get_additional_image_sizes();

	foreach( $sizes as $name => $dims ) {
		if ( $size == $name ) {
			$dimensions = '-' . $dims["width"] . 'x' . $dims["height"];
			$custom_size_width = $dims["width"];
			$custom_size_height = $dims["height"];
		}
	}

	if ( false != get_the_post_thumbnail($pid, $size) ) {
		return get_the_post_thumbnail($pid, $size);
	}

	if ( file_exists( get_theme_root() . '/agentevo/images/default-thumb' . $dimensions . '.png' ) ) {
		return '<img class="thumbnail" src="http://agentevolution.com/ae-framework-images/default-thumb' . $dimensions  . '.png" alt="no preview available" />';
	}

	return '<img class="thumbnail" src="http://agentevolution.com/ae-framework-images/default-thumb-150x150.png" alt="no preview available" style="width:' . $custom_size_width . 'px; height:' . $custom_size_height . 'px;"/>';
}

/**
 * Returns true if the queried taxonomy is a taxonomy of the given post type
 */
function ae_is_taxonomy_of($post_type) {
	$taxonomies = get_object_taxonomies($post_type);
	$queried_tax = get_query_var('taxonomy');

	if ( in_array($queried_tax, $taxonomies) ) {
		return true;
	}

	return false;
}

function agentevo_bootstrap_carousel($category='Slider', $post_count=3, $wrap_id='tbsCarousel', $nav = array('next' => '&rsaquo;', 'prev' => '&lsaquo;') ) {

	global $post;

	$id = get_cat_ID($category);

	$args = array(
		'numberposts'   => $post_count,
		'category'      => $id
	);

	$slider_posts = get_posts($args);

	$items = '';
	$count = 0;

	foreach( $slider_posts as $post ) {

		$count++;

		$class = ( $count == 1 ) ? 'item active' : 'item';

		$items .= sprintf(
			'<div class="%s">%s<div class="carousel-caption"><h4>%s</h4><p>%s</p></div><!-- .carousel-caption --></div><!-- .item -->',
			$class, get_the_post_thumbnail($post->ID,"slider"), get_the_title($post->ID), $post->post_excerpt
		);
	}

	$toggle = sprintf(
		'<a class="carousel-control left" href="#%1$s" data-slide="prev">%2$s</a><a class="carousel-control right" href="#%1$s" data-slide="next">%3$s</a>',
		$wrap_id, $nav['prev'], $nav['next']
	);

	$carousel = sprintf(
		'<div id="%1$s" class="carousel slide"><div class="carousel-inner">%2$s</div><!-- .carousel-inner -->%3$s</div><!-- #%1$s -->',
		$wrap_id, $items, $toggle
	);

	return $carousel;
}

function agentevo_linked_title() {
	return sprintf('<a href="%s">%s</a>', get_permalink(), get_the_title());
}

class Agentevo_Description_Walker extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before . '<div class="circle-border"><div class="circle"><div class="spr-icon"></div></div></div>';
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

		if ( $item->description ) {
			$item_output .= '<span>' . esc_attr( $item->description ) . '</span>';
		}

		$item_output .= '</a>';

		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}