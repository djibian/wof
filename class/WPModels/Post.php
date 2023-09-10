<?php

namespace Wof\WPModels;

class Post
{

    static public function getByType($type, $status='publish', $count = -1, $orderBy = 'date', $order = 'DESC')
    {
        $queryFilters = [
            'post_type' => $type,
            'post_status' => $status,
            'orderby' => $orderBy,
            'order' => $order,
            'posts_per_page' => $count //
        ];

        $query = new \WP_Query($queryFilters);
        return $query->posts;
    }
}
