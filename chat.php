<?php
session_start();
require 'db.php';

// بررسی وضعیت ورود کاربر
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ثبت پیام یا فایل جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'] ?? '';
    $file_path = '';

    // آپلود فایل (در صورت وجود)
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $file_path = 'uploads/' . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
    }

    // ذخیره پیام در پایگاه داده
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, file_path) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $message, $file_path]);
}

// دریافت پیام‌ها
$messages = $pdo->query("
    SELECT m.*, u.username, u.avatar 
    FROM messages m 
    JOIN users u ON m.user_id = u.id 
    ORDER BY m.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چت‌روم</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0ffe0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .chat-container {
            width: 90%;
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .chat-header {
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .chat-messages {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .message img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            cursor: pointer;
        }
        .message span {
            font-weight: bold;
            margin-right: 5px;
        }
        form {
            margin-top: 20px;
        }
        textarea, input[type="file"], button {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">چت‌روم عمومی</div>
        <div class="chat-messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <img src="<?= htmlspecialchars($msg['avatar']) ?>" alt="Avatar" 
                         onclick="window.open('<?= htmlspecialchars($msg['avatar']) ?>', '_blank')">
                    <div>
                        <span><?= htmlspecialchars($msg['username']) ?>:</span>
                        <?= htmlspecialchars($msg['message']) ?>
                        <?php if ($msg['file_path']): ?>
                            <br><a href="<?= htmlspecialchars($msg['file_path']) ?>" target="_blank">فایل پیوست</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <textarea name="message" placeholder="پیام خود را بنویسید..."></textarea>
            <input type="file" name="file">
            <button type="submit">ارسال</button>
        </form>
    </div>
</body>
</html>
