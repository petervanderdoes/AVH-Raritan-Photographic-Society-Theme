<?php
namespace Rps\Tutorials;

class Tutorials
{

    static function setupPostType ()
    {
        // @formatter:off
        $labels = array(
                        'name' =>  'Tutorials',
                        'singular_name' =>  'Tutorial',
                        'add_new' =>  'Add New',
                        'add_new_item' =>  'Add New Tutorial',
                        'edit_item' =>  'Edit Tutorial',
                        'new_item' =>  'New Tutorial',
                        'view_item' =>  'View Tutorial',
                        'search_items' =>  'Search Tutorials',
                        'not_found' =>  'No tutorials found',
                        'not_found_in_trash' =>  'No tutorials found in Trash',
                        'parent_item_colon' =>  'Parent Tutorial:',
                        'menu_name' =>  'Tutorials',
                );
        $args = array(
                        'labels' => $labels,
                        'hierarchical' => false,
                        'description' => 'Tutorials',
                        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions' ),
                        'public' => true,
                        'show_ui' => true,
                        'show_in_menu' => true,
                        'show_in_nav_menus' => true,
                        'publicly_queryable' => true,
                        'exclude_from_search' => false,
                        'has_archive' => true,
                        'query_var' => true,
                        'can_export' => true,
                        'rewrite' => array( 'slug' =>'tutorials', 'with_front'=>false ),
                        'slug' => 'tutorial',
                        'capability_type' => 'post'
                );
        //@formatter:off
        register_post_type('rps_tutorial', $args);
    }

    static function setupTaxonomies()
    {
        // Categories
        $labels = array(
                    'name' =>  'Tutorial Categories',
                    'singular_name' =>  'Tutorial Category',
                    'search_items' =>  'Search Tutorial Categories',
                    'popular_items' =>  'Popular Tutorial Categories',
                    'all_items' =>  'All Tutorial Categories',
                    'parent_item' =>  'Parent Tutorial Category',
                    'parent_item_colon' =>  'Parent Tutorial Category:',
                    'edit_item' =>  'Edit Tutorial Category',
                    'update_item' =>  'Update Tutorial Category',
                    'add_new_item' =>  'Add New Tutorial Category',
                    'new_item_name' =>  'New Tutorial Category',
                    'separate_items_with_commas' =>  'Separate Tutorial Categories with commas',
                    'add_or_remove_items' =>  'Add or remove Tutorial Categories',
                    'choose_from_most_used' =>  'Choose from the most used Tutorial Categories',
                    'menu_name' =>  'Tutorial Categories',
            );
        $args = array(
                    'labels' => $labels,
                    'public' => true,
                    'show_in_nav_menus' => true,
                    'show_ui' => true,
                    'show_tagcloud' => false,
                    'show_admin_column' => false,
                    'hierarchical' => true,
                    'rewrite' => array( 'slug' => 'tutorials/categories', 'with_front' => false, 'hierarchical' => true ),
                    'query_var' => true
            );
        register_taxonomy( 'tutorial_categories', array('rps_tutorial'), $args );
    }
}