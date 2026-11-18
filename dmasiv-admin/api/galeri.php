<?php
require_once __DIR__ . '/_data.php';
api_jalankan(function () use ($pdo) { api_kirim(["foto" => data_galeri($pdo, "foto"), "video" => data_galeri($pdo, "video")]); });
