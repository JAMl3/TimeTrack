<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Department Overview Report</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $period['start'] }} - {{ $period['end'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <canvas id="departmentChart"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <canvas id="percentageChart"></canvas>
            </div>
        </div>
    </div>
</x-app-layout>
