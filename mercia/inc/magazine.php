<?php
/**
 * Magazine Functions
 *
 * Custom Functions and Template tags used in the Magazine widgets and Magazine templates
 *
 * @package Mercia
 */


/**
* Displays Magazine widget area
*/
function mercia_magazine_widgets() {

	// Return early if Magazine widgets are deactivated on blog index.
	if ( is_home() && false === mercia_get_option( 'blog_magazine_widgets' ) ) {
		return;
	}

	// Only display on first page of blog.
	if ( is_home() && is_paged() ) {
		return;
	}

	// Check if there are widgets in Magazine sidebar.
	if ( is_active_sidebar( 'magazine-homepage' ) ) : ?>

		<div id="magazine-homepage-widgets" class="widget-area clearfix">

			<?php dynamic_sidebar( 'magazine-homepage' ); ?>

		</div><!-- #magazine-homepage-widgets -->

		<?php
	elseif ( is_page_template( 'template-magazine.php' ) && current_user_can( 'edit_theme_options' ) ) :

		echo '<p class="empty-widget-area">';
		esc_html_e( 'Please go to Customize &#8594; Widgets and add at least one widget to the Magazine Homepage widget area.', 'mercia' );
		echo '</p>';

	endif;
}


if ( ! function_exists( 'mercia_magazine_widget_title' ) ) :
	/**
	 * Displays the widget title with link to the category archive
	 *
	 * @param String $widget_title Widget Title.
	 * @param int    $category_id  Category ID.
	 * @return String Widget Title
	 */
	function mercia_magazine_widget_title( $widget_title, $category_id ) {

		// Check if widget shows a specific category.
		if ( $category_id > 0 ) {

			// Set URL and Title for Category.
			$category_title = sprintf( esc_html__( 'View all posts from category %s', 'mercia' ), get_cat_name( $category_id ) );
			$category_url = get_category_link( $category_id );

			// Set Widget Title with link to category archive.
			$widget_title = '<a class="category-archive-link" href="' . esc_url( $category_url ) . '" title="' . esc_attr( $category_title ) . '">' . $widget_title . '</a>';
		}

		return $widget_title;
	}
endif;


if ( ! function_exists( 'mercia_magazine_entry_meta' ) ) :
	/**
	 * Displays the date and author of magazine posts
	 */
	function mercia_magazine_entry_meta() {

		$postmeta = mercia_meta_date();
		$postmeta .= mercia_meta_author();

		echo '<div class="entry-meta">' . $postmeta . '</div>';
	}
endif;


if ( ! function_exists( 'mercia_magazine_entry_date' ) ) :
	/**
	 * Displays the date of magazine posts
	 */
	function mercia_magazine_entry_date() {
		echo '<div class="entry-meta">' . mercia_meta_date() . '</div>';
	}
endif;


/**
 * Function to change excerpt length for posts in category posts widgets
 *
 * @param int $length Length of excerpt in number of words.
 * @return int
 */
function mercia_magazine_posts_excerpt_length( $length ) {
	return 15;
}


/**
 * Get Magazine Post IDs
 *
 * @param String $cache_id        Magazine Widget Instance.
 * @param int    $category        Category ID.
 * @param int    $number_of_posts Number of posts.
 * @return array Post IDs
 */
function mercia_get_magazine_post_ids( $cache_id, $category, $number_of_posts ) {

	$cache_id = sanitize_key( $cache_id );
	$post_ids = get_transient( 'mercia_magazine_post_ids' );
	$post_ids = is_array( $post_ids ) ? $post_ids : array();

	if ( ! isset( $post_ids[ $cache_id ] ) || is_customize_preview() ) {

		// Get Posts from Database.
		$query_arguments = array(
			'fields'              => 'ids',
			'cat'                 => (int) $category,
			'posts_per_page'      => (int) $number_of_posts,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);
		$query = new WP_Query( $query_arguments );

		// Create an array of all post ids.
		$post_ids[ $cache_id ] = $query->posts;

		// Set Transient.
		set_transient( 'mercia_magazine_post_ids', $post_ids );
	}

	return apply_filters( 'mercia_magazine_post_ids', $post_ids[ $cache_id ], $cache_id );
}


/**
 * Delete Cached Post IDs
 *
 * @return void
 */
function mercia_flush_magazine_post_ids() {
	delete_transient( 'mercia_magazine_post_ids' );
}
add_action( 'save_post', 'mercia_flush_magazine_post_ids' );
add_action( 'deleted_post', 'mercia_flush_magazine_post_ids' );
add_action( 'customize_save_after', 'mercia_flush_magazine_post_ids' );
add_action( 'switch_theme', 'mercia_flush_magazine_post_ids' );
