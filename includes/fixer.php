<?php

declare(strict_types=1);

namespace YoastCptBreadcrumbs;

function get_post_parent_parts(string $url): ?array
{
    $post_id = url_to_postid($url);

    if ($post_id) {
        return [
            'type' => 'post',
            'id' => $post_id,
        ];
    }

    return null;
}

function get_post_type_archive_parts(string $url): ?array
{
    $post_type = url_to_post_type_archive($url);

    if ($post_type) {
        return [
            'type' => 'post_type_archive',
            'post_type' => $post_type,
        ];
    }

    return null;
}

function get_term_parent_parts(string $url): ?array
{
    [$term_id, $term_tax] = url_to_term($url);

    if ($term_id) {
        return [
            'type' => 'term',
            'term_id' => $term_id,
            'term_tax' => $term_tax,
        ];
    }

    return null;
}

function get_author_parent_parts(string $url): ?array
{
    $author_id = url_to_author($url);

    if ($author_id) {
        return [
            'type' => 'author',
            'id' => $author_id,
        ];
    }

    return null;
}

function find_post_type_url_parent_parts(string $url): array
{
    $parent_parts = [];
    $link_parts = explode("/", rtrim($url, "/"));

    while (!empty($link_parts)) {
        $check_url = implode("/", $link_parts);

        $post_part = get_yoast_breadcrumb_option('ignore_posts')==1 ? false : get_post_parent_parts($check_url);
        $archive_part = get_yoast_breadcrumb_option('ignore_post_type_archives')==1 ? false : get_post_type_archive_parts($check_url);
        $term_part = get_yoast_breadcrumb_option('ignore_terms')==1 ? false : get_term_parent_parts($check_url);
        $author_part = get_yoast_breadcrumb_option('ignore_authors')==1 ? false : get_author_parent_parts($check_url);

        if ($post_part) {
            $parent_parts[] = $post_part;
        }

        if ($archive_part) {
            $parent_parts[] = $archive_part;
        }

        if ($term_part) {
            $parent_parts[] = $term_part;
        }

        if ($author_part) {
            $parent_parts[] = $author_part;
        }

        array_pop($link_parts);
    }

    return $parent_parts;
}

function generate_post_breadcrumb(array $parent_part): array
{
    $post = get_post($parent_part['id'], 'ARRAY_A');

    return [
        'url' => $post['guid'],
        'text' => $post['post_title'],
    ];
}

function generate_post_type_archive_breadcrumb(array $parent_part): array
{
    $url = get_post_type_archive_link($parent_part['post_type']);
    $title = get_post_type_object($parent_part['post_type'])->labels->name;

    return [
        'url' => $url,
        'text' => $title,
    ];
}

function generate_term_breadcrumb(array $parent_part): array
{
    $term_url = get_term_link($parent_part['term_id'], $parent_part['term_tax']);
    $term_name = get_term($parent_part['term_id'], $parent_part['term_tax'])->name;

    return [
        'url' => $term_url,
        'text' => $term_name,
    ];
}

function generate_author_breadcrumb(array $parent_part): array
{
    $author_url = get_author_posts_url($parent_part['id']);
    $author_name = get_user_by('id', $parent_part['id'])->user_nicename;

    return [
        'url' => $author_url,
        'text' => $author_name,
    ];
}

function generate_breadcrumbs(array $parent_parts): array
{
    $breadcrumbs = [];

    foreach ($parent_parts as $parent_part) {
        switch ($parent_part['type'] ?? '') {
            case 'post':
                $breadcrumbs[] = generate_post_breadcrumb($parent_part);
                break;
            case 'post_type_archive':
                $breadcrumbs[] = generate_post_type_archive_breadcrumb($parent_part);
                break;
            case 'term':
                $breadcrumbs[] = generate_term_breadcrumb($parent_part);
                break;
            case 'author':
                $breadcrumbs[] = generate_author_breadcrumb($parent_part);
                break;
        }
    }

    return $breadcrumbs;
}

add_filter("wpseo_breadcrumb_links", function (array $links): array {
    if (!is_archive() && !is_single()) {
        return $links;
    }

    $current_urls = array_column($links, 'url');

    $cpt_archive_url = get_post_type_archive_link(get_post_type());

    $parent_parts = find_post_type_url_parent_parts($cpt_archive_url);
    $parent_breadcrumbs = generate_breadcrumbs($parent_parts);

    $parent_breadcrumbs = apply_filters('yoast_cpt_breadcrumbs_parents', $parent_breadcrumbs);

    foreach ($parent_breadcrumbs as $breadcrumb) {
        if (!in_array($breadcrumb['url'], $current_urls, true) &&
            !in_array("{$breadcrumb['url']}/", $current_urls, true)
        ) {
            array_splice($links, 1, 0, [$breadcrumb]);
        }
    }

    return $links;
}, 9, 1);
