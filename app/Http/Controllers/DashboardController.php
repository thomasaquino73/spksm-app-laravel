<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use App\Models\KategoriBerita;
use App\Models\PesanAmbulance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pengurus = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Ketua', 'Anggota', 'Reporter']);
        })
            ->where('active', 1)
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->orderByRaw("CASE
                WHEN roles.name = 'Ketua' THEN 1
                WHEN roles.name = 'Anggota' THEN 2
            END")
            ->select('users.*')
            ->get();

        $query = Galeri::latest()->where('status', '<>', 0);

        // FILTER KATEGORI
        if ($request->kategori) {
            $query->where('kategori_berita_id', 'like', '%'.$request->kategori.'%');
        }

        $galleries = $query->paginate(12)->withQueryString();

        $kategoris = KategoriBerita::orderBy('name')->get();
        $total_ambulance = PesanAmbulance::whereNotIn('status', ['ditolak', 'batal'])->count();
        return view('dashboard', [
            'pengurus' => $pengurus,
            'galleries' => $galleries,
            'kategoris' => $kategoris,
            'total_ambulance' => $total_ambulance,
        ]);
    }
    public function HalamanDepan(){
       $x=[
             'kategori' => KategoriBerita::where('status', 3)->get(),
             'hastags' => DB::table('hastags')->get(),
       ] ;
        return view('welcome', $x);
    }
}
