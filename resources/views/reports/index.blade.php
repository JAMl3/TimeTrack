<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Reporting</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Generate and download various reports</p>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <form action="{{ route('reports.generate') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">From</label>
                                <input type="date" name="start_date" required 
                                    class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm px-3 py-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">To</label>
                                <input type="date" name="end_date" required 
                                    class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm px-3 py-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600">
                            </div>
                        </div>
                    </div>

                    <!-- Available Reports -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available Reports</label>
                        <div class="space-y-3">
                            @foreach ($navigation['reports']['children'] as $key => $report)
                                <div class="flex items-center">
                                    <input type="radio" name="report_type" value="{{ $key }}" 
                                        class="h-4 w-4 text-blue-600 dark:text-blue-500 border-gray-300 dark:border-gray-700 focus:ring-blue-500 dark:focus:ring-blue-600"
                                        id="report_{{ $key }}" {{ $loop->first ? 'checked' : '' }}>
                                    <label for="report_{{ $key }}" 
                                        class="ml-3 block text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                        {{ $report['name'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Employee Selection for Timesheet -->
                    <div id="employee_selection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee</label>
                        <select name="employee_id" 
                            class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm px-3 py-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600">
                            <option value="">Select Employee</option>
                        </select>
                    </div>

                    <!-- Format -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Format</label>
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="format" value="pdf" checked 
                                    class="h-4 w-4 text-blue-600 dark:text-blue-500 border-gray-300 dark:border-gray-700 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">PDF</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="format" value="csv" 
                                    class="h-4 w-4 text-blue-600 dark:text-blue-500 border-gray-300 dark:border-gray-700 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">CSV</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportTypeInputs = document.querySelectorAll('input[name="report_type"]');
            const employeeSelection = document.getElementById('employee_selection');
            const departmentSelect = document.querySelector('select[name="department_id"]');
            const employeeSelect = document.querySelector('select[name="employee_id"]');

            function toggleEmployeeSelection() {
                const selectedReport = document.querySelector('input[name="report_type"]:checked').value;
                employeeSelection.classList.toggle('hidden', selectedReport !== 'timesheet');
            }

            function loadEmployees() {
                const selectedReport = document.querySelector('input[name="report_type"]:checked').value;
                if (selectedReport === 'timesheet') {
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

            reportTypeInputs.forEach(input => {
                input.addEventListener('change', toggleEmployeeSelection);
            });
            
            departmentSelect.addEventListener('change', loadEmployees);

            toggleEmployeeSelection();
            loadEmployees();
        });
    </script>
    @endpush
</x-app-layout>
