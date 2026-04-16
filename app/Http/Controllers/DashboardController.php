<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permintaan;
use App\Models\Mesin;
use App\Models\ProsesMfg;
use App\Models\Schedule;
use App\Models\PartList;
use App\Models\ProductionTracking;
use App\Models\User;

class DashboardController extends Controller
{
    public function admin()
    {
        $user = auth()->user();

        // ── TRACKING MESIN ────────────────────────────────────────────────
        $mesins = Mesin::with([
            'permintaan',
            'permintaan.partLists',
            'schedules',
            'productionTracking',           // relasi baru ke tabel tracking
        ])
        ->orderBy('nama_mesin')
        ->get()
        ->map(function ($mesin) {

            $permintaan = $mesin->permintaan;
            $partLists  = $permintaan?->partLists ?? collect();

            // ── Ambil atau buat record tracking ──────────────────────────
            $tracking = $mesin->productionTracking;

            if (!$tracking && $permintaan) {
                // Auto-create record tracking saat pertama kali load
                $tracking = ProductionTracking::create([
                    'mesin_id'       => $mesin->mesin_id,
                    'permintaan_id'  => $permintaan->permintaan_id,
                    'tanggal_po'     => $permintaan->tanggal_permintaan,
                    'created_by'     => auth()->id(),
                    'updated_by'     => auth()->id(),
                ]);
                // Sinkron data PO dari part list
                $tracking->load('permintaan.partLists');
                $tracking->syncFromPartLists();
                $tracking->refresh();
            }

            // ── PO Mekanik & Elektrikal (dari tabel tracking) ────────────
            $po_mekanik_count    = $tracking?->po_mekanik_count    ?? 0;
            $po_elektrikal_count = $tracking?->po_elektrikal_count ?? 0;
            $po_mekanik          = $tracking?->po_mekanik_preview   ?? '-';
            $po_elektrikal       = $tracking?->po_elektrikal_preview ?? '-';

            // ── Assy (dari tabel tracking atau fallback ke schedules) ─────
            $assy_mekanik    = $tracking?->tanggal_assy_mekanik;
            $assy_elektrikal = $tracking?->tanggal_assy_elektrikal;

            // Fallback: cari dari schedules jika belum diisi manual
            if (!$assy_mekanik || !$assy_elektrikal) {
                $schedules = $mesin->schedules ?? collect();

                if (!$assy_mekanik) {
                    $assy_mekanik = $schedules->filter(fn($s) =>
                        $s->tanggal_actual &&
                        (stripos($s->proses_nama ?? '', 'mekanik') !== false ||
                         stripos($s->proses_nama ?? '', 'assy')    !== false)
                    )->sortByDesc('tanggal_actual')->first()?->tanggal_actual;
                }

                if (!$assy_elektrikal) {
                    $assy_elektrikal = $schedules->filter(fn($s) =>
                        $s->tanggal_actual &&
                        (stripos($s->proses_nama ?? '', 'elektrik') !== false ||
                         stripos($s->proses_nama ?? '', 'electric') !== false)
                    )->sortByDesc('tanggal_actual')->first()?->tanggal_actual;
                }
            }

            // ── Schedules untuk progress card ─────────────────────────────
            $schedules = $mesin->schedules ?? collect();

            return [
                'mesin_id'             => $mesin->mesin_id,
                'nama_mesin'           => $mesin->nama_mesin,
                'kode_mesin'           => $mesin->kode_mesin,
                'status'               => $mesin->status,
                'jenis_proses'         => $mesin->jenis_proses,
                'lokasi'               => $mesin->lokasi,
                'nomor_permintaan'     => $permintaan?->nomor_permintaan ?? '-',
                'permintaan_id' => $permintaan?->permintaan_id,
                'jenis_produk'         => $permintaan?->jenis_produk     ?? '-',
                'tanggal_po'           => $tracking?->tanggal_po
                                            ?? $permintaan?->tanggal_permintaan,

                // PO Mekanik
                'po_mekanik'           => $po_mekanik,
                'po_mekanik_count'     => $po_mekanik_count,
                'po_mekanik_status'    => $tracking?->po_mekanik_status ?? 'belum_po',

                // PO Elektrikal
                'po_elektrikal'        => $po_elektrikal,
                'po_elektrikal_count'  => $po_elektrikal_count,
                'po_elektrikal_status' => $tracking?->po_elektrikal_status ?? 'belum_po',

                // Assy
                'assy_mekanik'         => $assy_mekanik,
                'assy_mekanik_status'  => $tracking?->assy_mekanik_status  ?? 'belum',
                'assy_elektrikal'      => $assy_elektrikal,
                'assy_elektrikal_status' => $tracking?->assy_elektrikal_status ?? 'belum',

                // Trial
                'tanggal_trial'        => $tracking?->tanggal_trial,
                'trial_status'         => $tracking?->trial_status ?? 'belum',

                // Delivery
                'tanggal_delivery'     => $tracking?->tanggal_delivery,
                'delivery_status'      => $tracking?->delivery_status ?? 'belum',

                // Tracking ID (dipakai untuk AJAX update)
                'tracking_id'          => $tracking?->tracking_id,

                // Progress card
                'total_activity' => $schedules->count(),
                'plan_count'     => $schedules->where('status', 'planned')->count(),
                'act_count'      => $schedules->where('status', 'in_progress')->count(),
                'done_count'     => $schedules->where('status', 'completed')->count(),
            ];
        });

        // ── DATA DASHBOARD ────────────────────────────────────────────────
        $data = [
            'total_permintaan'      => Permintaan::count(),
            'permintaan_pending'    => Permintaan::where('status', 'submitted')->count(),
            'permintaan_inprogress' => Permintaan::where('status', 'approved')->count(),

            'mesin_aktif'       => Mesin::where('status', 'active')->count(),
            'mesin_maintenance' => Mesin::where('status', 'maintenance')->count(),

            'total_activity'   => ProsesMfg::count(),
            'activity_pending' => ProsesMfg::where('status', 'pending')->count(),
            'activity_running' => ProsesMfg::where('status', 'running')->count(),
            'activity_done'    => ProsesMfg::where('status', 'completed')->count(),

            'permintaan_by_status' => Permintaan::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),

            'permintaan_by_month' => Permintaan::selectRaw('DATE_FORMAT(tanggal_permintaan, "%Y-%m") as month, count(*) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->take(6)
                ->pluck('total', 'month')
                ->toArray(),

            'tabel_gabungan' => ProsesMfg::with(['mesin.permintaan'])
                ->latest()
                ->take(10)
                ->get(),

            'mesins' => $mesins,
        ];

        return view('admin.dashboard', compact('data', 'user'));
    }

    // ── UPDATE TRACKING (AJAX PATCH) ─────────────────────────────────────
    public function updateTracking(Request $request, $tracking_id)
    {
        $request->validate([
            // Tanggal
            'tanggal_po'              => 'nullable|date',
            'tanggal_assy_mekanik'    => 'nullable|date',
            'tanggal_assy_elektrikal' => 'nullable|date',
            'tanggal_trial'           => 'nullable|date',
            'tanggal_trial_actual'    => 'nullable|date',
            'tanggal_delivery'        => 'nullable|date',
            'tanggal_delivery_actual' => 'nullable|date',

            // Status
            'po_mekanik_status'    => 'nullable|in:belum_po,proses_po,selesai_po',
            'po_elektrikal_status' => 'nullable|in:belum_po,proses_po,selesai_po',
            'assy_mekanik_status'  => 'nullable|in:belum,proses,selesai',
            'assy_elektrikal_status' => 'nullable|in:belum,proses,selesai',
            'trial_status'         => 'nullable|in:belum,dijadwalkan,selesai',
            'delivery_status'      => 'nullable|in:belum,dijadwalkan,terkirim',

            // Catatan
            'assy_mekanik_catatan'    => 'nullable|string|max:1000',
            'assy_elektrikal_catatan' => 'nullable|string|max:1000',
            'trial_catatan'           => 'nullable|string|max:1000',
            'delivery_catatan'        => 'nullable|string|max:1000',
        ]);

        $tracking = ProductionTracking::findOrFail($tracking_id);

        $tracking->update(array_merge(
            $request->only([
                'tanggal_po',
                'tanggal_assy_mekanik', 'assy_mekanik_status', 'assy_mekanik_catatan',
                'tanggal_assy_elektrikal', 'assy_elektrikal_status', 'assy_elektrikal_catatan',
                'tanggal_trial', 'tanggal_trial_actual', 'trial_status', 'trial_catatan',
                'tanggal_delivery', 'tanggal_delivery_actual', 'delivery_status', 'delivery_catatan',
                'po_mekanik_status', 'po_elektrikal_status',
            ]),
            ['updated_by' => auth()->id()]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Tracking updated',
            'data'    => $tracking->fresh(),
        ]);
    }

    // ── SINKRON PO DARI PART LIST (dipanggil setelah part ditambah/hapus) ─
    public function syncPo($tracking_id)
    {
        $tracking = ProductionTracking::with('permintaan.partLists')
            ->findOrFail($tracking_id);

        $tracking->syncFromPartLists();

        return response()->json([
            'success'             => true,
            'po_mekanik_count'    => $tracking->po_mekanik_count,
            'po_mekanik_preview'  => $tracking->po_mekanik_preview,
            'po_elektrikal_count' => $tracking->po_elektrikal_count,
            'po_elektrikal_preview' => $tracking->po_elektrikal_preview,
        ]);
    }

    // ── OPERATOR ──────────────────────────────────────────────────────────
    public function operator()
    {
        $user = auth()->user();

        $tugas_hari_ini = Schedule::where('pic', $user->user_id)
            ->whereDate('tanggal_plan', today())
            ->whereIn('status', ['planned', 'in_progress'])
            ->with(['mesin', 'partList'])
            ->get();

        $proses_aktif = ProsesMfg::where('operator_id', $user->user_id)
            ->where('status', 'running')
            ->with(['partList', 'mesin'])
            ->get();

        return view('operator.dashboard', compact('user', 'tugas_hari_ini', 'proses_aktif'));
    }

    // ── ENGINEER ──────────────────────────────────────────────────────────
    public function engineer()
    {
        $user = auth()->user();

        $permintaan_baru = Permintaan::where('status', 'submitted')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $schedules = Schedule::with(['mesin', 'part'])
            ->orderBy('tanggal_plan', 'asc')
            ->take(10)
            ->get();

        $mesin_maintenance = Mesin::where('status', 'maintenance')->get();

        return view('engineer.dashboard', compact('user', 'permintaan_baru', 'schedules', 'mesin_maintenance'));
    }
}