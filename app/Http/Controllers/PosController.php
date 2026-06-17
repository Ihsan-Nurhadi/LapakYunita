<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Employee;
use App\Models\Outlet;
use App\Models\PosTransaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PosController extends Controller
{
    public function index()
    {
        return view('pos');
    }

    public function products()
    {
        return Product::orderBy('name')->get();
    }

    public function storeProduct(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'modal' => 'nullable|integer',
            'category' => 'nullable|string',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($r->hasFile('image')) {
            $data['image'] = $r->file('image')->store('products', 'public');
        }

        return Product::create($data);
    }

    public function updateProduct(Request $r, Product $product)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'modal' => 'nullable|integer',
            'category' => 'nullable|string',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($r->hasFile('image')) {
            $data['image'] = $r->file('image')->store('products', 'public');
        } else {
            unset($data['image']);
        }

        $product->update($data);
        return $product;
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    public function employees()
    {
        return Employee::with('outlet')->orderBy('name')->get();
    }

    public function outlets()
    {
        return Outlet::orderBy('name')->get();
    }

    public function storeEmployee(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'role' => 'required|string',
            'email' => 'nullable|email',
            'outlet_id' => 'nullable|integer|exists:outlets,id',
            'photo' => 'nullable|image|max:2048',
            'pin' => 'required|string|size:4|regex:/^[0-9]+$/',
        ]);

        $data['access'] = strtolower($data['role']);

        if ($r->hasFile('photo')) {
            $data['photo'] = $r->file('photo')->store('employees', 'public');
        }

        return Employee::create($data);
    }

    public function updateEmployee(Request $r, Employee $employee)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'role' => 'required|string',
            'email' => 'nullable|email',
            'outlet_id' => 'nullable|integer|exists:outlets,id',
            'photo' => 'nullable|image|max:2048',
            'pin' => 'nullable|string|size:4|regex:/^[0-9]+$/',
        ]);

        $data['access'] = strtolower($data['role']);

        if ($r->hasFile('photo')) {
            $data['photo'] = $r->file('photo')->store('employees', 'public');
        } else {
            unset($data['photo']);
        }

        if (empty($data['pin'])) {
            unset($data['pin']);
        }

        $employee->update($data);
        return $employee;
    }

    public function deleteEmployee(Employee $employee)
    {
        $employee->delete();
        return response()->noContent();
    }

    public function storeOutlet(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kode_pos' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($r->hasFile('image')) {
            $data['image'] = $r->file('image')->store('outlets', 'public');
        }

        return Outlet::create($data);
    }

    public function updateOutlet(Request $r, Outlet $outlet)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kode_pos' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($r->hasFile('image')) {
            $data['image'] = $r->file('image')->store('outlets', 'public');
        } else {
            unset($data['image']);
        }

        $outlet->update($data);
        return $outlet;
    }

    public function deleteOutlet(Outlet $outlet)
    {
        $outlet->delete();
        return response()->noContent();
    }

    public function transactions()
    {
        return PosTransaction::with('items')->orderByDesc('id')->limit(100)->get();
    }

    public function storeTransaction(Request $r)
    {
        $data = $r->validate(['items'=>'required|array','total'=>'required|integer','paid'=>'required|integer','cashier'=>'nullable','outlet'=>'nullable']);

        $employee = null;
        if (session('employee_id')) {
            $employee = Employee::with('outlet')->find(session('employee_id'));
        }

        $cashierName = $employee ? $employee->name : ($data['cashier'] ?? 'Kasir');
        $outletName = ($employee && $employee->outlet) ? $employee->outlet->name : ($data['outlet'] ?? 'Outlet Pusat');

        $trx = PosTransaction::create([
            'trx_id' => 'TRX'.substr((string)time(), -6).mt_rand(100,999),
            'total' => $data['total'],
            'paid' => $data['paid'],
            'change' => max(0, $data['paid'] - $data['total']),
            'cashier' => $cashierName,
            'outlet' => $outletName,
        ]);

        foreach ($data['items'] as $it) {
            $product = Product::find($it['id']);
            TransactionItem::create([
                'transaction_id' => $trx->id,
                'product_id' => $product?->id,
                'name' => $it['name'] ?? ($product?->name ?? 'Item'),
                'qty' => $it['qty'] ?? 1,
                'price' => $it['price'] ?? 0,
            ]);
            if ($product) {
                $product->decrement('stock', $it['qty'] ?? 1);
            }
        }

        return $trx->load('items');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'pin' => 'required|string|size:4|regex:/^[0-9]+$/',
        ]);

        $employee = Employee::find($data['employee_id']);

        if (!$employee || !\Illuminate\Support\Facades\Hash::check($data['pin'], $employee->pin)) {
            return response()->json(['message' => 'PIN yang Anda masukkan salah.'], 422);
        }

        session(['employee_id' => $employee->id]);

        return response()->json([
            'message' => 'Login berhasil',
            'employee' => $employee->load('outlet')
        ]);
    }

    public function logout()
    {
        session()->forget('employee_id');
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me()
    {
        $employeeId = session('employee_id');
        if (!$employeeId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $employee = Employee::with('outlet')->find($employeeId);
        if (!$employee) {
            session()->forget('employee_id');
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json($employee);
    }
}
