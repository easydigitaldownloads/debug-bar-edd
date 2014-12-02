<?php
/**
 * Plugin Name: Debug Bar - Easy Digital Downloads
 * Description: Helps debug various aspects of Easy Digital Downloads
 * Version: 0.1
 * Author: Pippin Williamson
 * Author URI: http://easydigitaldownloads.com
 * License: GPLv2 or later
 */

class Debug_Bar_EDD {

	function __construct() {
		add_filter( 'debug_bar_panels', array( $this, 'debug_bar_panels' ), 9000 );
	}

	function debug_bar_panels( $panels ) {

		require_once( dirname( __FILE__ ) . '/class-debug-bar-edd-cart-panel.php' );
		require_once( dirname( __FILE__ ) . '/class-debug-bar-edd-session-panel.php' );
		
		$cart_panel = new Debug_Bar_EDD_Cart_Panel( 'EDD Cart' );
		$cart_panel->set_callback( array( $this, 'cart_panel_callback' ) );

		$session_panel = new Debug_Bar_EDD_Session_Panel( 'EDD Sessions' );
		$session_panel->set_callback( array( $this, 'session_panel_callback' ) );
		
		$panels[] = $cart_panel;
		$panels[] = $session_panel;
		
		return $panels;
	}

	function cart_panel_callback() {

		$cart_details = edd_get_cart_content_details();

		if( $cart_details ) {

			echo '<h3>' . __( 'Cart Items', 'edd_debug_bar' ) . '</h3>';
			echo '<table style="width:100%;border-collapse: collapse; border-spacing: 0;">';

				echo '<tr>';

					echo '<th>' . __( 'ID', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Name', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Item Price', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Item Options', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Quantity', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Discount', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Subtotal', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Tax', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Fees', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Total Price', 'edd_debug_bar' ) . '</th>';

				echo '</tr>';

				$i = 0;
				foreach( $cart_details as $item ) {

					$alternate = $i % 2 == 0 ? 'alternate' : '';

					echo '<tr class="' . $alternate . '">';

						echo '<td>' . $item['id'] . '</td>';
						echo '<td>' . $item['name'] . '</td>';
						echo '<td>' . $item['item_price'] . '</td>';
						echo '<td>' . print_r( $item['item_number']['options'], true ) . '</td>';
						echo '<td>' . $item['quantity'] . '</td>';
						echo '<td>' . $item['discount'] . '</td>';
						echo '<td>' . $item['subtotal'] . '</td>';
						echo '<td>' . $item['tax'] . '</td>';
						echo '<td>' . print_r( $item['fees'], true ) . '</td>';
						echo '<td>' . $item['price'] . '</td>';
						
					echo '</tr>';

					$i++;

				}

			echo '</table>';

			echo '<h3>' . __( 'Cart Totals', 'edd_debug_bar' ) . '</h3>';
			echo '<table style="width:100%;border-collapse: collapse; border-spacing: 0;">';

				echo '<tr>';

					echo '<th>' . __( 'Subtotal', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Discounts', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Taxes', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Fees', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Total', 'edd_debug_bar' ) . '</th>';
					
				echo '</tr>';

				echo '<tr>';

					echo '<td>' . edd_get_cart_subtotal() . '</td>';
					echo '<td>' . edd_get_cart_discounted_amount() . '</td>';
					echo '<td>' . edd_get_cart_tax() . '</td>';
					echo '<td>' . edd_get_cart_fee_total() . '</td>';
					echo '<td>' . edd_get_cart_total() . '</td>';
					
				echo '</tr>';
			
			echo '</table>';

		} else {
			echo '<strong>' . __( 'Cart is empty', 'edd_debug_bar' ) . ' </strong>';
		}

	}

	function session_panel_callback() {


		if( EDD()->session->use_php_sessions() ) {
			$session = isset( $_SESSION['edd'] ) && is_array( $_SESSION['edd'] ) ? $_SESSION['edd'] : array();
		} else {
			$session = WP_Session::get_instance();
		}

		if( $session ) {

			echo '<h3>' . __( 'Session Contents', 'edd_debug_bar' ) . '</h3>';
			echo '<table style="width:100%;border-collapse: collapse; border-spacing: 0;">';

				echo '<tr>';

					echo '<th>' . __( 'Key', 'edd_debug_bar' ) . '</th>';
					echo '<th>' . __( 'Value', 'edd_debug_bar' ) . '</th>';

				echo '</tr>';

				$i = 0;
				foreach( $session as $key => $value ) {

					$alternate = $i % 2 == 0 ? 'alternate' : '';

					echo '<tr class="' . $alternate . '">';

						echo '<td>' . $key . '</td>';

						$value = maybe_unserialize( $value );

						if( is_array( $value ) || is_object( $value ) ) {

							echo '<td>' . print_r( $value, true ) . '</td>';						
							
						} else {

							echo '<td>' . $value . '</td>';
	
						}
						
					echo '</tr>';

					$i++;

				}

			echo '</table>';

		} else {
			echo '<strong>' . __( 'No session data stored', 'edd_debug_bar' ) . ' </strong>';
		}

	}

}
new Debug_Bar_EDD;