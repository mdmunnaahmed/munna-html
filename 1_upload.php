<?php
$host = 'localhost';
$user = 'gdiifznm_munna';
$pass = 'MunnaDatabase';
$db_name = 'gdiifznm_munna';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $link = $_POST['link'];
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = 'assets/imgs/products/';
        $image_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("UPDATE items SET title = ?, category = ?, image_path = ?, link = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $title, $category, $image_path, $link, $item_id); // Use the correct item ID
    $success = $stmt->execute();

    echo json_encode(['success' => $success]);
    $stmt->close();
}

$conn->close();
?>
