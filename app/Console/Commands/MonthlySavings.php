<?php

namespace App\Console\Commands;

use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonthlySavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically process monthly mandatory and voluntary savings.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Perintah MonthlySavings dijalankan.');
        
        $this->info('Processing monthly savings...');

        $anggotas = Anggota::where('simpanan_wajib', '>', 0)
                            ->orWhere('simpanan_manasuka', '>', 0)
                            ->get();

        foreach ($anggotas as $anggota) {
            // Proses Simpanan Wajib
            if ($anggota->simpanan_wajib > 0) {
                $this->info("Processing mandatory savings for {$anggota->nama_lengkap}...");

                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jumlah_simpanan' => $anggota->simpanan_wajib,
                    'jenis_simpanan' => 'wajib',
                    'tanggal_simpanan' => now(),
                ]);

                // Perbarui saldo anggota
                $anggota->increment('saldo_simpanan', $anggota->simpanan_wajib);
            }

            // Proses Simpanan Manasuka
            if ($anggota->simpanan_manasuka > 0) {
                $this->info("Processing voluntary savings for {$anggota->nama_lengkap}...");

                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jumlah_simpanan' => $anggota->simpanan_manasuka,
                    'jenis_simpanan' => 'manasuka',
                    'tanggal_simpanan' => now(),
                ]);

                // Perbarui saldo anggota
                $anggota->increment('saldo_simpanan', $anggota->simpanan_manasuka);
            }
        }

        $this->info('Monthly savings processing complete!');
        return 0;
    }
}