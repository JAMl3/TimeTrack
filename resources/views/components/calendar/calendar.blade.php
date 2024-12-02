@props(['events' => [], 'month' => null, 'year' => null])

@php
    $month = $month ?? now()->month;
    $year = $year ?? now()->year;
    $date = \Carbon\Carbon::createFromDate($year, $month, 1);
    $daysInMonth = $date->daysInMonth;
    $firstDayOfWeek = $date->copy()->startOfMonth()->dayOfWeek;
    $lastDayOfWeek = $date->copy()->endOfMonth()->dayOfWeek;
@endphp

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b">
        <div class="flex space-x-4">
            <button type="button"
                onclick="window.location.href = '{{ request()->fullUrlWithQuery(['month' => $date->copy()->subMonth()->month, 'year' => $date->copy()->subMonth()->year]) }}'"
                class="p-2 text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h2 class="text-xl font-semibold text-gray-900">{{ $date->format('F Y') }}</h2>
            <button type="button"
                onclick="window.location.href = '{{ request()->fullUrlWithQuery(['month' => $date->copy()->addMonth()->month, 'year' => $date->copy()->addMonth()->year]) }}'"
                class="p-2 text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-7 gap-px bg-gray-200 border-b">
        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
            <div class="bg-gray-50 py-2 text-center text-sm font-semibold text-gray-700">
                {{ $dayName }}
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-7 gap-px bg-gray-200">
        @for ($i = 0; $i < $firstDayOfWeek; $i++)
            <div class="bg-gray-50 py-8"></div>
        @endfor

        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $currentDate = \Carbon\Carbon::createFromDate($year, $month, $day);
                $dayEvents = collect($events)->filter(function ($event) use ($currentDate) {
                    return $event['date']->isSameDay($currentDate);
                });
                $isToday = $currentDate->isToday();
                $isWeekend = $currentDate->isWeekend();
            @endphp

            <div class="bg-white relative py-2 px-3 h-32 overflow-y-auto">
                <div
                    class="@if ($isToday) bg-blue-500 text-white rounded-full w-7 h-7 flex items-center justify-center @endif
                            @if ($isWeekend) text-gray-400 @else text-gray-700 @endif">
                    {{ $day }}
                </div>

                @foreach ($dayEvents as $event)
                    <div class="mt-1">
                        <div
                            class="px-2 py-1 text-xs rounded
                                  @if ($event['type'] === 'holiday') @if ($event['status'] === 'approved')
                                          bg-green-100 text-green-800 border border-green-200
                                      @elseif($event['status'] === 'pending')
                                          bg-yellow-100 text-yellow-800 border border-yellow-200
                                      @else
                                          bg-red-100 text-red-800 border border-red-200 @endif
@elseif($event['type'] === 'absence')
bg-purple-100 text-purple-800 border border-purple-200
                                  @endif">
                            {{ Str::limit($event['title'], 20) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endfor

        @for ($i = $lastDayOfWeek + 1; $i < 7; $i++)
            <div class="bg-gray-50 py-8"></div>
        @endfor
    </div>
</div>
