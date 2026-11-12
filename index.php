<?php
$judul = 'Dashboard';
$menu  = 'dashboard';
require __DIR__ . '/includes/header.php';

// Daftar tabel ditulis tetap (bukan dari input pengguna) sehingga aman dipakai di query
$kartu = [
    ['tabel' => 'event',  'label' => 'Event',  'ikon' => 'bi-calendar-event', 'warna' => 'primary', 'url' => '/event/bacaevent.php'],
    ['tabel' => 'galeri', 'label' => 'Galeri', 'ikon' => 'bi-images',         'warna' => 'success', 'url' => '/galeri/bacagaleri.php'],
    ['tabel' => 'album',  'label' => 'Album',  'ikon' => 'bi-disc',           'warna' => 'warning', 'url' => '/album/bacaalbum.php'],
    ['tabel' => 'produk', 'label' => 'Produk', 'ikon' => 'bi-bag',            'warna' => 'danger', 'url' => '/produk/bacaproduk.php'],
    ['tabel' => 'fans',   'label' => 'Fans',   'ikon' => 'bi-heart',          'warna' => 'info', 'url' => '/fans/bacafans.php'],
];
?>
<style>
  .kartu-dashboard .small-box { transition: transform .15s, box-shadow .15s; }
  .kartu-dashboard:hover .small-box { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.3); }
</style>
<div class="row">
  <?php foreach ($kartu as $k):
      $jumlah = (int)$pdo->query("SELECT COUNT(*) FROM `{$k['tabel']}`")->fetchColumn(); ?>
    <div class="col-lg-3 col-6">
      <a href="<?= e(BASE_URL . $k['url']) ?>" class="d-block text-decoration-none kartu-dashboard">
        <div class="small-box text-bg-<?= $k['warna'] ?>">
          <div class="inner">
            <h3><?= $jumlah ?></h3>
            <p><?= e($k['label']) ?></p>
          </div>
          <i class="small-box-icon bi <?= $k['ikon'] ?>"></i>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
