<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\KategoriBerita;
use Illuminate\Http\Request;

class ApiGaleriController extends Controller
{
     public function index(Request $r)
    {
        $query = Galeri::query();

        // filter status jika ada
        if ($r->status !== null) {
            $query->where('status', $r->status);
        }

        // filter kategori jika ada
        if ($r->kategori) {
            $query->where('kategori_berita_id', 'like', '%'.$r->kategori.'%');
        }

        // hanya yang status bukan 0 (delete)
        $query->where('status',3);

        // ambil data terbaru, paginasi 12 per page
        $galleries = $query->latest()->paginate(12);

        // statistik kategori
        $kategoris = KategoriBerita::orderBy('name')->get();

        // hitung jumlah untuk dashboard / info
        $countAll = Galeri::count();
        $countPublish = Galeri::where('status', 3)->count();
        $countDraft = Galeri::where('status', 1)->count();
        $countUnpublish = Galeri::where('status', 2)->count();
        $countDeleted = Galeri::where('status', 0)->count();

        // kembalikan sebagai JSON
        return response()->json([
            'galleries' => $galleries,
            'stats' => [
                'all' => $countAll,
                'publish' => $countPublish,
                'draft' => $countDraft,
                'unpublish' => $countUnpublish,
                'deleted' => $countDeleted,
            ],
            'categories' => $kategoris,
        ]);
    }
}
