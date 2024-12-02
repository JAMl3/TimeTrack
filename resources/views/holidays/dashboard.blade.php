<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Holiday Management</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage holiday requests and entitlements</p>
            </div>
            <div class="flex space-x-4">
                @can('create', App\Models\HolidayRequest::class)
                    <a href="{{ route('holidays.requests.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Request Holiday
                    </a>
                @endcan
                @can('create', App\Models\HolidayEntitlement::class)
                    <a href="{{ route('holidays.entitlements.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Entitlement
                    </a>
                @endcan
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('holidays.dashboard', ['tab' => 'requests']) }}"
                        class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'requests' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Holiday Requests
                    </a>
                    <a href="{{ route('holidays.dashboard', ['tab' => 'entitlements']) }}"
                        class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'entitlements' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Entitlements
                    </a>
                </nav>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6">
                <form action="{{ route('holidays.dashboard') }}" method="GET" class="flex gap-4">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Search...">
                    </div>
                    @if ($activeTab === 'requests')
                        <select name="status"
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    @endif
                    <button type="submit"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Content -->
            <div>
                @if ($activeTab === 'requests')
                    @include('holidays.requests._list', ['requests' => $requests])
                @elseif($activeTab === 'entitlements')
                    @include('holidays.entitlements._list', ['entitlements' => $entitlements])
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
