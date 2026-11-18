<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$cari   = trim($_GET['cari'] ?? '');
$filter = trim($_GET['filter'] ?? '');

// Query SELECT untuk tabel event
$sql = "SELECT e.*, k.nama_kategori FROM event e JOIN kategori_event k ON k.id_kategori = e.id_kategori";
$kondisi    = [];
$bind_tipe  = "";
$bind_nilai = [];
if ($cari !== '') {
  $kondisi[]    = "e.judul LIKE ?";
  $bind_tipe   .= "s";
  $bind_nilai[] = '%' . addcslashes($cari, '%_\\') . '%';   // tanda % dan _ yang diketik dianggap huruf biasa
}
if ($filter !== '') {
  $kondisi[]    = "e.lokasi = ?";
  $bind_tipe   .= "s";
  $bind_nilai[] = $filter;
}
if ($kondisi) {
  $sql .= " WHERE " . implode(" AND ", $kondisi);
}
$sql .= " ORDER BY e.tanggal_event DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($bind_tipe !== "") {
  mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);
}
mysqli_stmt_execute($stmt);
// Execute the SQL query
$result = mysqli_stmt_get_result($stmt);
$jumlah = $result->num_rows;

$opsi_filter = [];
$res_filter = mysqli_query($conn, "SELECT DISTINCT lokasi FROM event WHERE lokasi <> '' ORDER BY lokasi");
while ($fl = mysqli_fetch_assoc($res_filter)) {
  $opsi_filter[$fl['lokasi']] = $fl['lokasi'];
}

$judul = 'List Event';
$menu  = 'event';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h3 class="card-title mb-0">List Event</h3>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <form method="get" action="bacaevent.php" class="d-flex flex-wrap align-items-center gap-2 mb-0">
          <div class="input-group input-group-sm" style="width:240px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="cari" class="form-control" placeholder="Cari judul" value="<?php echo e($cari); ?>">
          </div>
          <select name="filter" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua lokasi</option>
            <?php foreach ($opsi_filter as $ov => $ol) { ?>
              <option value="<?php echo e($ov); ?>" <?php echo ((string)$filter === (string)$ov) ? 'selected' : ''; ?>><?php echo e($ol); ?></option>
            <?php } ?>
          </select>
          <?php if ($cari !== '' || $filter !== '') { ?><a href="bacaevent.php" class="btn btn-outline-secondary btn-sm">Reset</a><?php } ?>
        </form>
        <!-- tombol untuk tambah data event -->
        <a href="form_event.php" class="btn btn-primary btn-sm">+ Add Event</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
<?php
// Process the result set
if ($result->num_rows > 0) {
  echo "<table class='table table-striped table-hover mb-0'>";
  echo "<thead>";
  echo "<tr><th>No</th><th>Judul</th><th>Kategori</th><th>Lokasi</th><th>Tanggal</th><th>Mulai</th><th>Aksi</th></tr>";
  echo "</thead>";
  echo "<tbody>";

  $no = 1;
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . e($row["judul"]) . "</td>";
    echo "<td>" . crud_sel(['nama_kategori', 'Kategori', 'badge'], $row) . "</td>";
    echo "<td>" . e($row["lokasi"]) . "</td>";
    echo "<td>" . crud_sel(['tanggal_event', 'Tanggal', 'date'], $row) . "</td>";
    echo "<td>" . ($row["waktu_mulai"] ? e(substr($row["waktu_mulai"], 0, 5)) : "-") . "</td>";
    echo "<td>" .
    "<a href='form_editevent.php?id=" . (int)$row["id_event"] . "' class='btn btn-warning btn-sm'>Edit</a> " .
    "<form method='post' action='hapusevent.php' class='d-inline' onsubmit='return confirm(\"Apakah anda yakin ingin menghapus data ini?\")'>" .
    "<input type='hidden' name='csrf' value='" . csrf_token() . "'>" .
    "<input type='hidden' name='id' value='" . (int)$row["id_event"] . "'>" .
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
