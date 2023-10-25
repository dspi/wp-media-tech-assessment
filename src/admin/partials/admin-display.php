<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    PluginName
 * @subpackage PluginName/admin/partials
 */

namespace DSPI_ROCKET_WP_CRAWLER\Admin;

?>

<div class="wrap">
	<h2>Internal Crawler</h2>
	<?php settings_errors(); ?>
	<!-- <form method="POST" action="options.php"> -->
	<form method="POST" action="options.php" id="crawl-form" data-action="crawl_now">
		<?php
			settings_fields( 'admin_page_general_settings' );
			do_settings_sections( 'admin_page_general_settings' );
		?>
		<?php submit_button('Crawl Now'); ?>
	</form>
</div>
