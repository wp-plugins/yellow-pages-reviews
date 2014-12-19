<?php

/* Display a notice that can be dismissed */

add_action('admin_notices', 'ypr_activation_admin_notice');

function ypr_activation_admin_notice() {
	global $current_user ;
	$user_id = $current_user->ID;
	/* Check that the user hasn't already clicked to ignore the message */
	global $pagenow;
	if ( $pagenow == 'plugins.php' ) {
		if (!get_user_meta($user_id, 'ypr_activation_ignore_notice')) { ?>
			<style>div.updated.ypr,
				div.updated.ypr header,
				div.updated.ypr header img,
				div.updated.ypr header h3,
				div.updated.ypr .dismiss,
				.ypr-actions,
				.ypr-action,
				.ypr-action #mc_embed_signup,
				div.updated.ypr .ypr-action span.dashicons:before {
					-webkit-box-sizing: border-box;
					/* Safari/Chrome, other WebKit */
					-moz-box-sizing: border-box;
					/* Firefox, other Gecko */
					box-sizing: border-box;
					/* Opera/IE 8+ */
					width: 100%;
					position: relative;
					padding: 0;
					margin: 0;
					overflow: hidden;
					float: none;
					display: block;
					text-align: left;
				}
				.ypr-action a,
				.ypr-action a:hover,
				div.updated.ypr .ypr-action.mailchimp:hover,
				div.updated.ypr .ypr-action.mailchimp span {
					-webkit-transition: all 500ms ease-in-out;
					-moz-transition: all 500ms ease-in-out;
					-ms-transition: all 500ms ease-in-out;
					-o-transition: all 500ms ease-in-out;
					transition: all 500ms ease-in-out;
				}
				div.updated.ypr {
					margin: 1rem 0 2rem 0;
				}
				div.updated.ypr header h3 {
					line-height: 1.4;
				}
				@media screen and (min-width: 280px) {
					div.updated.ypr {
						border: 0px;
						background: transparent;
						-webkit-box-shadow: 0 1px 1px 1px rgba(0, 0, 0, 0.1);
						box-shadow: 0 1px 1px 1px rgba(0, 0, 0, 0.1);
					}
					div.updated.ypr header {
						background: #333333;
						color: white;
						position: relative;
						height: 6rem;
					}
					div.updated.ypr header img {
						display: none;
						margin: 1rem;
						float: left;
						max-height: 69px;
						width: auto;
					}
					div.updated.ypr header h3 {
						float: left;
						max-width: 60%;
						margin: 1rem;
						display: inline-block;
						color: white;
					}
					div.updated.ypr a.dismiss {
						display: block;
						position: absolute;
						left: auto;
						top: 0;
						bottom: 0;
						right: 0;
						width: 6rem;
						background: rgba(255, 255, 255, .15);
						color: white;
						text-align: center;
					}
					.ypr a.dismiss:before {
						font-family: 'Dashicons';
						content: "\f153";
						display: inline-block;
						position: absolute;
						top: 50%;

						transform: translate(-50%);
						right: 40%;
						margin: auto;
						line-height: 0;
					}
					div.updated.ypr a.dismiss:hover {
						color: #777;
						background: rgba(255, 255, 255, .5)
					}

					/* END ACTIVATION HEADER
					 * START ACTIONS
					 */
					div.updated.ypr .ypr-action {
						display: table;
					}
					.ypr-action a,
					.ypr-action #mc_embed_signup {
						background: rgba(0,0,0,.1);
						color: rgba(51, 51, 51, 1);
						padding: 0 1rem 0 6rem;
						height: 4rem;
						display: table-cell;
						vertical-align: middle;
					}
					.ypr-action.mailchimp {
						margin-bottom: -1.5rem;
						top: -.5rem;
					}
					.ypr-action.mailchimp p {
						margin: 9px 0 0 0;
					}

					.ypr-action #mc_embed_signup form {
						display: inline-block;
					}

					div.updated.ypr .ypr-action span {
						display: block;
						position: absolute;
						left: 0;
						top: 0;
						bottom: 0;
						height: 100%;
						width: auto;
					}
					div.updated.ypr .ypr-action span.dashicons:before {
						padding: 2rem 1rem;
						color: #333333;
						line-height: 0;
						top: 50%;
						transform: translateY(-50%);
						background: rgba(163, 163, 163, .25);
					}
					div.updated.ypr .ypr-action a:hover,
					div.updated.ypr .ypr-action.mailchimp:hover {
						background: rgba(0,0,0,.2);
					}
					div.updated.ypr .ypr-action a {
						text-decoration: none;
					}

					div.updated.ypr .ypr-action a,
					div.updated.ypr .ypr-action #mc_embed_signup {
						position: relative;
						overflow: visible;
					}
					.ypr-action #mc_embed_signup form,
					.ypr-action #mc_embed_signup form input#mce-EMAIL {
						width: 100%;
					}
					div.updated.ypr .mailchimp form input#mce-EMAIL + input.submit-button {
						display: block;
						position: relative;
						top: -1.75rem;
						float: right;
						right: 4px;
						border: 0;
						background: #cccccc;
						border-radius: 2px;
						font-size: 10px;
						color: white;
						cursor: pointer;
					}

					div.updated.ypr .mailchimp form input#mce-EMAIL:focus + input.submit-button {
						background: #333333;
					}

					.ypr-action #mc_embed_signup form input#mce-EMAIL div#placeholder,
					input#mce-EMAIL:-webkit-input-placeholder {opacity: 0;}
				}
				@media screen and (min-width: 780px) {
					div.updated.ypr header h3 {line-height: 3;}

					div.updated.ypr .mailchimp form input#mce-EMAIL + input.submit-button {
						top: -1.55rem;
					}
					div.updated.ypr header img {
						display: inline-block;
					}
					div.updated.ypr header h3 {
						max-width: 50%;
					}
					.ypr-action {
						width: 30%;
						float: left;
					}
					div.updated.ypr .ypr-action a {

					}
					.ypr-action a,
					.ypr-action #mc_embed_signup {
						padding: 0 1rem 0 4rem;
					}
					div.updated.ypr .ypr-action span.dashicons:before {

					}
					div.updated.ypr .ypr-action.mailchimp {
						width: 40%;
					}
				}</style>
			<div class="updated ypr">
				<header>
					<img src="<?php echo YPR_PLUGIN_URL; ?>/assets/images/yp_logo.png"  class="ypr-logo"/>
					<h3><?php _e('Thanks for installing Yellow Pages Reviews!','ypr'); ?></h3>
					<?php printf(__('<a href="%1$s" class="dismiss"></a>'), '?ypr_nag_ignore=0'); ?>
				</header>
				<div class="ypr-actions">
					<div class="ypr-action">
						<a href="<?php echo admin_url(); ?>options-general.php?page=yellowpagesreviews">
							<span class="dashicons dashicons-admin-settings"></span><?php _e('Go to Settings','ypr'); ?>
						</a>
					</div>

					<div class="ypr-action">
						<a href="<?php echo admin_url(); ?>widgets.php">
							<span class="dashicons dashicons-admin-generic"></span><?php _e('Add a Yellow Pages Widget','ypr'); ?>
						</a>
					</div>

					<div class="ypr-action mailchimp">
						<div id="mc_embed_signup">
							<span class="dashicons dashicons-edit"></span>
							<form action="//wordimpress.us3.list-manage.com/subscribe/post?u=3ccb75d68bda4381e2f45794c&amp;id=cf1af2563c" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
								<div class="mc-field-group">
									<p><small><?php _e('Get notified of plugin updates:','yrp'); ?></small></p>
									<input type="text" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="my.email@wordpress.com">
									<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="submit-button">
									<input type="hidden" id="group_4" name="group[13857]" value="4" checked="checked">
								</div>
								<div id="mce-responses" class="clear">
									<div class="response" id="mce-error-response" style="display:none"></div>
									<div class="response" id="mce-success-response" style="display:none"></div>
								</div>
								<div style="position: absolute; left: -5000px;">
									<input type="text" name="b_3ccb75d68bda4381e2f45794c_83609e2883" value="">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php
		}
	}
}

add_action('admin_init', 'ypr_nag_ignore');

function ypr_nag_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	/* If user clicks to ignore the notice, add that to their user meta */
	if ( isset($_GET['ypr_nag_ignore']) && '0' == $_GET['ypr_nag_ignore'] ) {
		add_user_meta($user_id, 'ypr_activation_ignore_notice', 'true', true);
	}
}