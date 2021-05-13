<?php

use Uncanny_Automator\Recipe;

/**
 * Class UOA_RECIPENOTCOMPLETED
 */
class UOA_RECIPENOTCOMPLETED {
	use Recipe\Triggers;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->setup_trigger();
		add_action( 'automator_before_trigger_completed', array( $this, 'save_token_values' ), 10, 2 );
	}

	/**
	 *
	 */
	protected function setup_trigger() {
		$this->set_integration( 'UOA' );
		$this->set_trigger_code( 'UOARECIPESNOTCOMPLETED' );
		$this->set_trigger_meta( 'UOARECIPE' );
		/* Translators: Some information for translators */
		$this->set_sentence( sprintf( esc_attr__( '{{A recipe:%1$s}} is not completed', 'uncanny-automator' ), $this->get_trigger_meta() ) );
		/* Translators: Some information for translators */
		$this->set_readable_sentence( esc_attr__( '{{A recipe}} is not completed', 'uncanny-automator' ) );

		$this->add_action( 'automator_recipe_completed_with_errors', 90, 4 );

		$options = array(
			Automator()->helpers->recipe->uncanny_automator->options->get_recipes(),
		);

		$this->set_options( $options );

		$this->register_trigger();
	}

	/**
	 * @param $args
	 *
	 * @return array
	 */
	protected function do_action_args( $args ) {
		return array(
			'recipe_id'     => $args[0],
			'user_id'       => $args[1],
			'recipe_log_id' => $args[2],
			'args'          => $args[3],
		);
	}

	/**
	 * @param ...$args
	 */
	protected function validate_trigger( ...$args ) {
		$recipe_log_id = absint( $args['recipe_log_id'] );
		global $wpdb;
		// get recipe actions
		$table_name = $wpdb->prefix . Automator()->db->tables->action;
		$errors     = $wpdb->get_results( $wpdb->prepare( "SELECT automator_action_id FROM $table_name WHERE automator_recipe_log_id = {$recipe_log_id} AND error_message != ''" ) ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( empty( $errors ) ) {
			// bail early
			return false;
		}

		return true;
	}

	/**
	 * @param mixed ...$args
	 */
	protected function prepare_to_run( ...$args ) {
		$recipe_id = absint( $args['recipe_id'] );
		$this->set_post_id( $recipe_id );
	}

	/**
	 * @param $args
	 * @param $object
	 */
	public function save_token_values( $args, $object ) {
		$recipe_id = absint( $args['trigger_args']['recipe_id'] );
		$recipe    = get_post( $recipe_id );
		if ( ! $recipe instanceof \WP_Post ) {
			return;
		}
		$user_id       = absint( $args['trigger_args']['user_id'] );
		$trigger_entry = $args['trigger_entry'];
		Automator()->db->trigger->add_token_meta( 'UOARECIPES_recipe_id', $recipe_id, $trigger_entry );
		Automator()->db->trigger->add_token_meta( 'UOARECIPES_recipe_title', $recipe->post_title, $trigger_entry );
		Automator()->db->trigger->add_token_meta( 'UOARECIPES_recipe_edit_link', get_edit_post_link( $recipe->ID ), $trigger_entry );
		Automator()->db->trigger->add_token_meta( 'UOARECIPES_recipe_log_url', "recipe_id=$recipe_id&user_id=$user_id", $trigger_entry );
		Automator()->db->trigger->add_token_meta( 'UOARECIPES_action_log_url', "recipe_id=$recipe_id&user_id=$user_id", $trigger_entry );
	}
}