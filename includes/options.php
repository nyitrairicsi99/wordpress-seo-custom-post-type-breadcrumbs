<?php

declare(strict_types=1);

namespace YoastCptBreadcrumbs;

function get_yoast_breadcrumb_option(string $key): int
{
    $options = get_option('yoast_breadcrumbs_settings');

    $options = apply_filters('yoast_cpt_breadcrumbs_options', $options);

    $checked = $options[$key] ?? 0;

    return (int)apply_filters('yoast_cpt_breadcrumbs_option', $checked, $key);
}
