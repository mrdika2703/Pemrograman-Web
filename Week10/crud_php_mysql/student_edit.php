<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("Location: login.php");
}

if (isset($_SESSION['start_time'])) {
  if (time() - $_SESSION['start_time'] > 35) {
    session_unset();
    session_destroy();
  } else {
    $_SESSION['start_time'] = time();
  }
} else {
  $_SESSION['start_time'] = time();
}

include("connection.php");

if (isset($_GET["message"])) {
  $message = $_GET["message"];
}

// Handle deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['nim'])) {
    $nim = $_GET['nim'];
    $deleteQuery = "DELETE FROM student WHERE nim = '$nim'";
    if (mysqli_query($connection, $deleteQuery)) {
        $message = "Data berhasil dihapus.";
    } else {
        $message = "Gagal menghapus data: " . mysqli_error($connection);
    }
    // Redirect untuk menghindari deletion on refresh
    header("Location: student_edit.php?message=" . urlencode($message));
    exit;
}

$query = "SELECT * FROM student ORDER BY nim ASC";
?>


<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Data Mahasiswa</title>
  <link href="assets/style.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <div id="header">
      <h1 id="logo">Data Mahasiswa</h1>
    </div>
    <hr>
    <nav>
      <ul>
        <li><a href="student_view.php">Tampil</a></li>
        <li><a href="student_add.php">Tambah</a>
        <li><a href="student_edit.php"><b>Edit</b></a></li>
        <li><a href="logout.php">Logout</a>
      </ul>
    </nav>
    <?php
    if (isset($message)) {
      echo "<div class='pesan'>$message</div>";
    }
    ?>
    <table border="1">
      <tr>
        <th>NIM</th>
        <th>Nama</th>
        <th>Tempat Lahir</th>
        <th>Tanggal Lahir</th>
        <th>Fakultas</th>
        <th>Jurusan</th>
        <th>IPK</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
      <?php
      $result = mysqli_query($connection, $query);
      if (!$result) {
        die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
      }

      while ($data = mysqli_fetch_assoc($result)) {
        $birth_date = strtotime($data["birth_date"]);
        $formatted_date = date("d-m-Y", $birth_date);

        echo "<tr>";
        echo "<td>$data[nim]</td>";
        echo "<td>$data[name]</td>";
        echo "<td>$data[birth_city]</td>";
        echo "<td>$formatted_date</td>";
        echo "<td>$data[faculty]</td>";
        echo "<td>$data[department]</td>";
        echo "<td>$data[gpa]</td>";
        echo "<td><a href='edit_form.php?nim=$data[nim]'>Edit</a></td>";
        echo "<td><a href='student_edit.php?action=delete&nim=$data[nim]' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Delete</a></td>
        ";
        echo "</tr>";
      }

      mysqli_free_result($result);
      mysqli_close($connection);
      ?>
      
    </table>
  </div>
</body>

</html>