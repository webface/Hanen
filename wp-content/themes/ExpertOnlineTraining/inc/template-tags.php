<?php
/**
 * Custom template tags for Twenty Fourteen
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

if ( ! function_exists( 'twentyfourteen_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => __( '&larr; Previous', 'twentyfourteen' ),
		'next_text' => __( 'Next &rarr;', 'twentyfourteen' ),
	) );

	if ( $links ) :

	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'twentyfourteen' ); ?></h1>
		<div class="pagination loop-pagination">
			<?php echo $links; ?>
		</div><!-- .pagination -->
	</nav><!-- .navigation -->
	<?php
	endif;
}
endif;

if ( ! function_exists( 'twentyfourteen_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'twentyfourteen' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '%link', __( '<span class="meta-nav">Published In</span>%title', 'twentyfourteen' ) );
			else :
				previous_post_link( '%link', __( '<span class="meta-nav">Previous Post</span>%title', 'twentyfourteen' ) );
				next_post_link( '%link', __( '<span class="meta-nav">Next Post</span>%title', 'twentyfourteen' ) );
			endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'twentyfourteen_posted_on' ) ) :
/**
 * Print HTML with meta information for the current post-date/time and author.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_posted_on() {
	if ( is_sticky() && is_home() && ! is_paged() ) {
		echo '<span class="featured-post">' . __( 'Sticky', 'twentyfourteen' ) . '</span>';
	}

	// Set up and print post meta information.
	printf( '<span class="entry-date"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span> <span class="byline"><span class="author vcard"><a class="url fn n" href="%4$s" rel="author">%5$s</a></span></span>',
		esc_url( get_permalink() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		get_the_author()
	);
}
endif;

/**
 * Find out if blog has more than one category.
 *
 * @since Twenty Fourteen 1.0
 *
 * @return boolean true if blog has more than 1 category
 */
function twentyfourteen_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'twentyfourteen_category_count' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'twentyfourteen_category_count', $all_the_cool_cats );
	}

	if ( 1 !== (int) $all_the_cool_cats ) {
		// This blog has more than 1 category so twentyfourteen_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so twentyfourteen_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in twentyfourteen_categorized_blog.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'twentyfourteen_category_count' );
}
add_action( 'edit_category', 'twentyfourteen_category_transient_flusher' );
add_action( 'save_post',     'twentyfourteen_category_transient_flusher' );

/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index
 * views, or a div element when on single views.
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_post_thumbnail() {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
	?>

	<div class="post-thumbnail">
	<?php
		if ( ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'page-templates/full-width.php' ) ) ) {
			the_post_thumbnail( 'twentyfourteen-full-width' );
		} else {
			the_post_thumbnail();
		}
	?>
	</div>

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>">
	<?php
		if ( ( ! is_active_sidebar( 'sidebar-2' ) || is_page_template( 'page-templates/full-width.php' ) ) ) {
			the_post_thumbnail( 'twentyfourteen-full-width' );
		} else {
			the_post_thumbnail();
		}
	?>
	</a>

	<?php endif; // End is_singular()
}

/**
 * Custom template tags for this theme
 * 
 * @package bootstrap-basic
 */


if (!function_exists('bootstrapBasicCategoriesList')) {
	/**
	 * Display categories list with bootstrap icon
	 * 
	 * @param string $categories_list list of categories.
	 * @return string
	 */
	function bootstrapBasicCategoriesList($categories_list = '') 
	{
		return sprintf('<span class="categories-icon glyphicon glyphicon-th-list" title="' . __('Posted in', 'bootstrap-basic') . '"></span> %1$s', $categories_list);
	}// bootstrapBasicCategoriesList
}


if (!function_exists('bootstrapBasicComment')) {
	/**
	 * Displaying a comment
	 * 
	 * @param object $comment
	 * @param array $args
	 * @param integer $depth
	 * @return string the content already echo.
	 */
	function bootstrapBasicComment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;

		if ('pingback' == $comment->comment_type || 'trackback' == $comment->comment_type) { 
			echo '<li id="comment-';
				comment_ID();
				echo '" ';
				comment_class('comment-type-pt');
			echo '>';
			echo '<div class="comment-body media">';
				echo '<div class="media-body">';
					_e('Pingback:', 'bootstrap-basic');
					comment_author_link(); 
					edit_comment_link(__('Edit', 'bootstrap-basic'), '<span class="edit-link">', '</span>');
				echo '</div>';
			echo '</div>';
		} else {
			echo '<li id="comment-';
				comment_ID();
				echo '" ';
				comment_class(empty($args['has_children']) ? '' : 'parent media');
			echo '>';

			echo '<article id="div-comment-';
				comment_ID();
			echo '" class="comment-body media">';

				// footer
				echo '<footer class="comment-meta pull-left">';
					if (0 != $args['avatar_size']) {
						echo get_avatar($comment, $args['avatar_size']);
					}
				echo '</footer><!-- .comment-meta -->';
				// end footer

				// comment content
				echo '<div class="comment-content media-body">';
					echo '<div class="comment-author vcard">';
						echo '<div class="comment-metadata">';

						// date-time
						echo '<a href="';
							echo esc_url(get_comment_link($comment->comment_ID));
						echo '">';
						echo '<time datetime="';
							comment_time('c');
						echo '">';
						printf(_x('%1$s at %2$s', '1: date, 2: time', 'bootstrap-basic'), get_comment_date(), get_comment_time());
						echo '</time>';
						echo '</a>';
						// end date-time

						echo ' ';

						edit_comment_link('<span class="fa fa-pencil-square-o "></span>' . __('Edit', 'bootstrap-basic'), '<span class="edit-link">', '</span>');

						echo '</div><!-- .comment-metadata -->';

						// if comment was not approved
						if ('0' == $comment->comment_approved) {
							echo '<div class="comment-awaiting-moderation text-warning"> <span class="glyphicon glyphicon-info-sign"></span> ';
								_e('Your comment is awaiting moderation.', 'bootstrap-basic');
							echo '</div>';
						} //endif;

						// comment author says
						printf(__('%s <span class="says">says:</span>', 'bootstrap-basic'), sprintf('<cite class="fn">%s</cite>', get_comment_author_link()));
					echo '</div><!-- .comment-author -->';

					// comment content body
					comment_text();
					// end comment content body

					// reply link
					comment_reply_link(array_merge($args, array(
						'add_below' => 'div-comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'reply_text' => '<span class="fa fa-reply"></span> ' . __('Reply', 'bootstrap-basic'),
						'login_text' => '<span class="fa fa-reply"></span> ' . __('Log in to Reply', 'bootstrap-basic')
					)));
					// end reply link
				echo '</div><!-- .comment-content -->';
				// end comment content



			echo '</article><!-- .comment-body -->';
		} //endif;
	}// bootstrapBasicComment
}


if (!function_exists('bootstrapBasicCommentsPopupLink')) {
	/**
	 * Custom comment popup link
	 * 
	 * @return string
	 */
	function bootstrapBasicCommentsPopupLink() 
	{
		$comment_icon = '<span class="comment-icon glyphicon glyphicon-comment"><small class="comment-total">%d</small></span>';
		$comments_icon = '<span class="comment-icon glyphicon glyphicon-comment"><small class="comment-total">%s</small></span>';
		return comments_popup_link(sprintf($comment_icon, ''), sprintf($comment_icon, '1'), sprintf($comments_icon, '%'), 'btn btn-default btn-xs');
	}// bootstrapBasicCommentsPopupLink
}


if (!function_exists('bootstrapBasicEditPostLink')) {
	/**
	 * Display edit post link
	 */
	function bootstrapBasicEditPostLink() 
	{
		$edit_post_link = get_edit_post_link();
		if ($edit_post_link != null) {
		    $edit_btn = '<a class="post-edit-link btn btn-default btn-xs" href="'.$edit_post_link.'" title="' . __('Edit', 'bootstrap-basic') . '"><i class="edit-post-icon glyphicon glyphicon-pencil" title="' . __('Edit', 'bootstrap-basic') . '"></i></a>';
		    unset($edit_post_link);
		    echo $edit_btn;
		}
	}// bootstrapBasicEditPostLink
}


if (!function_exists('bootstrapBasicFullPageSearchForm')) {
	/**
	 * Display full page search form
	 * 
	 * @return string the search form element
	 */
	function bootstrapBasicFullPageSearchForm() 
	{
		$output = '<form class="form-horizontal" method="get" action="' . esc_url(home_url('/')) . '" role="form">';
		$output .= '<div class="form-group">';
		$output .= '<div class="col-xs-10">';
		$output .= '<input type="text" name="s" value="' . esc_attr(get_search_query()) . '" placeholder="' . esc_attr_x('Search &hellip;', 'placeholder', 'bootstrap-basic') . '" title="' . esc_attr_x('Search &hellip;', 'label', 'bootstrap-basic') . '" class="form-control" />';
		$output .= '</div>';
		$output .= '<div class="col-xs-2">';
		$output .= '<button type="submit" class="btn btn-default">' . __('Search', 'bootstrap-basic') . '</button>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</form>';

		return $output;
	}// bootstrapBasicFullPageSearchForm
}


if (!function_exists('bootstrapBasicGetLinkInContent')) {
	/**
	 * get the link in content
	 * 
	 * @return string
	 */
	function bootstrapBasicGetLinkInContent() 
	{
		$content = get_the_content();
		$has_url = get_url_in_content($content);

		if ($has_url) {
			return $has_url;
		} else {
			return apply_filters('the_permalink', get_permalink());
		}
	}// bootstrapBasicGetLinkInContent
}


if (!function_exists('bootstrapBasicMoreLinkText')) {
	/**
	 * Custom more link (continue reading) text
	 * @return string
	 */
	function bootstrapBasicMoreLinkText() 
	{
		return __('Continue reading <span class="meta-nav">&rarr;</span>', 'bootstrap-basic');
	}// bootstrapBasicMoreLinkText
}


if (!function_exists('bootstrapBasicPagination')) {
	/**
	 * display pagination (1 2 3 ...) instead of previous, next of wordpress style.
	 * 
	 * @param string $pagination_align_class
	 * @return string the content already echo
	 */
	function bootstrapBasicPagination($pagination_align_class = 'pagination-center pagination-row') 
	{
		global $wp_query;
			$big = 999999999;
			$pagination_array = paginate_links(array(
				'base' => str_replace($big, '%#%', get_pagenum_link($big)),
				'format' => '/page/%#%',
				'current' => max(1, get_query_var('paged')),
				'total' => $wp_query->max_num_pages,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
				'type' => 'array'
			));

			unset($big);

			if (is_array($pagination_array) && !empty($pagination_array)) {
				echo '<nav class="' . $pagination_align_class . '">';
				echo '<ul class="pagination">';
				foreach ($pagination_array as $page) {
					echo '<li';
					if (strpos($page, '<a') === false && strpos($page, '&hellip;') === false) {
						echo ' class="active"';
					}
					echo '>';
					if (strpos($page, '<a') === false && strpos($page, '&hellip;') === false) {
						echo '<span>' . $page . '</span>';
					} else {
						echo $page;
					}
					echo '</li>';
				}
				echo '</ul>';
				echo '</nav>';
			}

			unset($page, $pagination_array);
	}// bootstrapBasicPagination
}


if (!function_exists('bootstrapBasicPostOn')) {
	/**
	 * display post date/time and author
	 * 
	 * @return string
	 */
	function bootstrapBasicPostOn() 
	{
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		if (get_the_time('U') !== get_the_modified_time('U')) {
			$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf($time_string,
			esc_attr(get_the_date('c')),
			esc_html(get_the_date()),
			esc_attr(get_the_modified_date('c')),
			esc_html(get_the_modified_date())
		);

		printf(__('<span class="posted-on">Posted on %1$s</span><span class="byline"> by %2$s</span>', 'bootstrap-basic'),
			sprintf('<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
				esc_url(get_permalink()),
				esc_attr(get_the_time()),
				$time_string
			),
			sprintf('<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
				esc_url(get_author_posts_url(get_the_author_meta('ID'))),
				esc_attr(sprintf(__('View all posts by %s', 'bootstrap-basic'), get_the_author())),
				esc_html(get_the_author())
			)
		);
	}// bootstrapBasicPostOn
}


if (!function_exists('bootstrapBasicTagsList')) {
	/**
	 * display tags list
	 * 
	 * @param string $tags_list
	 * @return string
	 */
	function bootstrapBasicTagsList($tags_list = '') 
	{
		return sprintf('<span class="tags-icon glyphicon glyphicon-tags" title="' . __('Tagged', 'bootstrap-basic') . '"></span>&nbsp; %1$s', $tags_list);
	}// bootstrapBasicTagsList
}


if (!function_exists('bootstrapBasicTheAttachedImage')) {
	/**
	 * Display attach image with link.
	 * 
	 * @return string image element with link.
	 */
	function bootstrapBasicTheAttachedImage() 
	{
		$post                = get_post();
		$attachment_size     = apply_filters('bootstrap_basic_attachment_size', array(1140, 1140));
		$next_attachment_url = wp_get_attachment_url();

		/**
		 * Grab the IDs of all the image attachments in a gallery so we can get the
		 * URL of the next adjacent image in a gallery, or the first image (if
		 * we're looking at the last image in a gallery), or, in a gallery of one,
		 * just the link to that image file.
		 */
		$attachment_ids = get_posts(array(
			'post_parent'    => $post->post_parent,
			'fields'         => 'ids',
			'numberposts'    => -1,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'menu_order ID'
		));

		// If there is more than 1 attachment in a gallery...
		if (count($attachment_ids) > 1) {
			foreach ($attachment_ids as $attachment_id) {
				if ($attachment_id == $post->ID) {
					$next_id = current($attachment_ids);
					break;
				}
			}


			if ($next_id) {
				// get the URL of the next image attachment...
				$next_attachment_url = get_attachment_link($next_id);
			} else {
				// or get the URL of the first image attachment.
				$next_attachment_url = get_attachment_link(array_shift($attachment_ids));
			}
		}

		printf('<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
			esc_url($next_attachment_url),
			the_title_attribute(array('echo' => false)),
			wp_get_attachment_image($post->ID, $attachment_size, false, array('class' => 'img-responsive aligncenter'))
		);
	}// bootstrapBasicTheAttachedImage
}