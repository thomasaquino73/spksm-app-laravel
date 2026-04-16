<?php

namespace App\Http\Controllers;

use App\Http\Requests\GaleriRequest;
use App\Models\Galeri;
use App\Models\KategoriBerita;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $query = Galeri::query();

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }
        // FILTER KATEGORI
        if ($request->kategori) {
            $query->where('kategori_berita_id', 'like', '%'.$request->kategori.'%');
        }
        $galleries = $query->latest()->where('status', '<>', 0)->paginate(12)->withQueryString();
        $kategoris = KategoriBerita::orderBy('name')->get();
        $countAll = Galeri::count();
        $countPublish = Galeri::where('status', 3)->count();
        $countDraft = Galeri::where('status', 1)->count();
        $countUnpublish = Galeri::where('status', 2)->count();
        $countDeleted = Galeri::where('status', 0)->count();

        return view('galeri.index', [
            'title' => 'Daftar Galeri Foto',
            'breadcrumb' => [
                ['label' => 'Galeri', 'url' => route('galeri.index')],
                ['label' => 'Daftar Galeri', 'url' => ''],
            ],
            'galleries' => $galleries,
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
            'title' => 'Tambah Galeri',
            'breadcrumb' => [
                ['label' => 'Galeri', 'url' => route('galeri.index')],
                ['label' => 'Tambah Galeri', 'url' => ''],
            ],
            'fotographer' => User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 5); // role Umat
            })
                ->where('active', 1)
                ->where('status', 'Active')
                ->get(),
            'kategori' => KategoriBerita::whereIn('status', [1, 3])->get(),
        ];

        return view('galeri.create', $x);
    }

    public function searchtags(Request $request)
    {
        $keyword = $request->get('q', '');
        $tags = Galeri::searchTags($keyword);

        $results = $tags->map(function ($tag) {
            return [
                'id' => $tag->tag_id,
                'value' => $tag->tag_name,
            ];
        });

        return response()->json($results);
    }

    private function writeIptc($filename, $iptcData)
    {
        $data = '';

        foreach ($iptcData as $tag => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $tagParts = explode('#', $tag);
            $rec = (int) $tagParts[0];
            $dataset = (int) $tagParts[1];

            if (is_array($value)) {
                foreach ($value as $v) {
                    $data .= $this->iptcMakeTag($rec, $dataset, $v);
                }
            } else {
                $data .= $this->iptcMakeTag($rec, $dataset, $value);
            }
        }

        $content = iptcembed($data, $filename);
        if ($content) {
            file_put_contents($filename, $content);
        }
    }

    private function iptcMakeTag($rec, $dataset, $value)
    {
        $length = strlen($value);
        $retval = chr(0x1C).chr($rec).chr($dataset);

        if ($length < 0x8000) {
            $retval .= chr($length >> 8).chr($length & 0xFF);
        } else {
            $retval .= chr(0x80).chr(0x04).
                       chr(($length >> 24) & 0xFF).
                       chr(($length >> 16) & 0xFF).
                       chr(($length >> 8) & 0xFF).
                       chr($length & 0xFF);
        }

        return $retval.$value;
    }

    public function store(GaleriRequest $r)
    {
        DB::beginTransaction();
        try {
            $isSaveAndNew = $r->input('save_and_new') == '1';

            $data = $r->except(['_token', 'save_and_new', 'search-photo']);
            $data['created_by'] = Auth::id();
            $file = $r->file('photo_filename');
            $photoAlias = null;
            $thumbFilename = null;
            $filename = null;
            $photographerId = $r->photographer_id;
            $photographer = User::find($photographerId);
            if ($file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = Str::random(32).'.'.$extension;
                $year = date('Y');
                $month = date('m');
                $relativePath = "/image/foto_galeri/{$year}/{$month}/";
                $uploadPath = public_path($relativePath);
                $file->move($uploadPath, $filename);
                $fullPath = $uploadPath.$filename;

                $photoAlias = Str::limit(
                    ucfirst(str_replace(['-', '_'], ' ', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))),
                    100
                );

                if ($extension === 'png') {
                    $img = imagecreatefrompng($fullPath);
                    $jpgName = pathinfo($filename, PATHINFO_FILENAME).'.jpg';
                    $jpgPath = $uploadPath.$jpgName;
                    imagejpeg($img, $jpgPath, 90);
                    imagedestroy($img);
                    unlink($fullPath);
                    $filename = $jpgName;
                    $fullPath = $jpgPath;
                }
                // $imgObj->text('SPKSM', 100, 100, function ($font) {
                //     $font->file(public_path('fonts/arial.ttf'));
                //     $font->size(40);
                //     $font->color('rgba(255,255,255,0.5)');
                // });
                /*
                |--------------------------------
                | Tambahkan WATERMARK
                |--------------------------------
                */

                $imgObj = Image::read($fullPath);

                $logoPath = public_path('logo.png');

                if (file_exists($logoPath)) {

                    $logo = Image::read($logoPath);

                    $logo->resize(250, 35, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $imgObj->place($logo, 'bottom-right', 10, 10, 50);
                }

                $imgObj->save($fullPath);

                $relativeThumbPath = "/image/foto_galeri/thumbnails/{$year}/{$month}/";
                $thumbnailPath = public_path($relativeThumbPath);
                if (! file_exists($thumbnailPath)) {
                    mkdir($thumbnailPath, 0777, true);
                }
                $thumbFilename = 'thumb_'.$filename;
                $thumbFullPath = $thumbnailPath.$thumbFilename;

                Image::read($fullPath)->resize(300, 200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($thumbFullPath);

                // IPTC Metadata
                $iptc = [
                    '2#005' => $r->photo_caption, // title
                    '2#055' => Carbon::now()->toDateTimeString(),
                    '2#080' => $photographer->nama_lengkap,
                    '2#105' => $r->photo_caption,
                    '2#120' => $r->description,
                    '2#101' => 'Indonesia',
                    '2#110' => 'SPKSM',
                    '2#116' => date('Y').' SPKSM',
                ];
                $this->writeIptc($fullPath, $iptc);

                // Tambahkan ke $data
                $data['photo_folder'] = $relativePath;
                $data['photo_filename'] = $filename;
                $data['photo_alias'] = $photoAlias;
                $data['photo_thumbnail'] = $relativeThumbPath.$thumbFilename;
                $data['kategori_berita_id'] = json_encode($r->kategori_berita_id);
            }

            $hashtags = $r->keyword;
            if (! empty($hashtags) && is_string($hashtags)) {
                $decoded = json_decode($hashtags, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $hashtags = array_map(fn ($item) => $item['value'], $decoded);
                }
            }
            $data['keyword'] = implode(', ', $hashtags) ?? '';
            $slug = $r->caption;
            $data['slug'] = Str::slug($slug);
            $album = Galeri::create($data);
            // Simpan Hashtag
            Galeri::storeNewHashtag([
                // 'tag_newsid' => $r->ext_lenggang_jakarta_photo_id,
                'tag_newsid' => $album->id,
                'hashtags' => $hashtags,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);
            // $user = Auth::user();
            // DB::table('ext_lenggang_jakarta_tracking')->insert([
            //     'artikel_id' => $album->id,
            //     'artikel_status' => 'DRAFT',
            //     'artikel_process_date' => now(),
            //     'created_by' => $user->id,
            //     'notes' => $user->fullname.' telah membuat artikel baru dengan judul '.$r->title,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // $roleIds = [19, 10];

            // $users = User::whereHas('roles', function ($query) use ($roleIds) {
            //     $query->whereIn('id', $roleIds);
            // })
            //     ->get();
            // Notification::send($users, new \App\Notifications\LenggangJakartaArticleNewNotification($album));
            DB::commit();

            if ($isSaveAndNew) {
                return response()->json([
                    'success' => true,
                    'message' => 'Galeri berhasil disimpan. Silakan unggah galeri baru.',
                    'redirect' => route('galeri.create'),
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Galeri berhasil ditambahkan.',
                    'redirect' => route('galeri.index'),
                ]);
            }

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
        $galeri = Galeri::findOrFail($id);

        $x = [
            'title' => 'Edit Galeri Foto',
            'breadcrumb' => [
                ['label' => 'Galeri', 'url' => route('galeri.index')],
                ['label' => 'Edit Galeri', 'url' => ''],
            ],
            'fotographer' => User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 5);
            })
                ->where('active', 1)
                ->where('status', 'Active')
                ->get(),
            'galeri' => $galeri,
            'kategori' => KategoriBerita::whereIn('status', [1, 3])->get(),
        ];

        return view('galeri.edit', $x);
    }

    public function update(GaleriRequest $r, $id)
    {
        DB::beginTransaction();
        try {
            $galeri = Galeri::findOrFail($id);

            $data = $r->except(['_token', '_method', 'search-photo', 'save_and_new']);
            $file = $r->file('photo_filename');
            $photoAlias = $galeri->photo_alias;
            $thumbFilename = basename($galeri->photo_thumbnail);
            $filename = $galeri->photo_filename;
            $photographerId = $r->photographer_id;
            $photographer = User::find($photographerId);
            if ($r->has('kategori_berita_id')) {
                $data['kategori_berita_id'] = json_encode($r->kategori_berita_id);
            }

            if ($file) {
                // Hapus file lama jika ada
                if ($galeri->photo_filename && file_exists(public_path($galeri->photo_folder.$galeri->photo_filename))) {
                    unlink(public_path($galeri->photo_folder.$galeri->photo_filename));
                }
                if ($galeri->photo_thumbnail && file_exists(public_path($galeri->photo_thumbnail))) {
                    unlink(public_path($galeri->photo_thumbnail));
                }

                $extension = strtolower($file->getClientOriginalExtension());
                $filename = Str::random(32).'.'.$extension;
                $year = date('Y');
                $month = date('m');
                $relativePath = "/image/foto_galeri/{$year}/{$month}/";
                $uploadPath = public_path($relativePath);
                if (! file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $filename);
                $fullPath = $uploadPath.$filename;

                $photoAlias = Str::limit(
                    ucfirst(str_replace(['-', '_'], ' ', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))),
                    100
                );

                if ($extension === 'png') {
                    $img = imagecreatefrompng($fullPath);
                    $jpgName = pathinfo($filename, PATHINFO_FILENAME).'.jpg';
                    $jpgPath = $uploadPath.$jpgName;
                    imagejpeg($img, $jpgPath, 90);
                    imagedestroy($img);
                    unlink($fullPath);
                    $filename = $jpgName;
                    $fullPath = $jpgPath;
                }

                // Tambahkan watermark
                $imgObj = Image::read($fullPath);
                $logoPath = public_path('logo.png');
                if (file_exists($logoPath)) {
                    $logo = Image::read($logoPath);
                    $logo->resize(250, 35, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $imgObj->place($logo, 'bottom-right', 10, 10, 50);
                }
                $imgObj->save($fullPath);

                // Buat thumbnail
                $relativeThumbPath = "/image/foto_galeri/thumbnails/{$year}/{$month}/";
                $thumbnailPath = public_path($relativeThumbPath);
                if (! file_exists($thumbnailPath)) {
                    mkdir($thumbnailPath, 0777, true);
                }

                $thumbFilename = 'thumb_'.$filename;
                $thumbFullPath = $thumbnailPath.$thumbFilename;
                Image::read($fullPath)->resize(300, 200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($thumbFullPath);

                // IPTC Metadata
                $iptc = [
                    '2#005' => $r->photo_caption, // title
                    '2#055' => Carbon::now()->toDateTimeString(),
                    '2#080' => $photographer->nama_lengkap,
                    '2#105' => $r->photo_caption,
                    '2#120' => $r->description,
                    '2#101' => 'Indonesia',
                    '2#110' => 'SPKSM',
                    '2#116' => date('Y').' SPKSM',
                ];
                $this->writeIptc($fullPath, $iptc);

                $data['photo_folder'] = $relativePath;
                $data['photo_filename'] = $filename;
                $data['photo_alias'] = $photoAlias;
                $data['photo_thumbnail'] = $relativeThumbPath.$thumbFilename;

            }

            // Update hashtag
            $hashtags = $r->keyword;
            if (! empty($hashtags) && is_string($hashtags)) {
                $decoded = json_decode($hashtags, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $hashtags = array_map(fn ($item) => $item['value'], $decoded);
                }
            }
            $data['keyword'] = implode(', ', $hashtags) ?? '';
            $slug = $r->caption;
            $data['slug'] = Str::slug($slug);
            $data['updated_by'] = Auth::user()->id;

            $galeri->update($data);

            // Simpan Hashtag baru
            Galeri::storeNewHashtag([
                'tag_newsid' => $galeri->id,
                'hashtags' => $hashtags,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Galeri berhasil diperbarui.',
                'redirect' => route('galeri.index'),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Terjadi kesalahan pada server.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $r, $id)
    {
        try {
            $album = Galeri::findOrFail($id);

            // Soft delete album
            $album->status = 0;
            $album->publish_reason = $r->reason;
            $album->updated_by = Auth::id();
            $album->updated_at = now();
            $album->save();
            $user = Auth::user();

            return response()->json(['message' => 'Galeri di-nonaktifkan dan foto di-update.', 'redirect' => route('galeri.index')]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function trash_bin(Request $request)
    {
        $query = Galeri::query();

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $galleries = $query->latest()->where('status', 0)->paginate(12)->withQueryString();

        $countAll = Galeri::count();
        $countPublish = Galeri::where('status', 3)->count();
        $countDraft = Galeri::where('status', 1)->count();
        $countUnpublish = Galeri::where('status', 2)->count();
        $countDeleted = Galeri::where('status', 0)->count();

        return view('galeri.trash', [
            'title' => 'Daftar Galeri Foto Terhapus',
            'breadcrumb' => [
                ['label' => 'Galeri', 'url' => route('galeri.index')],
                ['label' => 'Daftar Galeri Terhapus', 'url' => ''],
            ],
            'galleries' => $galleries,
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
            $album = Galeri::find($id);
            $album->status = 1;
            $album->updated_by = Auth::id();
            $album->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('galeri.trash'),
                'message' => 'Galeri berhasil dikembalikan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'redirect' => false,
                'message' => 'Galeri gagal dikembalikan.',
            ]);
        }
    }

    public function publish($id)
    {
        $galeri = Galeri::findOrFail($id);

        $x = [
            'title' => 'Edit Galeri Foto',
            'breadcrumb' => [
                ['label' => 'Galeri', 'url' => route('galeri.index')],
                ['label' => 'Edit Galeri', 'url' => ''],
            ],
            'fotographer' => User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 5);
            })
                ->where('active', 1)
                ->where('status', 'Active')
                ->get(),
            'galeri' => $galeri,
        ];

        return view('galeri.publish', $x);
    }

    public function togglePublish(Request $r, $id)
    {
        $album = Galeri::findOrFail($id);
        if ($album->status === 3 || $album->status === 4) {
            // Unpublish
            $album->update([
                'status' => 2,
                'publish_reason' => $r->reason,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
            $message = 'Galeri berhasil di-unpublish.';
        } else {
            // Publish
            $album->update([
                'status' => 3,
                'publish_reason' => null,
                'updated_by' => Auth::id(),
                'updated_at' => null,
            ]);

            $message = 'Galeri berhasil dipublish.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'reason' => $album->publish_reason,
        ]);
    }

    public function updatePublish(Request $request, $id)
    {
        $request->validate([
            'album_status' => 'required|in:3,4',
            'publish_date' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $album = Galeri::findOrFail($id);

            $updateData = [
                'status' => $request->album_status,
                'updated_by' => Auth::id(),
            ];

            if ($request->album_status == 3) {
                $updateData['publish_date'] = now();
            } else {
                $updateData['publish_date'] = Carbon::parse($request->publish_date)->format('Y-m-d H:i');
            }

            $album->update($updateData);

            // QUEUE SCHEDULE
            if ($request->album_status == 4) {

                Queue::create([
                    'queue_name' => 'Publish Galeri Foto Album ID '.$id,
                    'queue_tablename' => 'galeri_foto',
                    'queue_processdata' => json_encode([
                        'status' => 3,
                        'where' => ['id' => $id],
                    ]),
                    'queue_processdate' => Carbon::parse($request->publish_date)->format('Y-m-d H:i'),
                    'create_by' => Auth::id(),
                    'create_at' => Carbon::now(),
                    'update_by' => null,
                    'update_at' => null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diperbarui.',
                'redirect' => route('galeri.index'),
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }
}
