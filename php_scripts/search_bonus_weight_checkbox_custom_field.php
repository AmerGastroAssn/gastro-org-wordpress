
<?php

add_filter('searchwp_query_join', function ($sql, $engine) {
    global $wpdb;

    $my_meta_key = 'search_precedence';
    // $my_meta_key = 'proprietary';

    return $sql . " LEFT JOIN {$wpdb->postmeta} AS swp9633meta ON {$wpdb->posts}.ID = swp9633meta.post_id AND swp9633meta.meta_key = '{$my_meta_key}'";
}, 10, 2);

add_filter('searchwp_weight_mods', function ($sql) {
    $modifier = 1000;

    return $sql .= " + IF(swp9633meta.meta_value+0 = 1, {$modifier}, 0)";
});
