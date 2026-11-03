<?php

// Pastikan direktori view cache di /tmp tersedia untuk compiler Blade
if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0755, true);
}

// Forward request ke index.php publik Laravel
require __DIR__ . '/../public/index.php';
