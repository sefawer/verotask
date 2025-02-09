<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = 'uploads/'; // Yüklenecek dosyaların kaydedileceği klasör
    $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['image']['name']);

    // Dosya uzantısını kontrol et (sadece resim dosyalarına izin ver)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        die(json_encode(['status' => 'error', 'message' => 'Only JPEG, PNG, and GIF files are allowed.']));
    }

    // Dosyayı kaydet
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully.', 'file' => $uploadFile]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload error.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
