<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $q = mysqli_query($conn,"
        SELECT * FROM admin WHERE username='$username'
    ");

    if(mysqli_num_rows($q) > 0){
        $data = mysqli_fetch_assoc($q);

        if(password_verify($password, $data['password'])){
            $_SESSION['admin'] = $data['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <h2>Login Admin</h2>

            <?php if(isset($error)) { ?>
                <div class="login-error">
                    <?= $error ?>
                </div>
            <?php } ?>

            <form method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button name="login">Login</button>
            </form>
        </div>
    </div>

</body>


</html>
