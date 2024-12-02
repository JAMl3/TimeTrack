<x-clock-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Company Logo/Name -->
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Change Your PIN</h1>
                        <p class="text-gray-600 mt-2">For security reasons, you need to change your default PIN before
                            clocking in.</p>
                    </div>

                    <!-- Error Messages -->
                    <div class="max-w-md mx-auto">
                        @if ($errors->any())
                            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center"
                                role="alert">
                                @foreach ($errors->all() as $error)
                                    <span class="block">{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- PIN Change Form -->
                    <div class="max-w-md mx-auto">
                        <form method="POST" action="{{ route('clock.update-pin') }}" class="space-y-6"
                            autocomplete="off">
                            @csrf
                            <input type="hidden" name="employee_number" value="{{ $employee->employee_number }}">

                            <div>
                                <x-input-label for="current_pin" :value="__('Current PIN')" />
                                <x-text-input id="current_pin" class="block mt-1 w-full text-center text-xl"
                                    type="password" name="current_pin" required maxlength="4" pattern="[0-9]*"
                                    inputmode="numeric" autocomplete="off" />
                            </div>

                            <div>
                                <x-input-label for="new_pin" :value="__('New PIN')" />
                                <x-text-input id="new_pin" class="block mt-1 w-full text-center text-xl"
                                    type="password" name="new_pin" required maxlength="4" pattern="[0-9]*"
                                    inputmode="numeric" autocomplete="off" />
                            </div>

                            <div>
                                <x-input-label for="new_pin_confirmation" :value="__('Confirm New PIN')" />
                                <x-text-input id="new_pin_confirmation" class="block mt-1 w-full text-center text-xl"
                                    type="password" name="new_pin_confirmation" required maxlength="4" pattern="[0-9]*"
                                    inputmode="numeric" autocomplete="off" />
                            </div>

                            <div class="flex justify-center">
                                <x-primary-button class="px-8 py-4 text-lg">
                                    {{ __('Change PIN') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-clock-layout>
