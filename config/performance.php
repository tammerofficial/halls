<?php

return [
    /*
    |--------------------------------------------------------------------------
    | تحسينات الأداء
    |--------------------------------------------------------------------------
    */
    
    'view_cache' => true,
    'route_cache' => true,
    'config_cache' => true,
    'event_cache' => true,
    
    'lazy_loading' => true,
    'preload_assets' => true,
    'compress_output' => true,
    
    'database_optimizations' => [
        'query_cache' => true,
        'connection_pooling' => true,
        'eager_loading' => true,
    ],
    
    'frontend_optimizations' => [
        'minify_css' => true,
        'minify_js' => true,
        'image_optimization' => true,
        'gzip_compression' => true,
    ],
]; 