<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Galeri extends Model
{
    protected $table = 'galeri_foto';

    protected $primaryKey = 'id';

    protected $casts = [
        'kategori_berita_id' => 'array',
    ];

    protected $guarded = [];

    /**
     * Nama tabel hashtag
     */
    protected $tabletagspublished = 'hastags';

    /**
     * Relasi pembuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBerita::class, 'kategori_berita_id');
    }

    /**
     * Ambil nama tabel hashtag
     */
    public function getTabletagspublished()
    {
        return $this->tabletagspublished;
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

    public static function searchTags($keyword = '')
    {
        $table = (new self)->getTabletagspublished();

        return DB::table($table)
            ->select('tag_id', 'tag_name')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('tag_name', 'like', '%'.$keyword.'%');
            })
            ->orderBy('tag_name', 'asc')
            ->distinct()
            ->limit(20)
            ->get();
    }

    /**
     * Simpan hashtag baru jika belum ada
     */
    public static function storeNewHashtag(array $data)
    {
        $table = (new self)->getTabletagspublished();

        if (! empty($data['hashtags'] ?? null)) {

            $hashtags = is_array($data['hashtags'])
                ? $data['hashtags']
                : explode(',', $data['hashtags']);

            foreach ($hashtags as $tag) {

                $tag = trim($tag);

                if ($tag === '') {
                    continue;
                }

                $slug = Str::slug($tag);

                $exists = DB::table($table)
                    ->where('tag_slug', $slug)
                    ->orWhere('tag_name', $tag)
                    ->exists();

                if (! $exists) {

                    DB::table($table)->insert([
                        'tag_newsid' => $data['tag_newsid'],
                        'tag_name' => $tag,
                        'tag_slug' => $slug,
                        'tag_click_count' => 0,
                        'created_by' => $data['created_by'],
                        'created_at' => $data['created_at'],
                        'updated_by' => $data['updated_by'],
                        'updated_at' => $data['updated_at'],
                    ]);
                }
            }
        }
    }

    /**
     * Auto generate slug ketika create
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->caption)) {
                $model->slug = Str::slug($model->caption);
            }
        });
    }
}
