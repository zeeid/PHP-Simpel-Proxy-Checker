<?php
if (isset($_POST['proxyAndPort']) && isset($_POST['server']) && isset($_POST['url']) && isset($_POST['timeout'])) {
    $proxyAndPort = $_POST['proxyAndPort'];
    $selectedServer = $_POST['server'];
    $url = $_POST['url'];
    $timeout = intval($_POST['timeout']); // Ambil nilai timeout dan pastikan itu integer

    // Validasi nilai timeout di antara kelipatan 5 dari 5 hingga 30
    // $timeout = max(5, min(30, $timeout));

    // Inisialisasi cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PROXY, $proxyAndPort);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    // Menonaktifkan verifikasi sertifikat SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // Set timeout

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch) . ' - IP: ' . $proxyAndPort . '<br>';
    } else {
        // Tentukan teks yang akan dicari berdasarkan server
        $searchText = ($selectedServer === 'google') ? 'Google is built by' : '#btn-shorten';

        // Periksa apakah teks ditemukan dalam respons
        if (strpos($response, $searchText) !== false) {
            echo 'Berhasil - IP: ' . $proxyAndPort;
        } else {
            echo 'Gagal - IP: ' . $proxyAndPort;
        }
    }

    curl_close($ch);
} else {
    echo "<h2>Error: Invalid Request</h2>";
}
?>
