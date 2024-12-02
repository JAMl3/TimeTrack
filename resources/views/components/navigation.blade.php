<div class="min-h-screen bg-gray-100">
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        @foreach ($navigation as $key => $item)
                            @if (!isset($item['children']))
                                <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['route'])">
                                    <i class="{{ $item['icon'] }} mr-2"></i>
                                    {{ $item['name'] }}
                                </x-nav-link>
                            @else
                                <div class="relative group">
                                    <x-nav-link href="#" :active="str_starts_with($currentRoute, $key)" class="inline-flex items-center">
                                        <i class="{{ $item['icon'] }} mr-2"></i>
                                        {{ $item['name'] }}
                                        <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </x-nav-link>

                                    <div
                                        class="absolute z-50 hidden group-hover:block w-48 bg-white rounded-md shadow-lg py-1">
                                        @foreach ($item['children'] as $child)
                                            <a href="{{ route($child['route'], $child['params'] ?? []) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ $child['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </nav>

    <!-- Responsive Navigation Menu -->
    <div class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($navigation as $key => $item)
                @if (!isset($item['children']))
                    <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['route'])">
                        <i class="{{ $item['icon'] }} mr-2"></i>
                        {{ $item['name'] }}
                    </x-responsive-nav-link>
                @else
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            <i class="{{ $item['icon'] }} mr-2"></i>
                            {{ $item['name'] }}
                            <svg class="ml-auto h-4 w-4" :class="{ 'rotate-180': open }" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" class="pl-4">
                            @foreach ($item['children'] as $child)
                                <x-responsive-nav-link :href="route($child['route'], $child['params'] ?? [])" :active="request()->routeIs($child['route'])">
                                    {{ $child['name'] }}
                                </x-responsive-nav-link>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
