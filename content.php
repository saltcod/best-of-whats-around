<?php
/**
 * @package WordPress
 * @subpackage Victoria Park
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('row'); ?>>
	<header class="entry-header ">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'victoria_park' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	</header><!-- .entry-header -->


	<div class="featured-image">
		<?php if ( has_post_thumbnail() ) {
			the_post_thumbnail();
		} ?>
	</div>

	
	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
<?php else : ?>
	<div class="entry-content">
		

		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'victoria_park' ) ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'victoria_park' ), 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
<?php endif; ?>


<footer class="entry-meta">


	<?php edit_post_link( __( 'Edit', 'victoria_park' ), '<span class="edit-link">', '</span>' ); ?>
</footer><!-- #entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
