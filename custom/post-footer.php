<?php
/**
 * Shows the footer of the post with the meta information. This file should not be loaded by itself, but should instead be included using get_template_part or locate_template.
 * Users can override this in a child theme. If you want a different style of the footer for a different custom post type, you can create a file
 * called post-footer-<post-type>.php. E.g. post-footer-book.php. If you want a different structure for posts / pages, you could use post-footer-post.php and/or
 * post-footer-page.php.
 *
 * @since 3.8.3
 * @package Suffusion
 * @subpackage Custom
 */

global $suf_page_show_posted_by, $suf_page_show_comment, $post, $suf_page_meta_position, $suf_byline_before_permalink, $suf_byline_after_permalink,
       $suf_byline_before_category, $suf_byline_after_category, $suf_byline_before_tag, $suf_byline_after_tag;
$format = suffusion_get_post_format();
if ($format == 'standard') {
	$format = '';
}
else {
	$format = $format.'_';
}
$meta_position = 'suf_post_'.$format.'meta_position';
$show_cats = 'suf_post_'.$format.'show_cats';
$show_posted_by = 'suf_post_'.$format.'show_posted_by';
$show_tags = 'suf_post_'.$format.'show_tags';
$show_comment = 'suf_post_'.$format.'show_comment';
$show_perm = 'suf_post_'.$format.'show_perm';
$with_title_show_perm = 'suf_post_'.$format.'with_title_show_perm';

global $$meta_position, $$show_cats, $$show_posted_by, $$show_tags, $$show_comment, $$show_perm, $$with_title_show_perm;
$post_meta_position = apply_filters('suffusion_byline_position', $$meta_position);
$post_show_cats = $$show_cats;
$post_show_posted_by = $$show_posted_by;
$post_show_tags = $$show_tags;
$post_show_comment = $$show_comment;
$post_show_perm = $$show_perm;
$post_with_title_show_perm = $$with_title_show_perm;
?>
<footer class="post-footer postdata fix">
<?php
if ((!is_page() && $post_meta_position == 'corners' && ($post_show_posted_by == 'show' || $post_show_posted_by == 'show-bright')) ||
	(is_page() && $suf_page_meta_position == 'corners' && ($suf_page_show_posted_by == 'show' || $suf_page_show_posted_by == 'show-bright'))) {
	suffusion_print_author_byline();
}
?>
</footer><!-- .post-footer -->
