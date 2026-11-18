<?php
require_once __DIR__ . '/_data.php';
api_jalankan(function () use ($pdo) { api_kirim(["album" => data_album($pdo)]); });
