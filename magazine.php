<?php
/**
 * Template Name: Magazine
 * Creates a page with a magazine-style layout. If you have a magazine-themed
 * blog you should can use this to define your front page.
 *
 * @package    Suffusion
 * @subpackage Templates
 */
get_header();

global $post, $suf_mag_content_enabled, $suf_mag_entity_order, $suf_mag_headlines_enabled, $suf_mag_excerpts_enabled, $suf_mag_categories_enabled, $suf_mag_total_excerpts;
global $suf_mag_excerpt_full_story_text, $suf_mag_excerpts_images_enabled, $suf_mag_excerpt_full_story_position, $suf_mag_excerpt_title_alignment;
global $suf_post_show_comment;

// $suf_post_show_comment = 'hide';

echo '<div id="main-col">';
suffusion_before_begin_content();
echo '<div id="content" class="hfeed">';

if (!rps_is_paid_member(get_current_user_id())) {
    echo '<section class="rps-welcome">';
    echo '<div class="suf-tiles suf-tiles-1">';

    echo '<div class="suf-tile suf-tile-1c suf-tile-ctr-0">';
    echo '<div class="suf-gradient suf-tile-topmost"><h3>Welcome</h3></div>';
    echo '<div class="suf-tile-text entry-content">';
    echo the_content();
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</section>';
}

// Setup query for sticky posts.
$sticky = get_option('sticky_posts');
$post_to_skip = [];
$sticky_queries = [];
if (is_array($sticky) && !empty($sticky) && is_numeric($sticky[0])) {
    rsort($sticky);
    $amount_of_stickies_to_display = 3;
    $sticky = array_slice($sticky, 0, $amount_of_stickies_to_display);
    /* Query sticky posts */
    $sticky_articles = new WP_Query(['post__in' => $sticky, 'ignore_sticky_posts' => 1]);

    if (is_object($sticky_articles)) {
        $sticky_queries[] = $sticky_articles;
        while ($sticky_articles->have_posts()) {
            $sticky_articles->the_post();
            // We need to skip this article in the next queries.
            $post_to_skip[] = $post->ID;
        }
    }
    wp_reset_query();
}

// Get the query for articles marked for Magazine Excerpts marked per post and in the selected categories.
$mag_queries = rps_suffusion_get_mag_section_queries(
    [
        'meta_check_field' => 'suf_magazine_excerpt',
        'category_prefix'  => 'suf_mag_excerpt_categories',
        'to_skip'          => $post_to_skip
    ]
);
$queries = array_merge($sticky_queries, $mag_queries);
$total = 0;
foreach ($queries as $query) {
    if (isset($query->posts) && is_array($query->posts)) {
        $total += count($query->posts);
    }
}

if ($total > 0) {
    global $suf_mag_excerpts_per_row, $suf_mag_excerpts_title, $suf_mag_total_excerpts;

    $ctr = 0;
    $tiles = 0;
    $cols_per_row = $suf_mag_excerpts_per_row;

    foreach ($queries as $query) {
        if (isset($query->posts) && is_array($query->posts)) {
            $num_results = count($query->posts);
            while ($query->have_posts()) {
                if ($ctr >= $suf_mag_total_excerpts) {
                    echo "</section>";
                    break;
                }
                if ($tiles == 0) {
                    echo "<section class='suf-mag-excerpts suf-mag-excerpts-$suf_mag_excerpts_per_row'>";
                }
                $query->the_post();
                if ($ctr % $suf_mag_excerpts_per_row == 0) {
                    if ($total - 1 - $ctr < $suf_mag_excerpts_per_row) {
                        $cols_per_row = $total - $ctr;
                    }
                }

                global $post, $suf_mag_excerpt_full_story_text, $suf_mag_excerpts_images_enabled, $suf_mag_excerpt_full_story_position, $suf_mag_excerpt_title_alignment;
                $post_to_skip[] = $post->ID;
                $categories = get_the_category($post->ID);
                if (empty($categories)) {
                    $categories = apply_filters('the_category', __('Uncategorized'), '', '');
                }

                $category = $categories[0];
                $category_link = '<a href="' .
                                 esc_url(get_category_link($category->term_id)) .
                                 '" title="' .
                                 esc_attr(sprintf(__("View all articles in %s"), $category->name)) .
                                 '" rel="category tag">';
                $category_text = '<h3>' . $category_link . $category->name . '</a></h3>';

                echo "<div class='suf-mag-excerpt entry-content suf-tile-{$cols_per_row}c $suf_mag_excerpt_full_story_position'>";

                echo "<div class='suf-gradient suf-tile-topmost'>" . $category_text . "</div>";

                echo "<h2 class='suf-mag-excerpt-title $suf_mag_excerpt_title_alignment'><a class='entry-title' rel='bookmark' href='" .
                     get_permalink($post->ID) .
                     "'>" .
                     get_the_title($post->ID) .
                     "</a></h2>";

                echo "<div class='suf-mag-excerpt-text entry-content'>";
                suffusion_excerpt();
                echo "</div>";

                if (trim($suf_mag_excerpt_full_story_text)) {
                    echo "<div class='suf-mag-excerpt-footer'>";
                    echo "<a href='" .
                         get_permalink($post->ID) .
                         "' class='suf-mag-excerpt-full-story button'>$suf_mag_excerpt_full_story_text</a>";
                    echo "</div>";
                }

                echo "</div>\n"; // The newline is important as it spreads the tiles evenly.
                $ctr++;
                $tiles++;
                if ($tiles == $cols_per_row) {
                    $tiles = 0;
                    echo "</section>";
                }
            }
            wp_reset_postdata();
        }
    }

    if ($tiles !== 0) {
        echo "</section>";
    }
}

echo "<section class='rps-showcases'>";
do_action('rps_showcase', '0');
echo "</section>";

echo '<section class="rps-misc">';
echo '<div class="suf-tiles suf-tiles-3">';

echo rps_display_suffu_tile_misc('RPS events', rps_EM_list_events(17), 0, 3);

$output = '<table>';
wp_reset_query();

$ctr = 0;
$queries = rps_suffusion_get_mag_section_queries(
    [
        'meta_check_field' => 'suf_magazine_excerpt',
        'category_prefix'  => 'suf_mag_excerpt_categories',
        'to_skip'          => $post_to_skip,
        'total'            => 10
    ]
);
$total = 0;
foreach ($queries as $query) {
    if (isset($query->posts) && is_array($query->posts)) {
        $total += count($query->posts);
    }
}
if ($total > 0) {
    foreach ($queries as $query) {
        if (isset($query->posts) && is_array($query->posts)) {
            $num_results = count($query->posts);
            while ($query->have_posts()) {
                if ($ctr >= 10) {
                    break;
                }
                global $post;
                $query->the_post();
                $categories = get_the_category($post->ID);
                if (empty($categories)) {
                    $categories = apply_filters('the_category', __('Uncategorized'), '', '');
                }

                $category = $categories[0];
                $category_link = '<a href="' .
                                 esc_url(get_category_link($category->term_id)) .
                                 '" title="' .
                                 esc_attr(sprintf(__("View all articles in %s"), $category->name)) .
                                 '" rel="category tag">';
                $category_text = $category_link . $category->name . '</a>';
                $title_text = "<a href='" . get_permalink($post->ID) . "'>" . get_the_title($post->ID) . "</a>";
                $output .= '<tr>';
                $output .= '<td style="white-space: nowrap; vertical-align: top;">' .
                           $category_text .
                           ':&nbsp;</td><td>' .
                           $title_text .
                           '</td>';
                $output .= '</tr>';
                $ctr++;
            }
        }
    }
}

$output .= '</tbody></table>';
echo rps_display_suffu_tile_misc('More articles', $output, 1, 3);

echo rps_display_suffu_tile_misc('Other events', rps_EM_list_events(24), 2, 3);

echo '</div>';
echo '</section>';
echo '</div><!-- content -->';
echo '</div><!-- main col -->';
get_footer();
