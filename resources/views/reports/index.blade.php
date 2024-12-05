<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Reports</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Generate and download various reports</p>

        <!-- Filter Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-4 gap-4">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required
                        class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm px-3 py-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600">
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" required
                        class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm px-3 py-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600">
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                    <select id="department_id" name="department_id"
                        class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm px-3 py-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600">
                        <option value="">All Departments</option>
                        @foreach ($departments ?? [] as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Format</label>
                    <select id="format" name="format"
                        class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm px-3 py-2 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600">
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="grid grid-cols-2 gap-6">
            <!-- Attendance Reports Column -->
            <div class="space-y-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Attendance Reports</h2>

                <!-- Basic Clocking Report -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Clocking In/Out Report
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daily clock in/out times and hours
                                worked</p>
                        </div>
                        <button type="button" onclick="generateReport('basic-clocking')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Generate
                        </button>
                    </div>
                </div>

                <!-- Late Arrival Patterns -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Late Arrival Patterns
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Analysis of late arrivals and
                                trends</p>
                        </div>
                        <button type="button" onclick="generateReport('late-arrivals')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Generate
                        </button>
                    </div>
                </div>

                <!-- Department Overview -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Department Overview</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Attendance statistics by department
                            </p>
                        </div>
                        <button type="button" onclick="generateReport('department-overview')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Generate
                        </button>
                    </div>
                </div>
            </div>

            <!-- Leave & Absence Reports Column -->
            <div class="space-y-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Leave & Absence Reports</h2>

                <!-- Holiday Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Holiday Summary</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Holiday entitlements and usage
                                overview</p>
                        </div>
                        <button type="button" onclick="generateReport('holiday-summary')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Generate
                        </button>
                    </div>
                </div>

                <!-- Absence Patterns -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Absence Patterns</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detailed analysis of absence
                                patterns and trends</p>
                        </div>
                        <button type="button" onclick="generateReport('absence-patterns')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Generate
                        </button>
                    </div>
                </div>

                <!-- Extended Absences -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Extended Absences</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Report on extended and recurring
                                absences</p>
                        </div>
                        <button type="button" onclick="generateReport('extended-absences')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const startDate = document.getElementById('start_date');
                const endDate = document.getElementById('end_date');

                // Set default dates (current month)
                const now = new Date();
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

                startDate.value = firstDay.toISOString().split('T')[0];
                endDate.value = lastDay.toISOString().split('T')[0];
            });

            function generateReport(reportType) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('reports.generate') }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add report type
                const reportTypeInput = document.createElement('input');
                reportTypeInput.type = 'hidden';
                reportTypeInput.name = 'report_type';
                reportTypeInput.value = reportType;
                form.appendChild(reportTypeInput);

                // Add start date
                const startDateInput = document.createElement('input');
                startDateInput.type = 'hidden';
                startDateInput.name = 'start_date';
                startDateInput.value = document.getElementById('start_date').value;
                form.appendChild(startDateInput);

                // Add end date
                const endDateInput = document.createElement('input');
                endDateInput.type = 'hidden';
                endDateInput.name = 'end_date';
                endDateInput.value = document.getElementById('end_date').value;
                form.appendChild(endDateInput);

                // Add department
                const departmentInput = document.createElement('input');
                departmentInput.type = 'hidden';
                departmentInput.name = 'department_id';
                departmentInput.value = document.getElementById('department_id').value;
                form.appendChild(departmentInput);

                // Add format
                const formatInput = document.createElement('input');
                formatInput.type = 'hidden';
                formatInput.name = 'format';
                formatInput.value = document.getElementById('format').value;
                form.appendChild(formatInput);

                document.body.appendChild(form);
                form.submit();
            }
        </script>
    @endpush
</x-app-layout>
