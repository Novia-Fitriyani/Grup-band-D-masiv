<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$id_album = (int)($_GET['album'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT judul_album FROM album WHERE id_album = ?");
mysqli_stmt_bind_param($stmt, "i", $id_album);
mysqli_stmt_execute($stmt);
$induk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$induk) {
  set_flash('danger', 'Album tidak ditemukan.');
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
}

$cari   = trim($_GET['cari'] ?? '');

// Query SELECT untuk tabel lagu
$sql = "SELECT * FROM lagu";
$kondisi    = ["id_album = ?"];
$bind_tipe  = "i";
$bind_nilai = [$id_album];
if ($cari !== '') {
  $kondisi[]    = "judul_lagu LIKE ?";
  $bind_tipe   .= "s";
  $bind_nilai[] = '%' . addcslashes($cari, '%_\\') . '%';   // tanda % dan _ yang diketik dianggap huruf biasa
}
if ($kondisi) {
  $sql .= " WHERE " . implode(" AND ", $kondisi);
}
$sql .= " ORDER BY COALESCE(no_urut, 999999), id_lagu";
$stmt = mysqli_prepare($conn, $sql);
if ($bind_tipe !== "") {
  mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);
}
mysqli_stmt_execute($stmt);
// Execute the SQL query
$result = mysqli_stmt_get_result($stmt);
$jumlah = $result->num_rows;

$judul = 'List Lagu';
$menu  = 'album';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h3 class="card-title mb-0">List Lagu - Album: <?php echo e($induk['judul_album']); ?></h3>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <form method="get" action="bacalagu.php" class="d-flex flex-wrap align-items-center gap-2 mb-0">
          <input type="hidden" name="album" value="<?php echo $id_album; ?>">
          <div class="input-group input-group-sm" style="width:240px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="cari" class="form-control" placeholder="Cari judul" value="<?php echo e($cari); ?>">
          </div>
          <?php if ($cari !== '') { ?><a href="bacalagu.php?album=<?php echo $id_album; ?>" class="btn btn-outline-secondary btn-sm">Reset</a><?php } ?>
        </form>
        <a href="<?php echo e(BASE_URL . '/album/bacaalbum.php'); ?>" class="btn btn-outline-secondary btn-sm">Kembali ke Album</a>
        <!-- tombol untuk tambah data lagu -->
        <a href="form_lagu.php?album=<?php echo $id_album; ?>" class="btn btn-primary btn-sm">+ Add Lagu</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
<?php
// Process the result set
if ($result->num_rows > 0) {
  echo "<table class='table table-striped table-hover mb-0'>";
  echo "<thead>";
  echo "<tr><th>No</th><th>No. Urut</th><th>Judul Lagu</th><th>Spotify</th><th>Aksi</th></tr>";
  echo "</thead>";
  echo "<tbody>";

  $no = 1;
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . e($row["no_urut"]) . "</td>";
    echo "<td>" . e($row["judul_lagu"]) . "</td>";
    echo "<td>" . crud_sel(['link_spotify', 'Spotify', 'link'], $row) . "</td>";
    echo "<td>" .
    "<a href='form_editlagu.php?id=" . (int)$row["id_lagu"] . "&album=" . $id_album . "' class='btn btn-warning btn-sm'>Edit</a> " .
    "<form method='post' action='hapuslagu.php' class='d-inline' onsubmit='return confirm(\"Apakah anda yakin ingin menghapus data ini?\")'>" .
    "<input type='hidden' name='csrf' value='" . csrf_token() . "'>" .
    "<input type='hidden' name='id' value='" . (int)$row["id_lagu"] . "'>" .
    "<input type='hidden' name='id_album' value='" . $id_album . "'>" .
    "<button type='submit' class='btn btn-danger btn-sm'>Delete</button>" .
    "</form>" .
    "</td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";
} else {
  echo "<div class='alert alert-warning m-3'>0 results - Data not found</div>";
}

$conn->close();
?>
  </div>
  <div class="card-footer text-muted small">
    Menampilkan <?php echo (int)$jumlah; ?> data<?php if ($cari !== '') { ?> untuk pencarian "<?php echo e($cari); ?>"<?php } ?>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
