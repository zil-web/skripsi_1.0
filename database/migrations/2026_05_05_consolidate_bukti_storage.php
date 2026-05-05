<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Move all pengeluaran files from 'bukti_transaksi' directory to 'bukti' directory
        // Update database records to point to new paths
        
        $records = DB::table('transaksis')
            ->where('tipe', 'pengeluaran')
            ->whereNotNull('bukti_transaksi')
            ->where('bukti_transaksi', 'like', 'bukti_transaksi/%')
            ->get();

        foreach ($records as $record) {
            $oldPath = $record->bukti_transaksi; // e.g., "bukti_transaksi/1777920074_use_case_TB_v2.png"
            
            if (Storage::disk('public')->exists($oldPath)) {
                // Extract just the filename
                $filename = basename($oldPath);
                $newPath = 'bukti/' . $filename;
                
                // Copy file to new location
                $fileContents = Storage::disk('public')->get($oldPath);
                Storage::disk('public')->put($newPath, $fileContents);
                
                // Update database record
                DB::table('transaksis')
                    ->where('id', $record->id)
                    ->update(['bukti_transaksi' => $newPath]);
                
                // Delete old file
                Storage::disk('public')->delete($oldPath);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: move files back and restore old paths
        $records = DB::table('transaksis')
            ->where('tipe', 'pengeluaran')
            ->whereNotNull('bukti_transaksi')
            ->where('bukti_transaksi', 'like', 'bukti/%')
            ->get();

        foreach ($records as $record) {
            $newPath = $record->bukti_transaksi; // e.g., "bukti/1777920074_use_case_TB_v2.png"
            
            if (Storage::disk('public')->exists($newPath)) {
                $filename = basename($newPath);
                $oldPath = 'bukti_transaksi/' . $filename;
                
                // Copy file back to old location
                $fileContents = Storage::disk('public')->get($newPath);
                Storage::disk('public')->put($oldPath, $fileContents);
                
                // Restore database record
                DB::table('transaksis')
                    ->where('id', $record->id)
                    ->update(['bukti_transaksi' => $oldPath]);
                
                // Delete new file
                Storage::disk('public')->delete($newPath);
            }
        }
    }
};
