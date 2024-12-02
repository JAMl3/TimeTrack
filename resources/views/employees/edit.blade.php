<x-app-layout>
    <div class="p-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Employee</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update employee information</p>
                </div>

                <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                <input type="text" name="full_name" id="full_name"
                                    value="{{ $employee->user->name }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                            </div>

                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" name="email" id="email" value="{{ $employee->user->email }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                            </div>

                            <div>
                                <label for="phone"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                <input type="tel" name="phone" id="phone" value="{{ $employee->phone }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                            </div>

                            <div>
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start
                                    Date</label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ $employee->start_date }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Company Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                                <input type="text" name="company" id="company" value="{{ $employee->company }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                            </div>

                            <div>
                                <label for="department_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                                <select name="department_id" id="department_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ $employee->department_id == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="position"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                <input type="text" name="position" id="position"
                                    value="{{ old('position', $employee->position) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                                @error('position')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="branch"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                                <input type="text" name="branch" id="branch" value="{{ $employee->branch }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                            </div>
                        </div>
                    </div>

                    <!-- Work Schedule -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Work Schedule</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work
                                Days</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                    <label class="relative">
                                        <input type="checkbox" name="shift_days[]" value="{{ $day }}"
                                            {{ in_array($day, $employee->shift_pattern['days'] ?? []) ? 'checked' : '' }}
                                            class="peer absolute opacity-0 w-full h-full cursor-pointer">
                                        <span
                                            class="block min-w-[2.5rem] text-center px-2 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 peer-hover:bg-gray-100 dark:peer-hover:bg-gray-600">
                                            {{ $day }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_time"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start
                                    Time</label>
                                <input type="time" name="start_time" id="start_time"
                                    value="{{ $employee->shift_pattern['start_time'] ?? '09:00' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                            </div>

                            <div>
                                <label for="end_time"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                                <input type="time" name="end_time" id="end_time"
                                    value="{{ $employee->shift_pattern['end_time'] ?? '17:00' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Status</h3>
                        <div>
                            <label for="status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employment
                                Status</label>
                            <select name="status" id="status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>
                                    Inactive</option>
                                <option value="on_leave" {{ $employee->status === 'on_leave' ? 'selected' : '' }}>On
                                    Leave</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('employees.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>