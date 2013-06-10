<?php
/**
 * A study in stuctural functionalism at Victoria Park 
 *
 * @package WordPress
 * @subpackage Victoria Park
 * @since Victoria Park 0.2
 */


/**
 * Setup defaults, register taxonomies/post types and other WordPress features.
 * This function is hooked into the after_setup_theme hook.
 *
 * @since Victoria Park 0.2
 */






add_action('after_setup_theme', 'victoria_park_theme_setup');

function victoria_park_theme_setup(){
	//add basic features
	add_theme_support('automatic-feed-links');
 	add_theme_support( 'post-thumbnails' ); 

	//add custom scripts
	add_action('wp_enqueue_scripts', 'victoria_park_enqueue_scripts');

	//add custom widgets/sidebars
	add_action('init', 'victoria_park_widgets_init');

	// add custom menus
	add_action('init', 'victoria_park_register_menus');


	// add various other custom actions/filters
	add_filter('body_class', 'victoria_park_better_body_classes');
	add_filter('wp_nav_menu', 'victoria_park_add_slug_class_to_menu_item');


	//print template file in footer — remove for production. 
	add_action('wp_footer', 'victoria_park_show_template');

}


/**
 * Loads theme-specific JavaScript files.
 *
 * @since 0.2
 */

function victoria_park_enqueue_scripts() {
    wp_enqueue_script( 'jquery' );
 
    wp_register_script( 'archivalmoments', get_template_directory_uri() .'/js/archivalmoments.js');
    wp_enqueue_script( 'archivalmoments' );

} 

 
/**
 * Register a taxonomy for the archival moment decade
 *
 * @since 0.1
 */
//
function register_decade_taxonomy() {
	// create a new taxonomy
	register_taxonomy(
		'decade',
		'post',
		array(
			'label' => __( 'Decade' ),
			'sort' => true,
 	    	'hierarchical' => true,
     		'rewrite' => array( 'slug' => 'decade' ),
		)
	);
}
add_action( 'init', 'register_decade_taxonomy' );


 /**
 * Include the page slug in the body class attribute.
 *
 * @since 0.2
 *
 * @param array $classes The existing classes for the body element
 * @return array The amended class array for the body element
 */

function victoria_park_better_body_classes( $classes ){
    global $post;
    if ( isset( $post ) ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}




/**
 * Print out the current template file to the footer. 
 * Obviously to be removed in production
 *
 * @since 0.2
 */

function victoria_park_show_template() {
	global $template;
	echo '<strong>Template file:</strong>';
	 print_r($template);
}
 


/**
 * Add slug to menu li classes
 *
 * @since 0.2
 */

function victoria_park_add_slug_class_to_menu_item($output){
	$ps = get_option('permalink_structure');
	if(!empty($ps)){
		$idstr = preg_match_all('/<li id="menu-item-(\d+)/', $output, $matches);
		foreach($matches[1] as $mid){
			$id = get_post_meta($mid, '_menu_item_object_id', true);
			$slug = basename(get_permalink($id));
			$output = preg_replace('/menu-item-'.$mid.'">/', 'menu-item-'.$mid.' menu-item-'.$slug.'">', $output, 1);
		}
	}
	return $output;
}

/**
 * This theme uses wp_nav_menu() in one location.
 *
 * @since 0.2
 */


function victoria_park_register_menus(){
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'victoria_park' ),
	) );
	
}

 
/**
 * Modify the Posted on output
 *
 * @since 0.2
 */

function victoria_park_posted_on() {
	printf( __( '<span class="sep"></span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="byline">   </span></span>', 'toolbox' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'toolbox' ), get_the_author() ) ),
		esc_html( get_the_author() )
	);
}



/**
 * Register widgetized area and update sidebar with default widgets
 */
function victoria_park_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar 1', 'victoria_park' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );

	register_sidebar( array(
		'name' => __( 'Sidebar 2', 'victoria_park' ),
		'id' => 'sidebar-2',
		'description' => __( 'An optional second sidebar area', 'victoria_park' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}




if ( ! function_exists( 'victoria_park_content_nav' ) ):
/**
 * Display navigation to next/previous pages when applicable
 *
 * @since victoria_park 1.2
 */
function victoria_park_content_nav( $nav_id ) {
	global $wp_query;

	?>
	<nav id="<?php echo $nav_id; ?>">
		<h1 class="assistive-text section-heading"><?php _e( 'Post navigation', 'victoria_park' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'victoria_park' ) . '</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'victoria_park' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'victoria_park' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'victoria_park' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo $nav_id; ?> -->
	<?php
}
endif; // victoria_park_content_nav



function victoria_park_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so toolbox_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so toolbox_categorized_blog should return false
		return false;
		 
	}
}


/**
 * Gets the current Archival Moment number
 * 
 */

function get_post_number( $postID ) {
 	$postNumberQuery = new WP_Query('orderby=date&order=ASC&posts_per_page=-1');
	$counter = 1;
	$postCount = 0;

	if( $postNumberQuery->have_posts()):
		while ($postNumberQuery->have_posts()):
			$postNumberQuery->the_post();

			if ($postID == get_the_ID()){
				$postCount = $counter;
			} else {
				$counter++;
			}
		endwhile;
	endif;

	wp_reset_query();
 	return $postCount;
} 


/**
 * Gets the total count of archival moments
 * 
 */

function total_archival_moment_count(){
	$published_posts = wp_count_posts();
	$total_moments =  $published_posts->publish;
	return $total_moments;
}
 

/**
 * A brutally long, borderline pointless function to convert a number to its word equivalent
 * 
 */
function convert_number_to_words($number) {
    
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    
    if (!is_numeric($number)) {
        return false;
    }
    
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    
    return $string;
}


