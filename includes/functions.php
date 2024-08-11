<?php

declare(strict_types=1);

namespace YoastCptBreadcrumbs;

function url_to_author(string $url): int
{
    $users = get_users();
    $clean_url = untrailingslashit($url);

    foreach ($users as $user) {
        $author_url = untrailingslashit(get_author_posts_url($user->ID));

        if ($author_url === $clean_url) {
            return $user->ID;
        }
    }

    return 0;
}

function url_to_term(string $url): array
{
    $terms = get_terms(['hide_empty' => true]);
    $clean_url = untrailingslashit($url);

    foreach ($terms as $term) {
        $term_url = untrailingslashit(get_term_link($term->term_id, $term->taxonomy));

        if ($term_url === $clean_url) {
            return [$term->term_id, $term->taxonomy];
        }
    }

    return [null, null];
}

function url_to_post_type_archive(string $url): ?string
{
    $post_types = get_post_types();
    $clean_url = untrailingslashit($url);

    foreach ($post_types as $post_type) {
        $archive_link = get_post_type_archive_link($post_type);

        if ($archive_link && untrailingslashit($archive_link) === $clean_url) {
            return $post_type;
        }
    }

    return null;
}
