<?php

namespace App\Controllers;


use App\Models\TambahDataModel;
use App\Models\TambahTorModel;
use App\Models\TambahBudgetModel;
use App\Models\TambahPpbjModel;
use App\Models\BidangModel;
use App\Models\TambahSuratpesananModel;
use App\Models\TambahSpphModel;
use App\Models\TambahKontrakModel;
use App\Models\TambahUmkModel;

class RekapController extends BaseController
{
    public function bulanan()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $model = new TambahDataModel();
        $selectedMonth = $this->request->getGet('month') ?? date('Y-m');
        $data['documents'] = $model->where("DATE_FORMAT(tanggal, '%Y-%m')", $selectedMonth)->findAll();
        $data['selectedMonth'] = $selectedMonth;

        return view('rekap_bulanan', $data);
    }

    public function tahunan()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $model = new TambahDataModel();
        $selectedYear = $this->request->getGet('year') ?? date('Y');
        $data['documents'] = $model->where("DATE_FORMAT(tanggal, '%Y')", $selectedYear)->findAll();
        $data['selectedYear'] = $selectedYear;

        return view('rekap_tahunan', $data);
    }


    public function getDocumentDetails()
    {
        $id = $this->request->getVar('id');
        $dataModel = new TambahDataModel();
        $torModel = new TambahTorModel();
        $budgetModel = new TambahBudgetModel();
        $ppbjModel = new TambahPpbjModel();
        $bidangModel = new BidangModel();
        $suratpesananModel = new TambahSuratpesananModel();
        $umkModel = new TambahUmkModel();
        $spphModel = new TambahSpphModel();
        $kontrakModel = new TambahKontrakModel();

        // Ambil data dokumen berdasarkan ID
        $document = $dataModel->find($id);

        // Ambil data TOR, Budgeting, dan PPBJ yang terkait
        $torDetails = $torModel->where('tambahdata_id', $id)->first();
        $budgetDetails = $budgetModel->where('tambahdata_id', $id)->first();
        $ppbjDetails = $ppbjModel->where('tambahdata_id', $id)->first();
        $suratpesananDetails = $suratpesananModel->where('tambahdata_id', $id)->first();
        $umkDetails = $umkModel->where('tambahdata_id', $id)->first();
        $spphDetails = $spphModel->where('tambahdata_id', $id)->first();
        $kontrakDetails = $kontrakModel->where('tambahdata_id', $id)->first();

        // Ambil nama_kepala dari kepala_bidang berdasarkan ID KABID
        $kabidId = $document['KABID'];
        $bidangData = $bidangModel->find($kabidId);
        $namaKepala = $bidangData['nama_kepala'] ?? '';

        // Fungsi untuk format Rupiah
        function formatRupiah($angka)
        {
            return 'Rp ' . number_format($angka, 0, ',', '.');
        }

        if ($document) {
            // Format harga satuan, total harga, dan nilai-nilai lainnya
            $document['harga_satuan'] = formatRupiah($document['harga_satuan']);
            $document['total_harga'] = formatRupiah($document['total_harga']);

            // Format nilai-nilai tambahan
            $ppbjDetails['nilai_ppbj'] = formatRupiah($ppbjDetails['nilai_ppbj'] ?? 0);
            $suratpesananDetails['harga_pesanan'] = formatRupiah($suratpesananDetails['harga_pesanan'] ?? 0);
            $umkDetails['harga_umk'] = formatRupiah($umkDetails['harga_umk'] ?? 0);
            $kontrakDetails['harga_kontrak'] = formatRupiah($kontrakDetails['harga_kontrak'] ?? 0);

            // Gabungkan data dokumen dengan data lainnya
            $data = array_merge($document, $torDetails ?? [], $budgetDetails ?? [], $ppbjDetails ?? [], $suratpesananDetails ?? [], $umkDetails ?? [], $spphDetails ?? [], $kontrakDetails ?? [], ['nama_kepala' => $namaKepala]);

            // Kembalikan data dalam format JSON
            return $this->response->setJSON($data);
        } else {
            // Kembalikan response error jika data tidak ditemukan
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Document not found']);
        }
    }
}
