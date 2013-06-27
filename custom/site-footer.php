<?php
/**
 * The site footer.
 * This file should not be loaded by itself, but should instead be included using get_template_part or locate_template.
 *
 * @since 3.8.3
 * @package Suffusion
 * @subpackage Custom
 */
global $suf_footer_left, $suf_footer_center, $suf_footer_layout_style;
$display = apply_filters('suffusion_can_display_site_footer', true);
if (! $display) {
    return;
}
?>
<footer>
<?php
if ($suf_footer_layout_style != 'in-align') {
    ?>
	<div id='page-footer'>
		<div class='col-control'>
	<?php
}
?>
	<div id="cred">
				<table>
					<tr>
						<td class="cred-left"><?php $strip = stripslashes($suf_footer_left); $strip = wp_specialchars_decode($strip, ENT_QUOTES); echo do_shortcode($strip); ?></td>
						<td class="cred-center"><?php $strip = stripslashes($suf_footer_center); $strip = wp_specialchars_decode($strip, ENT_QUOTES); echo do_shortcode($strip); ?></td>
						<td class="cred-right">Build using the Suffusion theme</td>
					</tr>
				</table>
			</div>
<?php
if ($suf_footer_layout_style != 'in-align') {
    ?>
		</div>
	</div>
	<?php
}
?>
</footer>