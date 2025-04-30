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

// Ambil daftar liga
$leagues = callAPI('leagues/', ['t' => 'list']);

// Tentukan liga yang dipilih (default: liga pertama)
$selected_league_id = isset($_GET['league_id']) ? $_GET['league_id'] : (isset($leagues['data'][0]['id']) ? $leagues['data'][0]['id'] : null);

// Ambil data klasemen liga
$standings = null;
if ($selected_league_id) {
    // Cari season_id dari liga yang dipilih
    $league_info = null;
    foreach ($leagues['data'] as $league) {
        if ($league['id'] == $selected_league_id) {
            $league_info = $league;
            break;
        }
    }
    
    if ($league_info && isset($league_info['current_season_id'])) {
        $standings = callAPI('leagues/', ['t' => 'standings_live', 'season_id' => $league_info['current_season_id']]);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klasemen Liga - BolaNow</title>
    <meta name="description" content="Klasemen liga sepak bola dari seluruh dunia">
    <meta name="keywords" content="klasemen, liga, sepak bola, standing, table">
    
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
                        <li><a href="jadwal.php" class="hover:text-yellow-300">Jadwal</a></li>
                        <li><a href="hasil.php" class="hover:text-yellow-300">Hasil</a></li>
                        <li><a href="klasemen.php" class="hover:text-yellow-300 font-medium">Klasemen</a></li>
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
                <li class="py-2"><a href="klasemen.php" class="block hover:text-yellow-300 font-medium">Klasemen</a></li>
            </ul>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Klasemen Liga</h1>
        
        <!-- League Selector -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form action="" method="get" class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="w-full md:w-auto flex-1">
                    <label for="league_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Liga:</label>
                    <select id="league_id" name="league_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <?php if (isset($leagues['data']) && !empty($leagues['data'])): ?>
                            <?php foreach ($leagues['data'] as $league): ?>
                                <option value="<?= $league['id'] ?>" <?= $selected_league_id == $league['id'] ? 'selected' : '' ?>>
                                    <?= $league['country_name'] ?> - <?= $league['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 md:self-end">
                    Tampilkan Klasemen
                </button>
            </form>
        </div>

        <!-- Standings -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (isset($standings['data']) && !empty($standings['data'])): ?>
                <?php
                // Ambil data klasemen
                $standings_data = $standings['data'];
                $league_id = $standings_data['league_id'];
                $season_id = $standings_data['season_id'];
                $has_groups = $standings_data['has_groups'];
                $number_standings = $standings_data['number_standings'];
                $standings_tables = $standings_data['standings'];
                ?>

                <?php foreach ($standings_tables as $index => $table): ?>
                    <?php
                    // Ambil nama grup jika ada
                    $group_name = '';
                    if ($has_groups && isset($table[0]['group_name'])) {
                        $group_name = $table[0]['group_name'];
                    }
                    ?>
                    
                    <!-- Group Header -->
                    <?php if ($has_groups && !empty($group_name)): ?>
                        <div class="bg-gray-100 p-3 border-b">
                            <h2 class="font-semibold"><?= $group_name ?></h2>
                        </div>
                    <?php endif; ?>

                    <!-- Standings Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 text-sm">
                                    <th class="py-3 px-4 text-left">Pos</th>
                                    <th class="py-3 px-4 text-left">Tim</th>
                                    <th class="py-3 px-2 text-center">Main</th>
                                    <th class="py-3 px-2 text-center">M</th>
                                    <th class="py-3 px-2 text-center">S</th>
                                    <th class="py-3 px-2 text-center">K</th>
                                    <th class="py-3 px-2 text-center">GM</th>
                                    <th class="py-3 px-2 text-center">GK</th>
                                    <th class="py-3 px-2 text-center">SG</th>
                                    <th class="py-3 px-2 text-center">Poin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php foreach ($table as $team): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center">
                                                <?php if (isset($team['status']) && $team['status'] == 'Promotion'): ?>
                                                    <div class="w-1 h-4 bg-green-500 mr-2"></div>
                                                <?php elseif (isset($team['status']) && $team['status'] == 'Relegation'): ?>
                                                    <div class="w-1 h-4 bg-red-500 mr-2"></div>
                                                <?php else: ?>
                                                    <div class="w-1 h-4 bg-transparent mr-2"></div>
                                                <?php endif; ?>
                                                <?= $team['overall']['position'] ?>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 font-medium"><?= $team['team_name'] ?></td>
                                        <td class="py-3 px-2 text-center"><?= $team['overall']['games_played'] ?></td>
                                        <td class="py-3 px-2 text-center"><?= $team['overall']['won'] ?></td>
                                        <td class="py-3 px-2 text-center"><?= $team['overall']['draw'] ?></td>
                                        <td class="py-3 px-2 text-center"><?= $team['overall']['lost'] ?></td>
                                        <td class="py-3 px-2 text-center"><?= $team['overall']['goals_scored'] ?></td>
                                        <td class="py-3 px-2 text-center"><?= $team['overall']['goals_against'] ?></td>
                                        <td class="py-3 px-2 text-center"><?= $team['overall']['goals_diff'] ?></td>
                                        <td class="py-3 px-2 text-center font-bold"><?= $team['overall']['points'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="p-4 text-sm text-gray-600 border-t">
                        <div class="flex items-center mb-2">
                            <div class="w-3 h-3 bg-green-500 mr-2"></div>
                            <span>Promosi / Kualifikasi Liga Champions</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 mr-2"></div>
                            <span>Degradasi / Zona Degradasi</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-6 text-center">
                    <p class="text-gray-500">Klasemen tidak tersedia untuk liga yang dipilih.</p>
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