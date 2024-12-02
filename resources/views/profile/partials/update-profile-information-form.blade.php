<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" class="text-gray-700 dark:text-gray-300" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        @if ($user->employee)
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" class="text-gray-700 dark:text-gray-300" />
                <x-text-input id="phone" name="phone" type="tel"
                    class="mt-1 block w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    :value="old('phone', $user->employee->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div>
                <x-input-label for="emergency_contact" :value="__('Emergency Contact')" class="text-gray-700 dark:text-gray-300" />
                <x-text-input id="emergency_contact" name="emergency_contact" type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    :value="old('emergency_contact', $user->employee->emergency_contact)" />
                <x-input-error class="mt-2" :messages="$errors->get('emergency_contact')" />
            </div>

            <div>
                <x-input-label for="emergency_phone" :value="__('Emergency Contact Phone')" class="text-gray-700 dark:text-gray-300" />
                <x-text-input id="emergency_phone" name="emergency_phone" type="tel"
                    class="mt-1 block w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    :value="old('emergency_phone', $user->employee->emergency_phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('emergency_phone')" />
            </div>

            <div>
                <x-input-label for="address" :value="__('Address')" class="text-gray-700 dark:text-gray-300" />
                <x-text-input id="address" name="address" type="text"
                    class="mt-1 block w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    :value="old('address', $user->employee->address)" />
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mt-4">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Employment Information') }}
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="position" :value="__('Position')" class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="position" type="text"
                            class="mt-1 block w-full bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100"
                            :value="$user->employee->position" disabled />
                    </div>
                    <div>
                        <x-input-label for="department" :value="__('Department')" class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="department" type="text"
                            class="mt-1 block w-full bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100"
                            :value="$user->employee->department->name" disabled />
                    </div>
                    <div>
                        <x-input-label for="employee_number" :value="__('Employee Number')"
                            class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="employee_number" type="text"
                            class="mt-1 block w-full bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100"
                            :value="$user->employee->employee_number" disabled />
                    </div>
                    <div>
                        <x-input-label for="employment_status" :value="__('Employment Status')"
                            class="text-gray-700 dark:text-gray-300" />
                        <x-text-input id="employment_status" type="text"
                            class="mt-1 block w-full bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100"
                            :value="$user->employee->employment_status" disabled />
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
