<?php
$host = 'localhost';
$user = 'gdiifznm_munna';
$pass = 'MunnaDatabase';
$db_name = 'gdiifznm_munna';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM items");
while ($row = $result->fetch_assoc()) {
    echo '
    <div class="swiper-slide">
        <div class="item">
            <div class="img">
                <img src="' . $row['image_path'] . '" alt="img" />
            </div>
            <div class="cont d-flex align-items-center mt-30 pb-15 bord-thin-bottom">
                <div>
                    <a href="' . $row['link'] . '">
                        <p class="h4">' . $row['title'] . '</p>
                    </a>
                    <p>' . $row['category'] . '</p>
                </div>
                <div class="ml-auto">
                    <a href="' . $row['link'] . '" class="rmore">
                        <img src="assets/imgs/arrow-right.png" alt="img" class="icon-img-20" />
                    </a>
                </div>
            </div>
        </div>
    </div>';
}

$conn->close();
?>
