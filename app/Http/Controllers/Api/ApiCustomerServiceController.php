<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomerService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApiCustomerServiceController extends Controller
{
    // ✅ LIST CS (untuk modal pilihan)
    public function list()
    {
        return CustomerService::where('is_online', true)
            ->get(['id', 'name', 'phone']);
    }

    // ✅ AUTO ASSIGN (opsional)
    public function assign()
    {
        try {
            $cs = DB::transaction(function () {

                $cs = CustomerService::where('is_online', true)
                    ->lockForUpdate()
                    ->orderBy('current_load', 'asc')
                    ->first();

                if (!$cs) return null;

                $cs->increment('current_load');

                return $cs;
            });

            if (!$cs) {
                return response()->json([
                    'message' => 'Semua CS sedang offline'
                ], 404);
            }

            return response()->json([
                'id' => $cs->id,
                'name' => $cs->name,
                'phone' => $cs->phone,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal assign CS'
            ], 500);
        }
    }
}
