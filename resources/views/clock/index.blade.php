<x-clock-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Company Logo/Name -->
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ config('app.name', 'Laravel') }}</h1>
                    </div>

                    <!-- Current Time Display -->
                    <div class="text-center mb-8">
                        <div class="text-6xl font-bold text-gray-700 dark:text-gray-200 mb-2" id="current-time">
                            00:00:00
                        </div>
                        <div class="text-xl text-gray-500 dark:text-gray-400" id="current-date">
                            Loading...
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <div class="max-w-md mx-auto">
                        @if (session('success'))
                            <div class="mb-6 {{ str_contains(session('success'), 'out') ? 'bg-red-100 dark:bg-red-900/50 border-red-400 text-red-700 dark:text-red-200' : 'bg-green-100 dark:bg-green-900/50 border-green-400 text-green-700 dark:text-green-200' }} border px-4 py-3 rounded relative text-center text-lg font-medium"
                                role="alert" id="success-message">
                                <span class="block">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 bg-red-100 dark:bg-red-900/50 border border-red-400 text-red-700 dark:text-red-200 px-4 py-3 rounded relative text-center"
                                role="alert">
                                @foreach ($errors->all() as $error)
                                    <span class="block">{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('clock.clockInOut') }}" class="max-w-md mx-auto space-y-6"
                        autocomplete="off" id="clock-form">
                        @csrf
                        <!-- Hidden fields -->
                        <input type="text" style="display:none" name="fakeusernameremembered" />
                        <input type="password" style="display:none" name="fakepasswordremembered" />
                        <input type="hidden" id="employee_number" name="employee_number" />

                        <div class="relative">
                            <x-input-label for="employee_search" :value="__('Search by Name or Employee Number')" class="text-gray-700 dark:text-gray-300" />
                            <x-text-input id="employee_search"
                                class="block mt-1 w-full text-xl bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100"
                                type="text"
                                placeholder="Type name or employee number..."
                                autocomplete="off"
                                required
                                autofocus />
                            <!-- Suggestions Dropdown -->
                            <div id="suggestions_dropdown"
                                class="absolute w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-10 hidden">
                            </div>
                        </div>

                        <div>
                            <x-input-label for="pin_code" :value="__('PIN Code')" class="text-gray-700 dark:text-gray-300" />
                            <x-text-input id="pin_code"
                                class="block mt-1 w-full text-center text-xl bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100"
                                type="password"
                                name="pin_code"
                                required
                                autocomplete="new-password"
                                data-lpignore="true"
                                data-form-type="other" />
                        </div>

                        <div class="flex justify-center">
                            <x-primary-button class="px-8 py-4 text-lg">
                                {{ __('Clock In/Out') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Add styles for suggestion items
            const style = document.createElement('style');
            style.textContent = `
                .suggestion-item {
                    padding: 0.75rem 1rem;
                    cursor: pointer;
                }
                .suggestion-item:hover {
                    background-color: #f3f4f6;
                }
                .dark .suggestion-item:hover {
                    background-color: #374151;
                }
                .suggestion-item .name {
                    color: #111827;
                }
                .dark .suggestion-item .name {
                    color: #f3f4f6;
                }
                .suggestion-item .employee-number {
                    color: #6b7280;
                    font-size: 0.875rem;
                }
                .dark .suggestion-item .employee-number {
                    color: #9ca3af;
                }
                .suggestion-item.bg-gray-100 {
                    background-color: #f3f4f6;
                }
                .dark .suggestion-item.bg-gray-100 {
                    background-color: #374151;
                }
            `;
            document.head.appendChild(style);

            // Add CSRF token to all AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            fetch = (originalFetch => {
                return (...args) => {
                    if (args[0].startsWith('/')) {
                        if (args.length > 1) {
                            args[1] = {
                                ...args[1],
                                headers: {
                                    ...args[1]?.headers,
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            };
                        } else {
                            args[1] = {
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            };
                        }
                    }
                    return originalFetch.apply(window, args);
                };
            })(window.fetch);

            function updateTime() {
                const now = new Date();

                // Update time
                const timeString = now.toLocaleTimeString('en-US', {
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                document.getElementById('current-time').textContent = timeString;

                // Update date
                const dateString = now.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                document.getElementById('current-date').textContent = dateString;
            }

            // Update immediately and then every second
            updateTime();
            setInterval(updateTime, 1000);

            // Form reset functionality
            function resetForm() {
                const form = document.getElementById('clock-form');
                const searchInput = document.getElementById('employee_search');
                const employeeNumberInput = document.getElementById('employee_number');
                const pinInput = document.getElementById('pin_code');
                const successMessage = document.getElementById('success-message');

                // Clear all inputs
                searchInput.value = '';
                employeeNumberInput.value = '';
                pinInput.value = '';

                // Hide suggestions dropdown
                const suggestionsDropdown = document.getElementById('suggestions_dropdown');
                suggestionsDropdown.innerHTML = '';
                suggestionsDropdown.classList.add('hidden');

                // Focus back on search input
                searchInput.focus();

                // Hide success message if it exists
                if (successMessage) {
                    successMessage.style.transition = 'opacity 3s ease-out';
                    successMessage.style.opacity = '0';
                    setTimeout(() => {
                        successMessage.remove();
                    }, 3000);
                }
            }

            // Check if there's a success message and set timer to reset
            document.addEventListener('DOMContentLoaded', function() {
                const successMessage = document.getElementById('success-message');
                if (successMessage) {
                    // Wait for message display + fade out duration
                    setTimeout(resetForm, 4000);
                }
            });

            // Clear form fields on page load
            window.onload = function() {
                resetForm();
            }

            // Employee search functionality
            const searchInput = document.getElementById('employee_search');
            const employeeNumberInput = document.getElementById('employee_number');
            const suggestionsDropdown = document.getElementById('suggestions_dropdown');
            let debounceTimer = null;

            searchInput.addEventListener('input', function(e) {
                const value = this.value.trim();

                // Clear any existing timer
                if (debounceTimer) {
                    clearTimeout(debounceTimer);
                }

                // Hide dropdown if input is empty
                if (!value) {
                    suggestionsDropdown.innerHTML = '';
                    suggestionsDropdown.classList.add('hidden');
                    return;
                }

                // Set a new timer
                debounceTimer = setTimeout(() => {
                    const url = "{{ route('employees.suggest') }}?term=" + encodeURIComponent(value);

                    fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        suggestionsDropdown.innerHTML = '';

                        if (data.employees && data.employees.length > 0) {
                            data.employees.forEach(employee => {
                                const div = document.createElement('div');
                                div.className = 'suggestion-item';
                                div.innerHTML = `
                                    <div class="name">${employee.name}</div>
                                    <div class="employee-number">#${employee.employee_number}</div>
                                `;
                                div.addEventListener('click', () => {
                                    searchInput.value = employee.name;
                                    employeeNumberInput.value = employee.employee_number;
                                    suggestionsDropdown.classList.add('hidden');
                                    document.getElementById('pin_code').focus();
                                });
                                suggestionsDropdown.appendChild(div);
                            });
                            suggestionsDropdown.classList.remove('hidden');
                        } else {
                            suggestionsDropdown.innerHTML = `
                                <div class="p-4 text-gray-500 dark:text-gray-400">
                                    No matches found
                                </div>
                            `;
                            suggestionsDropdown.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        suggestionsDropdown.innerHTML = `
                            <div class="p-4 text-red-600 dark:text-red-400">
                                Unable to fetch suggestions. Please try again.
                            </div>
                        `;
                        suggestionsDropdown.classList.remove('hidden');
                    });
                }, 300);
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsDropdown.contains(e.target)) {
                    suggestionsDropdown.classList.add('hidden');
                }
            });

            // Handle keyboard navigation
            searchInput.addEventListener('keydown', function(e) {
                const items = suggestionsDropdown.querySelectorAll('.suggestion-item');
                const currentIndex = Array.from(items).findIndex(item => item.classList.contains('bg-gray-100'));

                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        if (currentIndex < items.length - 1) {
                            items[currentIndex]?.classList.remove('bg-gray-100');
                            items[currentIndex + 1]?.classList.add('bg-gray-100');
                        } else {
                            items[currentIndex]?.classList.remove('bg-gray-100');
                            items[0]?.classList.add('bg-gray-100');
                        }
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        if (currentIndex > 0) {
                            items[currentIndex]?.classList.remove('bg-gray-100');
                            items[currentIndex - 1]?.classList.add('bg-gray-100');
                        } else {
                            items[currentIndex]?.classList.remove('bg-gray-100');
                            items[items.length - 1]?.classList.add('bg-gray-100');
                        }
                        break;
                    case 'Enter':
                        e.preventDefault();
                        const selectedItem = items[currentIndex];
                        if (selectedItem) {
                            selectedItem.click();
                        }
                        break;
                }
            });

            // Debug form submission
            document.getElementById('clock-form').addEventListener('submit', function(e) {
                console.log('Form submitted', {
                    employee_number: document.getElementById('employee_number').value,
                    pin_code: document.getElementById('pin_code').value,
                    csrf_token: document.querySelector('input[name="_token"]').value
                });
            });
        </script>
    @endpush
</x-clock-layout>
