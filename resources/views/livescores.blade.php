<?php
// Konfigurasi API
$user = "bpu000000003";
$token = "a844b5483943fe91a35cc3a58b576c05";
$base_url = "https://api.soccersapi.com/v2.2/";

// Fungsi untuk memanggil API
function callAPI($endpoint, $params = []) {
    global $user, $token, $base_url;
    
    // Tambahkan kredensial API ke parameter
    $params['user'] = $user;
    $params['token'] = $token;
    
    // Buat URL dengan parameter
    $url = $base_url . $endpoint . '?' . http_build_query($params);
    
    // Inisialisasi cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Eksekusi request
    $response = curl_exec($ch);
    
    // Tutup koneksi cURL
    curl_close($ch);
    
    // Kembalikan hasil sebagai array
    return json_decode($response, true);
}

// Ambil data livescore hari ini
$livescores = callAPI('livescores/', ['t' => 'today']);

// Format data untuk respons JSON
$matches = [];
if (isset($livescores['data']) && !empty($livescores['data'])) {
    foreach ($livescores['data'] as $match) {
        $matches[] = [
            'id' => $match['id'],
            'home_score' => $match['scores']['home_score'],
            'away_score' => $match['scores']['away_score'],
            'status' => $match['status'],
            'minute' => isset($match['time']['minute']) ? $match['time']['minute'] : '',
            'status_name' => $match['status_name']
        ];
    }
}

// Set header untuk JSON
header('Content-Type: application/json');

// Kembalikan respons
echo json_encode([
    'success' => true,
    'matches' => $matches
]);