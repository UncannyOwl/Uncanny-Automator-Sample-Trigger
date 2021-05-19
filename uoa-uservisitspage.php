<?php

use Uncanny_Automator\Recipe;

/**
 * Class UOA_USERVISITSPAGE
 */
class UOA_USERVISITSPAGE {
	use Recipe\Triggers;

	/**
	 * UOA_USERVISITSPAGE constructor.
	 */
	public function __construct() {
		$this->setup_trigger();
	}

	/**
	 *
	 */
	protected function setup_trigger() {

		$this->set_integration( 'UOA' ); // Display this trigger under UOA integration

		$this->set_trigger_code( 'USERVISITSPAGE' ); // Unique Trigger code

		$this->set_trigger_meta( 'WPPAGE' ); // Re-useable meta, selectable value in blue boxes

		/* Translators: Some information for translators */
		$this->set_sentence( sprintf( 'My site user views {{a page:%1$s}}', $this->get_trigger_meta() ) ); // Sentence to appear when trigger is added. {{a page:%1$s}} will be presented in blue box as selectable value

		/* Translators: Some information for translators */
		$this->set_readable_sentence( 'My site user views {{a page}}' ); // Non-active state sentence to show

		$this->add_action( 'template_redirect', 20, 1 ); // which do_action() fires this trigger

		$options = array(
			Automator()->helpers->recipe->wp->options->all_pages(), // Add your options for this trigger
		);

		$this->set_options( $options ); // Adding options so that {{a page:%1$s}} could display it in Recipe UI

		$this->register_trigger(); // Registering this trigger
	}


	/**
	 * @return bool
	 */
	public function validate_trigger(): bool {

		if ( ! is_page() && ! is_archive() ) {
			return false;
		}

		return true;
	}

	/**
	 *
	 */
	protected function prepare_to_run() {
		// Set Post ID here.
		global $post;
		$this->set_post_id( $post->ID );

	}
}