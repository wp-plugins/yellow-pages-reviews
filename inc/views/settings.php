<?php do_action( 'sunrise_page_before' ); ?>
<div class="ypr-about-wrap"></div>
<a href="https://wordimpress.com/plugins/yellow-pages-reviews-pro/" title="Upgrade to Yellow Pages Reviews Pro" target="_blank" class="update-link new-window">Upgrade to Premium</a>

<p class="label">Version <?php echo $this->version; ?></p>

<div id="sunrise-plugin-settings" class="wrap">
	<h2 id="sunrise-plugin-tabs" class="nav-tab-wrapper hide-if-no-js">
		<?php
		// Show tabs
		$this->render_tabs();
		?>
	</h2>
	<?php
	// Show notifications
	$this->notifications(
		array(
			'js'          => __( 'For full functionality of this page it is recommended to enable javascript.', $this->textdomain ),
			'reseted'     => __( 'Settings Reset Successfully', $this->textdomain ),
			'not-reseted' => __( 'Plugins already set to default settings', $this->textdomain ),
			'saved'       => __( 'Settings saved successfully', $this->textdomain ),
			'not-saved'   => __( 'Settings not saved due to no changes being made.', $this->textdomain )
		)
	);
	?>
	<form action="<?php echo $this->admin_url; ?>" method="post" id="sunrise-plugin-options-form">
		<?php
		// Show options
		$this->render_panes();
		?>
		<input type="hidden" name="action" value="save" />
	</form>

	<?php
	/**
	 * Output Licensing Fields
	 */
	if ( class_exists( 'YPR_License' ) ) {
		$this->licencing->edd_wordimpress_license_page();
	}
	?>

</div>
<?php do_action( 'sunrise_page_after' ); ?>
