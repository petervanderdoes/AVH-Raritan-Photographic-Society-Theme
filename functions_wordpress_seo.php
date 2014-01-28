<?php

function yoast_breadcrumb_output()
{
    if (function_exists('yoast_breadcrumb')) {
        if (!(is_home() || is_front_page())) {
            yoast_breadcrumb('<div class="breadcrumb">', "</div>");
        }
    }
}
