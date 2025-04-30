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

// Tentukan tanggal untuk jadwal
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Ambil data jadwal pertandingan
$fixtures = callAPI('fixtures/', ['t' => 'schedule', 'd' => $date]);

// Ambil data liga
$leagues = callAPI('leagues/', ['t' => 'list']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pertandingan - BolaNow</title>
    <meta name="description" content="Jadwal pertandingan sepak bola dari seluruh dunia">
    <meta name="keywords" content="jadwal pertandingan, sepak bola, jadwal bola, jadwal liga">
    
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
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold">BolaNow</h1>
                </div>
                <nav class="hidden md:block">
                    <ul class="flex space-x-6">
                        <li><a href="index.php" class="hover:text-yellow-300">Livescore</a></li>
                        <li><a href="jadwal.php" class="hover:text-yellow-300 font-medium">Jadwal</a></li>
                        <li><a href="hasil.php" class="hover:text-yellow-300">Hasil</a></li>
                        <li><a href="klasemen.php" class="hover:text-yellow-300">Klasemen</a></li>
                    </ul>
                </nav>
                <button class="md:hidden text-xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-green-800" id="mobile-menu">
            <ul class="px-4 py-2">
                <li class="py-2"><a href="index.php" class="block hover:text-yellow-300">Livescore</a></li>
                <li class="py-2"><a href="jadwal.php" class="block hover:text-yellow-300 font-medium">Jadwal</a></li>
                <li class="py-2"><a href="hasil.php" class="block hover:text-yellow-300">Hasil</a></li>
                <li class="py-2"><a href="klasemen.php" class="block hover:text-yellow-300">Klasemen</a></li>
            </ul>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Jadwal Pertandingan</h1>
        
        <!-- Date Picker -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form action="" method="get" class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="w-full md:w-auto">
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal:</label>
                    <input type="date" id="date" name="date" value="<?= $date ?>" class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Tampilkan Jadwal
                </button>
            </form>
        </div>

        <!-- Date Navigation -->
        <div class="flex justify-between items-center mb-6 overflow-x-auto whitespace-nowrap py-2">
            <a href="?date=<?= date('Y-m-d', strtotime($date . ' -3 days')) ?>" class="px-3 py-1 rounded-full hover:bg-gray-200">
                <?= date('D, d M', strtotime($date . ' -3 days')) ?>
            </a>
            <a href="?date=<?= date('Y-m-d', strtotime($date . ' -2 days')) ?>" class="px-3 py-1 rounded-full hover:bg-gray-200">
                <?= date('D, d M', strtotime($date . ' -2 days')) ?>
            </a>
            <a href="?date=<?= date('Y-m-d', strtotime($date . ' -1 day')) ?>" class="px-3 py-1 rounded-full hover:bg-gray-200">
                <?= date('D, d M', strtotime($date . ' -1 day')) ?>
            </a>
            <a href="?date=<?= $date ?>" class="px-3 py-1 rounded-full bg-green-700 text-white">
                <?= date('D, d M', strtotime($date)) ?>
            </a>
            <a href="?date=<?= date('Y-m-d', strtotime($date . ' +1 day')) ?>" class="px-3 py-1 rounded-full hover:bg-gray-200">
                <?= date('D, d M', strtotime($date . ' +1 day')) ?>
            </a>
            <a href="?date=<?= date('Y-m-d', strtotime($date . ' +2 days')) ?>" class="px-3 py-1 rounded-full hover:bg-gray-200">
                <?= date('D, d M', strtotime($date . ' +2 days')) ?>
            </a>
            <a href="?date=<?= date('Y-m-d', strtotime($date . ' +3 days')) ?>" class="px-3 py-1 rounded-full hover:bg-gray-200">
                <?= date('D, d M', strtotime($date . ' +3 days')) ?>
            </a>
        </div>

        <!-- Fixtures -->
        <div class="space-y-6">
            <?php if (isset($fixtures['data']) && !empty($fixtures['data'])): ?>
                <?php
                // Kelompokkan pertandingan berdasarkan liga
                $matches_by_league = [];
                foreach ($fixtures['data'] as $match) {
                    $league_id = $match['league']['id'];
                    if (!isset($matches_by_league[$league_id])) {
                        $matches_by_league[$league_id] = [
                            'league' => $match['league'],
                            'matches' => []
                        ];
                    }
                    $matches_by_league[$league_id]['matches'][] = $match;
                }
                ?>

                <?php foreach ($matches_by_league as $league_data): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- League Header -->
                        <div class="bg-gray-100 p-3 border-b flex items-center">
                            <?php if (!empty($league_data['league']['country_flag'])): ?>
                                <img src="<?= $league_data['league']['country_flag'] ?>" alt="<?= $league_data['league']['country_name'] ?>" class="w-5 h-5 mr-2">
                            <?php endif; ?>
                            <h2 class="font-semibold"><?= $league_data['league']['country_name'] ?> - <?= $league_data['league']['name'] ?></h2>
                        </div>

                        <!-- Matches -->
                        <div class="divide-y">
                            <?php foreach ($league_data['matches'] as $match): ?>
                                <div class="p-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-center">
                                        <!-- Match Time -->
                                        <div class="w-16 text-center">
                                            <span class="text-gray-600"><?= date('H:i', strtotime($match['time']['time'])) ?></span>
                                        </div>

                                        <!-- Teams -->
                                        <div class="flex-1 grid grid-cols-7 gap-2 items-center">
                                            <!-- Home Team -->
                                            <div class="col-span-3 text-right">
                                                <div class="font-medium"><?= $match['teams']['home']['name'] ?></div>
                                            </div>
                                            
                                            <!-- vs -->
                                            <div class="col-span-1 text-center">
                                                <span class="text-sm text-gray-500">vs</span>
                                            </div>
                                            
                                            <!-- Away Team -->
                                            <div class="col-span-3 text-left">
                                                <div class="font-medium"><?= $match['teams']['away']['name'] ?></div>
                                            </div>
                                        </div>

                                        <!-- Match Details Link -->
                                        <div class="w-8 text-center">
                                            <a href="detail.php?id=<?= $match['id'] ?>" class="text-gray-400 hover:text-green-700">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <p class="text-gray-500">Tidak ada pertandingan yang dijadwalkan pada tanggal ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">BolaNow</h3>
                    <p class="text-gray-400">Situs livescore sepak bola terbaik dengan update skor pertandingan secara real-time dari seluruh dunia.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Link Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white">Livescore</a></li>
                        <li><a href="jadwal.php" class="text-gray-400 hover:text-white">Jadwal Pertandingan</a></li>
                        <li><a href="hasil.php" class="text-gray-400 hover:text-white">Hasil Pertandingan</a></li>
                        <li><a href="klasemen.php" class="text-gray-400 hover:text-white">Klasemen Liga</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-envelope mr-2"></i> info@bolanow.com</li>
                        <li><i class="fas fa-phone mr-2"></i> +62 123 4567 890</li>
                        <li class="flex space-x-4 mt-4">
                            <a href="#" class="hover:text-white"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="hover:text-white"><i class="fab fa-instagram"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> BolaNow. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Toggle Mobile Menu
        document.querySelector('button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>