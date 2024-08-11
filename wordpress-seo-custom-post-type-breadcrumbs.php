<?php
/**
 * Plugin Name:       Yoast SEO Custom Post Type Breadcrums
 * Plugin URI:        https://wordpress.org/plugins/wordpress-seo-custom-post-type-breadcrumbs/
 * Description:       Sets the correct breadcrumb on custom-post-type archive and single pages.
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Richard Nyitrai
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wordpress-seo-custom-post-type-breadcrumbs
 * Domain Path:       /languages
 *
 * @package wordpress-seo-custom-post-type-breadcrumbs
 */

namespace YoastCptBreadcrumbs;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('YOAST_CPT_BREADCRUMBS_VERSION', '1.0.0');
define('YOAST_CPT_BREADCRUMBS_PLUGIN', __FILE__);
define('YOAST_CPT_BREADCRUMBS_PLUGIN_DIR', __DIR__);
define('YOAST_CPT_BREADCRUMBS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('YOAST_CPT_BREADCRUMBS_PLUGIN_BASENAME', plugin_basename(YOAST_CPT_BREADCRUMBS_PLUGIN));

/**
 * Get the minimum version of PHP required by this plugin.
 *
 * @since 1.0.0
 *
 * @return string Minimum version required.
 */
function minimum_php_requirement()
{
    return '7.4';
}

/**
 * Whether PHP installation meets the minimum requirements
 *
 * @since 2.1.1
 *
 * @return bool True if meets minimum requirements, false otherwise.
 */
function site_meets_php_requirements()
{
    return version_compare(phpversion(), minimum_php_requirement(), '>=');
}

// Try and include our autoloader, ensuring our PHP version is met first.
if (! site_meets_php_requirements()) {
    add_action(
        'admin_notices',
        function () {
            ?>
            <div class="notice notice-error">
                <p>
                    <?php
                    echo wp_kses_post(
                        sprintf(
                        /* translators: %s: Minimum required PHP version */
                            __('Yoast SEO Custom Post Type Breadcrumbs requires PHP version %s or later. Please upgrade PHP or disable the plugin.', 'wordpress-seo-custom-post-type-breadcrumbs'),
                            esc_html(minimum_php_requirement())
                        )
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    );
    return;
}

require __DIR__ . '/includes/options.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/fixer.php';
require __DIR__ . '/includes/admin.php';