<?php
/*
Plugin Name: Movies API
Description: Displays movies as a frontpage using JSON API
Version:     0.1
Author:      Federico Rabinovich
License:     GPL2
*/

//------------------------------------------------------- Setup -----------------------
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
load_plugin_textdomain('movies-api', false, basename( dirname( __FILE__ ) ) . '/languages' );


// Defines endpoint location: www.example.com/wp-json/[MOVIES_API_JSON_API_PATH][MOVIES_API_JSON_API_RESOURCE]

define( "MOVIES_API_JSON_API_PATH",     'movies-api' );
define( "MOVIES_API_JSON_API_RESOURCE",  '/movies.json' );

// This plugin folder path

define( "MOVIES_API_PLUGIN_FOLDER",  plugin_dir_url(__FILE__) );


// This following file is where actual magic happens

require_once( 'includes/class.movies-api.php');
$movies_api = new moviesapi;


// Adds Custom fields (year, rating) meta box

if ( is_admin() ) {

	require_once( 'includes/class.movies-api-movie-meta-box.php' );
	$movies_api_movie_meta_box = new movies_api_movie_meta_box;

}
	