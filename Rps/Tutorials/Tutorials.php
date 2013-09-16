<?php
namespace Rps\Tutorials;

class Tutorials
{

    static function setupPostType ()
    {
        register_post_type('rps_tutorial', array('labels' => array('name' => __('Tutorials'),'singular_name' => __('Tutorial')),'public' => true,'has_archive' => true,'rewrite' => array('slug' => 'tutorial')));
    }
}