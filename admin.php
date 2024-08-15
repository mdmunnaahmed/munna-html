<?php
$host = 'localhost';
$user = 'gdiifznm_munna';
$pass = 'MunnaDatabase';
$db_name = 'gdiifznm_munna';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle add or update request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $link = $_POST['link'];
    $item_id = isset($_POST['update_id']) ? intval($_POST['update_id']) : null;
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = 'assets/imgs/products/';
        $image_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    } else {
        $image_path = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    }

    if ($item_id) {
        $stmt = $conn->prepare("UPDATE items SET title = ?, category = ?, image_path = ?, link = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $title, $category, $image_path, $link, $item_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO items (title, category, image_path, link) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $title, $category, $image_path, $link);
    }
    
    $success = $stmt->execute();
    $stmt->close();
}

// Handle delete request
if (isset($_POST['delete_id'])) {
    $item_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
}

// Fetch all items
$result = $conn->query("SELECT * FROM items");
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Items</title>
    <style>
         * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            color: #444;
        }
        .container {
            max-width: 1300px;
            margin-inline: auto;
            padding-block: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-size: 15px;
            display: block;
            margin-bottom: 8px;
        }
        input {
            height: 45px;
            padding: 10px 12px;
            border: 1px solid #00000040;
            outline-color: gray;
            width: 100%;
        }
        #updateForm {
            max-width: 500px;
            width: 100%;
        }
        button[type='submit'] {
            padding: 12px 25px;
            cursor: pointer;
        }
        .wrapper {
          display: flex;
          gap: 50px;
        }
        .update-form {
            width: 700px;
        }
        #items {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        #items li button {
            padding: 8px 15px;
        }
        #items li > div {
            display: flex;
            align-items: center;
            gap: 15px
        }
        #items li img {
            width: 120px;
            border-radius: 8px;
            vertical-align: middle;
        }
        #preview {
            max-height: 200px;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }
        h3 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
      <div class="wrapper">

        <!-- Update Form -->
        <div class="update-form">
            <form id="updateForm" enctype="multipart/form-data" method="POST">
                <img id="preview" src="" alt="preview" style="width: 350px; height: auto; margin-bottom: 40px" />
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" id="title" required />
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <input type="text" name="category" id="category" required />
                </div>
                <div class="form-group">
                    <label>Image:</label>
                    <input type="file" name="image" id="image" accept="image/*" />
                </div>
                <div class="form-group">
                    <label>Link:</label>
                    <input type="text" name="link" id="link" required />
                </div>
                <input type="hidden" name="update_id" id="update_id" /> <!-- Hidden input for item ID -->
                <input type="hidden" name="existing_image" id="existing_image" /> <!-- Hidden input for existing image -->
                <button type="submit" id='mainSubmitBtn'>Add Item</button>
            </form>
        </div>

        <!-- Item List -->
        <div class="item-list">
            <h3>Items</h3>
            <ul id="items">
                <?php foreach ($items as $item): ?>
                    <li>
                        <div>
                            <img src="<?= $item['image_path']; ?>" alt="<?= $item['title']; ?>" />
                            <span><?= $item['title']; ?></span>
                            <button onclick="editItem(<?= $item['id']; ?>)">Update</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                <input type="hidden" name="delete_id" value="<?= $item['id']; ?>" />
                                <button type="submit">Delete</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
          </div>


        </div>

    </div>

    <script>
        // Load item details into the form for editing
        function editItem(itemId) {
          document.querySelector('#mainSubmitBtn').textContent = 'Update Item';

            const items = <?= json_encode($items); ?>;
            const itemIdInt = parseInt(itemId, 10); // Convert itemId to integer
            const item = items.find(i => parseInt(i.id, 10) === itemIdInt); // Convert item id to integer for comparison
            console.log('item found:', item);
            
            if (item) {
                document.getElementById('title').value = item.title;
                document.getElementById('category').value = item.category;
                document.getElementById('link').value = item.link;
                document.getElementById('preview').src = item.image_path;
                document.getElementById('update_id').value = item.id;
                document.getElementById('existing_image').value = item.image_path;
            } else {
                console.error('No item found with id:', itemIdInt);
            }
        }

        // Image preview
        document.getElementById('image').addEventListener('change', function (event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
