<?php
// Daftar menu sidebar. Ubah 'ada' menjadi true jika modulnya sudah dibuat.
$daftar_menu = [
    ['kode' => 'dashboard', 'label' => 'Dashboard',       'ikon' => 'bi-speedometer2',  'url' => '/index.php',        'ada' => true],
    ['kode' => 'event',     'label' => 'Event',           'ikon' => 'bi-calendar-event','url' => '/event/bacaevent.php',  'ada' => true],
    ['kode' => 'kategori',  'label' => 'Kategori Event',  'ikon' => 'bi-tags',          'url' => '/kategori_event/bacakategorievent.php', 'ada' => true],
    ['kode' => 'galeri',    'label' => 'Galeri',          'ikon' => 'bi-images',        'url' => '/galeri/bacagaleri.php', 'ada' => true],
    ['kode' => 'album',     'label' => 'Album dan Lagu',  'ikon' => 'bi-disc',          'url' => '/album/bacaalbum.php', 'ada' => true],
    ['kode' => 'produk',    'label' => 'Produk',          'ikon' => 'bi-bag',           'url' => '/produk/bacaproduk.php', 'ada' => true],
    ['kode' => 'personel',  'label' => 'Personel',        'ikon' => 'bi-people',        'url' => '/personel/bacapersonel.php', 'ada' => true],
    ['kode' => 'sosmed',    'label' => 'Media Sosial',    'ikon' => 'bi-share',         'url' => '/media_sosial/bacamediasosial.php', 'ada' => true],
    ['kode' => 'mitra',     'label' => 'Mitra',           'ikon' => 'bi-people-fill',   'url' => '/mitra/bacamitra.php', 'ada' => true],
    ['kode' => 'fans',      'label' => 'Data Fans',       'ikon' => 'bi-heart',         'url' => '/fans/bacafans.php', 'ada' => true],
    ['kode' => 'admin',     'label' => 'Kelola Admin',    'ikon' => 'bi-shield-lock',   'url' => '/kelola_admin/bacaadmin.php', 'ada' => true],
];
?>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="<?= BASE_URL ?>/index.php" class="brand-link">
      <span class="brand-text fw-light">D'MASIV Admin</span>
    </a>
  </div>
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        <?php foreach ($daftar_menu as $m): ?>
          <li class="nav-item">
            <a href="<?= $m['ada'] ? BASE_URL . $m['url'] : '#' ?>"
               class="nav-link <?= $menu === $m['kode'] ? 'active' : '' ?> <?= $m['ada'] ? '' : 'disabled' ?>">
              <i class="nav-icon bi <?= $m['ikon'] ?>"></i>
              <p><?= e($m['label']) ?><?php if (!$m['ada']): ?> <span class="nav-badge badge text-bg-secondary me-3">Segera</span><?php endif; ?></p>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>
</aside>
