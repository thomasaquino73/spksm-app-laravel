<?php

namespace App\Http\Controllers;

use App\Http\Requests\PesanAmbulanceRequest;
use App\Models\PesanAmbulance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PesanAmbulanceController extends Controller
{
    public function index()
    {
        $x = [
            'title' => 'Daftar Pesan Ambulance',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Daftar Pesan Ambulance', 'url' => ''],
            ],
        ];

        return view('ambulance.index', $x);
    }
       public function data(Request $r)
    {
        if ($r->ajax()) {
            $query = PesanAmbulance::where('user_id', Auth::id())->latest()->get();

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return $row->created_at
                        ? (($row->creator->nama_lengkap ?? 'Unknown')).
                        ' <br><small class="text-muted"> '.$row->created_at->diffForHumans().'</small>'
                        : 'N/A';
                })
                ->addColumn('updated_at', function ($row) {
                    if ($row->updated_at) {
                        $updaterName = $row->updater->nama_lengkap ?? 'Unknown';
                        $timeAgo = $updaterName !== 'Unknown' ? $row->updated_at->diffForHumans() : 'N/A';

                        return $updaterName.
                            ' <br><small class="text-muted">'.$timeAgo.'</small>';
                    }

                    return 'N/A';
                })
                ->addColumn('jenis_kelamin', function ($row) {
                    if ($row->jenis_kelamin == 'P') {
                        return '<span class="badge bg-success">Perempuan</span>';
                    } else {
                        return '<span class="badge bg-warning">Laki-laki</span>';
                    }
                })
                ->addColumn('kondisi_pasien', function ($row) {
                    if ($row->kondisi_pasien == 0) {
                        return '<span class="badge bg-danger">Sakit</span>';
                    } else {
                        return '<span class="badge bg-dark">Meninggal</span>';
                    }
                })
                ->addColumn('status', function ($row) {
                    switch ($row->status) {
                        case 'diterima':
                            return '<span class="badge bg-primary">Diterima</span>';
                        case 'ditolak':
                            return '<span class="badge bg-danger">Ditolak</span>';
                        case 'selesai':
                            return '<span class="badge bg-success">Selesai</span>';
                        case 'batal':
                            return '<span class="badge bg-dark">Batal</span>';
                        default:
                            return '<span class="badge bg-secondary">Pending</span>';
                    }
                })
                ->addColumn('lokasi_pengantaran', function ($row) {
                    switch ($row->lokasi_pengantaran) {
                        case '1':
                            return '<span class="badge bg-primary">Rumah</span>';
                        case '2':
                            return '<span class="badge bg-danger">Rumah Sakit</span>';
                        default:
                            return '<span class="badge bg-secondary">Rumah Duka</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                     if ($row->status == 'pending' ) {
                    return '<a class="btn btn-danger btn-sm" href="javascript:void(0)" id="batalPesan"
                                data-id="'.$row->id.'"
                                data-name="'.$row->nama_pasien.'">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>';
                    } else if ($row->status == 'batal' ){
                    return '<span class="badge bg-secondary">Sudah Dibatalkan</span>';
                    }else {
                    return '<span class="badge bg-dark">Tidak Bisa Dibatalkan</span>';
                    }
                })
                ->rawColumns(['action', 'created_at', 'updated_at', 'status','jenis_kelamin','kondisi_pasien','lokasi_pengantaran'])
                ->make(true);
        }
    }
      public function pesan_ambulance()
    {
        $x = [
            'title' => 'Pesan Ambulance',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pesan Ambulance', 'url' => ''],
            ],
        ];

        return view('ambulance.pesan_ambulance', $x);
    }

   public function store(PesanAmbulanceRequest $r)
    {
        DB::beginTransaction();
        try {
            $userData = $r->all();
            $userData['created_by'] = Auth::id();
            $userData['user_id'] = Auth::id();
            $userData['waktu_pesan'] = now();

            // Generate kode_pesan
            $tanggal = date('Ymd');
            $last = PesanAmbulance::whereDate('created_at', date('Y-m-d'))->count();
            $noUrut = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
            $userData['kode_pesan'] = 'AMB-' . $tanggal . '-' . $noUrut;

            PesanAmbulance::create($userData);

            DB::commit();

            return response()->json([
                'title' => 'Success',
                'message' => 'Pesan ambulance berhasil dikirim.',
                'redirect' => route('ambulance.index'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'errors' => [
                    'general' => [$e->getMessage()],
                ],
            ], 500);
        }
    }

    public function batal_pesan($id)
    {
        try {
            $data = PesanAmbulance::findOrFail($id);

            // update status jadi batal
            $data->update([
                'status' => 'batal'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
