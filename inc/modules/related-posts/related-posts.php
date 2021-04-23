<?php
/**
 * Related Posts Functions
 *
 * @package Astra
 * @since x.x.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get related posts based on configurations.
 *
 * @param int $post_id Current Post ID.
 *
 * @since x.x.x
 *
 * @return WP_Query|bool
 */
function astra_get_related_posts_by_query( $post_id ) {
	$term_ids                  = array();
	$current_post_type         = get_post_type( $post_id );
	$related_posts_total_count = astra_get_option( 'related-posts-total-count', 2 );
	$related_posts_order_by    = astra_get_option( 'related-posts-order-by', 'date' );
	$related_posts_order       = astra_get_option( 'related-posts-order', 'desc' );
	$related_posts_based_on    = astra_get_option( 'related-posts-based-on', 'categories' );

	$query_args = array(
		'update_post_meta_cache' => false,
		'posts_per_page'         => $related_posts_total_count,
		'no_found_rows'          => true,
		'post_status'            => 'publish',
		'post__not_in'           => array( $post_id ),
		'post_type'              => $current_post_type,
		'orderby'                => $related_posts_order_by,
		'fields'                 => 'ids',
		'order'                  => $related_posts_order,
	);

	if ( 'tags' === $related_posts_based_on ) {
		$terms = get_the_tags( $post_id );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$term_ids = wp_list_pluck( $terms, 'term_id' );
		}

		$query_args['tag__in'] = $term_ids;

	} else {
		$terms = get_the_category( $post_id );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$term_ids = wp_list_pluck( $terms, 'term_id' );
		}

		$query_args['category__in'] = $term_ids;
	}

	$query_args = apply_filters( 'astra_related_posts_query_args', $query_args );

	return new WP_Query( $query_args );
}

/**
 * Render Featured Image HTML.
 *
 * @param int     $current_post_id current post ID.
 * @param string  $before Markup before thumbnail image.
 * @param string  $after  Markup after thumbnail image.
 * @param boolean $echo   Output print or return.
 * @return string|null
 *
 * @since x.x.x
 */
function astra_get_related_post_featured_image( $current_post_id, $before = '', $after = '', $echo = true ) {
	$related_post_structure = astra_get_option_meta( 'related-posts-structure' );

	if ( ! in_array( 'featured-image', $related_post_structure ) ) {
		return;
	}

	$post_thumb = apply_filters(
		'astra_related_post_featured_image_markup',
		get_the_post_thumbnail(
			$current_post_id,
			apply_filters( 'astra_related_posts_thumbnail_default_size', 'large' ),
			apply_filters( 'astra_related_posts_thumbnail_itemprop', '' )
		)
	);

	$appended_class = has_post_thumbnail( $current_post_id ) ? 'post-has-thumb' : 'ast-no-thumb';

	$featured_img_markup = '<div class="ast-related-post-featured-section ' . $appended_class . '">';

	if ( '' !== $post_thumb ) {
		$featured_img_markup .= '<div class="post-thumb-img-content post-thumb">';
		$featured_img_markup .= astra_markup_open(
			'ast-related-post-image',
			array(
				'open'  => '<a %s>',
				'echo'  => false,
				'attrs' => array(
					'class' => '',
					'href'  => esc_url( get_permalink() ),
				),
			)
		);
		$featured_img_markup .= $post_thumb;
		$featured_img_markup .= '</a> </div>';
	}

	$featured_img_markup  = apply_filters( 'astra_related_post_featured_image_after', $featured_img_markup );
	$featured_img_markup .= '</div>';

	$featured_img_markup = apply_filters( 'astra_related_post_thumbnail', $featured_img_markup, $before, $after );

	if ( false === $echo ) {
		return $before . $featured_img_markup . $after;
	}

	echo $before . $featured_img_markup . $after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Render Post Title HTML.
 *
 * @param int $current_post_id current post ID.
 *
 * @since x.x.x
 */
function astra_get_related_post_title( $current_post_id ) {
	$related_post_structure = astra_get_option_meta( 'related-posts-structure' );

	if ( ! in_array( 'title-meta', $related_post_structure ) ) {
		return;
	}

	$target    = apply_filters( 'astra_related_post_title_opening_target', '_self' );
	$title_tag = apply_filters( 'astra_related_post_title_tag', 'h3' );

	do_action( 'astra_related_post_before_title', $current_post_id );
	?>
		<<?php echo esc_html( $title_tag ); ?> class="ast-related-post-title entry-title">
			<a href="<?php echo esc_url( apply_filters( 'astra_related_post_link', get_the_permalink(), $current_post_id ) ); ?>" target="<?php echo esc_html( $target ); ?>" rel="bookmark noopener noreferrer"><?php the_title(); ?></a>
		</<?php echo esc_html( $title_tag ); ?>>
	<?php
	do_action( 'astra_related_post_after_title', $current_post_id );
}

/**
 * Related Posts Excerpt markup.
 *
 * @param int $current_post_id current post ID.
 *
 * @since x.x.x
 */
function astra_get_related_post_excerpt( $current_post_id ) {
	if ( ! astra_get_option( 'enable-related-posts-excerpt' ) ) {
		return;
	}

	$related_posts_content_type = apply_filters( 'astra_related_posts_content_type', 'excerpt' );

	if ( 'full-content' === $related_posts_content_type ) {
		return the_content();
	}

	$excerpt_length = (int) astra_get_option( 'related-posts-excerpt-count' );

	$excerpt = wp_trim_words( get_the_excerpt(), $excerpt_length );

	if ( ! $excerpt ) {
		$excerpt = null;
	}

	$excerpt = apply_filters( 'astra_related_post_excerpt', $excerpt, $current_post_id );

	do_action( 'astra_related_post_before_excerpt', $current_post_id );

	?>
		<p class="ast-related-post-excerpt entry-content clear">
			<?php echo wp_kses_post( $excerpt ); ?>
		</p>
	<?php

	do_action( 'astra_related_post_after_excerpt', $current_post_id );
}

/**
 * Render Post CTA button HTML marup.
 *
 * @param int $current_post_id current post ID.
 *
 * @since x.x.x
 */
function astra_get_related_post_read_more( $current_post_id ) {
	if ( ! astra_get_option( 'enable-related-posts-excerpt' ) ) {
		return;
	}

	$related_posts_content_type = apply_filters( 'astra_related_posts_content_type', 'excerpt' );

	if ( 'full-content' === $related_posts_content_type ) {
		return;
	}

	$target = apply_filters( 'astra_related_post_cta_target', '_self' );

	$cta_text = apply_filters( 'astra_related_post_read_more_text', astra_get_option( 'blog-read-more-text' ) );

	$show_read_more_as_button = apply_filters( 'astra_related_post_read_more_as_button', astra_get_option( 'blog-read-more-as-button' ) );

	$class = '';

	if ( $show_read_more_as_button ) {
		$class = 'ast-button';
	}

	$custom_class = apply_filters( 'astra_related_post_cta_custom_classes', $class );

	do_action( 'astra_related_post_before_cta', $current_post_id );

	?>
		<p class="ast-related-post-cta read-more">
			<a class="ast-related-post-link <?php echo esc_html( $custom_class ); ?>" href="<?php echo esc_url( apply_filters( 'astra_related_post_link', get_the_permalink(), $current_post_id ) ); ?>" target="<?php echo esc_html( $target ); ?>" rel="bookmark noopener noreferrer"><?php echo esc_html( $cta_text ); ?></a>
		</p>
	<?php

	do_action( 'astra_related_post_after_cta', $current_post_id );
}

/**
 * Related Posts markup.
 *
 * @since x.x.x
 * @return bool
 */
function astra_get_related_posts() {
	global $post;
	$post_id                = $post->ID;
	$related_posts_grid     = astra_get_option( 'related-posts-grid', 2 );
	$related_post_meta      = astra_get_option( 'related-posts-meta-structure' );
	$related_post_structure = astra_get_option_meta( 'related-posts-structure' );
	$output_str             = astra_get_post_meta( $related_post_meta );

	// Get related posts by WP_Query.
	$query_posts = astra_get_related_posts_by_query( $post_id );

	if ( $query_posts ) {

		if ( ! $query_posts->have_posts() ) {
			return apply_filters( 'astra_related_posts_no_posts_avilable_message', '', $post_id );
		}

		$grid_class = ( $related_posts_grid ) ? 'ast-grid-' . $related_posts_grid : 'ast-grid-2';

		echo '<div class="ast-single-related-posts-container">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'astra_related_posts_title_before' );

		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'astra_related_posts_title',
			sprintf(
				'<div class="ast-related-posts-title-section"> <%1$s class="ast-related-posts-title"> %2$s </%1$s> </div>',
				'h2',
				esc_html__( 'Related Posts', 'astra' )
			)
		);

		do_action( 'astra_related_posts_title_after' );

		echo '<div class="ast-related-posts-wrapper ' . $grid_class . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'astra_related_posts_loop_before' );

		while ( $query_posts->have_posts() ) {

			$query_posts->the_post();
			$post_id = get_the_ID();

			?>
				<article <?php post_class( 'ast-related-post' ); ?>>
					<div class="ast-related-posts-inner-section">
						<div class="ast-related-post-content">
							<?php
								// Render post based on order of Featured Image & Title-Meta.
							if ( is_array( $related_post_structure ) ) {
								foreach ( $related_post_structure as $post_thumb_title_order ) {
									if ( 'featured-image' === $post_thumb_title_order ) {
										do_action( 'astra_related_post_before_featured_image', $post_id );
										astra_get_related_post_featured_image( $post_id );
										do_action( 'astra_related_post_after_featured_image', $post_id );
									} else {
										?>
												<header class="entry-header">
												<?php
													astra_get_related_post_title( $post_id );
													echo apply_filters( 'astra_related_posts_meta_html', '<div class="entry-meta">' . $output_str . '</div>', $output_str ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												?>
												</header>
											<?php
									}
								}
							}
							?>
							<div class="entry-content clear">
								<?php
									astra_get_related_post_excerpt( $post_id );
									astra_get_related_post_read_more( $post_id );
								?>
							</div>
						</div>
					</div>
				</article>
			<?php

			wp_reset_postdata();
		}

		do_action( 'astra_related_posts_loop_after' );

		echo '</div> </div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Enable/Disable Single Post -> Related Posts section.
 *
 * @since x.x.x
 * @return void
 */
function astra_related_posts_markup() {
	if ( astra_target_rules_for_related_posts() ) {
		astra_get_related_posts();
	}
}

add_action( 'astra_entry_after', 'astra_related_posts_markup', 10 );

/**
 * Adds custom classes to the array of body classes.
 *
 * @since x.x.x
 * @param array $classes Classes for the body element.
 * @return array
 */
function astra_related_posts_body_class( $classes ) {
	if ( astra_target_rules_for_related_posts() ) {
		$related_posts_grid = astra_get_option( 'related-posts-grid', 2 );

		$classes[] = 'ast-related-posts-grid-' . esc_attr( $related_posts_grid );
	}

	return $classes;
}

add_filter( 'body_class', 'astra_related_posts_body_class' );

