<?php

function yoast_breadcrumb_output()
{
    if (function_exists('yoast_breadcrumb')) {
        yoast_breadcrumb('<div id="subnav" class="breadcrumb">', "</div>");
    }
}
