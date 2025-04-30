<!-- Header dengan Form Pencarian -->
<header class="bg-green-700 text-white shadow-md">
    <div class="container mx-auto px-4 py-3">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center">
                <a href="index.php" class="text-2xl font-bold">BolaNow</a>
                <span class="ml-2 text-sm bg-yellow-500 text-green-900 px-2 py-0.5 rounded-full">LIVE</span>
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
                    <li><a href="index.php" class="hover:text-yellow-300 font-medium">Livescore</a></li>
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
            <li class="py-2"><a href="index.php" class="block hover:text-yellow-300 font-medium">Livescore</a></li>
            <li class="py-2"><a href="jadwal.php" class="block hover:text-yellow-300">Jadwal</a></li>
            <li class="py-2"><a href="hasil.php" class="block hover:text-yellow-300">Hasil</a></li>
            <li class="py-2"><a href="klasemen.php" class="block hover:text-yellow-300">Klasemen</a></li>
        </ul>
    </div>
</header>