<?php
$host = 'db';
$user = 'root';
$pass = 'RootPassword123!';
$db   = 'qlsv_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Sửa lỗi phông chữ tiếng Việt UTF-8
$conn->set_charset("utf8mb4");

// 1. CHỨC NĂNG DELETE (XÓA SINH VIÊN)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM sinhvien WHERE id=$id");
    header("Location: index.php");
    exit();
}

// 2. LẤY THÔNG TIN SINH VIÊN CẦN SỬA (UPDATE PREPARATION)
$edit_sv = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM sinhvien WHERE id=$id");
    if ($res->num_rows > 0) {
        $edit_sv = $res->fetch_assoc();
    }
}

// 3. CHỨC NĂNG CREATE & UPDATE (THÊM MỚI HOẶC CẬP NHẬT)
if (isset($_POST['save'])) {
    $masv = $_POST['masv'];
    $hoten = $_POST['hoten'];
    $lop = $_POST['lop'];
    $diem = $_POST['diem'];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Cập nhật (Update)
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE sinhvien SET masv=?, hoten=?, lop=?, diem=? WHERE id=?");
        $stmt->bind_param("sssdi", $masv, $hoten, $lop, $diem, $id);
    } else {
        // Thêm mới (Create)
        $stmt = $conn->prepare("INSERT INTO sinhvien (masv, hoten, lop, diem) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $masv, $hoten, $lop, $diem);
    }
    $stmt->execute();
    header("Location: index.php");
    exit();
}

// 4. CHỨC NĂNG READ (LẤY DANH SÁCH SINH VIÊN)
$result = $conn->query("SELECT * FROM sinhvien");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản lý Sinh viên</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f6f9; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        form { background: #fff; padding: 20px; border-radius: 5px; max-width: 500px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        input { width: 95%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-edit { background: #ffc107; color: #333; padding: 6px 12px; text-decoration: none; border-radius: 3px; font-size: 13px; margin-right: 5px; font-weight: bold; }
        .btn-delete { background: #dc3545; color: white; padding: 6px 12px; text-decoration: none; border-radius: 3px; font-size: 13px; font-weight: bold; }
        .btn-cancel { background: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 13px; }
    </style>
</head>
<body>
    <h2>Hệ Thống Quản Lý Sinh Viên</h2>
    
    <form method="POST">
        <h3><?= $edit_sv ? "Cập Nhật Thông Tin Sinh Viên" : "Thêm Sinh Viên Mới" ?></h3>
        <?php if ($edit_sv): ?>
            <input type="hidden" name="id" value="<?= $edit_sv['id'] ?>">
        <?php endif; ?>
        
        <label>Mã SV:</label>
        <input type="text" name="masv" value="<?= $edit_sv ? htmlspecialchars($edit_sv['masv']) : '' ?>" placeholder="VD: DTC241200007" required>
        
        <label>Họ và Tên:</label>
        <input type="text" name="hoten" value="<?= $edit_sv ? htmlspecialchars($edit_sv['hoten']) : '' ?>" placeholder="VD: Vàng Thị Sinh" required>
        
        <label>Lớp:</label>
        <input type="text" name="lop" value="<?= $edit_sv ? htmlspecialchars($edit_sv['lop']) : '' ?>" placeholder="VD: CNTT-K23E" required>
        
        <label>Điểm số:</label>
        <input type="number" step="0.1" name="diem" value="<?= $edit_sv ? htmlspecialchars($edit_sv['diem']) : '' ?>" placeholder="VD: 9.5" required>
        
        <button type="submit" name="save"><?= $edit_sv ? "Lưu Thay Đổi" : "Thêm Sinh Viên" ?></button>
        <?php if ($edit_sv): ?>
            <a href="index.php" class="btn-cancel">Hủy bỏ</a>
        <?php endif; ?>
    </form>

    <h3>Danh Sách Sinh Viên</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Mã SV</th>
            <th>Họ Tên</th>
            <th>Lớp</th>
            <th>Điểm</th>
            <th>Thao tác</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['masv']) ?></td>
            <td><?= htmlspecialchars($row['hoten']) ?></td>
            <td><?= htmlspecialchars($row['lop']) ?></td>
            <td><?= $row['diem'] ?></td>
            <td>
                <a href="index.php?edit=<?= $row['id'] ?>" class="btn-edit">Sửa</a>
                <a href="index.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này?')">Xóa</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
