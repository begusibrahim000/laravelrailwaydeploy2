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

// Ambil query pencarian
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_results = [];

// Lakukan pencarian jika ada query
if (!empty($query)) {
    $search_results = callAPI('search/', ['t' => 'all', 'q' => $query]);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian - BolaNow</title>
    <meta name="description" content="Cari tim, liga, dan pertandingan sepak bola">
    <meta name="keywords" content="cari tim sepak bola, pencarian sepak bola, cari klub">
    
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
                    <a href="index.php" class="text-2xl font-bold">BolaNow</a>
                </div>
                <nav class="hidden md:block">
                    <ul class="flex space-x-6">
                        <li><a href="index.php" class="hover:text-yellow-300">Livescore</a></li>
                        <li><a href="jadwal.php" class="hover:text-yellow-300">Jadwal</a></li>
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
                <li class="py-2"><a href="jadwal.php" class="block hover:text-yellow-300">Jadwal</a></li>
                <li class="py-2"><a href="hasil.php" class="block hover:text-yellow-300">Hasil</a></li>
                <li class="py-2"><a href="klasemen.php" class="block hover:text-yellow-300">Klasemen</a></li>
            </ul>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Pencarian Tim</h1>
        
        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form action="search.php" method="get" class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="w-full md:flex-1">
                    <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Cari Tim, Liga, atau Negara:</label>
                    <div class="relative">
                        <input 
                            type="search" 
                            id="q" 
                            name="q" 
                            value="<?= htmlspecialchars($query) ?>" 
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: Bayern Munich, Premier League, Indonesia..."
                            required
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 md:self-end">
                    Cari
                </button>
            </form>
        </div>

        <!-- Search Results -->
        <?php if (!empty($query)): ?>
            <div class="mb-4">
                <h2 class="text-xl font-semibold mb-2">Hasil Pencarian untuk "<?= htmlspecialchars($query) ?>"</h2>
                <p class="text-gray-600">Menampilkan hasil pencarian dari tim, liga, dan pertandingan.</p>
            </div>

            <?php if (isset($search_results['data']) && !empty($search_results['data'])): ?>
                <div class="space-y-6">
                    <!-- Teams -->
                    <?php
                    $teams = array_filter($search_results['data'], function($item) {
                        return isset($item['type']) && $item['type'] === 'team';
                    });
                    ?>
                    
                    <?php if (!empty($teams)): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="bg-gray-100 p-3 border-b">
                                <h3 class="font-semibold">Tim</h3>
                            </div>
                            <div class="divide-y">
                                <?php foreach ($teams as $team): ?>
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <a href="team.php?id=<?= $team['id'] ?>" class="flex items-center">
                                            <div class="flex-shrink-0 mr-4">
                                                <?php if (isset($team['img'])): ?>
                                                    <img src="<?= $team['img'] ?>" alt="<?= $team['name'] ?>" class="w-10 h-10">
                                                <?php else: ?>
                                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-shield-alt text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h4 class="font-medium"><?= $team['name'] ?></h4>
                                                <?php if (isset($team['country']) && isset($team['country']['name'])): ?>
                                                    <p class="text-sm text-gray-600"><?= $team['country']['name'] ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Countries -->
                    <?php
                    $countries = array_filter($search_results['data'], function($item) {
                        return isset($item['type']) && $item['type'] === 'country';
                    });
                    ?>
                    
                    <?php if (!empty($countries)): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="bg-gray-100 p-3 border-b">
                                <h3 class="font-semibold">Negara</h3>
                            </div>
                            <div class="divide-y">
                                <?php foreach ($countries as $country): ?>
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <a href="country.php?id=<?= $country['id'] ?>" class="flex items-center">
                                            <div class="flex-shrink-0 mr-4">
                                                <?php if (isset($country['cc'])): ?>
                                                    <img src="https://cdn.soccersapi.com/images/countries/30/<?= $country['cc'] ?>.png" alt="<?= $country['name'] ?>" class="w-8 h-6">
                                                <?php else: ?>
                                                    <div class="w-8 h-6 bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-flag text-gray-400 text-xs"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h4 class="font-medium"><?= $country['name'] ?></h4>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Matches -->
                    <?php
                    $matches = array_filter($search_results['data'], function($item) {
                        return isset($item['type']) && $item['type'] === 'match';
                    });
                    ?>
                    
                    <?php if (!empty($matches)): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="bg-gray-100 p-3 border-b">
                                <h3 class="font-semibold">Pertandingan</h3>
                            </div>
                            <div class="divide-y">
                                <?php foreach ($matches as $match): ?>
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <a href="detail.php?id=<?= $match['id'] ?>" class="block">
                                            <div class="flex justify-between items-center">
                                                <div class="text-sm text-gray-600">
                                                    <?= date('d M Y', strtotime($match['startdate'])) ?>
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    <?= $match['league_name'] ?>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center mt-2">
                                                <div class="font-medium"><?= $match['home'] ?></div>
                                                <div class="text-sm">vs</div>
                                                <div class="font-medium"><?= $match['away'] ?></div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Leagues -->
                    <?php
                    $leagues = array_filter($search_results['data'], function($item) {
                        return isset($item['type']) && $item['type'] === 'league';
                    });
                    ?>
                    
                    <?php if (!empty($leagues)): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="bg-gray-100 p-3 border-b">
                                <h3 class="font-semibold">Liga</h3>
                            </div>
                            <div class="divide-y">
                                <?php foreach ($leagues as $league): ?>
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <a href="league.php?id=<?= $league['id'] ?>" class="flex items-center">
                                            <div class="flex-shrink-0 mr-4">
                                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-trophy text-gray-400"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-medium"><?= $league['name'] ?></h4>
                                                <?php if (isset($league['country_name'])): ?>
                                                    <p class="text-sm text-gray-600"><?= $league['country_name'] ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-5xl text-gray-300 mb-4">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Tidak Ada Hasil</h3>
                    <p class="text-gray-600">Tidak ditemukan hasil untuk pencarian "<?= htmlspecialchars($query) ?>".</p>
                    <p class="text-gray-600 mt-2">Coba dengan kata kunci lain atau periksa ejaan Anda.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-5xl text-gray-300 mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Cari Tim Favorit Anda</h3>
                <p class="text-gray-600">Masukkan nama tim, liga, atau negara untuk memulai pencarian.</p>
                <p class="text-gray-600 mt-2">Contoh: "Bayern Munich", "Premier League", "Indonesia"</p>
            </div>
        <?php endif; ?>
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