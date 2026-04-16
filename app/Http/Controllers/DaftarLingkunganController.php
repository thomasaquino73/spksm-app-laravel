<?php

namespace App\Http\Controllers;

use App\Http\Requests\DaftarLingkunganRequest;
use App\Models\DaftarLingkungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DaftarLingkunganController extends Controller
{
    public function index(Request $r)
    {
        if ($r->ajax()) {
            $query = DaftarLingkungan::where('status', '<>', 0);

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
                           <i class="ti ti-menu-2 ti-xs me-1"></i>  
                      Action
                      </button>
                      <ul class="dropdown-menu" style="">';
                    $btn .= '<a class="dropdown-item editPost" href="javascript:void(0)"
                            data-id="'.$row->id.'"> <i class="far fa-edit me-1"></i>Ubah</a>';
                    $btn .= '<a class="dropdown-item" href="javascript:void(0)" id="delete"
                                data-id="'.$row->id.'"
                                data-name="'.$row->nama_lingkungan.'"
                                ><i class="fa fa-trash me-1"></i> Hapus</a>';

                    return $btn;
                })
                ->rawColumns(['action', 'created_at', 'updated_at', 'status'])
                ->make(true);
        }
        $x = [
            'title' => 'Daftar Lingkungan',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Lingkungan', 'url' => ''],
            ],
        ];

        return view('lingkungan.index', $x);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(DaftarLingkunganRequest $request)
    {
        try {
            $id = $request->input('id');
            $data = $request->all();

            if (! empty($id)) {
                // Update existing record
                $data['updated_at'] = now();
                $data['updated_by'] = Auth::user()->id;
                DaftarLingkungan::updateOrCreate(['id' => $id], $data);

                return response()->json([
                    'message' => 'Data Updated Successfully',
                    'title' => 'Updated',
                    'updated_at' => now()->toDateTimeString(),
                ], 200);
            } else {
                // Create new record
                $data['created_at'] = now();
                $data['created_by'] = Auth::user()->id;
                DaftarLingkungan::create($data);

                return response()->json([
                    'message' => 'Data Added Successfully',
                    'title' => 'Created',
                    'created_at' => now()->toDateTimeString(),
                ], 201);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(Request $request)
    {

        $where = [
            'id' => $request->id,
        ];
        $data = DaftarLingkungan::where($where)->first();

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Request $request, $id)
    {

        try {
            $table = DaftarLingkungan::findOrFail($id);
            $table->status = 0;
            $table->updated_by = Auth::user()->id;
            $table->save();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function trash_bin(Request $r)
    {
        if ($r->ajax()) {
            $query = DaftarLingkungan::where('status', 0);

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
                           <i class="ti ti-menu-2 ti-xs me-1"></i>  
                      Action
                      </button>
                      <ul class="dropdown-menu" style="">';
                    $btn .= ' <button class="dropdown-item restore "data-id="'.$row->id.'"
                                                data-name="'.$row->area_name.'">
                                                <i class="fa fa-undo me-1"></i> Restore
                                            </button>';

                    return $btn;
                })
                ->rawColumns(['action', 'created_at', 'updated_at', 'status'])
                ->make(true);
        }
        $x = [
            'title' => 'Daftar Daftar Lingkungan Terhapus',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Daftar Lingkungan Terhapus', 'url' => ''],
            ],
        ];

        return view('lingkungan.trash', $x);
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $album = DaftarLingkungan::find($id);

            $album->status = 1;
            $album->updated_by = Auth::id();
            $album->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => true,
                'message' => 'Data RW berhasil dikembalikan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'redirect' => false,
                'message' => 'Data RW gagal dikembalikan.',
            ]);
        }
    }
}
