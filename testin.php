<?php
// DB config
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "informatin"; // انتبهي للاسم

// اتصال
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset('utf8mb4');

// أفعال الصفحة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // إضافة مستخدم جديد (بدون id)
  if (isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');
    $age  = isset($_POST['age']) ? (int)$_POST['age'] : null;

    if ($name !== '' && $age !== null) {
      $stmt = $conn->prepare("INSERT INTO users (name, age, status) VALUES (?, ?, 0)");
      $stmt->bind_param("si", $name, $age);
      $stmt->execute();
      $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }
// تبديل الحالة 0/1
  if (isset($_POST['toggle_id'])) {
    $id = (int)$_POST['toggle_id'];
    $stmt = $conn->prepare("UPDATE users SET status = 1 - status WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }
}

// جلب البيانات
$res = $conn->query("SELECT id, name, age, status FROM users ORDER BY id");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Users</title>
<style>
  body{font-family:system-ui,Segoe UI,Tahoma,Arial; padding:24px}
  label{margin-right:12px}
  input[type=text],input[type=number]{padding:6px; width:160px}
  button{padding:6px 12px; cursor:pointer}
  form.inline{display:inline}
  table{border-collapse:collapse; width:520px; margin-top:16px}
  th,td{border:1px solid #ccc; padding:8px; text-align:center}
</style>
</head>
<body>

<form method="post">
  <label>Name:
    <input type="text" name="name" required>
  </label>
  <label>Age:
    <input type="number" name="age" min="0" required>
  </label>
  <button type="submit" name="add">Submit</button>
</form>

<table>
  <thead>
    <tr>
      <th>ID</th><th>Name</th><th>Age</th><th>Status</th><th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= (int)$row['age'] ?></td>
        <td><?= (int)$row['status'] ?></td>
        <td>
          <form method="post" class="inline">
            <input type="hidden" name="toggle_id" value="<?= (int)$row['id'] ?>">
            <button type="submit">Toggle</button>
          </form>
  </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>