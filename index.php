<?php
function curlReq($url, $authHeader, $requestbody, $method) {
    $curl = curl_init();
    curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_POSTFIELDS => $requestbody,
    CURLOPT_HTTPHEADER => [
        "Authorization: $authHeader",
        "Content-Type: application/json"
    ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
        exit();
    } else {
        return $response;
    }
}
$auth_response = curlReq(
    "https://api.baubuddy.de/index.php/login",
    "Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz",
    "{\"username\":\"365\", \"password\":\"1\"}",
    "POST"
);
$resAuth = json_decode($auth_response);
$authToken=$resAuth->oauth->access_token;
$response=curlReq(
    "https://api.baubuddy.de/index.php/v1/tasks/select",
    "Bearer ".$authToken,
    "{}",
    "GET"
);

$datas = json_decode($response);

if(isset($_GET['newDataCheck'])) {
    header("Content-Type: application/json");
    echo $response;
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Vero Digital Challenge</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .dataTables_wrapper .dataTables_filter input {
                margin-left: 0.5em;
            }
        </style>
        <script>
            $(document).ready(function() {
                var table = $('#table1').DataTable(); // Tabloyu değişkene atayalım

                function updateTable() {
                    $.ajax({
                        url: '?newDataCheck=1',
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            table.clear().draw();
                            response.forEach(function(data) {
                                table.row.add([
                                    data.task,
                                    data.title,
                                    data.description,
                                    '<span style="color:' + data.colorCode + '">' + data.colorCode + '</span>'
                                ]).draw(false);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                        }
                    });
                }

                updateTable();
                setInterval(updateTable, 60000);


                document.getElementById('imageUploadInput').addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Önizleme göster
                            const previewImage = document.getElementById('previewImage');
                            previewImage.src = e.target.result;
                            previewImage.style.display = 'block';
                            document.querySelector('#imagePreview p').style.display = 'none'; // Metni gizle

                            // Dosyayı otomatik yükle
                            const formData = new FormData();
                            formData.append('image', file);

                            $.ajax({
                                url: 'upload.php', // Dosya yükleme işlemini yapacak PHP dosyası
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    console.log('File uploaded successfully:', response);
                                    // Modal'ı kapatmadan yükleme başarılı mesajı gösterebilirsiniz
                                    alert('File uploaded successfully!');
                                },
                                error: function(xhr, status, error) {
                                    console.error('File upload error:', error);
                                    alert('File upload failed. Please try again.');
                                }
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        </script>
    </head>
    <body>
        <table id="table1">
            <thead>
                <th>task</th>
                <th>title</th>
                <th>description</th>
                <th>colorCode</th>
            </thead>
            <tbody>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#imageUploadModal">
            Photo Upload
        </button>

        <div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageUploadModalLabel">Upload Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Resim Önizleme ve Yükleme Alanı -->
                        <div id="imagePreview" style="text-align: center; margin-bottom: 20px; cursor: pointer;" onclick="document.getElementById('imageUploadInput').click()">
                            <img id="previewImage" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; display: none;">
                            <p style="color: #888;">Click here to upload an image</p>
                        </div>

                        <!-- Dosya Seçme Alanı (Gizli) -->
                        <input type="file" id="imageUploadInput" accept="image/*" style="display: none;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
