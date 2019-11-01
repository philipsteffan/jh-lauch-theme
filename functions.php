<?php
/**
 * Lauch functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Lauch
 */

if ( ! function_exists( 'lauch_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function lauch_setup() {
  /*
   * Make theme available for translation.
   * Translations can be filed in the /languages/ directory.
   * If you're building a theme based on Lauch, use a find and replace
   * to change 'lauch' to the name of your theme in all the template files.
   */
  load_theme_textdomain( 'lauch', get_template_directory() . '/languages' );

  // Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

  /*
   * Let WordPress manage the document title.
   * By adding theme support, we declare that this theme does not use a
   * hard-coded <title> tag in the document head, and expect WordPress to
   * provide it for us.
   */
  add_theme_support( 'title-tag' );

  /*
   * Enable support for Post Thumbnails on posts and pages.
   *
   * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
   */
  add_theme_support( 'post-thumbnails' );

  // This theme uses wp_nav_menu() in one location.
  register_nav_menus( array(
    'menu-main' => esc_html__( 'Primary', 'lauch' ),
    'menu-sub' => esc_html__( 'Secondary', 'lauch' ),
    'menu-footer-1' => esc_html__('Footer 1', 'lauch'),
    'menu-footer-2' => esc_html__('Footer 2', 'lauch'),
    'menu-footer-3' => esc_html__('Footer 3', 'lauch'),
  ) );

  /*
   * Switch default core markup for search form, comment form, and comments
   * to output valid HTML5.
   */
  add_theme_support( 'html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
  ) );

  // Add theme support for selective refresh for widgets.
  add_theme_support( 'customize-selective-refresh-widgets' );

  /**
   * Add support for core custom logo.
   *
   * @link https://codex.wordpress.org/Theme_Logo
   */
  add_theme_support( 'custom-logo', array(
    'height'      => 250,
    'width'       => 250,
    'flex-width'  => true,
    'flex-height' => true,
  ) );

  function replace_svg_css_class_fill($contents, $oldclass, $newclass, $newcolor) {
    preg_match('/'. $oldclass .'{fill:#([[0-9a-fA-F]+);}/i', $contents, $prev_color);
    $contents = str_replace($oldclass, $newclass, $contents);
    $contents = str_replace('#'.$prev_color[1], $newcolor, $contents);
    return $contents;
  }

  function get_random_illustration() {
    $illustrations = array('wrestler', 'octopus', 'teddy', 'monster', 'elias', 'robot');
    $rand_key = array_rand($illustrations, 1);
    return $illustrations[$rand_key];
  }

  function get_svg_content($svgname) {
    $filename = get_template_directory() . "/images/illustrations/change-". $svgname .".svg";
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    return $contents;
  }

  function get_svg($svgpath) {
    $filename = get_template_directory() . "". $svgpath;
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    return $contents;
  }

  function render_svg($svgpath) {
    echo get_svg($svgpath);
  }

}
endif;
add_action( 'after_setup_theme', 'lauch_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function lauch_content_width() {
  // This variable is intended to be overruled from themes.
  // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $GLOBALS['content_width'] = apply_filters( 'lauch_content_width', 640 );
}
add_action( 'after_setup_theme', 'lauch_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function lauch_widgets_init() {
  register_sidebar( array(
    'name'          => esc_html__( 'Sidebar', 'lauch' ),
    'id'            => 'sidebar-1',
    'description'   => esc_html__( 'Add widgets here.', 'lauch' ),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ) );
}
add_action( 'widgets_init', 'lauch_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function lauch_scripts() {
  wp_enqueue_style( 'lauch-style', get_template_directory_uri() . '/styles/main.min.css');
  wp_enqueue_script( 'lauch-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
  wp_enqueue_script( 'lauch-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

  wp_enqueue_script( 'vue', 'https://cdn.jsdelivr.net/npm/vue@2.5.2/dist/vue.min.js', [], '2.5.2', true);
  wp_enqueue_script( 'vueplayer', get_template_directory_uri() . '/js/vueplayer.js', ['vue'], '0.1.0');

  wp_enqueue_style( 'leaftlet-style', 'https://unpkg.com/leaflet@1.5.1/dist/leaflet.css');
  wp_enqueue_script( 'leaflet-js', 'https://unpkg.com/leaflet@1.5.1/dist/leaflet.js', [], '1.51');

  wp_enqueue_script( 'lauch-revolving-claims', get_template_directory_uri() . '/js/revolving-claims.js', ['vue'], '20190809');
  wp_enqueue_script( 'lauch-main', get_template_directory_uri() . '/js/main.min.js', array(), '20190830', true );

  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
add_action( 'wp_enqueue_scripts', 'lauch_scripts' );


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

require get_template_directory() . '/inc/template-tags.php';
//require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
  require get_template_directory() . '/inc/jetpack.php';
}

/**
 * ALL THE CUSTOM POST TYPES
 */



add_image_size( 'blog-alpaka-small', 300, 300, true );
add_image_size( 'blog-alpaka', 560, 560, true );
add_image_size( 'blog-alpaka-highdpi', 1120, 1120, true );
add_image_size( 'blog-large', 715, 590 );
//add_image_size( 'blog-small', 320, 200, true );
add_image_size( 'blog-large-highdpi', 1430, 1180 );
//add_image_size( 'blog-small-highdpi', 640, 400, true);
add_image_size( 'events-teaser-s', 90, 60, true);
add_image_size( 'events-teaser-m', 170, 120, true);
add_image_size( 'events-teaser-highdpi', 340, 240, true);


add_filter( 'image_size_names_choose', 'lauch_custom_sizes' );
function lauch_custom_sizes( $sizes ) {
  return array_merge( $sizes, array(
    'blog-alpaka' => __( 'Alpaka' ),
    'blog-alpaka-small' => __( 'Alpaka klein' ),
  ) );
}

#function lauch_hide_admin_barr(){ return false; }
#add_filter( 'show_admin_bar' , 'lauch_hide_admin_bar');


function vuevideo_handle_shortcode($atts = '') {
  $value = shortcode_atts( array(
    'location' => null,
    'year' => null,
    'type' => null
  ), $atts );

  $data_str = 'window.v = {}; window.v.location = "'. $value['location'] .'"; ';
  $data_str .= 'window.v.year = "'. $value['year'] .'"; ';
  $data_str .= 'window.v.type = "'. $value['type'] .'";';

  return '<div class="js"><script>'. $data_str .'</script><div id="vuevideo"></div></div><noscript>Aktiviere JavaScript um den Videoplayer zu benutzen</noscript>';
}
add_shortcode('vuevideo', 'vuevideo_handle_shortcode');


function contactperson_handle_shortcode($atts = '') {
  $value = shortcode_atts( array(
    'person' => null,
    'title' => null
  ), $atts );

  $person = get_posts(array( 'post_type' => 'person',
                             'p' => $value['person']))[0];
  $img = get_the_post_thumbnail_url( $person->ID, array(139, 106) );
  $description = get_field('person_description', $person->ID);
  $twitter = get_field('person_twitter', $person->ID);
  $mastodon = get_field('person_mastodon', $person->ID);
  $instagram = get_field('person_instagram', $person->ID);
  $email = get_field('person_email', $person->ID);

  $out = '<div class="c-contact">';
  if ($value['title']) {
    $out .= '<h4 class="c-contact-title">'. $value[title] .'</h4>';
  }
  $out .= '<div class="c-contact-body">';
  $out .= '<img src="'. $img .'" alt="" class="c-contact-image">';
  $out .= '<div class="c-contact-text"><p><strong>'. $person->post_title .'</strong>,<br>'. $description.'</p><p>';

  if ($twitter != "") {
    $out .= '<a href="'. $twitter .'" title="'. _('Bei Twitter', 'lauch') .'">'. get_svg('/images/icons/contact-twitter.svg') .'</a>';
  }
  if ($instagram != "") {
    $out .= '<a href="'. $instagram .'" title="'. _('Bei Instagram', 'lauch') .'">'. get_svg('/images/icons/contact-instagram.svg') .'</a>';
  }
  if ($mastodon != "") {
    $out .= '<a href="'. $mastodon .'" title="'. _('Bei Mastodon', 'lauch') .'">'. get_svg('/images/icons/contact-mastodon.svg') .'</a>';
  }
  if ($email != "") {
    $out .= '<a href=mailto:"'. $email .'" title="'. _('Schreib eine Mail', 'lauch') .'">'. get_svg('/images/icons/contact-mail.svg') .'</a>';
  }

  $out .= '</p></div></div></div>';

  return $out;
}
add_shortcode('contactperson', 'contactperson_handle_shortcode');


require get_template_directory() . '/inc/custom_types/event_type.php';
require get_template_directory() . '/inc/custom_types/lab_type.php';
require get_template_directory() . '/inc/custom_types/exchange_type.php';
require get_template_directory() . '/inc/custom_types/video_type.php';
require get_template_directory() . '/inc/custom_types/project_type.php';
require get_template_directory() . '/inc/custom_types/person_type.php';
require get_template_directory() . '/inc/custom_types/faq_type.php';
require get_template_directory() . '/inc/custom_types/learning_type.php';

require get_template_directory() . '/inc/taxonomies.php';
require get_template_directory() . '/inc/metaboxes.php';

//require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/api_endpoints.php';

//require get_template_directory() . '/blocks/faq/index.php';
