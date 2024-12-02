<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Absence Patterns</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Analysis for {{ $employee->name }}</p>
            </div>
            <a href="{{ route('absences.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 dark:bg-green-900 dark:border-green-600 dark:text-green-300 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Patterns Section -->
            <div class="space-y-6">
                @if (empty($patterns))
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <p class="text-gray-500 dark:text-gray-400">No significant absence patterns detected.</p>
                    </div>
                @else
                    <!-- Day of Week Patterns -->
                    @if (isset($patterns['day_of_week']))
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Day of Week Patterns
                            </h3>
                            <div class="space-y-4">
                                @foreach ($patterns['day_of_week'] as $pattern)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-gray-700 dark:text-gray-300 font-medium">
                                                {{ $pattern['day'] }}s
                                            </span>
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if ($pattern['severity'] === 'high') severity-high
                                                @elseif($pattern['severity'] === 'medium') severity-medium
                                                @else severity-low @endif">
                                                {{ ucfirst($pattern['severity']) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            <p>{{ $pattern['count'] }} absences ({{ $pattern['percentage'] }}%)</p>
                                            @if ($pattern['last_occurrence'])
                                                <p class="mt-1">Last occurrence:
                                                    {{ \Carbon\Carbon::parse($pattern['last_occurrence'])->format('M d, Y') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Consecutive Days -->
                    @if (isset($patterns['consecutive_days']))
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Consecutive Absences
                            </h3>
                            <div class="space-y-4">
                                @foreach ($patterns['consecutive_days'] as $pattern)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-gray-700 dark:text-gray-300 font-medium">
                                                {{ $pattern['days_count'] }} consecutive days
                                            </span>
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if ($pattern['severity'] === 'high') severity-high
                                                @elseif($pattern['severity'] === 'medium') severity-medium
                                                @else severity-low @endif">
                                                {{ ucfirst($pattern['severity']) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            <p>From
                                                {{ \Carbon\Carbon::parse($pattern['start_date'])->format('M d, Y') }}
                                            </p>
                                            <p>To {{ \Carbon\Carbon::parse($pattern['end_date'])->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Frequency Patterns -->
                    @if (isset($patterns['frequency']))
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Frequency Analysis
                            </h3>
                            <div class="space-y-4">
                                @foreach ($patterns['frequency'] as $period => $pattern)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-gray-700 dark:text-gray-300 font-medium">
                                                Last {{ $pattern['period'] }}
                                            </span>
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if ($pattern['severity'] === 'high') severity-high
                                                @elseif($pattern['severity'] === 'medium') severity-medium
                                                @else severity-low @endif">
                                                {{ ucfirst($pattern['severity']) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            <p>{{ $pattern['count'] }} absences in {{ $pattern['days_analyzed'] }}
                                                days</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Recommendations Section -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Recommendations
                    </h3>
                    @if (!empty($recommendations))
                        <div class="space-y-4">
                            @foreach ($recommendations as $recommendation)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">
                                            {{ ucfirst(str_replace('_', ' ', $recommendation['type'])) }}
                                        </span>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if ($recommendation['severity'] === 'high') severity-high
                                            @elseif($recommendation['severity'] === 'medium') severity-medium
                                            @else severity-low @endif">
                                            {{ ucfirst($recommendation['severity']) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $recommendation['description'] }}
                                    </p>
                                    @if ($recommendation['action_required'])
                                        <div class="mt-4">
                                            @if ($recommendation['type'] === 'extend_monitoring')
                                                <form action="{{ route('absences.extend', $employee->id) }}"
                                                    method="POST" class="flex items-center gap-2">
                                                    @csrf
                                                    <input type="number" name="days" min="1" max="30"
                                                        value="1"
                                                        class="w-20 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                                        Extend Next Absence
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">
                            No recommendations at this time.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
