<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

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
        return view('employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Add validation and store logic
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
