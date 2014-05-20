<?php

function filterRPS_gallery_output($foo, $attr)
{
    $post = get_post();

    static $instance = 0;
    $instance++;

    if (!empty($attr['ids'])) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if (empty($attr['orderby']))
            $attr['orderby'] = 'post__in';
        $attr['include'] = $attr['ids'];
    }

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }

    // @formatter:off
    extract(shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'id'         => $post ? $post->ID : 0,
    'itemtag'    => 'figure',
    'icontag'    => 'div',
    'captiontag' => 'figcaption',
    'columns'    => 3,
    'size'       => 'thumbnail',
    'include'    => '',
    'exclude'    => '',
    'link'       => '',
    'layout'     => 'row-equal'
        ), $attr, 'gallery'));
        // $formatter:on

        $id = intval($id);
        if ( 'RAND' == $order )
            $orderby = 'none';

        if ( !empty($include) ) {
            $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

            $attachments = array();
            foreach ( $_attachments as $key => $val ) {
                $attachments[$val->ID] = $_attachments[$key];
            }
        } elseif ( !empty($exclude) ) {
            $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
        } else {
            $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
        }

        if ( empty($attachments) )
            return '';

        if ( is_feed() ) {
            $output = "\n";
            foreach ( $attachments as $att_id => $attachment )
                $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
            return $output;
        }

        $itemtag = tag_escape($itemtag);
        $captiontag = tag_escape($captiontag);
        $icontag = tag_escape($icontag);
        $valid_tags = wp_kses_allowed_html( 'post' );
        if ( ! isset( $valid_tags[ $itemtag ] ) )
            $itemtag = 'dl';
        if ( ! isset( $valid_tags[ $captiontag ] ) )
            $captiontag = 'dd';
        if ( ! isset( $valid_tags[ $icontag ] ) )
            $icontag = 'dt';

        $columns = intval($columns);
        $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
        $float = is_rtl() ? 'right' : 'left';

        $selector = "gallery-{$instance}";

        $gallery_style = $gallery_div = '';

        $layout = strtolower($layout);

        $size_class = sanitize_html_class( $size );
        $masonry_class = ($layout == 'masonry') ? 'gallery-masonry' : '';
        $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} $masonry_class'>";


        /**
         * Filter the default gallery shortcode CSS styles.
         *
         * @since 2.5.0
         *
         * @param string $gallery_style Default gallery shortcode CSS styles.
         * @param string $gallery_div   Opening HTML div container for the gallery shortcode output.
         */
        $output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );
        if (strtolower($layout) == 'masonry')  {
            $output .= '<div class="grid-sizer"></div>';
        }
        $i = 0;
        foreach ( $attachments as $id => $attachment ) {
            if ($layout !== 'masonry') {
                if ($i % $columns == 0) {
                    if ($layout == 'row-equal') {
                        $output .= '<div class="gallery-row gallery-row-equal">';
                    } else {
                        $output .= '<div class="gallery-row">';
                    }
                }
            }
            if ( ! empty( $link ) && 'file' === $link )
                $image_output = wp_get_attachment_link( $id, $size, false, false );
            elseif ( ! empty( $link ) && 'none' === $link )
            $image_output = wp_get_attachment_image( $id, $size, false );
            else
                $image_output = wp_get_attachment_link( $id, $size, true, false );

            $image_meta  = wp_get_attachment_metadata( $id );

            $orientation = '';
            if ( isset( $image_meta['height'], $image_meta['width'] ) )
                $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

            $item_class = ($layout == 'masonry') ? 'gallery-item-masonry' : 'gallery-item';
            $output .= "<{$itemtag} class='{$item_class}'>";
            $output .= "<div class='gallery-item-content'>";
            $output .= "<{$icontag} class='gallery-icon {$orientation}'>$image_output</{$icontag}>";

            $caption_text = '';
            if ( $captiontag && trim($attachment->post_excerpt) ) {
                $caption_text .= $attachment->post_excerpt;
            }
            $photographer_name = get_post_meta($attachment->ID,'_rps_photographer_name', true);
            // If image credit fields have data then attach the image credit
            if ($photographer_name != '') {
                if (!empty($caption_text)) {
                    $caption_text .= '<br />';
                }
                $caption_text .= '<span class="wp-caption-credit">Credit: ' . $photographer_name . '</span>';
            }
            if (!empty($caption_text)) {
                $output .= "<{$captiontag} class='wp-caption-text gallery-caption'>" . wptexturize($caption_text) . "</{$captiontag}>";
            }

            $output  .= "</div>";
            $output .= "</{$itemtag}>";

            if ($layout !== 'masonry' && $columns > 0 && ++$i % $columns == 0)
                $output .= '</div>';
        }

        if ($columns > 0 && $i % $columns !== 0)
            $output .= '</div>';
        $output .= "
		</div>\n";

        return $output;
}


/**
 * Outputs a view template which can be used with wp.media.template
 */
function actionRPS_print_media_templates() {
    $default_gallery_type = 'row-equal';
    $gallery_type['masonry'] = 'Masonry';
    $gallery_type['row-equal'] = 'Each row equal height';
    ?>
<script type="text/html" id="tmpl-rps-gallery-settings">
<label class="setting">
<span><?php echo 'Layout'; ?></span>
<select class="layout" name="layout" data-setting="layout">
<?php foreach ( $gallery_type as $value => $caption ) : ?>
<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $default_gallery_type ); ?>><?php echo esc_html( $caption ); ?></option>
<?php endforeach; ?>
</select>
</label>
</script>

<?php
}
