<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Query SELECT untuk tabel media_sosial
$sql = "SELECT * FROM media_sosial ORDER BY platform";
// Execute the SQL query
$result = $conn->query($sql);

$judul = 'List Media Sosial';
$menu  = 'sosmed';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h3 class="card-title mb-0">List Media Sosial</h3>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- tombol untuk tambah data media sosial -->
        <a href="form_mediasosial.php" class="btn btn-primary btn-sm">+ Add Media Sosial</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
<?php
// Process the result set
if ($result->num_rows > 0) {
  echo "<table class='table table-striped table-hover mb-0'>";
  echo "<thead>";
  echo "<tr><th>No</th><th>Platform</th><th>Akun</th><th>Narahubung</th><th>Telepon</th><th>Aksi</th></tr>";
  echo "</thead>";
  echo "<tbody>";

  $no = 1;
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . e($row["platform"]) . "</td>";
    echo "<td>" . crud_sel(['url', 'Akun', 'link'], $row) . "</td>";
    echo "<td>" . e($row["nama_kontak"]) . "</td>";
    echo "<td>" . e($row["no_telepon"]) . "</td>";
    echo "<td>" .
    "<a href='form_editmediasosial.php?id=" . (int)$row["id_sosmed"] . "' class='btn btn-warning btn-sm'>Edit</a> " .
    "<form method='post' action='hapusmediasosial.php' class='d-inline' onsubmit='return confirm(\"Apakah anda yakin ingin menghapus data ini?\")'>" .
    "<input type='hidden' name='csrf' value='" . csrf_token() . "'>" .
    "<input type='hidden' name='id' value='" . (int)$row["id_sosmed"] . "'>" .
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
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
