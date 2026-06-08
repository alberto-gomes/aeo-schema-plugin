<?php
/**
 * Plugin Name: Advanced AEO & SEO Schema Generator
 * Description: Automatically generates Google-compliant JSON-LD markup. Features an ACF Free compatible Global Options workaround.
 * Version: 1.3.0
 * Author: Agency Innovation Team
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. INITIALIZATION & ACF CHECK
 */
add_action( 'admin_init', 'aeo_strict_schema_dependencies' );
function aeo_strict_schema_dependencies() {
    if ( ! function_exists('acf_add_local_field_group') ) {
        add_action( 'admin_notices', 'aeo_strict_schema_missing_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }
}

function aeo_strict_schema_missing_notice() {
    echo '<div class="error"><p><strong>Advanced AEO Schema Generator</strong> requires Advanced Custom Fields (ACF) to be active.</p></div>';
}

/**
 * 2. ACF FREE "OPTIONS PAGE" WORKAROUND
 * Creates a hidden single post to act as our global settings repository.
 */
add_action('init', 'aeo_register_global_settings_cpt');
function aeo_register_global_settings_cpt() {
    register_post_type('aeo_global', array(
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => false, // We will create a custom menu item instead
        'supports'     => array(''), // Passing an empty array hides the Title and Editor boxes!
        'capabilities' => array(
            'create_posts' => 'do_not_allow', // Prevent users from creating multiple global settings
        ),
        'map_meta_cap' => true,
    ));
}

// Automatically create the single global settings post if it doesn't exist
add_action('admin_init', 'aeo_ensure_global_post_exists');
function aeo_ensure_global_post_exists() {
    $post_id = get_option('aeo_global_schema_post_id');
    if (!$post_id || !get_post($post_id)) {
        $post_id = wp_insert_post(array(
            'post_title'  => 'Global Schema Settings',
            'post_type'   => 'aeo_global',
            'post_status' => 'publish'
        ));
        update_option('aeo_global_schema_post_id', $post_id);
    }
}

// Create the left menu item and redirect it to our hidden post editor
add_action('admin_menu', 'aeo_global_schema_menu_item');
function aeo_global_schema_menu_item() {
    add_menu_page(
        'Global Schema',
        'Global Schema',
        'manage_options',
        'aeo-global-schema',
        'aeo_global_schema_redirect',
        'dashicons-code-standards',
        80
    );
}

function aeo_global_schema_redirect() {
    $post_id = get_option('aeo_global_schema_post_id');
    $url = admin_url('post.php?post=' . $post_id . '&action=edit');
    // Safely redirect to the edit screen
    if (!headers_sent()) {
        wp_redirect($url);
        exit;
    } else {
        echo '<script>window.location.href="' . $url . '";</script>';
        exit;
    }
}

/**
 * 3. PROGRAMMATICALLY REGISTER ACF FIELDS
 */
add_action('acf/init', 'aeo_strict_schema_register_fields');
function aeo_strict_schema_register_fields() {
    if ( ! function_exists('acf_add_local_field_group') ) return;

    acf_add_local_field_group(array(
        'key' => 'group_aeo_strict_schema',
        'title' => 'Advanced Structured Data (Google Search Gallery Spec)',
        'fields' => array(
            array(
                'key' => 'field_strict_schema_type',
                'label' => 'Select Schema Markup Type',
                'name' => 'aeo_schema_type',
                'type' => 'select',
                'choices' => array(
                    'none' => '— None —',
                    'Dataset' => 'Dataset',
                    'ProfilePage' => 'Profile Page',
                    'VideoObject' => 'Video Object',
                    'SoftwareApplication' => 'Software Application',
                ),
                'default_value' => 'none',
                'ui' => 1,
            ),

            /* =================================================================
             * DATASET SCHEMA FIELDS
             * ================================================================= */
            array(
                'key' => 'tab_dataset_required',
                'label' => 'Dataset: Required Fields',
                'type' => 'tab',
                'conditional_logic' => array(array(array('field' => 'field_strict_schema_type', 'operator' => '==', 'value' => 'Dataset'))),
            ),
            array(
                'key' => 'field_ds_name',
                'label' => 'Dataset Name (Required)',
                'name' => 'ds_name',
                'type' => 'text',
                'instructions' => 'The name of the dataset.',
            ),
            array(
                'key' => 'field_ds_description',
                'label' => 'Dataset Description (Required)',
                'name' => 'ds_description',
                'type' => 'textarea',
                'instructions' => 'A summary describing the contents of the dataset.',
            ),
            array(
                'key' => 'tab_dataset_optional',
                'label' => 'Dataset: Optional / Recommended',
                'type' => 'tab',
                'conditional_logic' => array(array(array('field' => 'field_strict_schema_type', 'operator' => '==', 'value' => 'Dataset'))),
            ),
            array(
                'key' => 'field_ds_url',
                'label' => 'Dataset Landing Page URL (Optional)',
                'name' => 'ds_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_ds_same_as',
                'label' => 'Same As URL (Optional)',
                'name' => 'ds_same_as',
                'type' => 'url',
                'instructions' => 'Link to an official secondary master record of the dataset if applicable.',
            ),
            array(
                'key' => 'field_ds_license',
                'label' => 'License URL (Optional)',
                'name' => 'ds_license',
                'type' => 'url',
                'instructions' => 'Link to the dataset usage license (e.g., Creative Commons URL).',
            ),

            /* =================================================================
             * PROFILE PAGE SCHEMA FIELDS
             * ================================================================= */
            array(
                'key' => 'tab_profile_required',
                'label' => 'Profile Page: Required Fields',
                'type' => 'tab',
                'conditional_logic' => array(array(array('field' => 'field_strict_schema_type', 'operator' => '==', 'value' => 'ProfilePage'))),
            ),
            array(
                'key' => 'field_prof_name',
                'label' => 'Main Entity Name (Required)',
                'name' => 'prof_name',
                'type' => 'text',
                'instructions' => 'The full name of the person or group this profile represents.',
            ),
            array(
                'key' => 'tab_profile_optional',
                'label' => 'Profile Page: Optional / Recommended',
                'type' => 'tab',
                'conditional_logic' => array(array(array('field' => 'field_strict_schema_type', 'operator' => '==', 'value' => 'ProfilePage'))),
            ),
            array(
                'key' => 'field_prof_alternate',
                'label' => 'Alternate / Alias Name (Optional)',
                'name' => 'prof_alternate',
                'type' => 'text',
                'instructions' => 'Username or nickname handles (e.g., ahuff23).',
            ),
            array(
                'key' => 'field_prof_description',
                'label' => 'Bio / Description (Optional)',
                'name' => 'prof_description',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_prof_image',
                'label' => 'Avatar Image URL (Optional)',
                'name' => 'prof_image',
                'type' => 'url',
            ),
            array(
                'key' => 'field_prof_same_as',
                'label' => 'Authority Profile Links (Optional)',
                'name' => 'prof_same_as',
                'type' => 'textarea',
                'instructions' => 'Other social platforms or professional websites confirming identity. One URL per line.',
            ),

            /* =================================================================
             * VIDEO OBJECT SCHEMA FIELDS
             * ================================================================= */
            array(
                'key' => 'tab_video_required',
                'label' => 'Video: Required Fields',
                'type' => 'tab',
                'conditional_logic' => array(array(array('field' => 'field_strict_schema_type', 'operator' => '==', 'value' => 'VideoObject'))),
            ),
            array(
                'key' => 'field_vid_name',
                'label' => 'Video Title (Required)',
                'name' => 'vid_name',
                'type' => 'text',
            ),
            array(
                'key' => 'field_vid_description',
                'label' => 'Video Description (Required)',
                'name' => 'vid_description',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_vid_thumbnail',
                'label' => 'Thumbnail Image URL (Required)',
                'name' => 'vid_thumbnail',
                'type' => 'url',
                'instructions' => 'Google requires a high-res thumbnail URL.',
            ),
            array(
                'key' => 'field_vid_upload',
                'label' => 'Upload Date (Required)',
                'name' => 'vid_upload',
                'type' => 'date_time_picker',
                'display_format' => 'Y-m-d H:i:s',
                'return_format' => 'c', 
            ),
            array(
                'key' => 'tab_video_optional',
                'label' => 'Video: Optional / Recommended',
                'type' => 'tab',
                'conditional_logic' => array(array(array('field' => 'field_strict_schema_type', 'operator' => '==', 'value' => 'VideoObject'))),
            ),
            array(
                'key' => 'field_vid_duration',
                'label' => 'Duration (Optional)',
                'name' => 'vid_duration',
                'type' => 'text',
                'instructions' => 'ISO 8601 format (e.g., PT1M54S for 1 min 54 secs).',
            ),
            array(
                'key' => 'field_vid_content_url',
                'label' => 'Direct Video File URL (Optional)',
                'name' => 'vid_content_url',
                'type' => 'url',
                'instructions' => 'Link to the actual .mp4 file.',
            ),
            array(
                'key' => 'field_vid_embed_url',
                'label' => 'Embed URL (Optional)',
                'name' => 'vid_embed_url',
                'type' => 'url',
                'instructions' => 'Link pointing to the player embed frame.',
            ),

            /* =================================================================
             * SOFTWARE APPLICATION SCHEMA FIELDS
             * ================================================================= */
            array(
                'key' => 'tab_software_required',
                'label' => 'Software App: Required Fields',
                'type' => 'tab',
                'conditional_logic' => array(array(array('field' => 'field_strict_schema_type', 'operator' => '==', 'value' => 'SoftwareApplication'))),
            ),
            array(
                'key' => 'field_soft_name',
                'label' => 'Application Name (Required)',
                'name' => 'soft_name',
                'type' => 'text',
            ),
            array(
                'key' => 'tab_software_optional',
                'label' => 'Software App: Optional / Recommended',
                'type' => 'tab',
                'conditional_logic' => array(array(array('field' => 'field_strict_schema_type', 'operator' => '==', 'value' => 'SoftwareApplication'))),
            ),
            array(
                'key' => 'field_soft_os',
                'label' => 'Operating System (Optional)',
                'name' => 'soft_os',
                'type' => 'text',
                'instructions' => 'e.g., ANDROID, iOS, or Web-based.',
            ),
            array(
                'key' => 'field_soft_category',
                'label' => 'Application Category (Optional)',
                'name' => 'soft_category',
                'type' => 'text',
                'instructions' => 'e.g., GameApplication, BusinessApplication.',
            ),
            array(
                'key' => 'field_soft_price',
                'label' => 'Price (Optional)',
                'name' => 'soft_price',
                'type' => 'number',
                'default_value' => '0.00',
            ),
            array(
                'key' => 'field_soft_currency',
                'label' => 'Price Currency (Optional)',
                'name' => 'soft_currency',
                'type' => 'text',
                'default_value' => 'USD',
            ),
        ),
        'location' => array(
            array(array('param' => 'post_type', 'operator' => '==', 'value' => 'page')),
            array(array('param' => 'post_type', 'operator' => '==', 'value' => 'post')),
            array(array('param' => 'user_form', 'operator' => '==', 'value' => 'all')), 
            // Link fields to our hidden ACF Free options workaround
            array(array('param' => 'post_type', 'operator' => '==', 'value' => 'aeo_global')), 
        ),
    ));
}

/**
 * 4. FRONTEND INJECTION ENGINE (Updated for Free Options Fallback)
 */
add_action( 'wp_head', 'aeo_strict_schema_render_json', 20 );
function aeo_strict_schema_render_json() {
    $schema_type = 'none';
    $source_id = null;
    $global_post_id = get_option('aeo_global_schema_post_id');

    // 1. Check for Page-Specific explicit override first
    if ( is_singular() ) {
        $post_id = get_the_ID();
        $schema_type = get_field('aeo_schema_type', $post_id);
        $source_id = $post_id;
    }

    // 2. If no page schema is set, check if the Post Author has an active profile schema
    if ( ( !$schema_type || $schema_type === 'none' ) && is_singular('post') ) {
        $author_id = get_post_field( 'post_author', get_the_ID() );
        if ( $author_id ) {
            $author_source = 'user_' . $author_id;
            $author_schema_type = get_field('aeo_schema_type', $author_source);
            
            if ( $author_schema_type && $author_schema_type !== 'none' ) {
                $schema_type = $author_schema_type;
                $source_id = $author_source;
            }
        }
    }

    // 3. Fallback to our custom Global Settings Post
    if ( ( !$schema_type || $schema_type === 'none' ) && $global_post_id ) {
        $schema_type = get_field('aeo_schema_type', $global_post_id);
        $source_id = $global_post_id;
    }

    if ( !$schema_type || $schema_type === 'none' ) {
        return;
    }

    $json = array(
        '@context' => 'https://schema.org',
        '@type'    => $schema_type
    );

    switch ( $schema_type ) {

        case 'Dataset':
            $json['name'] = esc_html(get_field('ds_name', $source_id));
            $json['description'] = esc_html(get_field('ds_description', $source_id));
            if ($url = get_field('ds_url', $source_id)) $json['url'] = esc_url($url);
            if ($same_as = get_field('ds_same_as', $source_id)) $json['sameAs'] = esc_url($same_as);
            if ($license = get_field('ds_license', $source_id)) $json['license'] = esc_url($license);
            break;

        case 'ProfilePage':
            $is_global = ($source_id == $global_post_id);
            $json['dateCreated'] = (strpos((string)$source_id, 'user_') === 0) ? get_the_date('c') : get_the_date('c', $is_global ? null : $source_id);
            $json['dateModified'] = (strpos((string)$source_id, 'user_') === 0) ? get_the_modified_date('c') : get_the_modified_date('c', $is_global ? null : $source_id);
            
            $main_entity = array(
                '@type' => 'Person',
                'name'  => esc_html(get_field('prof_name', $source_id))
            );

            if ($alt = get_field('prof_alternate', $source_id)) $main_entity['alternateName'] = esc_html($alt);
            if ($desc = get_field('prof_description', $source_id)) $main_entity['description'] = esc_html($desc);
            if ($img = get_field('prof_image', $source_id)) $main_entity['image'] = esc_url($img);
            
            if ($same_as_text = get_field('prof_same_as', $source_id)) {
                $urls = array_map('trim', explode("\n", str_replace("\r", "", $same_as_text)));
                $main_entity['sameAs'] = array_filter($urls);
            }

            $json['mainEntity'] = $main_entity;
            break;

        case 'VideoObject':
            $json['name'] = esc_html(get_field('vid_name', $source_id));
            $json['description'] = esc_html(get_field('vid_description', $source_id));
            $json['thumbnailUrl'] = array(esc_url(get_field('vid_thumbnail', $source_id)));
            $json['uploadDate'] = get_field('vid_upload', $source_id);
            if ($dur = get_field('vid_duration', $source_id)) $json['duration'] = esc_html($dur);
            if ($c_url = get_field('vid_content_url', $source_id)) $json['contentUrl'] = esc_url($c_url);
            if ($e_url = get_field('vid_embed_url', $source_id)) $json['embedUrl'] = esc_url($e_url);
            break;

        case 'SoftwareApplication':
            $json['name'] = esc_html(get_field('soft_name', $source_id));
            if ($os = get_field('soft_os', $source_id)) $json['operatingSystem'] = esc_html($os);
            if ($cat = get_field('soft_category', $source_id)) $json['applicationCategory'] = esc_html($cat);
            if (get_field('soft_price', $source_id) !== '') {
                $json['offers'] = array(
                    '@type' => 'Offer',
                    'price' => floatval(get_field('soft_price', $source_id)),
                    'priceCurrency' => esc_html(get_field('soft_currency', $source_id))
                );
            }
            break;
    }

    echo "\n<!-- Custom AEO Schema Generator Output -->\n";
    echo '<script type="application/ld+json">' . json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . "</script>\n";
}