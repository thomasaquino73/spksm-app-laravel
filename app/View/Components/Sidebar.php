<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $links;

    public function __construct()
    {
        $this->links = [
            [
                'type' => 'single',
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'ti-home',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri', 'Ketua', 'Anggota'],

            ],
            [
                'type' => 'section',
                'label' => 'MASTER DATA',
                'roles' => ['SuperAdmin', 'Data Entri', 'Ketua'],
            ],
            [
                'type' => 'single',
                'name' => 'Daftar Lingkungan',
                'route' => 'daftar-lingkungan.index',
                'icon' => 'ti-map',
                'pattern' => 'daftar-lingkungan.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'single',
                'name' => 'Kategori Berita',
                'route' => 'kategori-berita.index',
                'icon' => 'ti-list',
                'pattern' => 'kategori-berita.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'single',
                'name' => 'Daftar Kendaraan',
                'route' => 'daftar-kendaraan.index',
                'icon' => 'ti-car',
                'pattern' => 'daftar-kendaraan.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri', 'Ketua'],
            ],
            [
                'type' => 'single',
                'name' => 'Daftar Rumah Duka',
                'route' => 'daftar-rumah-duka.index',
                'icon' => 'ti-building-arch',
                'pattern' => 'daftar-rumah-duka.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri', 'Ketua'],
            ],
            [
                'type' => 'section',
                'label' => 'MEDIA BERITA',
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'dropdown',
                'name' => 'Daftar Berita',
                'icon' => 'ti-news',
                'roles' => ['SuperAdmin', 'Data Entri'],
                'children' => [

                    [
                        'name' => 'Album',
                        'route' => 'daftar-kendaraan.index',
                        'pattern' => 'daftar-kendaraan.*',
                        'roles' => ['SuperAdmin', 'Data Entri'],
                    ],

                    [
                        'name' => 'Daftar Artikel',
                        'route' => 'daftar-kendaraan.index',
                        'pattern' => 'daftar-kendaraan.*',
                        'roles' => ['SuperAdmin', 'Data Entri'],
                    ],

                ]
            ],
            [
                'type' => 'single',
                'name' => 'Berita Video',
                'route' => 'berita-video.index',
                'icon' => 'ti-video',
                'pattern' => 'berita-video.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'single',
                'name' => 'Gallery',
                'route' => 'galeri.index',
                'icon' => 'ti-layout-collage',
                'pattern' => 'galeri.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'section',
                'label' => 'Pelayanan',
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'single',
                'name' => 'Kesehatan',
                'route' => 'user.index',
                'icon' => 'ti-heartbeat',
                'pattern' => 'user.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'single',
                'name' => 'Ambulance',
                'route' => 'ambulance.pesan',
                'icon' => 'ti-ambulance',
                'pattern' => 'ambulance.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri', 'Ketua', 'Anggota', 'Umat'],
            ],
            [
                'type' => 'section',
                'label' => 'Laporan',
                'roles' => ['SuperAdmin', 'Ketua'],
            ],
            [
                'type' => 'single',
                'name' => 'Daftar Umat',
                'route' => 'daftar-umat.index',
                'icon' => 'ti-users',
                'pattern' => 'daftar-umat.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri', 'Ketua', 'Anggota'],
            ],
            [
                'type' => 'single',
                'name' => 'Daftar Permintaan',
                'route' => 'daftar-lingkungan.index',
                'icon' => 'ti-users',
                'pattern' => 'daftar-lingkungan.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'section',
                'label' => 'pengaturan',
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'single',
                'name' => 'Pengguna',
                'route' => 'user.index',
                'icon' => 'ti-user-cog',
                'pattern' => 'user.*',
                'active' => true,
                'roles' => ['SuperAdmin', 'Data Entri'],
            ],
            [
                'type' => 'single',
                'name' => 'Sistem Aplikasi',
                'route' => 'pengaturan.sistem',
                'icon' => 'ti-database',
                'pattern' => 'pengaturan.*',
                'active' => true,
                'roles' => ['SuperAdmin'],
            ],

        ];
    }

    public function render()
    {
        return view('components.sidebar');
    }
}
