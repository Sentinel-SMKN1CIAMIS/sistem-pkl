<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaprogConsentrasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign existing Kaprog to their respective konsentrasi keahlian
        // If no Kaprog exists, create one for the first konsentrasi keahlian
        
        $kaprog = User::where('role', 'kaprog')->first();
        
        if ($kaprog && !$kaprog->konsentrasi_keahlian_id) {
            // Assign to first konsentrasi keahlian (Akuntansi - id 1)
            $kaprog->update(['konsentrasi_keahlian_id' => 1]);
            echo "Kaprog '{$kaprog->name}' assigned to Konsentrasi Keahlian #1";
        }
    }
}
