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
if (!$display) {
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
						<td class="cred-center">
						<?php
    echo "Raritan Photographic Society on the web";
    echo "<ul>";
    echo '<li><a href="https://www.facebook.com/pages/Raritan-Photographic-Society/157106827664588?sk=info"><span class="social-icon-16 social-icon-16-facebook"></span><span class="social-text">Facebook</span></a></li>';
    echo '<li><a href="http://www.meetup.com/http-www-raritanphoto-com/"><span class="social-icon-16 social-icon-16-meetup"></span><span class="social-text">Meetup</span></a></li>';
    echo '<li><a href="https://plus.google.com/114179804721869477091/about"><span class="social-icon-16 social-icon-16-google-plus"></span><span class="social-text">Google+</span></a></li>';
    echo '<li><a href="https://twitter.com/raritanphoto"><span class="social-icon-16 social-icon-16-twitter"></span><span class="social-text">Twitter</span></a></li>';
    echo '</ul>';
    ?></td>
						<td class="cred-right">Build by Peter van der Does using the
							Suffusion theme</td>
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