<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeritaVideo extends Model
{
    protected $table = 'berita_video';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBerita::class, 'kategori_berita_id');
    }

    public function getKategoriNamesAttribute()
    {
        $kategoriIds = $this->kategori_berita_id;

        // jika string JSON
        if (is_string($kategoriIds)) {
            $kategoriIds = json_decode($kategoriIds, true);
        }

        // jika masih bukan array
        if (! is_array($kategoriIds)) {
            $kategoriIds = [$kategoriIds];
        }

        return \App\Models\KategoriBerita::whereIn('id', $kategoriIds)
            ->pluck('name')
            ->implode(', ');
    }
}
