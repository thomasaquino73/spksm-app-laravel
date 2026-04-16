<?php

use App\Http\Controllers\BeritaVideoController;
use App\Http\Controllers\DaftarKendaraanController;
use App\Http\Controllers\DaftarLingkunganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\GuestEmailVerificationController;
use App\Http\Controllers\KategoriBeritaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanSistemController;
use App\Http\Controllers\PesanAmbulanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RumahDukaController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/', [DashboardController::class, 'HalamanDepan']);
Route::get('/guest/verification', [GuestEmailVerificationController::class, 'index'])
    ->name('guest.verification');
Route::post('/guest/send-verification', [GuestEmailVerificationController::class, 'resend'])
    ->name('kirim.ulang');
Route::post('/send-verification', [GuestEmailVerificationController::class, 'sendVerification'])
    ->name('guest.verify.email.send');
Route::get('/verify-guest-email/{id}', [GuestEmailVerificationController::class, 'verify'])
    ->name('guest.verify.email');

Route::get('/verify-email/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return redirect()->route('login')->with('status', 'Email Anda berhasil diverifikasi.');
})->middleware('signed')->name('verification.verify');
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/change-password', [ProfileController::class, 'change_password'])->name('profile.changepassword');
    Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile', [ProfileController::class, 'ganti_password'])->name('ganti.password');
    Route::get('/kartu/{id}', [ProfileController::class, 'cetak'])->name('cetak.kartu');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/daftar-pesan-ambulance', [PesanAmbulanceController::class, 'index'])->name('ambulance.index');
    Route::get('/pesan-ambulance', [PesanAmbulanceController::class, 'pesan_ambulance'])->name('ambulance.pesan');
    Route::post('/pesan-ambulance/store', [PesanAmbulanceController::class, 'store'])->name('ambulance.store');
    Route::get('/data', [PesanAmbulanceController::class, 'data'])->name('ambulance.data');
    Route::put('/batal-pesan/{id}', [PesanAmbulanceController::class, 'batal_pesan'])->name('ambulance.batal');


    Route::group(['middleware' => ['role:SuperAdmin|Data Entri|Ketua']], function () {
        Route::prefix('daftar-kendaraan')->name('daftar-kendaraan.')->group(function () {
            Route::get('/', [DaftarKendaraanController::class, 'index'])->name('index');
            Route::post('/store', [DaftarKendaraanController::class, 'store'])->name('store');
            Route::get('/data', [DaftarKendaraanController::class, 'data'])->name('data');
            Route::get('/trash', [DaftarKendaraanController::class, 'trash'])->name('trash');
            Route::get('/{id}/edit', [DaftarKendaraanController::class, 'edit'])->name('edit');
            Route::delete('/{id}', [DaftarKendaraanController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/restore', [DaftarKendaraanController::class, 'restore'])->name('restore');
            Route::get('/{id}/detail', [DaftarKendaraanController::class, 'show'])->name('show');
        });
        Route::prefix('daftar-rumah-duka')->name('daftar-rumah-duka.')->group(function () {
            Route::get('/', [RumahDukaController::class, 'index'])->name('index');
            Route::post('/store', [RumahDukaController::class, 'store'])->name('store');
            Route::get('/data', [RumahDukaController::class, 'data'])->name('data');
            Route::get('/trash', [RumahDukaController::class, 'trash'])->name('trash');
            Route::get('/{id}/edit', [RumahDukaController::class, 'edit'])->name('edit');
            Route::delete('/{id}', [RumahDukaController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/restore', [RumahDukaController::class, 'restore'])->name('restore');
        });
        Route::prefix('daftar-umat')->name('daftar-umat.')->group(function () {
            Route::get('/', [LaporanController::class, 'daftar_umat'])->name('index');
            Route::get('/tabelUmat', [LaporanController::class, 'tabelUmat'])->name('tabelUmat');
            Route::get('/{id}/detail', [LaporanController::class, 'daftar_umat_detail'])->name('detail');
        });
    });

    Route::group(['middleware' => ['role:SuperAdmin|Data Entri']], function () {
        Route::get('/pengaturan-sistem', [PengaturanSistemController::class, 'index'])->name('pengaturan.sistem');
        Route::get('/pengaturan-sistem/{id}/edit', [PengaturanSistemController::class, 'edit'])->name('pengaturan.edit');
        Route::put('/pengaturan-sistem/{id}/update', [PengaturanSistemController::class, 'store'])->name('pengaturan.update');
        Route::get('/pengaturan-background', [PengaturanSistemController::class, 'login_background_index'])->name('pengaturan.background.index');
        Route::post('/pengaturan-background/store', [PengaturanSistemController::class, 'login_background_store'])->name('pengaturan.background.store');
        Route::get('/pengaturan-background/{id}/edit', [PengaturanSistemController::class, 'login_background_edit'])->name('pengaturan.background.edit');
        Route::delete('/pengaturan-background/{id}', [PengaturanSistemController::class, 'login_background_destroy'])->name('pengaturan.background.delete');


        Route::get('daftar-lingkungan/trash', [DaftarLingkunganController::class, 'trash_bin'])->name('daftar-lingkungan.trash');
        Route::PUT('daftar-lingkungan/restore/{id}', [DaftarLingkunganController::class, 'restore'])->name('daftar-lingkungan.restore');
        Route::resource('daftar-lingkungan', DaftarLingkunganController::class);
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [UserController::class, 'update'])->name('update');
            Route::get('/trash', [UserController::class, 'trash'])->name('trash');
            Route::put('/restore/{id}', [UserController::class, 'restore_user'])->name('restore');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::put('/verify-user/{id}', [UserController::class, 'verify_user'])->name('verify');
        });
        // galeri
        Route::prefix('galeri')->name('galeri.')->group(function () {
            Route::get('/', [GaleriController::class, 'index'])->name('index');
            Route::get('/create', [GaleriController::class, 'create'])->name('create');
            Route::get('/trash', [GaleriController::class, 'trash_bin'])->name('trash');
            Route::post('/store', [GaleriController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [GaleriController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [GaleriController::class, 'update'])->name('update');
            Route::delete('/{id}', [GaleriController::class, 'destroy'])->name('destroy');
            Route::get('/search-tags', [GaleriController::class, 'searchtags'])->name('searchtags');
            Route::get('/trash', [GaleriController::class, 'trash_bin'])->name('trash');
            Route::PUT('/restore/{id}', [GaleriController::class, 'restore'])->name('restore');
            Route::get('/publish/{id}', [GaleriController::class, 'publish'])->name('publish');
            Route::patch('/{id}/toggle-publish', [GaleriController::class, 'togglePublish']);
            Route::put('/{id}/update-publish', [GaleriController::class, 'updatePublish'])->name('update-publish');

        });
        Route::prefix('berita-video')->name('berita-video.')->group(function () {
            Route::get('/', [BeritaVideoController::class, 'index'])->name('index');
            Route::get('/create', [BeritaVideoController::class, 'create'])->name('create');
            Route::post('/store', [BeritaVideoController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [BeritaVideoController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [BeritaVideoController::class, 'update'])->name('update');
            Route::delete('/{id}', [BeritaVideoController::class, 'destroy'])->name('destroy');
            Route::get('/trash', [BeritaVideoController::class, 'trash_bin'])->name('trash');
            Route::PUT('/restore/{id}', [BeritaVideoController::class, 'restore'])->name('restore');
            Route::patch('/{id}/toggle-publish', [BeritaVideoController::class, 'togglePublish']);

        });

        Route::resource('kategori-berita', KategoriBeritaController::class);
        Route::PATCH('kategori-berita/publish/{id}', [KategoriBeritaController::class, 'publish']);
        Route::PATCH('kategori-berita/unpublish/{id}', [KategoriBeritaController::class, 'unpublish']);
        Route::PATCH('kategori-berita/restore/{id}', [KategoriBeritaController::class, 'restore']);

    });
});

require __DIR__.'/auth.php';
