<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$cari   = trim($_GET['cari'] ?? '');
$filter = trim($_GET['filter'] ?? '');

// Query SELECT untuk tabel produk
$sql = "SELECT * FROM produk";
$kondisi    = [];
$bind_tipe  = "";
$bind_nilai = [];
if ($cari !== '') {
  $kondisi[]    = "nama_produk LIKE ?";
  $bind_tipe   .= "s";
  $bind_nilai[] = '%' . addcslashes($cari, '%_\\') . '%';   // tanda % dan _ yang diketik dianggap huruf biasa
}
if ($filter !== '') {
  $kondisi[]    = "status = ?";
  $bind_tipe   .= "s";
  $bind_nilai[] = $filter;
}
if ($kondisi) {
  $sql .= " WHERE " . implode(" AND ", $kondisi);
}
$sql .= " ORDER BY id_produk DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($bind_tipe !== "") {
  mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);
}
mysqli_stmt_execute($stmt);
// Execute the SQL query
$result = mysqli_stmt_get_result($stmt);
$jumlah = $result->num_rows;

$opsi_filter = ['tersedia' => 'Tersedia', 'habis' => 'Habis'];

$judul = 'List Produk';
$menu  = 'produk';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h3 class="card-title mb-0">List Produk</h3>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <form method="get" action="bacaproduk.php" class="d-flex flex-wrap align-items-center gap-2 mb-0">
          <div class="input-group input-group-sm" style="width:240px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="cari" class="form-control" placeholder="Cari produk" value="<?php echo e($cari); ?>">
          </div>
          <select name="filter" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua status</option>
            <?php foreach ($opsi_filter as $ov => $ol) { ?>
              <option value="<?php echo e($ov); ?>" <?php echo ((string)$filter === (string)$ov) ? 'selected' : ''; ?>><?php echo e($ol); ?></option>
            <?php } ?>
          </select>
          <?php if ($cari !== '' || $filter !== '') { ?><a href="bacaproduk.php" class="btn btn-outline-secondary btn-sm">Reset</a><?php } ?>
        </form>
        <!-- tombol untuk tambah data produk -->
        <a href="form_produk.php" class="btn btn-primary btn-sm">+ Add Produk</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
<?php
// Process the result set
if ($result->num_rows > 0) {
  echo "<table class='table table-striped table-hover mb-0'>";
  echo "<thead>";
  echo "<tr><th>No</th><th>Foto</th><th>Produk</th><th>Harga</th><th>Status</th><th>Link Beli</th><th>Aksi</th></tr>";
  echo "</thead>";
  echo "<tbody>";

  $no = 1;
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . crud_sel(['gambar', 'Foto', 'image'], $row) . "</td>";
    echo "<td>" . e($row["nama_produk"]) . "</td>";
    echo "<td>" . crud_sel(['harga', 'Harga', 'rupiah'], $row) . "</td>";
    echo "<td>" . crud_sel(['status', 'Status', 'badge'], $row) . "</td>";
    echo "<td>" . crud_sel(['link_beli', 'Link Beli', 'link'], $row) . "</td>";
    echo "<td>" .
    "<a href='form_editproduk.php?id=" . (int)$row["id_produk"] . "' class='btn btn-warning btn-sm'>Edit</a> " .
    "<form method='post' action='hapusproduk.php' class='d-inline' onsubmit='return confirm(\"Apakah anda yakin ingin menghapus data ini?\")'>" .
    "<input type='hidden' name='csrf' value='" . csrf_token() . "'>" .
    "<input type='hidden' name='id' value='" . (int)$row["id_produk"] . "'>" .
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
