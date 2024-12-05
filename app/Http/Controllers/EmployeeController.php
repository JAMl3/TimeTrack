<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view entitlements');
        $this->middleware('can:create entitlements')->only(['create', 'store']);
        $this->middleware('can:update entitlements')->only(['edit', 'update']);
        $this->middleware('can:delete entitlements')->only('destroy');
    }

    public function index()
    {
        $employees = Employee::with(['user', 'department'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $managers = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'hr', 'manager']);
            })
            ->get();
        return view('employees.create', compact('departments', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'employee_number' => 'required|string|unique:employees',
            'pin_code' => 'required|string|min:4|max:8',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt('password'), // Set a default password
        ]);

        // Assign employee role
        $employeeRole = Role::where('name', 'employee')->first();
        $user->roles()->attach($employeeRole);

        // Create employee
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_number' => $validated['employee_number'],
            'department_id' => $validated['department_id'],
            'position' => $validated['position'],
            'manager_id' => $validated['manager_id'],
            'start_date' => $validated['start_date'],
            'pin_code' => $validated['pin_code'],
            'pin_changed' => false,
            'status' => 'active',
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully. Default password is "password"');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        // Add validation and update logic
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
    }
}
