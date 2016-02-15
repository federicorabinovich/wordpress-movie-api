=== Movies API ===
Contributors: Federico Rabinovich
Tags: movies, api
Requires at least: 4.4
Tested up to: 4.4.2
Stable tag: 4.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin displays movies as a frontpage using JSON API.

== Description ==

Overview of task

Create a plugin able to do the following tasks:

    Create a JSON API from a custom post type, this should be an enpoint like: http://example.dev/movies.json
    Create a Custom Post Type: movies to create new movies and storage the data and meta fields.
    Displays the movies as a frontpage (home page of the site) using the JSON API created in the previous task, here a shortcode can be used like `[list-movies]' to display the data on the front page.

Data / Specification

    Custom Post Type: Movie
    Fields / Meta Data of CPT
        poster_url: a string to the url of an image associated with that movie
        rating: a number rating / score of the value of that respective movie
        year: date of release
        description: short html description of the movie
    Page should automatically display on home page
    Logic for no movies, etc
    Simple documentation for using the plugin
    API Structure should look like:

{
  data: [
     {
        id: 1
        title: 'UP'
        poster_url: 'http://localhost.dev/images/up.jpg',
        rating: 5,
        year: 2010
        short_description: 'Phasellus ultrices nulla quis nibh. Quisque a lectus',
     },
     {
        id: 2
        title: 'Avatar'
        poster_url: 'http://localhost.dev/images/avatar.jpg',
        rating: 3,
        year: 2012
        short_description: 'Phasellus ultrices nulla quis nibh. Quisque a lectus',
     }
  ]
}

Bonus for

    Angular or other SPA frameworks for displaying movies
    Caching of the API for movies (cleared upon adding new movie)
    Fancy UI Effects / Animations / Etc.
    Follow WordPress coding standards
    PHP Unit tests
    TravisCI or Circle CI integration
    Object Oriented Programming


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/movies-api` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Movies screen to add movies
4. Paste following code in pages where you want to display movies: <?php echo do_shortcode("[list-movies]"); ?>
5. Deactivate from plugins' menu when you don't want to use it anymore 



== Frequently Asked Questions ==




== Screenshots ==


== Changelog ==

= 0.1 =
* This is the first version