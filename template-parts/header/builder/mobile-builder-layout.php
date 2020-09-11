<?php
/**
 * Template part for displaying the Mobile Header
 *
 * @package Astra Builder
 */

$mobile_header_type = astra_get_option( 'mobile-header-type' );
?>

<div id="ast-mobile-header" class="ast-mobile-header-wrap " data-type="<?php echo esc_attr( $mobile_header_type ); ?>">
	<?php do_action( 'astra_mobile_header_bar_top' ); ?>

	<div class="ast-above-header-wrap">
		<?php
		/**
		 * Astra Top Header
		 */
		do_action( 'astra_mobile_above_header' );
		?>
	</div>

	<div class="main-header-bar-wrap">
		<?php
		/**
		 * Astra Main Header
		 */
		do_action( 'astra_mobile_primary_header' );
		?>
	</div>

	<div class="ast-below-header-wrap">
		<?php
		/**
		 * Astra Mobile Bottom Header
		 */
		do_action( 'astra_mobile_below_header' );

		?>
	</div>
	<?php astra_main_header_bar_bottom(); ?>

	<div class="ast-mobile-header-content">
		<?php do_action( 'astra_mobile_header_content', 'popup', 'content' ); ?>
	</div>
</div>
