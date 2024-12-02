<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Add Holiday Entitlement</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Create a new holiday entitlement record</p>
            </div>
            <a href="{{ route('holidays.dashboard', ['tab' => 'entitlements']) }}"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                Back to List
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form action="{{ route('holidays.entitlements.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Employee
                        </label>
                        <select name="user_id" id="user_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Year
                        </label>
                        <input type="number" name="year" id="year" required min="{{ date('Y') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('year', date('Y')) }}">
                        @error('year')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="total_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Total Days
                        </label>
                        <input type="number" name="total_days" id="total_days" required min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('total_days') }}">
                        @error('total_days')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Add Entitlement
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
