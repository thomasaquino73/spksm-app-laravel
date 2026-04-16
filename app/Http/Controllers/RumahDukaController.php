<?php

namespace App\Http\Controllers;

use App\Http\Requests\RumahDukaRequest;
use App\Models\RumahDuka;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RumahDukaController extends Controller
{
      public function index()
    {
        $x = [
            'title' => 'Daftar Rumah Duka',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Daftar Rumah Duka', 'url' => ''],
            ],
        ];

        return view('rumahduka.index', $x);
    }
      public function data(Request $r)
    {
        if ($r->ajax()) {
            $query = RumahDuka::where('status', '<>', 0);

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
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-warning">Not Active</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" aria-expanded="false">
                        Action
                      </button>
                      <ul class="dropdown-menu" style="">';
                    $btn .= '<a class="dropdown-item editPost" href="javascript:void(0)"
                            data-id="'.$row->id.'"> <i class="far fa-edit me-1"></i>Ubah</a>';
                    $btn .= '<a class="dropdown-item" href="javascript:void(0)" id="delete"
                                data-id="'.$row->id.'"
                                data-name="'.$row->plat_nomor.'"
                                ><i class="fa fa-trash me-1"></i> Hapus</a>';

                    return $btn;
                })
                ->rawColumns(['action', 'created_at', 'updated_at', 'status'])
                ->make(true);
        }
    }
    public function store(RumahDukaRequest $request)
    {
        try {
            $id = $request->input('id');

            // 1️⃣ Gabungkan plat nomor terpisah menjadi satu kolom
            $plat_nomor = strtoupper(
                trim($request->plat_depan).' '.
                trim($request->plat_tengah).' '.
                trim($request->plat_belakang)
            );

            // 2️⃣ Siapkan data untuk simpan/update
            $data = [
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'kontak' => $request->kontak,
                'status' => $request->status,
            ];

            if (! empty($id)) {
                // Update existing record
                $data['updated_at'] = Carbon::now();
                $data['updated_by'] = Auth::id();

                RumahDuka::updateOrCreate(
                    ['id' => $id],
                    $data
                );

                return response()->json([
                    'message' => 'Data Updated Successfully',
                    'title' => 'Updated',
                    'updated_at' => now()->toDateTimeString(),
                ], 200);
            } else {
                // Create new record
                $data['created_at'] = Carbon::now();
                $data['created_by'] = Auth::id();

                RumahDuka::create($data);

                return response()->json([
                    'message' => 'Data Added Successfully',
                    'title' => 'Created',
                    'created_at' => now()->toDateTimeString(),
                ], 201);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Jika validasi gagal
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Error umum
            return response()->json([
                'error' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $kendaraan = RumahDuka::findOrFail($id);
        return response()->json([
            'id' => $kendaraan->id,
            'nama' => $kendaraan->nama,
            'alamat' => $kendaraan->alamat,
            'kontak' => $kendaraan->kontak,
            'status' => $kendaraan->status,
        ], 200);
    }
       public function destroy(Request $request, $id)
    {

        try {
            $table = RumahDuka::findOrFail($id);
            $table->status = 0;
            $table->updated_by = Auth::user()->id;
            $table->save();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function trash(Request $r)
    {
        if ($r->ajax()) {
            $query = RumahDuka::where('status', 0);

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
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-warning">Not Active</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" aria-expanded="false">
                        Action
                      </button>
                      <ul class="dropdown-menu" style="">';
                    $btn .= ' <button class="dropdown-item restore "data-id="'.$row->id.'"
                                                data-name="'.$row->plat_nomor.'">
                                                <i class="fa fa-undo me-1"></i> Restore
                                            </button>';

                    return $btn;
                })
                ->rawColumns(['action', 'created_at', 'updated_at', 'status'])
                ->make(true);
        }

        return view('rumahduka.trash', [
            'title' => 'Daftar Rumah Duka Terhapus',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Rumah Duka Terhapus', 'url' => ''],
            ],
        ]);
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $album = RumahDuka::find($id);

            $album->status = 1;
            $album->updated_by = Auth::id();
            $album->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => true,
                'message' => 'Data Rumah Duka berhasil dikembalikan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'redirect' => false,
                'message' => 'Data Rumah Duka gagal dikembalikan.',
            ]);
        }
    }

}
