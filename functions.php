<?php
// Child theme stylesheet laden
add_action('wp_enqueue_scripts', 'herenboeren_hello_child_enqueue_styles');
function herenboeren_hello_child_enqueue_styles() {

    wp_enqueue_style(
        'hello-elementor-child-style',
        get_stylesheet_uri(),
        array('hello-elementor-theme-style'),
        wp_get_theme()->get('Version')
    );

}

// Dashboard widget toevoegen
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
function my_custom_dashboard_widgets() {
    wp_add_dashboard_widget('custom_help_widget', 'For The Better Support', 'custom_dashboard_help');
}

function custom_dashboard_help() {
    echo '<p>Hulp nodig? Neem contact met ons op via: <a href="mailto:service@herenboeren.nl">service@herenboeren.nl</a>.</p>';
}

// WP SEO Dashboard overzicht verwijderen
add_action('wp_dashboard_setup', 'remove_wpseo_dashboard_overview');
function remove_wpseo_dashboard_overview() {
    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'side');
}

// Favicon tonen in editor
add_action('wp_head', 'ah_blog_favicon');
function ah_blog_favicon() {
    echo '<link rel="Shortcut Icon" type="image/x-icon" href="' . get_bloginfo('wpurl') . '/favicon.ico" />';
}

// Elementor knop verbergen in editor
add_action('admin_head', 'remove_editor_button');
function remove_editor_button() {
    echo '<style>
        #elementor-switch-mode .elementor-switch-mode-on { display: none; }
        body.elementor-editor-active #elementor-switch-mode-button { visibility: hidden; }
        body.elementor-editor-active #elementor-switch-mode-button:hover { visibility: hidden; }
    </style>';
}

// Elementor menu verbergen voor editors op bepaalde pagina's
add_action('wp_head', 'hide_edit_elementor', 100);
function hide_edit_elementor() {
    $user = wp_get_current_user();
    if (in_array('editor', (array)$user->roles) && !is_admin()) {
        echo '<style>#wp-admin-bar-elementor_edit_page > .ab-sub-wrapper { display: none !important; }</style>';
        if (is_archive() || is_home() || is_single()) {
            echo '<style>#wp-admin-bar-elementor_edit_page { display: none !important; }</style>';
        }
    }
}

// Sta editors toe alleen 'editor'-rollen toe te voegen
add_action('user_register', 'allow_editor_add_users');
function allow_editor_add_users($user_id) {
    if (current_user_can('editor')) {
        $user = new WP_User($user_id);
        $user->set_role('editor');
    }
}

// Custom Logo
// Zorg dat Elementor geladen is en de widget kan worden geregistreerd
add_action('elementor/widgets/widgets_registered', 'register_custom_site_title_widget');

function register_custom_site_title_widget() {

    // Check of Elementor class is geladen
    if (!class_exists('Elementor\Widget_Base')) {
        return;
    }

    // Definieer de custom widget class
    class Elementor_Site_Title_Widget extends \Elementor\Widget_Base {

        public function get_name() {
            return 'custom_site_title_widget';
        }

        public function get_title() {
            return __('Herenboeren Site Title', 'text-domain');
        }

        public function get_icon() {
            return 'eicon-site-title';
        }

        public function get_categories() {
            return ['general'];
        }

        // Maak de widgetinstellingen
        protected function _register_controls() {
            $this->start_controls_section(
                'site_title_section',
                [
                    'label' => __('Herenboeren Logo Instellingen', 'text-domain'),
                ]
            );

            $this->add_control(
                'farm_image',
                [
                    'label' => __('Aangepast Logo', 'text-domain'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'default' => [
                        'url' => '',
                    ],
                ]
            );

            $this->end_controls_section();
        }

        // Render de widget
        protected function render() {
            $settings = $this->get_settings_for_display();
            $farm_image_url = $settings['farm_image']['url'] ?? '';
            $fallback_image_url = site_url('/wp-content/uploads/2025/02/cropped-favicon-herenboeren.png');
            $image_to_display = $farm_image_url ?: $fallback_image_url;

            $site_title = get_bloginfo('name');
            $site_tagline = get_bloginfo('description');

            // Fallbacks instellen
            if (empty($site_title)) {
                $site_title = 'Herenboeren Nederland';
            }

            if (empty($site_tagline)) {
                $site_tagline = 'Samen duurzaam voedsel produceren';
            }

            // Titel opschonen
            $cleaned_site_title = str_replace('Herenboeren ', '', $site_title);

            $homepage_url = esc_url(home_url('/'));


            echo '<style>
.custom-site-title-widget {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-family: "Montserrat", sans-serif;
  text-transform: uppercase;
  line-height: 1.1em;
  font-size: 12px;
  flex-wrap: wrap;
}

.custom-site-title-widget img {
  width: 45px;
  height: 45px;
  object-fit: cover;
  margin-right: 4px;
}

.custom-site-title-widget .text-container {
  display: flex;
  flex-direction: column;
  width: calc(100% - 55px); /* ruimte over naast de afbeelding */
  white-space: nowrap;
}

.custom-site-title-widget h2,
.custom-site-title-widget p {
  margin: 0;
  line-height: 1.1em;
  color: #612012;
}

.custom-site-title-widget .heren {
  font-weight: 400;
  font-size: 1.4em;
}

.custom-site-title-widget .boeren {
  font-weight: 600;
  font-size:1.4em;
}

.custom-site-title-widget .boerderij-naam {
  font-weight: 600;
  font-size: 1.4em;
}

.custom-site-title-widget .site-tagline {
  font-size: 0.6em;
  font-weight: 400;
}

/* 📱 Mobiel */
@media (max-width: 767px) {
  .custom-site-title-widget {
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
  }

  .custom-site-title-widget .text-container {
    width: 100%;
    white-space: normal;
  }

  .custom-site-title-widget img {
  width: 30px;
  height: 30px;
  object-fit: cover;
  margin-right: 4px;
}

  .custom-site-title-widget .heren {
  font-weight: 400;
  font-size:1.2em
}

.custom-site-title-widget .boeren {
  font-weight: 600;
  font-size: 1.2em
}

.custom-site-title-widget .site-tagline {
  font-size: 0.5em;
  font-weight: 400;
}

.custom-site-title-widget .boerderij-naam {
  font-weight: 600;
  font-size: 1.2em;
}

        
</style>';

echo '<div class="custom-site-title-widget">';
echo '<a href="' . esc_url($homepage_url) . '" style="display: flex; align-items: center; text-decoration: none;" aria-label="Logo ' . esc_attr($site_title) . ' - ga naar de homepagina" >';

echo '<img src="' . esc_url($image_to_display) . '" role="presentation" aria-hidden="true">';

echo '<div class="text-container">';
echo '<div style="display: flex;">';
echo '<h2 class="heren" aria-hidden="true">Heren</h2>';
echo '<h2 class="boeren" aria-hidden="true">Boeren</h2>';
echo '</div>';
echo '<p class="site-tagline" aria-hidden="true">' . esc_html($site_tagline) . '</p>';
echo '<h2 class="boerderij-naam" aria-hidden="true">' . esc_html($cleaned_site_title) . '</h2>';
echo '</div>'; // text-container

echo '</a>';
echo '</div>'; // custom-site-title-widget
		}
        
    }

    // Registreer de widget
    \Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_Site_Title_Widget());
    
}

// Show statistics on front-page
function show_statistics_faq($atts) {
	$num = shortcode_atts(
		array(
			'type' => 1,
		),
		 $atts,
        'statistics_faq'
	);

	if($atts['type'] == 'farmers') {
		$post_object = get_page_by_title('Aantal boeren', OBJECT, 'faq');
	} elseif ($atts['type'] == 'members') {
		$post_object = get_page_by_title('Aantal leden', OBJECT, 'faq');
	} elseif($atts['type'] == 'farms') {
		$post_object = get_page_by_title('Aantal boerderijen', OBJECT, 'faq');  
	} else {
		$post_object = null;
	}

    $post_id = $post_object ? array($post_object->ID) : array();

	$args = array(
		'post_type' => 'faq',
		'post__in' => $post_id,
		'orderby' => 'post__in',
        'posts_per_page' => 1,
	);
	$query = new WP_Query($args);

	$output = '';

	if($query->have_posts()) :
	while ($query->have_posts()) : $query->the_post();
            $output .= get_the_content(); 
        endwhile;
        wp_reset_postdata(); // Reset de query na het ophalen van de berichten
    else:
        $output .= 'Geen gegevens gevonden';
    endif;
	 wp_reset_postdata();

    return $output; 
}

add_shortcode('statistics_faq', 'show_statistics_faq');

// Show catories and task in FAQ title in listing.
function herenboeren_custom_title_and_terms_shortcode() {
  
  $title = get_the_title();

  $categories_slug = 'faq-categorieen';
  // $tags_slug = 'faq-tags';

  $categories_output = '';
  // $tags_output = '';

  $categories = get_the_terms(get_the_ID(), $categories_slug);
  if ($categories && !is_wp_error($categories)) {
      foreach ($categories as $term) {
          $categories_output .= '<span class="custom-category-term">' . esc_html($term->name) . '</span> ';
      }
  }
/*
  $tags = get_the_terms(get_the_ID(), $tags_slug);
  if ($tags && !is_wp_error($tags)) {
      foreach ($tags as $term) {
          $tags_output .= '<span class="custom-tag-term">' . esc_html($term->name) . '</span> ';
      }
  }
  */

  return '<span class=fb-faq-title>' . esc_html($title) . '</span>' . $categories_output;
  //return esc_html($title) . $categories_output; // add if want tags: . $tags_output
}
add_shortcode('custom_post_info', 'herenboeren_custom_title_and_terms_shortcode');

// Allow editors to edit menu's

function allow_editors_to_edit_menus() {
    $role = get_role('editor');
    if ($role && !$role->has_cap('edit_theme_options')) {
        $role->add_cap('edit_theme_options');
    }
}
add_action('admin_init', 'allow_editors_to_edit_menus');

// Verberg Customizer en andere Appearance-pagina’s voor redacteuren
function restrict_editor_appearance_access() {
    if (current_user_can('editor')) {
        // Verberg submenu's onder 'Weergave'
        remove_submenu_page('themes.php', 'themes.php');         // Thema's
        remove_submenu_page('themes.php', 'customize.php');      // Customizer
        remove_submenu_page('themes.php', 'widgets.php');        // Widgets
        remove_submenu_page('themes.php', 'theme-editor.php');   // Thema-editor
        remove_submenu_page('themes.php', 'site-editor.php');    // Site Editor (voor FSE themes)
    }
}
add_action('admin_menu', 'restrict_editor_appearance_access', 999);

// Blokkeer directe toegang tot Customizer en andere pagina’s
function block_editor_direct_access_to_appearance_pages() {
    if (current_user_can('editor')) {
        $blocked_pages = [
            'customize.php',
            'themes.php',
            'widgets.php',
            'theme-editor.php',
            'site-editor.php'
        ];

        $current_page = basename($_SERVER['PHP_SELF']);

        if (in_array($current_page, $blocked_pages)) {
            wp_die(__('Sorry, je hebt geen toegang tot deze pagina.'));
        }
    }
}
add_action('admin_init', 'block_editor_direct_access_to_appearance_pages');
?>