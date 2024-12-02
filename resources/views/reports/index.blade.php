<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('reports.generate') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Report Type -->
                            <div>
                                <x-input-label for="report_type" :value="__('Report Type')" />
                                <select id="report_type" name="report_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach ($navigation['reports']['children'] as $key => $report)
                                        <option value="{{ $key }}"
                                            {{ old('report_type', request('type')) == $key ? 'selected' : '' }}>
                                            {{ $report['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('report_type')" class="mt-2" />
                            </div>

                            <!-- Department -->
                            <div>
                                <x-input-label for="department_id" :value="__('Department')" />
                                <select id="department_id" name="department_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                            </div>

                            <!-- Date Range -->
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" type="date" name="start_date" :value="old('start_date')"
                                    required class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="end_date" :value="__('End Date')" />
                                <x-text-input id="end_date" type="date" name="end_date" :value="old('end_date')" required
                                    class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>

                            <!-- Employee Selection (for timesheet) -->
                            <div id="employee_selection" class="hidden">
                                <x-input-label for="employee_id" :value="__('Employee')" />
                                <select id="employee_id" name="employee_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Select Employee</option>
                                </select>
                                <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                            </div>

                            <!-- Format Selection -->
                            <div>
                                <x-input-label for="format" :value="__('Format')" />
                                <select id="format" name="format"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="pdf">PDF</option>
                                    <option value="csv">CSV</option>
                                </select>
                                <x-input-error :messages="$errors->get('format')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Generate Report') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reportType = document.getElementById('report_type');
                const employeeSelection = document.getElementById('employee_selection');
                const departmentSelect = document.getElementById('department_id');
                const employeeSelect = document.getElementById('employee_id');

                function toggleEmployeeSelection() {
                    employeeSelection.classList.toggle('hidden', reportType.value !== 'timesheet');
                }

                function loadEmployees() {
                    if (reportType.value === 'timesheet') {
                        const departmentId = departmentSelect.value;
                        fetch(`/employees/suggest?department_id=${departmentId}`)
                            .then(response => response.json())
                            .then(data => {
                                employeeSelect.innerHTML = '<option value="">Select Employee</option>';
                                data.employees.forEach(employee => {
                                    employeeSelect.innerHTML +=
                                        `<option value="${employee.id}">${employee.name} (${employee.employee_number})</option>`;
                                });
                            });
                    }
                }

                reportType.addEventListener('change', toggleEmployeeSelection);
                departmentSelect.addEventListener('change', loadEmployees);

                toggleEmployeeSelection();
                if (reportType.value === 'timesheet') {
                    loadEmployees();
                }
            });
        </script>
    @endpush
</x-app-layout>
