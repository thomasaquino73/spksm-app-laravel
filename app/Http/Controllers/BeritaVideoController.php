<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeritaVideRequest;
use App\Models\BeritaVideo;
use App\Models\Galeri;
use App\Models\KategoriBerita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BeritaVideoController extends Controller
{
    public function index(Request $request)
    {
        $query = BeritaVideo::query();

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }
        // FILTER KATEGORI
        if ($request->kategori) {
            $query->where('kategori_berita_id', 'like', '%'.$request->kategori.'%');
        }

        $videos = $query->latest()->where('status', '<>', 0)->paginate(12)->withQueryString();
        $kategoris = KategoriBerita::orderBy('name')->get();
        $countAll = BeritaVideo::count();
        $countPublish = BeritaVideo::where('status', 3)->count();
        $countDraft = BeritaVideo::where('status', 1)->count();
        $countUnpublish = BeritaVideo::where('status', 2)->count();
        $countDeleted = BeritaVideo::where('status', 0)->count();

        return view('beritavideo.index', [
            'title' => 'Daftar Berita Video ',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Daftar Berita Video', 'url' => ''],
            ],
            'videos' => $videos,
            'countAll' => $countAll,
            'countPublish' => $countPublish,
            'countDraft' => $countDraft,
            'countUnpublish' => $countUnpublish,
            'countDeleted' => $countDeleted,
            'kategoris' => $kategoris,
        ]);
    }

    public function create()
    {
        $x = [
            'title' => 'Tambah Berita Video',
            'breadcrumb' => [
                ['label' => 'Berita Video', 'url' => route('berita-video.index')],
                ['label' => 'Tambah Berita Video', 'url' => ''],
            ],
            'kategori' => KategoriBerita::whereIn('status', [1, 3])->get(),
        ];

        return view('beritavideo.create', $x);
    }

    public function update(BeritaVideRequest $r, $id)
    {
        DB::beginTransaction();

        try {
            $data = $r->except(['_token', '_method', 'save_and_new']);

            $data['updated_by'] = Auth::id();

            /*
            |--------------------------------
            | Ambil Youtube ID dari URL
            |--------------------------------
            */

            $link = $r->youtube_id;

            $youtubeId = null;

            if (preg_match('/youtu\.be\/([^\?]+)/', $link, $match)) {
                $youtubeId = $match[1];
            } elseif (preg_match('/v=([^&]+)/', $link, $match)) {
                $youtubeId = $match[1];
            } elseif (preg_match('/shorts\/([^\?]+)/', $link, $match)) {
                $youtubeId = $match[1];
            } else {
                $youtubeId = $link;
            }

            $data['youtube_id'] = $youtubeId;

            /*
            |--------------------------------
            | Thumbnail otomatis dari Youtube
            |--------------------------------
            */

            $data['thumbnail'] = 'https://img.youtube.com/vi/'.$youtubeId.'/hqdefault.jpg';

            /*
            |--------------------------------
            | Slug
            |--------------------------------
            */

            $data['slug'] = Str::slug($r->judul);

            /*
            |--------------------------------
            | Kategori
            |--------------------------------
            */

            if ($r->kategori_berita_id) {
                $data['kategori_berita_id'] = json_encode($r->kategori_berita_id);
            }

            /*
            |--------------------------------
            | Hashtag
            |--------------------------------
            */

            $hashtags = $r->keyword;

            if (! empty($hashtags) && is_string($hashtags)) {

                $decoded = json_decode($hashtags, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $hashtags = array_map(fn ($item) => $item['value'], $decoded);
                }

            }

            $data['keyword'] = implode(', ', $hashtags) ?? '';

            /*
            |--------------------------------
            | Update Video
            |--------------------------------
            */

            $video = BeritaVideo::findOrFail($id);

            $video->update($data);

            /*
            |--------------------------------
            | Update Hashtag
            |--------------------------------
            */

            // DB::table('hashtags')
            //     ->where('tag_newsid', $video->id)
            //     ->delete();

            Galeri::storeNewHashtag([
                'tag_newsid' => $video->id,
                'hashtags' => $hashtags,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Video berhasil diperbarui.',
                'redirect' => route('berita-video.index'),
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'error' => 'Terjadi kesalahan pada server.',
                'details' => $e->getMessage(),
            ], 500);

        }
    }

    public function edit($id)
    {
        $video = BeritaVideo::findOrFail($id);

        $x = [
            'title' => 'Edit Berita Video',
            'breadcrumb' => [
                ['label' => 'Berita Video', 'url' => route('berita-video.index')],
                ['label' => 'Edit Berita Video', 'url' => ''],
            ],
            'fotographer' => User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 5);
            })
                ->where('active', 1)
                ->where('status', 'Active')
                ->get(),
            'video' => $video,
            'kategori' => KategoriBerita::whereIn('status', [1, 3])->get(),
        ];

        return view('beritavideo.edit', $x);
    }

    public function destroy(Request $r, $id)
    {
        try {
            $album = BeritaVideo::findOrFail($id);

            // Soft delete album
            $album->status = 0;
            $album->publish_reason = $r->reason;
            $album->updated_by = Auth::id();
            $album->updated_at = now();
            $album->save();
            $user = Auth::user();

            return response()->json(['message' => 'Video di-nonaktifkan dan foto di-update.', 'redirect' => route('berita-video.index')]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function trash_bin(Request $request)
    {
        $query = BeritaVideo::query();

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $videos = $query->latest()->where('status', 0)->paginate(12)->withQueryString();

        $countAll = BeritaVideo::count();
        $countPublish = BeritaVideo::where('status', 3)->count();
        $countDraft = BeritaVideo::where('status', 1)->count();
        $countUnpublish = BeritaVideo::where('status', 2)->count();
        $countDeleted = BeritaVideo::where('status', 0)->count();

        return view('beritavideo.trash', [
            'title' => 'Daftar Berita Video Terhapus',
            'breadcrumb' => [
                ['label' => 'Berita Video', 'url' => route('galeri.index')],
                ['label' => 'Daftar Berita Video Terhapus', 'url' => ''],
            ],
            'videos' => $videos,
            'countAll' => $countAll,
            'countPublish' => $countPublish,
            'countDraft' => $countDraft,
            'countUnpublish' => $countUnpublish,
            'countDeleted' => $countDeleted,
        ]);
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $album = BeritaVideo::find($id);
            $album->status = 1;
            $album->updated_by = Auth::id();
            $album->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('berita-video.trash'),
                'message' => 'Video berhasil dikembalikan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'redirect' => false,
                'message' => 'Video gagal dikembalikan.',
            ]);
        }
    }

    // public function publish($id)
    // {
    //     $video = BeritaVideo::findOrFail($id);

    //     $x = [
    //         'title' => 'Edit Berita Video Foto',
    //         'breadcrumb' => [
    //             ['label' => 'Berita Video', 'url' => route('berita-video.index')],
    //             ['label' => 'Edit Berita Video', 'url' => ''],
    //         ],
    //         'video' => $video,
    //     ];

    //     return view('berita-video.publish', $x);
    // }

    public function togglePublish(Request $r, $id)
    {
        $album = BeritaVideo::findOrFail($id);

        // Jika status publish → unpublish
        if ($album->status == 3) {

            $album->update([
                'status' => 2,
                'publish_reason' => $r->reason,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

            $message = 'Berita Video berhasil di-unpublish.';
        }

        // Jika status draft → publish
        elseif ($album->status == 1) {

            $album->update([
                'status' => 3,
                'publish_reason' => null,
                'updated_by' => Auth::id(),
                'publish_date' => now(),
            ]);

            $message = 'Berita Video berhasil dipublish.';
        }

        // Jika status unpublish → publish ulang
        elseif ($album->status == 2) {

            $album->update([
                'status' => 3,
                'publish_reason' => null,
                'updated_by' => Auth::id(),
            ]);

            $message = 'Berita Video berhasil dipublish ulang.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'reason' => $album->publish_reason,
        ]);
    }

    public function publishNow() {}
}
