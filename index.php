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
//echo $authToken;
$response=curlReq(
    "https://api.baubuddy.de/dev/index.php/v1/tasks/select",
    "Bearer ".$authToken,
    "{}",
    "GET"
);
$datas = json_decode($response);
?>
<table>
    <thead>
        <th>task</th>
        <th>title</th>
        <th>description</th>
        <th>colorCode</th>
    </thead>
    <tbody>
<?php foreach ($datas as $data) { ?>
        <tr>
            <td><?php echo $data->task; ?></td>
            <td><?php echo $data->title; ?></td>
            <td><?php echo $data->description; ?></td>
            <td><?php echo $data->colorCode; ?></td>
        </tr>
<?php } ?>
    </tbody>
