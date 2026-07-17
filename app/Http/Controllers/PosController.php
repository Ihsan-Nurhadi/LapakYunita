<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Employee;
use App\Models\Outlet;
use App\Models\PosTransaction;
use App\Models\TransactionItem;
use App\Models\Customer;
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
        $query = Product::with('outlets')->orderBy('name');
        
        $employeeId = session('employee_id');
        $employee = null;
        if ($employeeId) {
            $employee = Employee::find($employeeId);
            if ($employee && strtolower($employee->access ?: $employee->role) !== 'admin') {
                if ($employee->outlet_id) {
                    $query->whereHas('outlets', function($q) use ($employee) {
                        $q->where('outlets.id', $employee->outlet_id);
                    });
                } else {
                    $query->whereDoesntHave('outlets');
                }
            }
        }
        
        return $query->get()->map(function($product) use ($employee) {
            $isAdmin = $employee && strtolower($employee->access ?: $employee->role) === 'admin';
            
            if ($isAdmin) {
                $product->stocks = $product->outlets->map(function($o) {
                    return [
                        'outlet_id' => $o->id,
                        'outlet_name' => $o->name,
                        'stock' => $o->pivot->stock,
                        'discount' => $o->pivot->discount
                    ];
                });
                $product->stock = $product->outlets->sum('pivot.stock');
            } else {
                $outletId = $employee ? $employee->outlet_id : null;
                if ($outletId) {
                    $pivot = $product->outlets->firstWhere('id', $outletId);
                    $product->stock = $pivot ? $pivot->pivot->stock : 0;
                    $product->discount = $pivot ? $pivot->pivot->discount : 0;
                    $product->outlet_id = $outletId;
                } else {
                    $product->stock = 0;
                    $product->discount = 0;
                    $product->outlet_id = null;
                }
            }
            return $product;
        });
    }

    public function storeProduct(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'modal' => 'nullable|integer',
            'category' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'outlet_stocks' => 'nullable|string',
        ]);

        if ($r->hasFile('image')) {
            $data['image'] = $r->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        $outletStocks = json_decode($r->input('outlet_stocks'), true);
        if (is_array($outletStocks)) {
            $syncData = [];
            foreach ($outletStocks as $os) {
                $syncData[$os['outlet_id']] = [
                    'stock' => $os['stock'],
                    'discount' => $os['discount'] ?? 0
                ];
            }
            $product->outlets()->sync($syncData);
        }

        return $product;
    }

    public function updateProduct(Request $r, Product $product)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'modal' => 'nullable|integer',
            'category' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'outlet_stocks' => 'nullable|string',
        ]);

        if ($r->hasFile('image')) {
            $data['image'] = $r->file('image')->store('products', 'public');
        } else {
            unset($data['image']);
        }

        $product->update($data);

        $outletStocks = json_decode($r->input('outlet_stocks'), true);
        if (is_array($outletStocks)) {
            $syncData = [];
            foreach ($outletStocks as $os) {
                $syncData[$os['outlet_id']] = [
                    'stock' => $os['stock'],
                    'discount' => $os['discount'] ?? 0
                ];
            }
            $product->outlets()->sync($syncData);
        }

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
        return Outlet::with('employees')->orderBy('name')->get();
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

    public function getDiscountRule()
    {
        return \App\Models\DiscountRule::first() ?: response()->json(null);
    }

    public function saveDiscountRule(Request $r)
    {
        $data = $r->validate([
            'min_purchase' => 'required|integer|min:0',
            'discount_percent' => 'required|integer|min:0|max:100',
        ]);

        $rule = \App\Models\DiscountRule::first();
        if ($rule) {
            $rule->update($data);
        } else {
            $rule = \App\Models\DiscountRule::create($data);
        }
        return $rule;
    }

    public function customers()
    {
        return Customer::withSum(['transactions' => function ($query) {
            $query->where('is_draft', false);
        }], 'total')
        ->withCount(['transactions' => function ($query) {
            $query->where('is_draft', false);
        }])
        ->orderBy('name')->get();
    }

    public function storeCustomer(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        return Customer::create($data);
    }
    public function transactions()
    {
        $query = PosTransaction::with(['items', 'customer'])->where('is_draft', false);
        
        $employeeId = session('employee_id');
        if ($employeeId) {
            $employee = Employee::with('outlet')->find($employeeId);
            if ($employee && strtolower($employee->access ?: $employee->role) !== 'admin') {
                if ($employee->outlet) {
                    $query->where('outlet', $employee->outlet->name);
                } else {
                    $query->where('outlet', 'Outlet Pusat');
                }
            }
        }
        
        return $query->orderByDesc('id')->get();
    }

    public function drafts()
    {
        $query = PosTransaction::with(['items', 'customer'])->where('is_draft', true);
        $employeeId = session('employee_id');
        if ($employeeId) {
            $employee = Employee::with('outlet')->find($employeeId);
            if ($employee && strtolower($employee->access ?: $employee->role) !== 'admin') {
                if ($employee->outlet) {
                    $query->where('outlet', $employee->outlet->name);
                } else {
                    $query->where('outlet', 'Outlet Pusat');
                }
            }
        }
        return $query->orderByDesc('id')->get();
    }

    public function deleteDraft(PosTransaction $draft)
    {
        if (!$draft->is_draft) {
            return response()->json(['message' => 'Hanya draft yang dapat dihapus.'], 422);
        }

        $draft->items()->delete();
        $draft->delete();

        return response()->noContent();
    }

    public function storeDraft(Request $r)
    {
        $data = $r->validate([
            'items' => 'required|array',
            'total' => 'required|integer',
            'draft_id' => 'nullable|integer|exists:pos_transactions,id',
            'cashier' => 'nullable|string',
            'outlet' => 'nullable|string',
            'customer_id' => 'required|integer|exists:customers,id',
            'global_discount_amount' => 'nullable|integer|min:0'
        ]);

        $employee = null;
        if (session('employee_id')) {
            $employee = Employee::with('outlet')->find(session('employee_id'));
        }

        $cashierName = $employee ? $employee->name : ($data['cashier'] ?? 'Kasir');
        $outletName = ($employee && $employee->outlet) ? $employee->outlet->name : ($data['outlet'] ?? 'Outlet Pusat');

        if (!empty($data['draft_id'])) {
            $trx = PosTransaction::where('id', $data['draft_id'])->where('is_draft', true)->firstOrFail();
            $trx->update([
                'total' => $data['total'],
                'paid' => 0,
                'change' => 0,
                'payment_method' => 'draft',
                'cashier' => $cashierName,
                'outlet' => $outletName,
                'customer_id' => $data['customer_id'] ?? null,
                'global_discount_amount' => $data['global_discount_amount'] ?? 0,
            ]);
            $trx->items()->delete();
        } else {
            $trx = PosTransaction::create([
                'trx_id' => 'DRAFT'.substr((string)time(), -6).mt_rand(100,999),
                'total' => $data['total'],
                'paid' => 0,
                'change' => 0,
                'payment_method' => 'draft',
                'cashier' => $cashierName,
                'outlet' => $outletName,
                'is_draft' => true,
                'customer_id' => $data['customer_id'] ?? null,
                'global_discount_amount' => $data['global_discount_amount'] ?? 0,
            ]);
        }

        foreach ($data['items'] as $it) {
            $product = Product::find($it['id']);
            TransactionItem::create([
                'transaction_id' => $trx->id,
                'product_id' => $product?->id,
                'name' => $it['name'] ?? ($product?->name ?? 'Item'),
                'qty' => $it['qty'] ?? 1,
                'price' => $it['price'] ?? 0,
            ]);
        }

        return $trx->load('items');
    }

    public function storeTransaction(Request $r)
    {
        $data = $r->validate([
            'items' => 'required|array',
            'total' => 'required|integer',
            'paid' => 'required|integer',
            'payment_method' => 'required|string|in:cash,qr,tf,offline_cash,offline_qr,online_qr,offline_tf,online_tf',
            'cashier' => 'nullable|string',
            'outlet' => 'nullable|string',
            'draft_id' => 'nullable|integer|exists:pos_transactions,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'global_discount_amount' => 'nullable|integer|min:0',
        ]);

        $employee = null;
        if (session('employee_id')) {
            $employee = Employee::with('outlet')->find(session('employee_id'));
        }

        $cashierName = $employee ? $employee->name : ($data['cashier'] ?? 'Kasir');
        $outletName = ($employee && $employee->outlet) ? $employee->outlet->name : ($data['outlet'] ?? 'Outlet Pusat');

        if (!empty($data['draft_id'])) {
            $trx = PosTransaction::where('id', $data['draft_id'])->where('is_draft', true)->firstOrFail();
            $trx->update([
                'total' => $data['total'],
                'paid' => $data['paid'],
                'change' => max(0, $data['paid'] - $data['total']),
                'payment_method' => $data['payment_method'],
                'cashier' => $cashierName,
                'outlet' => $outletName,
                'is_draft' => false,
                'customer_id' => $data['customer_id'] ?? null,
                'global_discount_amount' => $data['global_discount_amount'] ?? 0,
            ]);
            $trx->items()->delete();
        } else {
            $trx = PosTransaction::create([
                'trx_id' => 'TRX'.substr((string)time(), -6).mt_rand(100,999),
                'total' => $data['total'],
                'paid' => $data['paid'],
                'change' => max(0, $data['paid'] - $data['total']),
                'payment_method' => $data['payment_method'],
                'cashier' => $cashierName,
                'outlet' => $outletName,
                'is_draft' => false,
                'customer_id' => $data['customer_id'] ?? null,
                'global_discount_amount' => $data['global_discount_amount'] ?? 0,
            ]);
        }

        $outlet = null;
        if ($employee && $employee->outlet_id) {
            $outlet = $employee->outlet;
        } else {
            $outlet = Outlet::where('name', $outletName)->first();
        }

        foreach ($data['items'] as $it) {
            $product = Product::find($it['id']);
            TransactionItem::create([
                'transaction_id' => $trx->id,
                'product_id' => $product?->id,
                'name' => $it['name'] ?? ($product?->name ?? 'Item'),
                'qty' => $it['qty'] ?? 1,
                'price' => $it['price'] ?? 0,
            ]);
            if ($product && $outlet) {
                $pivot = $product->outlets()->where('outlet_id', $outlet->id)->first();
                if ($pivot) {
                    $newStock = max(0, $pivot->pivot->stock - ($it['qty'] ?? 1));
                    $product->outlets()->updateExistingPivot($outlet->id, ['stock' => $newStock]);
                }
            }
        }

        return $trx->load('items', 'customer');
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

    public function changePin(Request $r)
    {
        $data = $r->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'old_pin' => 'required|string|size:4|regex:/^[0-9]+$/',
            'new_pin' => 'required|string|size:4|regex:/^[0-9]+$/',
        ]);
        
        $employee = Employee::find($data['employee_id']);
        if (!$employee || !\Illuminate\Support\Facades\Hash::check($data['old_pin'], $employee->pin)) {
            return response()->json(['message' => 'PIN lama yang Anda masukkan salah.'], 422);
        }
        
        $employee->pin = $data['new_pin'];
        $employee->save();
        
        return response()->json(['message' => 'PIN berhasil diperbarui.']);
    }
}
