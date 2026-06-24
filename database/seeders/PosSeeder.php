<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Outlet;

class PosSeeder extends Seeder
{
    public function run(): void
    {
        $o = Outlet::create(['name'=>'Outlet Pusat','phone'=>'021-1234567','address'=>'Jl. Sudirman No.1','kelurahan'=>'Karet','kode_pos'=>'12920']);

        $p1 = Product::create(['name'=>'Nasi Goreng Spesial','price'=>25000,'modal'=>15000,'category'=>'Makanan','image'=>'🍳']);
        $p1->outlets()->attach($o->id, ['stock'=>50]);

        $p2 = Product::create(['name'=>'Es Teh Manis','price'=>5000,'modal'=>2000,'category'=>'Minuman','image'=>'🧊']);
        $p2->outlets()->attach($o->id, ['stock'=>100]);

        $p3 = Product::create(['name'=>'Ayam Bakar','price'=>30000,'modal'=>18000,'category'=>'Makanan','image'=>'🍗']);
        $p3->outlets()->attach($o->id, ['stock'=>30]);

        $p4 = Product::create(['name'=>'Keripik Singkong','price'=>8000,'modal'=>4000,'category'=>'Snack','image'=>'🥔']);
        $p4->outlets()->attach($o->id, ['stock'=>60]);

        $p5 = Product::create(['name'=>'Jus Alpukat','price'=>15000,'modal'=>8000,'category'=>'Minuman','image'=>'🥑']);
        $p5->outlets()->attach($o->id, ['stock'=>40]);

        Employee::create([
            'name' => 'Admin Lapak',
            'phone' => '081111111111',
            'role' => 'Admin',
            'email' => 'admin@email.com',
            'pin' => '9999',
            'access' => 'admin',
            'outlet_id' => $o->id,
            'photo' => null,
        ]);

        Employee::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'role' => 'Supervisor',
            'email' => 'budi@email.com',
            'pin' => '1234',
            'access' => 'supervisor',
            'outlet_id' => $o->id,
            'photo' => 'BS',
        ]);

        Employee::create([
            'name' => 'Siti Rahayu',
            'phone' => '082345678901',
            'role' => 'Kasir',
            'email' => 'siti@email.com',
            'pin' => '5678',
            'access' => 'kasir',
            'outlet_id' => $o->id,
            'photo' => 'SR',
        ]);
    }
}
