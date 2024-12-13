<?php
include('../includes/koneksidb.php');  // Pastikan koneksi ke database sudah benar
include('../includes/navbar.php');

// Proses form submit untuk menambah santri
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_santri = mysqli_real_escape_string($conn, $_POST['nama_santri']);
    $id_kelas = $_POST['id_kelas']; // Pastikan menggunakan kelas_id di form

    // Query untuk menambahkan santri baru
    $sql = "INSERT INTO santri (nama_santri, id_kelas) VALUES ('$nama_santri', '$id_kelas')";

    // Eksekusi query
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success'>Santri berhasil ditambahkan!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Ambil data unit dan kelas
$sql_unit = "SELECT * FROM unit";
$result_unit = mysqli_query($conn, $sql_unit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Santri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Tambah Santri Baru</h1>
        <form method="POST">

        <!-- Input Nama Santri -->
        <div class="mb-3">
                <label for="nama_santri" class="form-label">Nama Santri</label>
                <input type="text" name="nama_santri" id="nama_santri" class="form-control" required>
            </div>
            <!-- Pilih Tingkat -->
            <div class="mb-3">
                <label for="id_unit" class="form-label">Pilih Unit</label>
                <select name="id_unit" id="id_unit" class="form-select" required>
                    <option value="">--Pilih Unit--</option>
                    <?php while ($unit = mysqli_fetch_assoc($result_unit)) { ?>
                        <option value="<?= $unit['id'] ?>"><?= $unit['nama_unit'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Pilih Kelas (ini akan di-load berdasarkan tingkat yang dipilih) -->
            <div class="mb-3">
                <label for="id_kelas" class="form-label">Pilih Kelas</label>
                <select name="id_kelas" id="id_kelas" class="form-select" required>
                    <option value="">--Pilih Kelas--</option>
                   
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Santri</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#id_unit').on('change', function() {
        var id_unit = $(this).val();  // Mendapatkan nilai id_unit yang dipilih

        if (id_unit) {
            $.ajax({
                url: 'get_kelas.php',  // File PHP untuk mengambil kelas
                method: 'GET',
                data: { id_unit: id_unit },  // Kirimkan id_unit melalui parameter GET
                success: function(response) {
                    $('#id_kelas').html(response);  // Update dropdown kelas dengan hasil response
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);  // Debugging jika ada error
                }
            });
        } else {
            $('#id_kelas').html('<option value="">--Pilih Kelas--</option>');
        }
    });
</script>
</body>
</html>
