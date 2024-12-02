<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Notifications</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">View and manage your notifications</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <!-- Notifications content -->
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
