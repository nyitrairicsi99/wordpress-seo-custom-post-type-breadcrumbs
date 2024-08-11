<?php

namespace YoastCptBreadcrumbs;

// Hook to add admin menu
add_action('admin_menu', 'YoastCptBreadcrumbs\\yoast_breadcrumbs_custom_settings_menu');

// Hook to register settings
add_action('admin_init', 'YoastCptBreadcrumbs\\yoast_breadcrumbs_custom_settings_init');

function yoast_breadcrumbs_custom_settings_menu(): void
{
    add_options_page(
        'Yoast CPT Breadcrumbs Settings',
        'Yoast CPT Breadcrumbs Settings',
        'manage_options',
        'yoast-breadcrumbs-settings',
        'YoastCptBreadcrumbs\\yoast_breadcrumbs_custom_settings_page'
    );
}

function yoast_breadcrumbs_custom_settings_init(): void
{
    // Register a new setting for "yoast_breadcrumbs" page.
    register_setting('yoast_breadcrumbs', 'yoast_breadcrumbs_settings');

    // Add a new section in the "yoast_breadcrumbs" page.
    add_settings_section(
        'yoast_breadcrumbs_section',
        __('Yoast Breadcrumbs Settings', 'wordpress-seo-custom-post-type-breadcrumbs'),
        'YoastCptBreadcrumbs\\yoast_breadcrumbs_section_callback',
        'yoast_breadcrumbs'
    );

    // Add fields to the section
    add_settings_field(
        'ignore_posts',
        __('Ignore Posts', 'wordpress-seo-custom-post-type-breadcrumbs'),
        'YoastCptBreadcrumbs\\yoast_breadcrumbs_ignore_posts_callback',
        'yoast_breadcrumbs',
        'yoast_breadcrumbs_section'
    );

    add_settings_field(
        'ignore_post_type_archives',
        __('Ignore Post Type Archives', 'wordpress-seo-custom-post-type-breadcrumbs'),
        'YoastCptBreadcrumbs\\yoast_breadcrumbs_ignore_post_type_archives_callback',
        'yoast_breadcrumbs',
        'yoast_breadcrumbs_section'
    );

    add_settings_field(
        'ignore_terms',
        __('Ignore Terms', 'wordpress-seo-custom-post-type-breadcrumbs'),
        'YoastCptBreadcrumbs\\yoast_breadcrumbs_ignore_terms_callback',
        'yoast_breadcrumbs',
        'yoast_breadcrumbs_section'
    );

    add_settings_field(
        'ignore_authors',
        __('Ignore Authors', 'wordpress-seo-custom-post-type-breadcrumbs'),
        'YoastCptBreadcrumbs\\yoast_breadcrumbs_ignore_authors_callback',
        'yoast_breadcrumbs',
        'yoast_breadcrumbs_section'
    );
}

function yoast_breadcrumbs_section_callback(): void
{
    echo sprintf(
        "<p><strong>%s</strong></p><p>%s</p>",
        __('The plugin will try to find each page represented in url and update Yoast SEO\'s breadcrumbs.', 'wordpress-seo-custom-post-type-breadcrumbs'),
        __('You can speed up the plugin by disabling unnecessary checks.', 'wordpress-seo-custom-post-type-breadcrumbs')
    );
}

function yoast_breadcrumbs_ignore_posts_callback(): void
{
    $checked = get_yoast_breadcrumb_option('ignore_posts');
    echo '<input type="checkbox" name="yoast_breadcrumbs_settings[ignore_posts]" value="1" ' . checked(1, $checked, false) . '>';
}

function yoast_breadcrumbs_ignore_post_type_archives_callback(): void
{
    $checked = get_yoast_breadcrumb_option('ignore_post_type_archives');
    echo '<input type="checkbox" name="yoast_breadcrumbs_settings[ignore_post_type_archives]" value="1" ' . checked(1, $checked, false) . '>';
}

function yoast_breadcrumbs_ignore_terms_callback(): void
{
    $checked = get_yoast_breadcrumb_option('ignore_terms');
    echo '<input type="checkbox" name="yoast_breadcrumbs_settings[ignore_terms]" value="1" ' . checked(1, $checked, false) . '>';
}

function yoast_breadcrumbs_ignore_authors_callback(): void
{
    $checked = get_yoast_breadcrumb_option('ignore_authors');
    echo '<input type="checkbox" name="yoast_breadcrumbs_settings[ignore_authors]" value="1" ' . checked(1, $checked, false) . '>';
}

function yoast_breadcrumbs_custom_settings_page(): void
{
    ?>
    <div class="wrap">
        <h1>Yoast Breadcrumbs Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('yoast_breadcrumbs');
            do_settings_sections('yoast_breadcrumbs');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_filter('plugin_action_links', function ($links, $file) {
    if ($file != YOAST_CPT_BREADCRUMBS_PLUGIN_BASENAME) {
        return $links;
    }

    $url = get_admin_url() . "options-general.php?page=yoast-breadcrumbs-settings";
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'wordpress-seo-custom-post-type-breadcrumbs') . '</a>';
    $links[] = $settings_link;
    return $links;
}, 10, 2);

