<?php
/**
 * Outputs a view template which can be used with wp.media.template
 */
function actionRPS_print_media_templates()
{
    $default_gallery_type = 'row-equal';
    $gallery_type['masonry'] = 'Masonry';
    $gallery_type['row-equal'] = 'Each row equal height';
    ?>
    <script type="text/html" id="tmpl-rps-gallery-settings">
        <label class="setting">
            <span><?php echo 'Layout'; ?></span>
            <select class="layout" name="layout" data-setting="layout">
                <?php foreach ($gallery_type as $value => $caption) : ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected(
                        $value,
                        $default_gallery_type
                    ); ?>><?php echo esc_html($caption); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </script>

    <?php
}
