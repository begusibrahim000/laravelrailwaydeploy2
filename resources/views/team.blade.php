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

// Ambil ID tim dari parameter URL
$team_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$team_id) {
    // Redirect ke halaman utama jika tidak ada ID tim
    header('Location: index.php');
    exit;
}

// Ambil data tim
$team_info = callAPI('teams/', ['t' => 'info', 'id' => $team_id]);
$team_squad = callAPI('teams/', ['t' => 'squad', 'id' => $team_id]);

// Ambil jadwal pertandingan mendatang tim
$upcoming_matches = callAPI('fixtures/', ['t' => 'upcoming', 'team_id' => $team_id, 'limit' => 5]);

// Ambil hasil pertandingan terakhir tim
$past_matches = callAPI('fixtures/', ['t' => 'past', 'team_id' => $team_id, 'limit' => 5]);

// Periksa apakah tim ditemukan
$team = isset($team_info['data']) ? $team_info['data'] : null;

if (!$team) {
    // Redirect ke halaman utama jika tim tidak ditemukan
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $team['name'] ?> - Detail Tim - BolaNow</title>
    <meta name="description" content="Informasi lengkap tentang <?= $team['name'] ?>, jadwal pertandingan, hasil, dan skuad tim">
    <meta name="keywords" content="<?= $team['name'] ?>, detail tim, skuad, jadwal, hasil pertandingan">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Header -->
    <header class="bg-green-700 text-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold">BolaNow</a>
                </div>
                
                <!-- Form Pencarian -->
                <div class="w-full md:w-1/3 order-3 md:order-2">
                    <form action="search.php" method="get" class="relative">
                        <input 
                            type="search" 
                            name="q" 
                            placeholder="Cari tim, liga, atau negara..." 
                            class="w-full px-3 py-2 pl-10 text-gray-800 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        >
                        <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </button>
                    </form>
                </div>
                
                <nav class="hidden md:block order-2 md:order-3">
                    <ul class="flex space-x-6">
                        <li><a href="index.php" class="hover:text-yellow-300">Livescore</a></li>
                        <li><a href="jadwal.php" class="hover:text-yellow-300">Jadwal</a></li>
                        <li><a href="hasil.php" class="hover:text-yellow-300">Hasil</a></li>
                        <li><a href="klasemen.php" class="hover:text-yellow-300">Klasemen</a></li>
                    </ul>
                </nav>
                <button class="md:hidden text-xl order-1 md:order-4 self-end">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-green-800" id="mobile-menu">
            <ul class="px-4 py-2">
                <li class="py-2"><a href="index.php" class="block hover:text-yellow-300">Livescore</a></li>
                <li class="py-2"><a href="jadwal.php" class="block hover:text-yellow-300">Jadwal</a></li>
                <li class="py-2"><a href="hasil.php" class="block hover:text-yellow-300">Hasil</a></li>
                <li class="py-2"><a href="klasemen.php" class="block hover:text-yellow-300">Klasemen</a></li>
            </ul>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm mb-6">
            <a href="index.php" class="text-gray-500 hover:text-green-700">Beranda</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="search.php" class="text-gray-500 hover:text-green-700">Pencarian</a>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-700 font-medium"><?= $team['name'] ?></span>
        </div>

        <!-- Team Header -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row items-center">
                    <!-- Team Logo -->
                    <div class="mb-4 md:mb-0 md:mr-6">
                        <?php if (!empty($team['img'])): ?>
                            <img src="<?= $team['img'] ?>" alt="<?= $team['name'] ?>" class="w-24 h-24 object-contain">
                        <?php else: ?>
                            <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-shield-alt text-gray-400 text-4xl"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Team Info -->
                    <div class="text-center md:text-left">
                        <h1 class="text-3xl font-bold mb-2"><?= $team['name'] ?></h1>
                        <?php if (isset($team['country']) && isset($team['country']['name'])): ?>
                            <div class="flex items-center justify-center md:justify-start mb-2">
                                <?php if (isset($team['country']['img'])): ?>
                                    <img src="<?= $team['country']['img'] ?>" alt="<?= $team['country']['name'] ?>" class="w-5 h-5 mr-2">
                                <?php endif; ?>
                                <span><?= $team['country']['name'] ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($team['founded'])): ?>
                            <p class="text-gray-600">Didirikan: <?= $team['founded'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Tabs -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="border-b">
                <nav class="flex overflow-x-auto">
                    <button class="tab-btn active px-4 py-3 font-medium border-b-2 border-green-700 whitespace-nowrap" data-tab="info">Informasi</button>
                    <button class="tab-btn px-4 py-3 font-medium border-b-2 border-transparent whitespace-nowrap" data-tab="squad">Skuad Tim</button>
                    <button class="tab-btn px-4 py-3 font-medium border-b-2 border-transparent whitespace-nowrap" data-tab="fixtures">Jadwal</button>
                    <button class="tab-btn px-4 py-3 font-medium border-b-2 border-transparent whitespace-nowrap" data-tab="results">Hasil</button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-4">
                <!-- Info Tab -->
                <div class="tab-content active" id="info-content">
                    <h2 class="text-xl font-semibold mb-4">Informasi Tim</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium mb-3">Detail Tim</h3>
                            <table class="w-full">
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Nama Lengkap</td>
                                    <td class="py-2 font-medium"><?= $team['name'] ?></td>
                                </tr>
                                <?php if (isset($team['common_name']) && !empty($team['common_name'])): ?>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Nama Umum</td>
                                    <td class="py-2 font-medium"><?= $team['common_name'] ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (isset($team['country']) && isset($team['country']['name'])): ?>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Negara</td>
                                    <td class="py-2 font-medium"><?= $team['country']['name'] ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (isset($team['founded'])): ?>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Tahun Berdiri</td>
                                    <td class="py-2 font-medium"><?= $team['founded'] ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (isset($team['venue_id'])): ?>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Stadion</td>
                                    <td class="py-2 font-medium">Stadion #<?= $team['venue_id'] ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (isset($team['coach_id'])): ?>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Pelatih</td>
                                    <td class="py-2 font-medium">Pelatih #<?= $team['coach_id'] ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium mb-3">Kompetisi</h3>
                            <?php if (isset($team['leagues']) && !empty($team['leagues'])): ?>
                                <div class="space-y-4">
                                    <?php foreach ($team['leagues'] as $league): ?>
                                        <div class="border rounded-lg p-3">
                                            <h4 class="font-medium"><?= $league['name'] ?></h4>
                                            <?php if (isset($league['country'])): ?>
                                                <p class="text-sm text-gray-600"><?= $league['country']['name'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-600">Tidak ada informasi kompetisi yang tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Squad Tab -->
                <div class="tab-content hidden" id="squad-content">
                    <h2 class="text-xl font-semibold mb-4">Skuad Tim</h2>
                    
                    <?php if (isset($team_squad['data']['squad']) && !empty($team_squad['data']['squad'])): ?>
                        <?php
                        // Kelompokkan pemain berdasarkan posisi
                        $goalkeepers = [];
                        $defenders = [];
                        $midfielders = [];
                        $forwards = [];
                        
                        foreach ($team_squad['data']['squad'] as $player) {
                            if ($player['position'] === 'G') {
                                $goalkeepers[] = $player;
                            } elseif ($player['position'] === 'D') {
                                $defenders[] = $player;
                            } elseif ($player['position'] === 'M') {
                                $midfielders[] = $player;
                            } elseif ($player['position'] === 'F') {
                                $forwards[] = $player;
                            }
                        }
                        ?>
                        
                        <!-- Goalkeepers -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-3 bg-gray-100 p-2">Penjaga Gawang</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <?php foreach ($goalkeepers as $player): ?>
                                    <div class="border rounded-lg p-3 flex items-center">
                                        <div class="flex-shrink-0 mr-3">
                                            <?php if (isset($player['player']['img'])): ?>
                                                <img src="<?= $player['player']['img'] ?>" alt="<?= $player['player']['common_name'] ?>" class="w-12 h-12 rounded-full object-cover">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-medium"><?= $player['player']['common_name'] ?></div>
                                            <?php if (isset($player['number']) && !empty($player['number'])): ?>
                                                <div class="text-sm text-gray-600">No. <?= $player['number'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Defenders -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-3 bg-gray-100 p-2">Bek</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <?php foreach ($defenders as $player): ?>
                                    <div class="border rounded-lg p-3 flex items-center">
                                        <div class="flex-shrink-0 mr-3">
                                            <?php if (isset($player['player']['img'])): ?>
                                                <img src="<?= $player['player']['img'] ?>" alt="<?= $player['player']['common_name'] ?>" class="w-12 h-12 rounded-full object-cover">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-medium"><?= $player['player']['common_name']