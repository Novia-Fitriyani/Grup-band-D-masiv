<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$cari   = trim($_GET['cari'] ?? '');

// Query SELECT untuk tabel album
$sql = "SELECT a.*, (SELECT COUNT(*) FROM lagu l WHERE l.id_album = a.id_album) AS jumlah_lagu FROM album a";
$kondisi    = [];
$bind_tipe  = "";
$bind_nilai = [];
if ($cari !== '') {
  $kondisi[]    = "a.judul_album LIKE ?";
  $bind_tipe   .= "s";
  $bind_nilai[] = '%' . addcslashes($cari, '%_\\') . '%';   // tanda % dan _ yang diketik dianggap huruf biasa
}
if ($kondisi) {
  $sql .= " WHERE " . implode(" AND ", $kondisi);
}
$sql .= " ORDER BY a.tanggal_rilis DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($bind_tipe !== "") {
  mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);
}
mysqli_stmt_execute($stmt);
// Execute the SQL query
$result = mysqli_stmt_get_result($stmt);
$jumlah = $result->num_rows;

$judul = 'List Album';
$menu  = 'album';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h3 class="card-title mb-0">List Album</h3>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <form method="get" action="bacaalbum.php" class="d-flex flex-wrap align-items-center gap-2 mb-0">
          <div class="input-group input-group-sm" style="width:240px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="cari" class="form-control" placeholder="Cari judul" value="<?php echo e($cari); ?>">
          </div>
          <?php if ($cari !== '') { ?><a href="bacaalbum.php" class="btn btn-outline-secondary btn-sm">Reset</a><?php } ?>
        </form>
        <!-- tombol untuk tambah data album -->
        <a href="form_album.php" class="btn btn-primary btn-sm">+ Add Album</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
<?php
// Process the result set
if ($result->num_rows > 0) {
  echo "<table class='table table-striped table-hover mb-0'>";
  echo "<thead>";
  echo "<tr><th>No</th><th>Cover</th><th>Judul</th><th>Rilis</th><th>Jumlah Lagu</th><th>Spotify</th><th>Aksi</th></tr>";
  echo "</thead>";
  echo "<tbody>";

  $no = 1;
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . crud_sel(['cover', 'Cover', 'image'], $row) . "</td>";
    echo "<td>" . e($row["judul_album"]) . "</td>";
    echo "<td>" . crud_sel(['tanggal_rilis', 'Rilis', 'date'], $row) . "</td>";
    echo "<td>" . e($row["jumlah_lagu"]) . "</td>";
    echo "<td>" . crud_sel(['link_spotify', 'Spotify', 'link'], $row) . "</td>";
    echo "<td>" .
    "<a href='" . BASE_URL . "/lagu/bacalagu.php?album=" . (int)$row["id_album"] . "' class='btn btn-info btn-sm'>Kelola Lagu</a> " .
    "<a href='form_editalbum.php?id=" . (int)$row["id_album"] . "' class='btn btn-warning btn-sm'>Edit</a> " .
    "<form method='post' action='hapusalbum.php' class='d-inline' onsubmit='return confirm(\"Apakah anda yakin ingin menghapus album ini? Semua lagu di dalam album ini juga ikut terhapus.\")'>" .
    "<input type='hidden' name='csrf' value='" . csrf_token() . "'>" .
    "<input type='hidden' name='id' value='" . (int)$row["id_album"] . "'>" .
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
