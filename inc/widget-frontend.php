<?php
/**
 *  Widget Frontend Display
 *
 * @description: Responsible for the frontend display of the Yellow Pages Reviews
 * @since      : 1.0
 */

?>
	<div class="ypr-<?php echo sanitize_title( $widget_style ); ?>">

		<?php

		//Business Information
		if ( $hide_header !== '1' ) {
			?>

			<div class="ypr-business-header ypr-clearfix">


				<div class="ypr-business-avatar" style="background-image: url(<?php echo YPR_PLUGIN_URL, '/assets/images/yp_logo.png' ?>)"></div>

				<div class="ypr-header-content-wrap ypr-clearfix">
					<?php
					//Handle website link
					$website = ! empty( $response['listingsDetails']['listingDetail']['websiteURL'] ) ? esc_url( $response['listingsDetails']['listingDetail']['websiteURL'] ) : '';
					$yp_page = ! empty( $response['listingsDetails']['listingDetail']['moreInfoURL'] ) ? $response['listingsDetails']['listingDetail']['moreInfoURL'] : '';
					//use the location's YP page since they have no website
					if ( empty( $website ) ) {
						$website = ! empty( $yp_page ) ? esc_url( $yp_page ) : '#';
					}
					?>

					<span class="ypr-business-name"><a href="<?php echo $website; ?>" title="<?php echo $response['listingsDetails']['listingDetail']['businessName']; ?>" <?php echo $target_blank . $no_follow; ?>><span><?php echo $response['listingsDetails']['listingDetail']['businessName']; ?></span></a></span>

					<?php
					//Overall Reviews
					$overall_rating = isset( $response['metaProperties']['rating'] ) ? $response['metaProperties']['rating'] : '';
					if ( ! empty( $overall_rating ) ) {
						echo $this->get_star_rating( $overall_rating, null, $hide_out_of_rating );
					} else {
						?>
						<span class="no-reviews"><?php echo sprintf( __( '<a href="%1$s" class="leave-review" target="_blank">Write a review</a>', 'ypr' ), esc_url( $yp_page ) ); ?></span>

					<?php } ?>

				</div>
			</div>

		<?php } ?>


		<?php
		//Yellow Pages Reviews
		if ( isset( $response['reviews'] ) && ! empty( $response['reviews'] ) ) {
			?>

			<div class="ypr-reviews-wrap">
				<?php
				$reviews_array = isset( $response['reviews']['review'] ) ? $response['reviews']['review'] : '';

				//Ensure our data is in an array
				if ( ! is_array( $reviews_array ) ) {
					$reviews_array = array( $response['reviews']['review'] );
				}

				//Account for only one review
				if ( ! isset( $reviews_array[0]['rating'] ) ) {
					$reviews_array    = '';
					$reviews_array[0] = isset( $response['reviews']['review'] ) ? $response['reviews']['review'] : '';
				}

				$counter      = 0;
				$review_limit = isset( $review_limit ) ? $review_limit : 3;

				//Loop Google Places reviews
				foreach ( $reviews_array as $review ) {

					//Set review vars
					$author_name    = isset( $review['reviewer'] ) ? $review['reviewer'] : 'John Doe';
					$overall_rating = isset( $review['rating'] ) ? $review['rating'] : '5';
					$review_subject = isset( $review['reviewSubject'] ) ? $review['reviewSubject'] : '';
					$review_text    = isset( $review['reviewBody'] ) ? $review['reviewBody'] : __( 'No Review Text...', 'ypr' );
					$time           = isset( $review['reviewDate'] ) ? strtotime( $review['reviewDate'] ) : __( 'No Date for Review', 'ypr' );
					$avatar         = isset( $review['avatar'] ) ? $review['avatar'] : YPR_PLUGIN_URL . '/assets/images/mystery-man.png';
					$counter ++;


					//Review filter set OR count limit reached?
					if ( $overall_rating >= $review_filter && $counter <= $review_limit ) {
						?>

						<div class="ypr-review">

							<div class="ypr-review-header ypr-clearfix">
								<div class="ypr-review-avatar">
									<img src="<?php echo $avatar; ?>" alt="<?php echo $author_name; ?>" title="<?php echo $author_name; ?>" />
								</div>
								<div class="ypr-review-info ypr-clearfix">

									<span class="ypr-reviewer-name"><?php echo $author_name; ?></span>

									<?php echo $this->get_star_rating( $overall_rating, $time, $hide_out_of_rating ); ?>
								</div>

							</div>

							<div class="ypr-review-content">
								<?php echo wpautop( $review_text ); ?>
							</div>

						</div><!--/.ypr-review -->
					<?php } //endif review filter ?>

				<?php } //end review loop	?>


			</div><!--/.ypr-reviews -->

		<?php
		} else { //No review for this biz
			?>
			<div class="ypr-no-reviews-wrap">
				<p class="no-reviews"><?php echo sprintf( __( 'There are no reviews yet for this business. <a href="%1$s" class="leave-review" target="_blank">Be the first to review</a>', 'ypr' ), esc_url( $yp_page ) ); ?></p>
			</div>

		<?php
		} //end review if
		?>


	</div>


<?php
//after widget
echo ! empty( $after_widget ) ? $after_widget : '</div>';

if ( YPR_DEBUG ) {
	echo "<pre>";
	var_dump( $response );
	echo "</pre>";
}
