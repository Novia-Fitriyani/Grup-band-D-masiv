<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Query SELECT untuk tabel personel
$sql = "SELECT * FROM personel ORDER BY COALESCE(urutan, 999999), id_personel";
// Execute the SQL query
$result = $conn->query($sql);

$judul = 'List Personel';
$menu  = 'personel';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h3 class="card-title mb-0">List Personel</h3>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- tombol untuk tambah data personel -->
        <a href="form_personel.php" class="btn btn-primary btn-sm">+ Add Personel</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
<?php
// Process the result set
if ($result->num_rows > 0) {
  echo "<table class='table table-striped table-hover mb-0'>";
  echo "<thead>";
  echo "<tr><th>No</th><th>Foto</th><th>Nama</th><th>Posisi</th><th>Urutan</th><th>Aksi</th></tr>";
  echo "</thead>";
  echo "<tbody>";

  $no = 1;
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . crud_sel(['foto', 'Foto', 'image'], $row) . "</td>";
    echo "<td>" . e($row["nama_panggilan"]) . "</td>";
    echo "<td>" . e($row["peran"]) . "</td>";
    echo "<td>" . e($row["urutan"]) . "</td>";
    echo "<td>" .
    "<a href='form_editpersonel.php?id=" . (int)$row["id_personel"] . "' class='btn btn-warning btn-sm'>Edit</a> " .
    "<form method='post' action='hapuspersonel.php' class='d-inline' onsubmit='return confirm(\"Apakah anda yakin ingin menghapus data ini?\")'>" .
    "<input type='hidden' name='csrf' value='" . csrf_token() . "'>" .
    "<input type='hidden' name='id' value='" . (int)$row["id_personel"] . "'>" .
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
