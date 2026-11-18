<?php
require_once __DIR__ . '/_data.php';
api_jalankan(function () use ($pdo) { api_kirim(["kategori" => data_kategori_event($pdo), "event" => data_event($pdo)]); });
