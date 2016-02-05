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
						<td class="cred-left">&copy; 2013-<?php echo date("Y") ?> <a href='http://www.raritanphoto.com'>Raritan Photographic Society</a><br />New Jersey's oldest photography club.</td>
						<td class="cred-center">Raritan Photographic Society on the web
							<ul>
								<li><a
									href="https://www.facebook.com/raritanphotographicsociety"><span
										class="social-icon-16 social-icon-16-facebook"></span><span
										class="social-text">Facebook</span></a></li>
								<li><a href="http://www.meetup.com/http-www-raritanphoto-com/"><span
										class="social-icon-16 social-icon-16-meetup"></span><span
										class="social-text">Meetup</span></a></li>
								<li><a href="https://google.com/+Raritanphoto"><span
										class="social-icon-16 social-icon-16-google-plus"></span><span
										class="social-text">Google+</span></a></li>
								<li><a href="https://twitter.com/raritanphoto"><span
										class="social-icon-16 social-icon-16-twitter"></span><span
										class="social-text">Twitter</span></a></li>
							</ul>
						</td>
						<td class="cred-right">Built by Peter van der Does</td>
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
