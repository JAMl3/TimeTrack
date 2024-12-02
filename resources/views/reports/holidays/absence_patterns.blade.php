<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Absence Patterns Report') }}
            </h2>
            <span class="text-sm text-gray-600">
                {{ $period['start'] }} - {{ $period['end'] }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <canvas id="absenceTypeChart"></canvas>
                        </div>
                        <div>
                            <canvas id="employeeAbsenceChart"></canvas>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Detailed Absence Records</h3>
                        <div class="space-y-6">
                            @forelse ($absence_patterns as $employee)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium text-lg mb-2">{{ $employee['employee_name'] }}</h4>
                                    <div class="space-y-4">
                                        @foreach ($employee['patterns'] as $pattern)
                                            <div class="bg-white p-3 rounded border">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium capitalize">{{ $pattern['type'] }}</span>
                                                    <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                        {{ $pattern['total'] }}
                                                        {{ Str::plural('day', $pattern['total']) }}
                                                    </span>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-600">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach ($pattern['dates'] as $date)
                                                            <span
                                                                class="bg-gray-100 px-2 py-1 rounded">{{ $date }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No absence records found for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const data = @json($absence_patterns);

                // Prepare data for absence type chart
                const absenceTypes = {
                    sick: 0,
                    unpaid: 0,
                    other: 0
                };

                data.forEach(employee => {
                    employee.patterns.forEach(pattern => {
                        absenceTypes[pattern.type] += pattern.total;
                    });
                });

                // Absence Type Chart
                new Chart(document.getElementById('absenceTypeChart').getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: Object.keys(absenceTypes).map(type => type.charAt(0).toUpperCase() + type.slice(
                            1)),
                        datasets: [{
                            data: Object.values(absenceTypes),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(54, 162, 235, 0.5)',
                                'rgba(255, 206, 86, 0.5)'
                            ],
                            borderColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 206, 86)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Absence Types Distribution'
                            }
                        }
                    }
                });

                // Employee Absence Chart
                const employeeData = data.map(employee => ({
                    name: employee.employee_name,
                    total: employee.patterns.reduce((sum, pattern) => sum + pattern.total, 0)
                }));

                new Chart(document.getElementById('employeeAbsenceChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: employeeData.map(item => item.name),
                        datasets: [{
                            label: 'Total Absences',
                            data: employeeData.map(item => item.total),
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgb(75, 192, 192)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Days'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Total Absences by Employee'
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
