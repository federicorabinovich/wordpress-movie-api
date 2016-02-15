<?php
/*
This class adds custom fields:
	Rating
	Year
to custom post type Movies
*/

class movies_api_movie_meta_box {

	//class constructor

	public function __construct() {

		if ( is_admin() ) {
			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}

	}

	public function init_metabox() {

		add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
		add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );

	}

	public function add_metabox() {

		add_meta_box(
			'movie_info',
			__( 'Movie Info', 'movies-api' ),
			array( $this, 'render_metabox' ),
			'movie',
			'normal',
			'default'
		);

	}

	public function render_metabox( $post ) {

		// Add nonce for security and authentication.

		wp_nonce_field( 'movie_nonce_action', 'movie_nonce' );


		// Retrieve an existing value from the database.

		$movie_year = get_post_meta( $post->ID, 'movie_year', true );
		$movie_rating = get_post_meta( $post->ID, 'movie_rating', true );


		// Set default values.

		if( empty( $movie_year ) ) $movie_year = '';
		if( empty( $movie_rating ) ) $movie_rating = '';


		// Form fields.

		echo '<table class="form-table">';

		echo '	<tr>';
		echo '		<th><label for="movie_year" class="movie_year_label">' . __( 'Year', 'movies-api' ) . '</label></th>';
		echo '		<td>';
		echo '			<input type="text" id="movie_year" name="movie_year" class="movie_year_field" placeholder="' . esc_attr__( 'Relase year', 'movies-api' ) . '" value="' . esc_attr__( $movie_year ) . '">';
		echo '		</td>';
		echo '	</tr>';

		echo '	<tr>';
		echo '		<th><label for="movie_rating" class="movie_rating_label">' . __( 'Rating (1-5)', 'movies-api' ) . '</label></th>';
		echo '		<td>';
		echo '			<select id="movie_rating" name="movie_rating" class="movie_rating_field">';
		for ( $value = 1 ; $value <= 5; $value ++ ){
			$selected = '';
			if ( $value == $movie_rating ) $selected = ' selected';
			echo '				<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
		}
		echo '			</select>';
		echo '		</td>';
		echo '	</tr>';

		echo '</table>';

	}

	public function save_metabox( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce_name   = $_POST['movie_nonce'];
		$nonce_action = 'movie_nonce_action';

		// Check if a nonce is set.
		if ( ! isset( $nonce_name ) )
			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )
			return;

		// Sanitize user input.
		$movie_new_year = isset( $_POST[ 'movie_year' ] ) ? sanitize_text_field( $_POST[ 'movie_year' ] ) : '';
		$movie_new_rating = isset( $_POST[ 'movie_rating' ] ) ? sanitize_text_field( $_POST[ 'movie_rating' ] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, 'movie_year', $movie_new_year );
		update_post_meta( $post_id, 'movie_rating', $movie_new_rating );

	}

}