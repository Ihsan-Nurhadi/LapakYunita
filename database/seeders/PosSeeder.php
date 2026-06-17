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
        Product::insert([
            ['name'=>'Nasi Goreng Spesial','price'=>25000,'modal'=>15000,'category'=>'Makanan','stock'=>50,'image'=>'🍳','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Es Teh Manis','price'=>5000,'modal'=>2000,'category'=>'Minuman','stock'=>100,'image'=>'🧊','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Ayam Bakar','price'=>30000,'modal'=>18000,'category'=>'Makanan','stock'=>30,'image'=>'🍗','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Keripik Singkong','price'=>8000,'modal'=>4000,'category'=>'Snack','stock'=>60,'image'=>'🥔','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Jus Alpukat','price'=>15000,'modal'=>8000,'category'=>'Minuman','stock'=>40,'image'=>'🥑','created_at'=>now(),'updated_at'=>now()],
        ]);

        $o = Outlet::create(['name'=>'Outlet Pusat','phone'=>'021-1234567','address'=>'Jl. Sudirman No.1','kelurahan'=>'Karet','kode_pos'=>'12920']);

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
