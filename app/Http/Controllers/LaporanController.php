<?php

namespace App\Http\Controllers;

use App\Models\PengaturanSistem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LaporanController extends Controller
{
    public function daftar_umat()
    {

        $x = [
            'title' => 'Daftar Umat',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Daftar Umat', 'url' => ''],
            ],
        ];

        return view('laporan.daftar_umat.daftar_umat_index', $x);
    }

    public function daftar_umat_detail($id)
    {
        $detail=User::findorfail($id);

        $x = [
            'title' => 'Detail Umat',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Detail Umat', 'url' => ''],
            ],
            'detail'=>$detail,
        ];

        return view('laporan.daftar_umat.daftar_umat_detail', $x);
    }

    public function tabelUmat(Request $r)
    {
        if ($r->ajax()) {
            $query = User::where('status', '<>', 0)->where('active', '<>', 0);

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
                ->addColumn('avatar', function ($row) {

                    // Tentukan avatar berdasarkan data user
                    if ($row->avatar) {
                        $avatarUrl = asset($row->avatar);
                    } else {
                        $avatarUrl = $row->gender == 'Perempuan'
                            ? asset('image/foto_user/avatar_women.png')
                            : asset('image/foto_user/avatar_user_default.png');
                    }

                    // Kembalikan HTML img untuk datatable
                    return '<img class="avatar avatar-md rounded-circle me-2 avatar-online"
                                src="'.$avatarUrl.'"
                                alt="Pengguna profile picture">';
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 'Active' ? '<span class="badge bg-success">'.$row->status.'</span>' : '<span class="badge bg-danger">'.$row->status.'</span>';
                })
                ->addColumn('lingkungan', function ($row) {
                    return $row->lingkungan->nama_lingkungan
                        ?? '<span class="badge bg-danger">Lingkungan Nonaktif</span>';
                })

                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-sm btn-primary" href="'.route('daftar-umat.detail', $row->id).'"><i class="ti ti-search "></i> </a>';

                    return $btn;
                })
                ->rawColumns(['action', 'created_at', 'updated_at', 'status', 'avatar', 'lingkungan'])
                ->make(true);
        }
    }


}
