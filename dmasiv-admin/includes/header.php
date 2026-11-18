<?php
require_once __DIR__ . '/auth.php';
$judul = $judul ?? 'Dashboard';
$menu  = $menu ?? '';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul) ?> | D'MASIV Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.10.0/dist/css/adminlte.min.css">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Buka/tutup menu"><i class="bi bi-list"></i></a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item d-none d-md-block"><span class="nav-link"><?= e($_SESSION['nama'] ?? '') ?></span></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
      </ul>
    </div>
  </nav>

  <?php require __DIR__ . '/sidebar.php'; ?>

  <main class="app-main">
    <div class="app-content-header">
      <div class="container-fluid"><h3 class="mb-0"><?= e($judul) ?></h3></div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
        <?php if ($flash): ?>
          <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
          </div>
        <?php endif; ?>
