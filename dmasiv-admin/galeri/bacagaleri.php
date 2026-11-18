<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$cari   = trim($_GET['cari'] ?? '');
$filter = trim($_GET['filter'] ?? '');

// Query SELECT untuk tabel galeri
$sql = "SELECT * FROM galeri";
$kondisi    = [];
$bind_tipe  = "";
$bind_nilai = [];
if ($cari !== '') {
  $kondisi[]    = "judul LIKE ?";
  $bind_tipe   .= "s";
  $bind_nilai[] = '%' . addcslashes($cari, '%_\\') . '%';   // tanda % dan _ yang diketik dianggap huruf biasa
}
if ($filter !== '') {
  $kondisi[]    = "tipe = ?";
  $bind_tipe   .= "s";
  $bind_nilai[] = $filter;
}
if ($kondisi) {
  $sql .= " WHERE " . implode(" AND ", $kondisi);
}
$sql .= " ORDER BY tanggal DESC, id_galeri DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($bind_tipe !== "") {
  mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);
}
mysqli_stmt_execute($stmt);
// Execute the SQL query
$result = mysqli_stmt_get_result($stmt);
$jumlah = $result->num_rows;

$opsi_filter = ['foto' => 'Foto', 'video' => 'Video'];

$judul = 'List Galeri';
$menu  = 'galeri';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h3 class="card-title mb-0">List Galeri</h3>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <form method="get" action="bacagaleri.php" class="d-flex flex-wrap align-items-center gap-2 mb-0">
          <div class="input-group input-group-sm" style="width:240px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="cari" class="form-control" placeholder="Cari judul" value="<?php echo e($cari); ?>">
          </div>
          <select name="filter" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua jenis</option>
            <?php foreach ($opsi_filter as $ov => $ol) { ?>
              <option value="<?php echo e($ov); ?>" <?php echo ((string)$filter === (string)$ov) ? 'selected' : ''; ?>><?php echo e($ol); ?></option>
            <?php } ?>
          </select>
          <?php if ($cari !== '' || $filter !== '') { ?><a href="bacagaleri.php" class="btn btn-outline-secondary btn-sm">Reset</a><?php } ?>
        </form>
        <!-- tombol untuk tambah data galeri -->
        <a href="form_galeri.php" class="btn btn-primary btn-sm">+ Add Galeri</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
<?php
// Process the result set
if ($result->num_rows > 0) {
  echo "<table class='table table-striped table-hover mb-0'>";
  echo "<thead>";
  echo "<tr><th>No</th><th>Gambar</th><th>Judul</th><th>Jenis</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>";
  echo "</thead>";
  echo "<tbody>";

  $no = 1;
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . crud_sel(['file_thumbnail', 'Gambar', 'image'], $row) . "</td>";
    echo "<td>" . e($row["judul"]) . "</td>";
    echo "<td>" . crud_sel(['tipe', 'Jenis', 'badge'], $row) . "</td>";
    echo "<td>" . crud_sel(['tanggal', 'Tanggal', 'date'], $row) . "</td>";
    echo "<td>" . crud_sel(['status_aktif', 'Status', 'badge'], $row) . "</td>";
    echo "<td>" .
    "<a href='form_editgaleri.php?id=" . (int)$row["id_galeri"] . "' class='btn btn-warning btn-sm'>Edit</a> " .
    "<form method='post' action='hapusgaleri.php' class='d-inline' onsubmit='return confirm(\"Apakah anda yakin ingin menghapus data ini?\")'>" .
    "<input type='hidden' name='csrf' value='" . csrf_token() . "'>" .
    "<input type='hidden' name='id' value='" . (int)$row["id_galeri"] . "'>" .
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
