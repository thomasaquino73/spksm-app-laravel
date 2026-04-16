<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriBeritaRequest;
use App\Models\KategoriBerita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class KategoriBeritaController extends Controller
{
    public function index(Request $r)
    {
        $data = KategoriBerita::all();

        if ($r->ajax()) {
            return Datatables::of($data)
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
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Deleted</span>';
                    } elseif ($row->status == 1) {
                        return '<span class="badge bg-secondary">Draft</span>';
                    } elseif ($row->status == 2) {
                        return '<span class="badge bg-warning">Unpublish</span>';
                    } else {
                        return '<span class="badge bg-success">Publish</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-menu-2 ti-xs me-1"></i>
                      Action
                      </button>
                      <ul class="dropdown-menu" style="">';
                    // Deleted
                    if ($row->status == 0) {
                        $btn .= '<a class="dropdown-item restore" href="javascript:void(0)"
                            data-id="'.$row->id.'"  data-name="'.$row->name.'"> <i class="ti ti-trash-off me-1"></i>Restore</a>';
                        // Terminate
                    } elseif ($row->status == 2) {

                        $btn .= '<a class="dropdown-item publishPost" href="javascript:void(0)"
                            data-id="'.$row->id.'" data-name="'.$row->name.'"> <i class="fa fa-cloud-upload me-1"></i>Publish</a>';
                        $btn .= '<a class="dropdown-item editPost" href="javascript:void(0)"
                            data-id="'.$row->id.'"> <i class="far fa-edit"></i> Ubah</a>';
                        $btn .= '<a class="dropdown-item" href="javascript:void(0)" id="delete"
                                data-id="'.$row->id.'"
                                data-name="'.$row->name.'"
                                ><i class="fa fa-trash"></i> Hapus</a>';
                        // publish
                    } elseif ($row->status == 3) {
                        $btn .= '<a class="dropdown-item unpublishPost" href="javascript:void(0)"
                            data-id="'.$row->id.'" data-name="'.$row->name.'"> <i class="ti ti-cloud-off me-1"></i>Unpublish</a>';
                    } else {

                        $btn .= '<a class="dropdown-item editPost" href="javascript:void(0)"
                            data-id="'.$row->id.'"> <i class="far fa-edit"></i> Ubah</a>';

                        $btn .= '<a class="dropdown-item publishPost" href="javascript:void(0)"
                            data-id="'.$row->id.'" data-name="'.$row->name.'"> <i class="fa fa-cloud-upload me-1"></i>Publish</a>';
                        $btn .= '<a class="dropdown-item" href="javascript:void(0)" id="delete"
                                data-id="'.$row->id.'"
                                data-name="'.$row->name.'"
                                ><i class="ti ti-trash"></i> Delete</a>';
                    }

                    return $btn;
                })

                ->rawColumns(['action', 'created_at', 'updated_at', 'status'])
                ->make(true);
        }
        $x = [
            'title' => 'Daftar Kategori Berita',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Daftar Kategori Berita', 'url' => ''],
            ],
        ];

        return view('kategori.kategori_berita_index', $x);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(KategoriBeritaRequest $request)
    {
        try {
            $id = $request->input('id');
            $data = $request->all();

            if (! empty($id)) {
                // Update existing record
                $data['updated_at'] = now();
                $data['updated_by'] = Auth::user()->id;
                KategoriBerita::updateOrCreate(['id' => $id], $data);

                return response()->json([
                    'message' => 'Data Updated Successfully',
                    'updated_at' => now()->toDateTimeString(),
                ], 200);
            } else {
                // Create new record
                $data['created_at'] = now();
                $data['status'] = 1;
                $data['created_by'] = Auth::user()->id;
                KategoriBerita::create($data);

                return response()->json([
                    'message' => 'Data Added Successfully',
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
        $data = KategoriBerita::where($where)->first();

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
            $table = KategoriBerita::findOrFail($id);
            $table->status = '0';
            $table->updated_by = Auth::user()->id;
            $table->save();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function publish(Request $request, $id)
    {

        try {
            $table = KategoriBerita::findOrFail($id);
            $table->status = '3';
            $table->updated_by = Auth::user()->id;
            $table->save();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function unpublish(Request $request, $id)
    {

        try {
            $table = KategoriBerita::findOrFail($id);
            $table->status = '2';
            $table->updated_by = Auth::user()->id;
            $table->save();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function restore(Request $request, $id)
    {

        try {
            $table = KategoriBerita::findOrFail($id);
            $table->status = '1';
            $table->updated_by = Auth::user()->id;
            $table->save();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
