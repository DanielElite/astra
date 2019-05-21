<?php
/**
 * Styling Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2019, Astra
 * @link        https://wpastra.com/
 * @since       1.4.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Footer_Colors_Configs' ) ) {

	/**
	 * Register Footer Color Configurations.
	 */
	class Astra_Footer_Colors_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Footer Color Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {
			$_configs = array(

				/**
				 * Option: Color
				 */
				array(
					'name'     => 'footer-color',
					'type'     => 'sub-control',
					'tab'      => __( 'Normal', 'astra-addon' ),
					'priority' => 5,
					'parent'   => ASTRA_THEME_SETTINGS . '[footer-bar-content-group]',
					'control'  => 'ast-color',
					'title'    => __( 'Text Color', 'astra' ),
					'default'  => '',
				),

				/**
				 * Option: Link Color
				 */
				array(
					'name'     => 'footer-link-color',
					'type'     => 'sub-control',
					'tab'      => __( 'Normal', 'astra-addon' ),
					'priority' => 6,
					'parent'   => ASTRA_THEME_SETTINGS . '[footer-bar-content-group]',
					'control'  => 'ast-color',
					'default'  => '',
					'title'    => __( 'Link Color', 'astra' ),
				),

				/**
				 * Option: Link Hover Color
				 */
				array(
					'name'     => 'footer-link-h-color',
					'type'     => 'sub-control',
					'tab'      => __( 'Hover', 'astra-addon' ),
					'priority' => 5,
					'parent'   => ASTRA_THEME_SETTINGS . '[footer-bar-content-group]',
					'control'  => 'ast-color',
					'title'    => __( 'Link Hover Color', 'astra' ),
					'default'  => '',
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[divider-footer-image]',
					'type'     => 'control',
					'control'  => 'ast-divider',
					'required' => array( ASTRA_THEME_SETTINGS . '[footer-sml-layout]', '!=', 'disabled' ),
					'section'  => 'section-colors-footer',
					'settings' => array(),
				),

				/**
				 * Option: Footer Background
				 */
				array(
					'name'     => 'footer-bg-obj',
					'type'     => 'sub-control',
					'priority' => 7,
					'parent'   => ASTRA_THEME_SETTINGS . '[footer-bar-background-group]',
					'control'  => 'ast-background',
					'default'  => astra_get_option( 'footer-bg-obj' ),
					'title'    => __( 'Background', 'astra' ),
				),

			);

			$configurations = array_merge( $configurations, $_configs );

			return $configurations;
		}
	}
}

new Astra_Footer_Colors_Configs;


