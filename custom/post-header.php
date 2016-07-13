<?php
/**
 * Shows the title of the post along with the meta information.
 * This file should not be loaded by itself, but should instead be included using get_template_part or locate_template.
 * Users can override this in a child theme. If you want a different style of title and meta for a different custom
 * post type, you can create a file called post-header-<post-type>.php. E.g. post-header-book.php. If you want a
 * different structure for posts / pages, you could use post-header-post.php and/or post-header-page.php.
 *
 * @since      3.8.3
 * @package    Suffusion
 * @subpackage Custom
 */
global $post, $suf_page_show_comment, $suf_page_show_posted_by, $suf_page_meta_position, $suf_byline_before_permalink, $suf_byline_after_permalink, $suf_byline_before_category, $suf_byline_after_category, $suf_byline_before_tag, $suf_byline_after_tag, $suf_byline_before_edit, $suf_byline_after_edit;
$format = suffusion_get_post_format();
if ($format == 'standard') {
    $format = '';
} else {
    $format = $format . '_';
}
$meta_position = 'suf_post_' . $format . 'meta_position';
$show_cats = 'suf_post_' . $format . 'show_cats';
$show_posted_by = 'suf_post_' . $format . 'show_posted_by';
$show_tags = 'suf_post_' . $format . 'show_tags';
$show_comment = 'suf_post_' . $format . 'show_comment';
$show_perm = 'suf_post_' . $format . 'show_perm';
$with_title_show_perm = 'suf_post_' . $format . 'with_title_show_perm';

global $$meta_position, $$show_cats, $$show_posted_by, $$show_tags, $$show_comment, $$show_perm, $$with_title_show_perm;
$post_meta_position = $$meta_position;
$post_show_cats = $$show_cats;
$post_show_posted_by = $$show_posted_by;
$post_show_tags = $$show_tags;
$post_show_comment = $$show_comment;
$post_show_perm = $$show_perm;
$post_with_title_show_perm = $$with_title_show_perm;

if (is_singular() && (!is_front_page())) {
    $header_tag = "h1";
} else {
    $header_tag = "h2";
}

if ($post->post_type == 'post') {
    ?>
    <header class='entry-header post-header title-container fix'>
        <div class="date">
            <span class="month"><?php the_time('M'); ?></span> <span class="day"><?php the_time('d'); ?></span><span
                class="year"><?php the_time('Y'); ?></span>
        </div>
        <?php echo '<span class="vcard" style="display: none"><a href="' .
                   get_author_posts_url(get_the_author_meta('ID')) .
                   '" class="url fn" rel="author">' .
                   get_the_author() .
                   '</a></span>'; ?>
        <div class="title">
            <<?php echo $header_tag; ?> class="entry-title posttitle"><?php echo suffusion_get_post_title_and_link(
            ); ?></<?php echo $header_tag; ?>>
        <?php
        global $suf_category_excerpt, $suf_tag_excerpt, $suf_archive_excerpt, $suf_index_excerpt, $suf_search_excerpt, $suf_author_excerpt, $suf_show_excerpt_thumbnail, $suffusion_current_post_index, $suffusion_full_post_count_for_view, $suf_pop_excerpt, $page_of_posts;
        global $suffusion_cpt_post_id;

        if (isset($suffusion_cpt_post_id)) {
            $cpt_excerpt = suffusion_get_post_meta($suffusion_cpt_post_id, 'suf_cpt_post_type_layout', true);
            $cpt_image = suffusion_get_post_meta($suffusion_cpt_post_id, 'suf_cpt_show_excerpt_thumb', true);
        } else {
            $cpt_excerpt = false;
        }

        if (!(($suffusion_current_post_index > $suffusion_full_post_count_for_view) &&
              ($cpt_excerpt ||
               (is_category() && $suf_category_excerpt == "excerpt") ||
               (is_tag() && $suf_tag_excerpt == "excerpt") ||
               (is_search() && $suf_search_excerpt == "excerpt") ||
               (is_author() && $suf_author_excerpt == "excerpt") ||
               ((is_date() || is_year() || is_month() || is_day() || is_time()) && $suf_archive_excerpt == "excerpt") ||
               (isset($page_of_posts) && $page_of_posts && $suf_pop_excerpt == "excerpt") ||
               (!(is_singular() ||
                  is_category() ||
                  is_tag() ||
                  is_search() ||
                  is_author() ||
                  is_date() ||
                  is_year() ||
                  is_month() ||
                  is_day() ||
                  is_time()) && $suf_index_excerpt == "excerpt")))
        ) {

            do_action('rps-social-buttons');
        }
        ?>
        </div>
        <!-- /.title -->
    </header>
    <!-- /.title-container -->
    <?php
} else {
    if (!is_singular()) {
        ?>
        <header class="entry-header post-header fix">
        <<?php echo $header_tag; ?> class="entry-title posttitle"><?php echo suffusion_get_post_title_and_link(
        ); ?></<?php echo $header_tag; ?>>
        </header>
        <?php
    } else {
        ?>
        <header class="entry-header post-header fix">
        <<?php echo $header_tag; ?> class="entry-title posttitle"><?php the_title(); ?></<?php echo $header_tag; ?>>
        <?php
        echo '<span class="vcard" style="display: none"><a href="' .
             get_author_posts_url(get_the_author_meta('ID')) .
             '" class="url fn" rel="author">' .
             get_the_author() .
             '</a></span>';
        if ($post->post_type == 'page' && $suf_page_meta_position == 'corners') {
            do_action('rps-social-buttons');
        }
        ?>
        </header> <?php
    }
}
?>
