<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai - Monitoring Pengajuan TOR</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .underline-effect {
            position: relative;
            display: inline-block;
            text-decoration: none;
        }

        .underline-effect::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0.2rem;
            background-color: #FFC630;
            transition: all 0.3s ease;
        }

        .underline-effect:hover::before {
            width: 100%;
        }

        .underline-effect.active::before {
            width: 100%;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 10rem;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 0.5rem 1rem;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .table-fixed {
            table-layout: fixed;
        }

        .w-1 {
            width: 4%;
        }

        .w-5 {
            width: 10%;
        }

        .w-3 {
            width: 10%;
        }

        .w-4 {
            width: 15%;
        }

        .w-2 {
            width: 21%;
        }

        .w-6 {
            width: 40%;
        }

        td {
            word-wrap: break-word;
            word-break: break-all;
        }

        .username-container {
            max-width: 8rem;
            /* Adjust as needed */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Tambahkan ini di file CSS Anda */
        #documentDetailsModal .bg-white {
            max-height: 80vh;
            /* Sesuaikan tinggi maksimum sesuai kebutuhan */
            overflow-y: auto;
            /* Tambahkan scroll jika konten melebihi tinggi */
        }

        /* CSS untuk modal */
        #documentDetailsModal {
            overflow: hidden;
            /* Pastikan modal tidak scroll */
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white py-4 px-6 shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="/images/logosci.png" alt="Logo" class="h-12 mr-3">
                <h2 class="font-bold text-xl relative pb-2"> <!-- Menambahkan padding bottom pada judul -->
                    Monitoring Pengajuan TOR
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 to-transparent"></div>
                </h2>
            </div>
            <button id="hamburger-menu" class="md:hidden text-gray-600 focus:outline-none">
                ☰
            </button>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="fixed top-20 left-0 h-full w-36 bg-blue-700 text-white py-6 pl-4 pr-6 font-medium shadow-md hidden md:flex flex-col items-center">
        <div class="flex items-center justify-center w-full bg-blue-500 p-2 rounded-full mb-5 mt-4">
            <img src="/images/profile.png" alt="Profile" class="h-7 w-6 mr-2">
            <div class="username-container font-bold"><?php echo session()->get('username'); ?></div>
        </div>
        <ul class="space-y-2">
            <li><a href="/user/dashboard" class="block py-1 px-2 rounded-md underline-effect active">Beranda</a></li>
        </ul>
        <!-- Logout Button -->
        <div class="fixed bottom-4 left-4 z-50">
            <a href="/auth/logout" class="flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-700 rounded-full py-2 px-4 font-bold text-white max-w-[8rem]">
                <img src="/images/logoLogout.png" alt="Logout" class="h-5 mr-2">
                Logout
            </a>
        </div>

    </div>

    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="fixed top-20 left-0 h-full w-1/5 bg-blue-700 text-white py-6 pl-4 pr-6 font-medium shadow-md hidden flex-col items-center md:hidden ">
        <div class="flex items-center justify-center w-full bg-blue-500 p-2 rounded-full mb-5 mt-4">
            <img src="/images/profile.png" alt="Profile" class="h-7 w-6 mr-2">
            <div class="username-container font-bold"><?php echo session()->get('username'); ?></div>
        </div>
        <ul class="space-y-2">
            <li><a href="/user/dashboard" class="block py-0 px-1 rounded-md underline-effect active">Beranda</a></li>
        </ul>
        <ul class="space-y-2 mt-4">
            <a href="/auth/logout" class="block py-0 px-1 rounded-md underline-effect">
                Logout</a>
        </ul>
    </div>


    <!-- Main Content -->
    <div class="mt-24 ml-36 p-6">
        <div class=" container mx-auto">
            <div class="flex justify-between items-center mb-6">
                <a href="/userTambah" class="bg-cyan-500 text-white border-none py-2 px-4 mr-2 rounded cursor-pointer hover:bg-cyan-600 transition bg-blue-600 sm:hidden">+</a>
                <a href="/userTambah" class="hidden sm:block bg-cyan-500 text-white border-none py-2 px-4 rounded cursor-pointer hover:bg-cyan-600 transition bg-blue-600">+ Tambah Dokumen</a>
                <div class="flex items-center space-x-4">
                    <input type="text" id="search" class="border border-gray-300 rounded py-1 px-2 w-80" placeholder="Cari dokumen" oninput="searchTable()">
                    <input type="date" id="date-picker" class="border border-gray-300 rounded py-1 px-2 w-10" onchange="filterByDate()">

                    <button onclick="clearDateFilter()" class="border border-none rounded py-1 px-2 bg-blue-600  text-white hover:bg-blue-500">All</button>
                </div>
            </div>
            <table id="tableData" class="w-full table-auto bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="w-1 border p-3 text-left">No</th>
                        <th class="w-2 border p-3 text-left">Nama Barang/Jasa</th>
                        <th class="w-3 border p-3 text-left">Jenis Dokumen</th>
                        <th class="w-4 border p-3 text-left">Nama Bidang/Portofolio</th>
                        <th class="w-5 border p-3 text-left">Aksi</th>
                        <th class="w-6 border p-3 text-left">Tracking Proses Surat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dokumen as $index => $dokumenItem) : ?>
                        <tr>
                            <td class="border p-3"><?= $index + 1 ?></td>
                            <td class="border p-3"><?= $dokumenItem['nama_dokumen'] ?></td>
                            <td class="border p-3"><?= $dokumenItem['jenis_dokumen'] ?></td>
                            <td class="border p-3"><?= $dokumenItem['nama_bidang'] ?></td>
                            <td class="border p-3 relative">
                                <button class="bg-blue-600 text-white border-none py-1 px-3 rounded cursor-pointer" data-id="<?= $dokumenItem['id'] ?>" onclick="fetchDocumentDetails(this)">
                                    ☰
                                </button>
                                <button class="bg-gray-200 border-none py-1 px-3 rounded cursor-pointer dropdown-btn">▼</button>
                                <div class="dropdown-content absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-lg">
                                    <a href="/prosesTor?tambahdata_id=<?= $dokumenItem['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Proses TOR</a>
                                    <a href="/prosesBudgeting?tambahdata_id=<?= $dokumenItem['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Proses Budgeting</a>
                                    <a href="/prosesPpbj?tambahdata_id=<?= $dokumenItem['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Proses PPBJ</a>
                                </div>
                            </td>
                            <!-- Contoh tampilan status -->
                            <td class="border p-3">
                                <div class="flex justify-between">
                                    <!-- Proses TOR -->
                                    <div class="relative text-center">
                                        <!-- Lingkaran default abu-abu -->
                                        <div class="inline-block w-8 h-8 rounded-full bg-gray-300 text-white font-bold flex items-center justify-center mx-auto">1</div>
                                        <!-- Overlay untuk warna status -->
                                        <div class="absolute inset-0 w-8 h-8 rounded-full flex items-center justify-center mx-auto <?= empty($dokumenItem['status_tor']) || $dokumenItem['status_tor'] == null || $dokumenItem['status_tor'] == '' ? '' : ($dokumenItem['status_tor'] == 'pending' ? 'bg-orange-500' : ($dokumenItem['status_tor'] == 'syarat_tidak_terpenuhi' ? 'bg-red-500' : 'bg-green-500')) ?> text-white font-bold">
                                            <span><?= !empty($dokumenItem['status_tor']) ? '1' : '' ?></span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-2">PROSES TOR</div>
                                    </div>

                                    <!-- Proses Budgeting -->
                                    <div class="relative text-center">
                                        <div class="inline-block w-8 h-8 rounded-full bg-gray-300 text-white font-bold flex items-center justify-center mx-auto">2</div>
                                        <div class="absolute inset-0 w-8 h-8 rounded-full flex items-center justify-center mx-auto <?= empty($dokumenItem['status_budgeting']) || $dokumenItem['status_budgeting'] == null || $dokumenItem['status_budgeting'] == '' ? '' : ($dokumenItem['status_budgeting'] == 'pending' ? 'bg-orange-500' : ($dokumenItem['status_budgeting'] == 'syarat_tidak_terpenuhi' ? 'bg-red-500' : 'bg-green-500')) ?> text-white font-bold">
                                            <span><?= !empty($dokumenItem['status_budgeting']) ? '2' : '' ?></span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-2">BUDGETING</div>
                                    </div>

                                    <!-- Proses PPBJ -->
                                    <div class="relative text-center">
                                        <div class="inline-block w-8 h-8 rounded-full bg-gray-300 text-white font-bold flex items-center justify-center mx-auto">3</div>
                                        <div class="absolute inset-0 w-8 h-8 rounded-full flex items-center justify-center mx-auto <?= empty($dokumenItem['status_ppbj']) || $dokumenItem['status_ppbj'] == null || $dokumenItem['status_ppbj'] == '' ? '' : ($dokumenItem['status_ppbj'] == 'pending' ? 'bg-orange-500' : ($dokumenItem['status_ppbj'] == 'syarat_tidak_terpenuhi' ? 'bg-red-500' : 'bg-green-500')) ?> text-white font-bold">
                                            <span><?= !empty($dokumenItem['status_ppbj']) ? '3' : '' ?></span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-2">PROSES PPBJ</div>
                                    </div>

                                    <!-- Proses Pesan -->
                                    <div class="relative text-center">
                                        <div class="inline-block w-8 h-8 rounded-full bg-gray-300 text-white font-bold flex items-center justify-center mx-auto">4</div>
                                        <div class="absolute inset-0 w-8 h-8 rounded-full flex items-center justify-center mx-auto <?= empty($dokumenItem['status_pesan']) || $dokumenItem['status_pesan'] == null || $dokumenItem['status_pesan'] == '' ? '' : ($dokumenItem['status_pesan'] == 'pending' ? 'bg-orange-500' : ($dokumenItem['status_pesan'] == 'syarat_tidak_terpenuhi' ? 'bg-red-500' : 'bg-green-500')) ?> text-white font-bold">
                                            <span><?= !empty($dokumenItem['status_pesan']) ? '4' : '' ?></span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-2">PROSES PESAN</div>
                                    </div>

                                    <!-- Proses Selesai -->
                                    <div class="relative text-center">
                                        <div class="inline-block w-8 h-8 rounded-full bg-gray-300 text-white font-bold flex items-center justify-center mx-auto">5</div>
                                        <div class="absolute inset-0 w-8 h-8 rounded-full flex items-center justify-center mx-auto <?= empty($dokumenItem['status_selesai']) || $dokumenItem['status_selesai'] == null || $dokumenItem['status_selesai'] == '' ? '' : ($dokumenItem['status_selesai'] == 'pending' ? 'bg-orange-500' : ($dokumenItem['status_selesai'] == 'syarat_tidak_terpenuhi' ? 'bg-red-500' : 'bg-green-500')) ?> text-white font-bold">
                                            <span><?= !empty($dokumenItem['status_selesai']) ? '5' : '' ?></span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-2">PROSES SELESAI</div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Link ke dokument detail -->
    <?php include 'userDokdetail.php'; ?>

    <!-- Link the JavaScript file -->
    <script src="<?= base_url('js/userScript.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerMenu = document.getElementById('hamburger-menu');
            const mobileSidebar = document.getElementById('mobile-sidebar');

            hamburgerMenu.addEventListener('click', function() {
                mobileSidebar.classList.toggle('hidden');
            });

        });
        document.getElementById('hamburger-menu').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.toggle('hidden');
        });
    </script>
</body>

</html>