<?php
/**
 * Template Name: Magazine
 *
 * Creates a page with a magazine-style layout. If you have a magazine-themed
 * blog you should can use this to define your front page.
 *
 * @package Suffusion
 * @subpackage Templates
 */
get_header();

global $post, $suf_mag_content_enabled, $suf_mag_entity_order, $suf_mag_headlines_enabled, $suf_mag_excerpts_enabled, $suf_mag_categories_enabled, $suf_mag_total_excerpts;
global $suf_mag_excerpt_full_story_text, $suf_mag_excerpts_images_enabled, $suf_mag_excerpt_full_story_position, $suf_mag_excerpt_title_alignment;
global $suf_post_show_comment;

// $suf_post_show_comment = 'hide';
?>

<div id="main-col">
<?php suffusion_before_begin_content(); ?>
	<div id="content" class="hfeed">
<?php
echo "\n" . '<section class="rps-welcome">';
echo '<div class="suf-tiles suf-tiles-1">';

echo '<div class="suf-tile suf-tile-1c suf-tile-ctr-0">';
echo '<div class="suf-gradient suf-tile-topmost"><h3>Welcome</h3></div>';
echo '<div class="suf-tile-text entry-content">';
echo '<p>Whether you’re a beginner or advanced amateur (even a professional) photographer, the Raritan Photographic Society, based in Middlesex County, New Jersey, has something to offer you.</p>';
echo '<p>In addition to monthly lecture and competition/critique meetings, our members enjoy field trips and workshops, as well as our holiday and banquet parties.<br />';
echo 'If you want to socialize with other photographers and at the same time improve your photography skills, the Raritan Photographic Society is a fun camera club to be a part of.</p>';
echo '<p>Before you decide to join, you are welcome to attend a meeting or two. You’ll be able to meet our members and find out in person what we’re all about. Check the ' . em_get_link('schedule of events') . ' for the season to see when and where we meet</p>
    ';
echo '</div>';
echo '</div>';
echo '</section>' . "\n";

$sticky = get_option('sticky_posts');
if (is_array($sticky) && is_numeric($sticky[0])) {
    rsort($sticky);
    $amount_of_stickies_to_display = 3;
    $sticky = array_slice($sticky, 0, $amount_of_stickies_to_display);
    /* Query sticky posts */
    $sticky_articles = new WP_Query(array('post__in' => $sticky, 'ignore_sticky_posts' => 1));

    if (is_object($sticky_articles)) {
        $sticky_queries[] = $sticky_articles;
        while ($sticky_articles->have_posts()) {
            $sticky_articles->the_post();
            $post_to_skip[] = $post->ID;
        }
    }
    wp_reset_query();
}

$mag_queries = rps_suffusion_get_mag_section_queries(array('meta_check_field' => 'suf_magazine_excerpt', 'category_prefix' => 'suf_mag_excerpt_categories', 'to_skip' => $post_to_skip));
$queries = array_merge($sticky_queries, $mag_queries);
$total = 0;
foreach ($queries as $query) {
    if (isset($query->posts) && is_array($query->posts)) {
        $total += count($query->posts);
    }
}
if ($total > 0) {
    global $suf_mag_excerpts_per_row, $suf_mag_excerpts_title, $suf_mag_total_excerpts;
    echo "<section class='suf-mag-excerpts suf-mag-excerpts-$suf_mag_excerpts_per_row'>\n";

    if (trim($suf_mag_excerpts_title) != '') {
        global $suf_mag_excerpts_main_title_alignment;
        echo "<div class='suf-mag-excerpts-header $suf_mag_excerpts_main_title_alignment'>" . stripslashes($suf_mag_excerpts_title) . "</div>";
    }

    $ctr = 0;
    $cols_per_row = $suf_mag_excerpts_per_row;
    foreach ($queries as $query) {
        if (isset($query->posts) && is_array($query->posts)) {
            $num_results = count($query->posts);
            while ($query->have_posts()) {
                if ($ctr >= 6) {
                    break;
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
                if (empty($categories))
                    $categories = apply_filters('the_category', __('Uncategorized'), '', '');

                $category = $categories[0];
                $category_link = '<a href="' . esc_url(get_category_link($category->term_id)) . '" title="' . esc_attr(sprintf(__("View all articles in %s"), $category->name)) . '" rel="category tag">';
                $category_text = '<h3>' . $category_link . $category->name . '</h3></a>';

                echo "\n\t<div class='suf-mag-excerpt entry-content suf-tile-{$cols_per_row}c $suf_mag_excerpt_full_story_position'>\n";

                echo "\t\t<div class='suf-gradient suf-tile-topmost'>" . $category_text . "</div>\n";

                echo "\t\t<h2 class='suf-mag-excerpt-title $suf_mag_excerpt_title_alignment'><a class='entry-title' rel='bookmark' href='" . get_permalink($post->ID) . "'>" . get_the_title($post->ID) . "</a></h2>\n";

                echo "\t\t<div class='suf-mag-excerpt-text entry-content'>\n";
                suffusion_excerpt();
                echo "\t\t</div>\n";

                if (trim($suf_mag_excerpt_full_story_text)) {
                    echo "\t<div class='suf-mag-excerpt-footer'>\n";
                    echo "\t\t<a href='" . get_permalink($post->ID) . "' class='suf-mag-excerpt-full-story button'>$suf_mag_excerpt_full_story_text</a>";
                    echo "\t</div>\n";
                }

                echo "\t</div>";
                $ctr ++;
            }
            wp_reset_postdata();
        }
    }

    echo "</section>\n";
}

echo "<section class='rps-showcases'>\n";
do_action('rps_showcase', '0');
echo "</section>\n";

echo '<section class="rps-misc">';
echo '<div class="suf-tiles suf-tiles-3">';

echo '<div class="suf-tile suf-tile-3c suf-tile-ctr-0">' . "\n";
echo "\t\t<div class='suf-gradient suf-tile-topmost'><h3>RPS events</h3></div>\n";
echo "\t\t<div class='suf-tile-text entry-content'>\n";
echo rps_EM_list_events(17);
echo "\t\t</div>\n";
echo '</div>' . "\n";

echo '<div class="suf-tile suf-tile-3c suf-tile-ctr-1">' . "\n";
echo "\t\t<div class='suf-gradient suf-tile-topmost'><h3>More articles</h3></div>\n";
echo "\t\t<div class='suf-tile-text entry-content'>\n";
echo '<table>';
wp_reset_query();

$ctr = 0;
$queries = rps_suffusion_get_mag_section_queries(array('meta_check_field' => 'suf_magazine_excerpt', 'category_prefix' => 'suf_mag_excerpt_categories', 'to_skip' => $post_to_skip, 'total' => 10));
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
                if (empty($categories))
                    $categories = apply_filters('the_category', __('Uncategorized'), '', '');

                $category = $categories[0];
                $category_link = '<a href="' . esc_url(get_category_link($category->term_id)) . '" title="' . esc_attr(sprintf(__("View all articles in %s"), $category->name)) . '" rel="category tag">';
                $category_text = $category_link . $category->name . '</a>';
                $title_text = "<a href='" . get_permalink($post->ID) . "'>" . get_the_title($post->ID) . "</a>\n";
                echo '<tr>';
                echo '<td style="white-space: nowrap; vertical-align: top;">' . $category_text . ':&nbsp;</td><td>' . $title_text . '</td>';
                echo '</tr>';
                $ctr ++;
            }
        }
    }
}

echo '</tbody></table>';
echo "\t\t</div>\n";
echo '</div>' . "\n";

echo '<div class="suf-tile suf-tile-3c suf-tile-ctr-2">' . "\n";
echo '</div>' . "\n";

echo '</div>' . "\n";
echo '</section>' . "\n";
?>
      </div>
	<!-- content -->
</div>
<!-- main col -->
<?php get_footer(); ?>