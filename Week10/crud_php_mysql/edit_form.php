<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
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

$nim = '';
$data = array('nim' => '', 'name' => '', 'birth_city' => '', 'birth_date' => '', 'faculty' => '', 'department' => '', 'gpa' => '');
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = $_POST["nim"];
    $name = mysqli_real_escape_string($connection, $_POST["name"]);
    $birth_city = mysqli_real_escape_string($connection, $_POST["birth_city"]);
    $birth_date = mysqli_real_escape_string($connection, $_POST["birth_date"]);
    $faculty = mysqli_real_escape_string($connection, $_POST["faculty"]);
    $department = mysqli_real_escape_string($connection, $_POST["department"]);
    $gpa = mysqli_real_escape_string($connection, $_POST["gpa"]);

    $query = "UPDATE student SET name = '$name', birth_city = '$birth_city', birth_date = '$birth_date', faculty = '$faculty', department = '$department', gpa = '$gpa' WHERE nim = '$nim'";
    $update_result = mysqli_query($connection, $query);

    if ($update_result) {
        $message = "Data mahasiswa berhasil diupdate.";
    } else {
        $message = "Gagal mengupdate data mahasiswa: " . mysqli_error($connection);
    }
    header("Location: student_edit.php?message=" . urlencode($message));
    exit;
} elseif (isset($_GET["nim"])) {
    $nim = $_GET["nim"];
    $query = "SELECT * FROM student WHERE nim = '$nim'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        $data = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    } else {
        die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
    <link href="assets/style.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div id="header">
            <h1 id="logo">Edit Data Mahasiswa</h1>
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
        <fieldset>
            <legend>Edit Data Mahasiswa NIM <?php echo $data['nim']; ?></legend>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <table style="width: 555px;">
                    <tr>
                        <td><label for="nim">NIM</label></td>
                        <td><input type="number" name="nim" value="<?php echo $data['nim']; ?>" disabled></td>
                    </tr>
                    <tr>
                        <td><label for="name">Nama</label></td>
                        <td><input type="text" id="name" name="name" value="<?php echo $data['name']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="birth_city">Tanggal Lahir</label></td>
                        <td><input type="text" id="birth_city" name="birth_city" value="<?php echo $data['birth_city']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="birth_date">Tanggal Lahir</label></td>
                        <td><input type="date" id="birth_date" name="birth_date" value="<?php echo $data['birth_date']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="faculty">Fakultas</label></td>
                        <td>
                            <select name="faculty" id="faculty">
                                <?php
                                if ($data['faculty'] == "FTIB") {
                                    echo "<option value='FTIB'>FTIB</option>";
                                    echo "<option value='FTIC'>FTEIC</option>";
                                } else {
                                    echo "<option value='FTIC'>FTEIC</option>";
                                    echo "<option value='FTIB'>FTIB</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="department">Jurusan</label></td>
                        <td> <input type="text" id="department" name="department" value="<?php echo $data['department']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="gpa">IPK:</label></td>
                        <td><input type="text" id="gpa" name="gpa" value="<?php echo $data['gpa']; ?>"></td>
                    </tr>
                </table>
                <input type="submit" value="Update">
            </form>
        </fieldset>

    </div>
</body>

</html>