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

// Ambil ID pertandingan dari parameter URL
$match_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$match_id) {
    // Redirect ke halaman utama jika tidak ada ID pertandingan
    header('Location: index.php');
    exit;
}

// Ambil data detail pertandingan
// Catatan: Dalam API sebenarnya, Anda mungkin perlu menggunakan endpoint yang berbeda untuk detail pertandingan
// Untuk contoh ini, kita akan menggunakan endpoint livescores dengan filter ID
$match_detail = callAPI('livescores/', ['id' => $match_id]);

// Ambil data pertandingan pertama (seharusnya hanya ada satu)
$match = isset($match_detail['data'][0]) ? $match_detail['data'][0] : null;

if (!$match) {
    // Redirect ke halaman utama jika pertandingan tidak ditemukan
    header('Location: index.php');
    exit;
}

// Ambil data statistik pertandingan (dalam API sebenarnya)
// Untuk contoh ini, kita akan membuat data dummy
$match_stats = [
    'possession' => ['home' => 55, 'away' => 45],
    'shots' => ['home' => 12, 'away' => 8],
    'shots_on_target' => ['home' => 5, 'away' => 3],
    'corners' => ['home' => 6, 'away' => 4],
    'fouls' => ['home' => 10, 'away' => 12],
    'yellow_cards' => ['home' => 2, 'away' => 3],
    'red_cards' => ['home' => 0, 'away' => 1],
    'offsides' => ['home' => 3, 'away' => 2],
];

// Ambil data timeline pertandingan (dalam API sebenarnya)
// Untuk contoh ini, kita akan membuat data dummy
$match_events = [
    [
        'minute' => 23,
        'type' => 'goal',
        'team' => 'home',
        'player' => 'John Doe',
        'assist' => 'James Smith'
    ],
    [
        'minute' => 35,
        'type' => 'yellow_card',
        'team' => 'away',
        'player' => 'Robert Johnson'
    ],
    [
        'minute' => 42,
        'type' => 'goal',
        'team' => 'away',
        'player' => 'Michael Brown',
        'assist' => 'David Wilson'
    ],
    [
        'minute' => 67,
        'type' => 'goal',
        'team' => 'home',
        'player' => 'James Smith',
        'assist' => 'John Doe'
    ],
    [
        'minute' => 75,
        'type' => 'red_card',
        'team' => 'away',
        'player' => 'Robert Johnson'
    ],
    [
        'minute' => 88,
        'type' => 'yellow_card',
        'team' => 'home',
        'player' => 'William Davis'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pertandingan - BolaNow</title>
    <meta name="description" content="Detail pertandingan sepak bola dengan statistik dan timeline">
    <meta name="keywords" content="detail pertandingan, statistik pertandingan, timeline pertandingan, sepak bola">
    
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
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm mb-6">
            <a href="index.php" class="text-gray-500 hover:text-green-700">Beranda</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="#" class="text-gray-500 hover:text-green-700"><?= $match['league']['name'] ?></a>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-700 font-medium">Detail Pertandingan</span>
        </div>

        <!-- Match Header -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-100 p-3 border-b">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <?php if (!empty($match['league']['country_flag'])): ?>
                            <img src="<?= $match['league']['country_flag'] ?>" alt="<?= $match['league']['country_name'] ?>" class="w-5 h-5 mr-2">
                        <?php endif; ?>
                        <h2 class="font-semibold"><?= $match['league']['country_name'] ?> - <?= $match['league']['name'] ?></h2>
                    </div>
                    <div class="text-sm text-gray-600">
                        <?= date('d M Y', strtotime($match['time']['date'])) ?>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <!-- Home Team -->
                    <div class="flex flex-col items-center mb-4 md:mb-0 w-full md:w-2/5">
                        <?php if (!empty($match['teams']['home']['img'])): ?>
                            <img src="<?= $match['teams']['home']['img'] ?>" alt="<?= $match['teams']['home']['name'] ?>" class="w-16 h-16 mb-2">
                        <?php endif; ?>
                        <h3 class="text-xl font-bold text-center"><?= $match['teams']['home']['name'] ?></h3>
                    </div>

                    <!-- Score -->
                    <div class="flex flex-col items-center mb-4 md:mb-0 w-full md:w-1/5">
                        <?php if ($match['status'] == 0): ?>
                            <div class="text-lg font-medium mb-2"><?= date('H:i', strtotime($match['time']['time'])) ?></div>
                            <div class="text-3xl font-bold">vs</div>
                        <?php else: ?>
                            <div class="text-lg font-medium mb-2">
                                <?php if ($match['status'] == 1): ?>
                                    <span class="text-red-600"><?= $match['time']['minute'] ?>'</span>
                                <?php else: ?>
                                    <span>Selesai</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-4xl font-bold">
                                <span class="<?= $match['scores']['home_score'] > $match['scores']['away_score'] ? 'text-green-600' : '' ?>"><?= $match['scores']['home_score'] ?></span>
                                -
                                <span class="<?= $match['scores']['away_score'] > $match['scores']['home_score'] ? 'text-green-600' : '' ?>"><?= $match['scores']['away_score'] ?></span>
                            </div>
                            <?php if (!empty($match['scores']['ht_score'])): ?>
                                <div class="text-sm text-gray-500 mt-2">HT: <?= $match['scores']['ht_score'] ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Away Team -->
                    <div class="flex flex-col items-center w-full md:w-2/5">
                        <?php if (!empty($match['teams']['away']['img'])): ?>
                            <img src="<?= $match['teams']['away']['img'] ?>" alt="<?= $match['teams']['away']['name'] ?>" class="w-16 h-16 mb-2">
                        <?php endif; ?>
                        <h3 class="text-xl font-bold text-center"><?= $match['teams']['away']['name'] ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Match Tabs -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="border-b">
                <nav class="flex">
                    <button class="tab-btn active px-4 py-3 font-medium border-b-2 border-green-700" data-tab="info">Info</button>
                    <button class="tab-btn px-4 py-3 font-medium border-b-2 border-transparent" data-tab="stats">Statistik</button>
                    <button class="tab-btn px-4 py-3 font-medium border-b-2 border-transparent" data-tab="timeline">Timeline</button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-4">
                <!-- Info Tab -->
                <div class="tab-content active" id="info-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Informasi Pertandingan</h3>
                            <table class="w-full">
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Tanggal</td>
                                    <td class="py-2 font-medium"><?= date('d M Y', strtotime($match['time']['date'])) ?></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Waktu</td>
                                    <td class="py-2 font-medium"><?= date('H:i', strtotime($match['time']['time'])) ?></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Status</td>
                                    <td class="py-2 font-medium">
                                        <?php if ($match['status'] == 0): ?>
                                            Belum Dimulai
                                        <?php elseif ($match['status'] == 1): ?>
                                            Sedang Berlangsung
                                        <?php elseif ($match['status'] == 3): ?>
                                            Selesai
                                        <?php else: ?>
                                            <?= $match['status_name'] ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Stadion</td>
                                    <td class="py-2 font-medium"><?= isset($match['venue_id']) ? 'Stadion #' . $match['venue_id'] : 'Tidak tersedia' ?></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Wasit</td>
                                    <td class="py-2 font-medium"><?= isset($match['referee_id']) ? 'Wasit #' . $match['referee_id'] : 'Tidak tersedia' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Informasi Liga</h3>
                            <table class="w-full">
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Liga</td>
                                    <td class="py-2 font-medium"><?= $match['league']['name'] ?></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Negara</td>
                                    <td class="py-2 font-medium"><?= $match['league']['country_name'] ?></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Musim</td>
                                    <td class="py-2 font-medium"><?= $match['season_name'] ?></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Babak</td>
                                    <td class="py-2 font-medium"><?= $match['round_name'] ?></td>
                                </tr>
                                <?php if (isset($match['stage_name']) && !empty($match['stage_name'])): ?>
                                <tr class="border-b">
                                    <td class="py-2 text-gray-600">Tahap</td>
                                    <td class="py-2 font-medium"><?= $match['stage_name'] ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Stats Tab -->
                <div class="tab-content hidden" id="stats-content">
                    <h3 class="text-lg font-semibold mb-4">Statistik Pertandingan</h3>
                    
                    <div class="space-y-4">
                        <!-- Possession -->
                        <div class="flex items-center">
                            <div class="w-1/4 text-right pr-2"><?= $match_stats['possession']['home'] ?>%</div>
                            <div class="w-1/2">
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-700 rounded-full" style="width: <?= $match_stats['possession']['home'] ?>%"></div>
                                </div>
                            </div>
                            <div class="w-1/4 pl-2"><?= $match_stats['possession']['away'] ?>%</div>
                        </div>
                        <div class="text-center text-sm text-gray-600">Penguasaan Bola</div>
                        
                        <!-- Shots -->
                        <div class="flex items-center">
                            <div class="w-1/4 text-right pr-2"><?= $match_stats['shots']['home'] ?></div>
                            <div class="w-1/2">
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-700 rounded-full" style="width: <?= ($match_stats['shots']['home'] / ($match_stats['shots']['home'] + $match_stats['shots']['away'])) * 100 ?>%"></div>
                                </div>
                            </div>
                            <div class="w-1/4 pl-2"><?= $match_stats['shots']['away'] ?></div>
                        </div>
                        <div class="text-center text-sm text-gray-600">Tembakan</div>
                        
                        <!-- Shots on Target -->
                        <div class="flex items-center">
                            <div class="w-1/4 text-right pr-2"><?= $match_stats['shots_on_target']['home'] ?></div>
                            <div class="w-1/2">
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-700 rounded-full" style="width: <?= ($match_stats['shots_on_target']['home'] / ($match_stats['shots_on_target']['home'] + $match_stats['shots_on_target']['away'])) * 100 ?>%"></div>
                                </div>
                            </div>
                            <div class="w-1/4 pl-2"><?= $match_stats['shots_on_target']['away'] ?></div>
                        </div>
                        <div class="text-center text-sm text-gray-600">Tembakan ke Gawang</div>
                        
                        <!-- Corners -->
                        <div class="flex items-center">
                            <div class="w-1/4 text-right pr-2"><?= $match_stats['corners']['home'] ?></div>
                            <div class="w-1/2">
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-700 rounded-full" style="width: <?= ($match_stats['corners']['home'] / ($match_stats['corners']['home'] + $match_stats['corners']['away'])) * 100 ?>%"></div>
                                </div>
                            </div>
                            <div class="w-1/4 pl-2"><?= $match_stats['corners']['away'] ?></div>
                        </div>
                        <div class="text-center text-sm text-gray-600">Tendangan Sudut</div>
                        
                        <!-- Fouls -->
                        <div class="flex items-center">
                            <div class="w-1/4 text-right pr-2"><?= $match_stats['fouls']['home'] ?></div>
                            <div class="w-1/2">
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-700 rounded-full" style="width: <?= ($match_stats['fouls']['home'] / ($match_stats['fouls']['home'] + $match_stats['fouls']['away'])) * 100 ?>%"></div>
                                </div>
                            </div>
                            <div class="w-1/4 pl-2"><?= $match_stats['fouls']['away'] ?></div>
                        </div>
                        <div class="text-center text-sm text-gray-600">Pelanggaran</div>
                        
                        <!-- Yellow Cards -->
                        <div class="flex items-center">
                            <div class="w-1/4 text-right pr-2"><?= $match_stats['yellow_cards']['home'] ?></div>
                            <div class="w-1/2">
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-500 rounded-full" style="width: <?= ($match_stats['yellow_cards']['home'] / ($match_stats['yellow_cards']['home'] + $match_stats['yellow_cards']['away'] + 0.0001)) * 100 ?>%"></div>
                                </div>
                            </div>
                            <div class="w-1/4 pl-2"><?= $match_stats['yellow_cards']['away'] ?></div>
                        </div>
                        <div class="text-center text-sm text-gray-600">Kartu Kuning</div>
                        
                        <!-- Red Cards -->
                        <div class="flex items-center">
                            <div class="w-1/4 text-right pr-2"><?= $match_stats['red_cards']['home'] ?></div>
                            <div class="w-1/2">
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-600 rounded-full" style="width: <?= ($match_stats['red_cards']['home'] / ($match_stats['red_cards']['home'] + $match_stats['red_cards']['away'] + 0.0001)) * 100 ?>%"></div>
                                </div>
                            </div>
                            <div class="w-1/4 pl-2"><?= $match_stats['red_cards']['away'] ?></div>
                        </div>
                        <div class="text-center text-sm text-gray-600">Kartu Merah</div>
                        
                        <!-- Offsides -->
                        <div class="flex items-center">
                            <div class="w-1/4 text-right pr-2"><?= $match_stats['offsides']['home'] ?></div>
                            <div class="w-1/2">
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-700 rounded-full" style="width: <?= ($match_stats['offsides']['home'] / ($match_stats['offsides']['home'] + $match_stats['offsides']['away'])) * 100 ?>%"></div>
                                </div>
                            </div>
                            <div class="w-1/4 pl-2"><?= $match_stats['offsides']['away'] ?></div>
                        </div>
                        <div class="text-center text-sm text-gray-600">Offside</div>
                    </div>
                </div>

                <!-- Timeline Tab -->
                <div class="tab-content hidden" id="timeline-content">
                    <h3 class="text-lg font-semibold mb-4">Timeline Pertandingan</h3>
                    
                    <div class="relative">
                        <!-- Timeline Line -->
                        <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-0.5 bg-gray-200"></div>
                        
                        <!-- Timeline Events -->
                        <div class="space-y-6 relative">
                            <?php foreach ($match_events as $event): ?>
                                <div class="flex items-start">
                                    <?php if ($event['team'] == 'home'): ?>
                                        <!-- Home Team Event -->
                                        <div class="w-1/2 pr-8 text-right">
                                            <div class="font-medium"><?= $event['player'] ?></div>
                                            <?php if ($event['type'] == 'goal' && isset($event['assist'])): ?>
                                                <div class="text-sm text-gray-600">Assist: <?= $event['assist'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="absolute left-1/2 transform -translate-x-1/2 w-8 h-8 rounded-full bg-white border-2 border-gray-200 flex items-center justify-center">
                                            <?php if ($event['type'] == 'goal'): ?>
                                                <i class="fas fa-futbol text-green-700"></i>
                                            <?php elseif ($event['type'] == 'yellow_card'): ?>
                                                <div class="w-4 h-5 bg-yellow-500"></div>
                                            <?php elseif ($event['type'] == 'red_card'): ?>
                                                <div class="w-4 h-5 bg-red-600"></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="w-1/2 pl-8">
                                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-medium">
                                                <?= $event['minute'] ?>'
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Away Team Event -->
                                        <div class="w-1/2 pr-8 text-right">
                                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-medium">
                                                <?= $event['minute'] ?>'
                                            </div>
                                        </div>
                                        <div class="absolute left-1/2 transform -translate-x-1/2 w-8 h-8 rounded-full bg-white border-2 border-gray-200 flex items-center justify-center">
                                            <?php if ($event['type'] == 'goal'): ?>
                                                <i class="fas fa-futbol text-green-700"></i>
                                            <?php elseif ($event['type'] == 'yellow_card'): ?>
                                                <div class="w-4 h-5 bg-yellow-500"></div>
                                            <?php elseif ($event['type'] == 'red_card'): ?>
                                                <div class="w-4 h-5 bg-red-600"></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="w-1/2 pl-8">
                                            <div class="font-medium"><?= $event['player'] ?></div>
                                            <?php if ($event['type'] == 'goal' && isset($event['assist'])): ?>
                                                <div class="text-sm text-gray-600">Assist: <?= $event['assist'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
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

        // Tab Functionality
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active', 'border-green-700'));
                tabButtons.forEach(btn => btn.classList.add('border-transparent'));
                tabContents.forEach(content => content.classList.add('hidden'));
                
                // Add active class to clicked button and corresponding content
                button.classList.add('active', 'border-green-700');
                button.classList.remove('border-transparent');
                
                const tabId = button.getAttribute('data-tab');
                document.getElementById(`${tabId}-content`).classList.remove('hidden');
            });
        });
    </script>
</body>
</html>