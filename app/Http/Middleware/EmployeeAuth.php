<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeAuth
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $employeeId = session('employee_id');

        if (!$employeeId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $employee = Employee::find($employeeId);

        if (!$employee) {
            session()->forget('employee_id');
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Share the authenticated employee with the request
        $request->merge(['current_employee' => $employee]);

        if (!empty($roles)) {
            $access = strtolower($employee->access ?: $employee->role);
            if (!in_array($access, $roles) && $access !== 'admin') {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }
        }

        return $next($request);
    }
}
