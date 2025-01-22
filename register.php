<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $national_code = $_POST['national_code'];
    $avatar = '';

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $avatar = 'uploads/' . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar);
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, password, phone, gender, national_code, avatar) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $password, $phone, $gender, $national_code, $avatar]);

    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        form input, form select, form button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>ثبت‌نام</h2>
        <input type="text" name="username" placeholder="نام کاربری" required>
        <input type="password" name="password" placeholder="رمز عبور" required>
        <input type="text" name="phone" placeholder="تلفن همراه" required>
        <select name="gender">
            <option value="male">مرد</option>
            <option value="female">زن</option>
        </select>
        <input type="text" name="national_code" placeholder="کد ملی" required>
        <input type="file" name="avatar" required>
        <button type="submit">ثبت‌نام</button>
    </form>
</body>
</html>
