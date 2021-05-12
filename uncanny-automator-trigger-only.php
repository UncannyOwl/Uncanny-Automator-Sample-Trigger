<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * @wordpress-plugin
 * Plugin Name:       Uncanny Automator Sample Trigger
 * Plugin URI:        https://www.automatorplugin.com
 * Description:       Sample trigger for Uncanny Automator
 * Version:           1.0.0
 * Author:            Uncanny Automator
 * Author URI:        https://www.automatorplugin.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */
class Uncanny_Automator_Trigger_Only {
	/**
	 * Uncanny_Automator_Trigger_Only constructor.
	 */
	public function __construct() {
		add_filter( 'automator_integrations_setup', array( $this, 'load_triggers' ) );
	}

	/**
	 * @param $integrations
	 *
	 * @return array|mixed
	 */
	public function load_triggers( $integrations ) {
		// Let's find integration by name so that trigger can be added it's list.
		$add_to_integration = automator_get_integration_by_name( 'Uncanny Automator' );
		if ( empty( $add_to_integration ) ) {
			return $integrations;
		}
		$trigger = __DIR__ . '/uoa-recipenotcompleted.php';

		return automator_add_trigger( $integrations, $trigger, $add_to_integration );
	}
}

new Uncanny_Automator_Trigger_Only();
