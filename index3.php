<?php
include 'koneksi.php';

// Tambah tugas
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task_title']) && !isset($_POST['edit_id'])) {
    $title = htmlspecialchars($_POST['task_title']);
    $stmt = $conn->prepare("INSERT INTO tasks (title, status) VALUES (?, 'belum')");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $stmt->close();
}

// Ubah status via checkbox
if (isset($_POST['task_id'])) {
    $id = intval($_POST['task_id']);
    $newStatus = isset($_POST['status_toggle']) ? 'selesai' : 'belum';
    mysqli_query($conn, "UPDATE tasks SET status = '$newStatus' WHERE id = $id");
    header("Location: index3.php");
    exit;
}


// Hapus tugas
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM tasks WHERE id = $id");
    header("Location: index3.php");
    exit;
}

// Edit judul tugas
if (isset($_POST['edit_id']) && isset($_POST['new_title'])) {
    $id = intval($_POST['edit_id']);
    $newTitle = htmlspecialchars($_POST['new_title']);
    mysqli_query($conn, "UPDATE tasks SET title = '$newTitle' WHERE id = $id");
    header("Location: index3.php");
    exit;
}

// Ambil semua data tugas
$tasks = [];
$result = mysqli_query($conn, "SELECT * FROM tasks ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $tasks[] = $row;
}

// Cek jika sedang mengedit
$editId = isset($_GET['edit']) ? intval($_GET['edit']) : null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>To-Do List Stylish</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: #fff;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-right: 10px;
        }

        input[type="submit"] {
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background: #fdfdfd;
            margin-bottom: 12px;
            padding: 15px;
            border-radius: 10px;
            border-left: 5px solid #007bff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .selesai {
            border-left-color: #28a745;
            text-decoration: line-through;
            color: #999;
        }

        .task-title {
            flex: 1;
            margin-left: 10px;
        }

        .buttons a,
        .buttons form {
            margin-left: 5px;
            display: inline-block;
        }

        .edit, .hapus {
            padding: 6px 10px;
            font-size: 13px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }

        .edit {
            background-color: #ffc107;
        }

        .hapus {
            background-color: #dc3545;
        }

        .edit:hover {
            background-color: #e0a800;
        }

        .hapus:hover {
            background-color: #c82333;
        }

        .checkbox-form {
            display: flex;
            align-items: center;
        }

        .edit-form {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .edit-form input[type="text"] {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📝 To-Do List</h2>

        <!-- Form tambah tugas -->
        <form method="post">
            <input type="text" name="task_title" placeholder="Tambahkan tugas baru..." required>
            <input type="submit" value="Tambah">
        </form>

        <!-- Daftar tugas -->
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li class="<?= $task['status'] ?>">
                    <!-- Checkbox Status -->
                    <form method="post" class="checkbox-form" style="margin: 0;">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <input type="checkbox" name="status_toggle" onchange="this.form.submit()" <?= $task['status'] === 'selesai' ? 'checked' : '' ?>>
                    </form>

                    <!-- Judul atau Form Edit -->
                    <div class="task-title">
                        <?php if ($editId === (int)$task['id']): ?>
                            <!-- Form edit judul (terpisah!) -->
                            <form method="post" class="edit-form" style="display: flex; gap: 10px; margin-top: 5px;">
                                <input type="text" name="new_title" value="<?= htmlspecialchars($task['title']) ?>" required style="flex: 1;">
                                <input type="hidden" name="edit_id" value="<?= $task['id'] ?>">
                                <input type="submit" value="Simpan" style="background:#007bff;color:#fff;border:none;padding:10px;border-radius:8px;">
                            </form>
                        <?php else: ?>
                            <?= htmlspecialchars($task['title']) ?>
                        <?php endif; ?>
                    </div>

                    <!-- Tombol Edit & Hapus -->
                    <div class="buttons">
                        <?php if ($editId !== (int)$task['id']): ?>
                            <a href="?edit=<?= $task['id'] ?>" class="edit">Edit</a>
                        <?php endif; ?>
                        <a href="?hapus=<?= $task['id'] ?>" class="hapus" onclick="return confirm('Yakin hapus tugas ini?')">Hapus</a>
                    </div>
                </li>

            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
