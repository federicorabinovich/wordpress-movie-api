<?php


//------------------------------------------------------- class moviesapi -----------------------

class moviesapi{


	//class constructor

	public function __construct() {
		
		//--------------------Admin section

		if ( is_admin() ){
		

			// Register CPT Movies

			add_action( 'init', array(__CLASS__, 'register_movies') );


			// Removes quick edit

			add_filter( 'post_row_actions', array(__CLASS__, 'remove_quick_edit_movies'), 10, 2);


			// Removes unwanted columns (date) and adds custom columns

			add_filter( 'manage_movie_posts_columns', array(__CLASS__, 'set_custom_edit_movie_columns') );
			add_action( 'manage_movie_posts_custom_column' , array(__CLASS__, 'set_custom_edit_movie_custom_columns'), 10, 2 );


			// Makes custom fields sortable 
			
			add_filter( 'manage_edit-movie_sortable_columns', array(__CLASS__, 'movies_sort') );
			add_filter( 'request', array(__CLASS__, 'movies_columns_orderby') );
			
			
			// Adds instructions for admin

			add_action( 'admin_menu', array(__CLASS__, 'add_instructions_submenu') );


			// Adds activation and deactivation routing flushes 

			register_deactivation_hook( __FILE__, array(__CLASS__, 'plugin_activation_deactivation_func') );
			register_activation_hook( __FILE__, array(__CLASS__, 'plugin_activation_deactivation_func') );
		
		
		}

		//--------------------Front end section

		if ( ! is_admin() ){	//instead of using "else" to improve code readability


			// Adds scripts and styles for front end app
			
			add_action( 'wp_enqueue_scripts',  array(__CLASS__, 'front_end_scripts') );

			
			// Adds shortcode "[list-movies]" for Front end app usage

			add_shortcode( 'list-movies', array(__CLASS__, 'shortcode_list_movies') );


			// Register rest route and get movies list for Json API

			add_action( 'rest_api_init', array(__CLASS__, 'register_movies_json_api') );

		}

	}




	//------------------------- Adds custom post type for Movies
	
	public function register_movies() {
	
		$labels = array(

			'name'               => __( 'Movies', 'movies-api' ),
			'singular_name'      => __( 'Movie', 'movies-api' ),
			'add_new'            => __( 'Add New', 'movies-api' ),
			'add_new_item'       => __( 'Add New Movie', 'movies-api' ),
			'edit_item'          => __( 'Edit Movie', 'movies-api' ),
			'new_item'           => __( 'New Movie', 'movies-api' ),
			'view_item'          => __( 'View Movie', 'movies-api' ),
			'search_items'       => __( 'Search Movies', 'movies-api' ),
			'not_found'          => __( 'No movies found', 'movies-api' ),
			'not_found_in_trash' => __( 'No movies found in Trash', 'movies-api' ), 
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Movies', 'movies-api' )

		);
				
		if ( function_exists( 'members_get_capabilities' ) ) {
	
			$capabilities = array(
		
				'edit_post'          => 'moviesapi_edit_movie',
				'edit_posts'         => 'moviesapi_edit_movies',
				'edit_others_posts'  => 'moviesapi_edit_others_movies',
				'publish_posts'      => 'moviesapi_publish_movies',
				'read_post'          => 'moviesapi_read_movie',
				'read_private_posts' => 'moviesapi_read_private_movie',
				'delete_post'        => 'moviesapi_delete_movie',
				'delete_posts'       => 'moviesapi_delete_movies'

			);
			
			$capabilitytype = 'movie';
			
			$mapmetacap = false;
		
		} else {
		
			$capabilities = array(
		
				'edit_post'          => 'edit_post',
				'edit_posts'         => 'edit_posts',
				'edit_others_posts'  => 'edit_others_posts',
				'publish_posts'      => 'publish_posts',
				'read_post'          => 'read_post',
				'read_private_posts' => 'read_private_posts',
				'delete_post'        => 'delete_post',
				'delete_posts'       => 'delete_posts'

			);
			
			$capabilitytype = 'post';
			
			$mapmetacap = true;
		
		}
		
		$args = array(
	
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-editor-video',
			'capability_type'     => $capabilitytype,
			'capabilities'        => $capabilities,
			'map_meta_cap'        => $mapmetacap,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'thumbnail', 'editor' ),
			'has_archive'         => true,
			'rewrite'             => false,
			'query_var'           => true,
			'can_export'          => false
		
		);
  
		register_post_type( 'movie', $args );
		
	}


	//------------------------- Removes quick edit
	
	public function remove_quick_edit_movies( $actions ) {
		
		global $post;
		
		if( $post->post_type == 'movie' ) {
			unset($actions['inline hide-if-no-js']);
		}
		
		return $actions;
	}


	//------------------------- Following 2 functions remove unwanted columns and adds custom columns

	public function set_custom_edit_movie_columns( $columns ) {

		unset( $columns['date'] );
		$columns['content'] = __( 'Description', 'movies-api' );
		$columns['movie_year'] = __( 'Year', 'movies-api' );
		$columns['movie_rating'] = __( 'Rating (1-5)', 'movies-api' );

		return $columns;
	}


	public function set_custom_edit_movie_custom_columns( $column, $post_id ) {
	
		switch ( $column ) {
			
			case 'content':
				echo wp_trim_words( get_the_content( ), 15 ); 
				break;
			
			case 'movie_year':
				echo get_post_meta( $post_id, 'movie_year', true ); 
				break;
			
			case 'movie_rating':
				echo get_post_meta( $post_id, 'movie_rating', true ); 
				break;
		}
	}


	//------------------------- Following 2 functions make custom fields sortable 

	public function movies_sort($columns) {

		$columns['movie_year'] = 'movie_year';
		$columns['movie_rating'] = 'movie_rating';
		return $columns;
		
	}

	public function movies_columns_orderby( $vars ) {
		
		switch($vars['orderby']){

			case 'movie_year':
				$vars = array_merge( $vars, array(
					'meta_key' => 'movie_year',
					'orderby' => 'meta_value'
				) );
				break;

			case 'movie_rating':
				$vars = array_merge( $vars, array(
					'meta_key' => 'movie_rating',
					'orderby' => 'meta_value'
				) );
				break;
		}
		
		return $vars;
	}


	//------------------------- Following 2 functions add instructions for admin

	public function add_instructions_submenu() {
		
		add_submenu_page( 'edit.php?post_type=movie', __( 'Instructions', 'movies-api' ), __( 'How to use', 'movies-api' ), true,  'instructions', array(__CLASS__, 'show_instructions') );
		
	}

	public function show_instructions(){
		
		?>	
		<h1>Instructions</h1>
		<h3>How to list your movies inside your site</h3>
		<p>Just add the following code to your theme files (php) wherever you want to list your movies</p><br>
		<p><b>&lt;?php echo do_shortcode("[list-movies]"); ?&gt;</b></p>
		<br><br>
		<p>Bear in mind that this plugin requires PHP 5 or later since it makes use of json_encode function among others</p>
		<?php

	}


	//------------------------- Adds activation and deactivation routing flushes 

	public function plugin_activation_deactivation_func() {
        
		flush_rewrite_rules();

	}




	//---------------------------- ADDS FRONT END SCRIPTS

	public function front_end_scripts() {

		
		// JS paths for Angular
		?><script>
			var movies_api_path = "<?php echo MOVIES_API_PLUGIN_FOLDER.'templates/';?>";
			var movies_json_api_full_path = "<?php echo site_url().'/wp-json/'.MOVIES_API_JSON_API_PATH.MOVIES_API_JSON_API_RESOURCE?>";
		</script>
		<?php
		
		
		// Includes styles

		wp_enqueue_style( 'movie-api-bootstrap', MOVIES_API_PLUGIN_FOLDER.'templates/app/assets/css/bootstrap.min.css', false );
		wp_enqueue_style( 'movie-api-animate', MOVIES_API_PLUGIN_FOLDER.'templates/app/assets/css/animate.css', false ); 
		wp_enqueue_style( 'movie-api-custom-css', MOVIES_API_PLUGIN_FOLDER.'templates/app/assets/css/custom-style.css', false ); 
		wp_enqueue_style( 'movie-api-google-fonts', 'https://fonts.googleapis.com/css?family=Lato:400,700', false ); 
		

		// Includes JS
		
		wp_enqueue_script( 'movie-api-angular', MOVIES_API_PLUGIN_FOLDER.'templates/app/libraries/angular.min.js', false ); 
		wp_enqueue_script( 'movie-api-angular-route', MOVIES_API_PLUGIN_FOLDER.'templates/app/libraries/angular-route.min.js', false ); 
		wp_enqueue_script( 'movie-api-angulat-animate', MOVIES_API_PLUGIN_FOLDER.'templates/app/libraries/angular-animate.js', false ); 
		wp_enqueue_script( 'movie-api-scriptjs', MOVIES_API_PLUGIN_FOLDER.'templates/app/libraries/script.js', false ); 
		wp_enqueue_script( 'movie-api-sanitize', MOVIES_API_PLUGIN_FOLDER.'templates/app/libraries/sanitize.min.js', false ); 


		// Includes specific Angular app Modules

		wp_enqueue_script( 'movie-api-angular-app-main', MOVIES_API_PLUGIN_FOLDER.'templates/app/app.js', false ); 
		wp_enqueue_script( 'movie-api-angular-app-lazy-loader', MOVIES_API_PLUGIN_FOLDER.'templates/app/lazy.js', false ); 


		// Includes specific Angular app Services

		wp_enqueue_script( 'movie-api-angular-app-fetchJson', MOVIES_API_PLUGIN_FOLDER.'templates/app/services/FetchJson.js', false ); 
		

		// Includes specific Angular app Directives

		wp_enqueue_script( 'movie-api-angular-app-movieFile', MOVIES_API_PLUGIN_FOLDER.'templates/app/directives/movieFile.js', false ); 
		
	}

	
	//---------------------------- Adds Shortcode for [list-movies]

	public function shortcode_list_movies( $atts ){

		ob_start();
		
		?>	
		<div ng-app="app">
			<div class="container" ng-view></div>
		</div>
		<?php

		return ob_get_clean();
		
	}


	//---------------------------- Register rest route

	public function register_movies_json_api(){
		register_rest_route( MOVIES_API_JSON_API_PATH, MOVIES_API_JSON_API_RESOURCE, array(
			array(
				'methods'         => \WP_REST_Server::READABLE,
				'callback'        => array( __CLASS__, 'get_movies_json' )
			),
		));
	}

	
	//---------------------------- Get movies list for Json API

	public function get_movies_json(){

		header( 'Content-Type: application/json' );

		$query = new WP_Query( 'post_type=movie' );
		$arr_movies_to_encode['data'] = null;
		$items = 0;
		
		if( $query->have_posts() ) {
		  while ( $query->have_posts() ) : $query->the_post(); 	  

			$poster_url = null;
			$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
			$poster_url = wp_get_attachment_url( $post_thumbnail_id );
			if ( ! $poster_url ) $poster_url = null;
							
			$arr_movies_to_encode['data'][$items] = array(
				'id'				=> get_the_id(),
				'title'				=> get_the_title(),
				'poster_url'		=> $poster_url,
				'rating'			=> ( int ) get_post_meta( get_the_id(), 'movie_rating', true ),
				'year'				=> ( int ) get_post_meta( get_the_id(), 'movie_year', true ),
				'short_description'	=> wp_trim_words( get_the_content( ), 50 ) 
			);
			$items++;

		  endwhile;
		}
		
		echo json_encode( $arr_movies_to_encode );

		wp_reset_query();  
		exit;
	}
	
}