<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PesanAmbulanceRequest;
use App\Models\PesanAmbulance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiPesanAmbulanceController extends Controller
{
   public function index()
    {
        $data = PesanAmbulance::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

        return response()->json([
            'success' => true,
            'data'    => $data
        ], 200);
    }
    public function store(PesanAmbulanceRequest $r)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Generate kode_pesan (AMB-YYYYMMDD-00001)
            $tanggal = Carbon::now()->format('Ymd');

            $last = PesanAmbulance::whereDate('created_at', Carbon::today())->count();
            $noUrut = str_pad($last + 1, 5, '0', STR_PAD_LEFT);

            $kodePesan = 'AMB-' . $tanggal . '-' . $noUrut;

            // Simpan data
            $data = PesanAmbulance::create([
                'kode_pesan' => $kodePesan,
                'user_id' => $user->id,
                'nama_pasien' => $r->nama_pasien,
                'jenis_kelamin' => $r->jenis_kelamin,
                'alamat_penjemputan' => $r->alamat_penjemputan,
                'waktu_pesan' => now(),
                'kondisi_pasien' => $r->kondisi_pasien,
                'lokasi_pengantaran' => $r->lokasi_pengantaran,
                'catatan_singkat' => $r->catatan_singkat,
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesan ambulance berhasil dikirim 🚑',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDashboardSummary()
    {
        // Menggunakan Auth::id() lebih aman
        $userId = auth()->id(); 
        
        // Pastikan mengembalikan default 0 jika data tidak ada
        return response()->json([
            'success' => true,
            'summary' => [
                'total_pesanan' => \App\Models\PesanAmbulance::where('user_id', $userId)->count() ?? 0,
                'pending'       => \App\Models\PesanAmbulance::where('user_id', $userId)->where('status', 'pending')->count() ?? 0,
                'selesai'       => \App\Models\PesanAmbulance::where('user_id', $userId)->where('status', 'selesai')->count() ?? 0,
            ]
        ]);
    }
}
