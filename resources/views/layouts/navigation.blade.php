<div class="flex justify-center items-center   ">

    <nav x-data="{ open: false }" class="bg-transparentBlack rounded-3xl  h-[auto] w-[1230px] " id ="responsiveDiv2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('morfeo3d.index') }}">
                            <x-application-logo
                                class="block h-[3rem] w-auto fill-current hover:scale-105 ease-in-out " />
                        </a>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        {{-- <div class="flex items-center space-x-8"> --}}
                        {{-- <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link> --}}
                        {{-- <x-nav-link :href="route('chirps.index')" :active="request()->routeIs('chirps.*')">
                            {{ __('Chirps') }}
                        </x-nav-link> --}}
                        {{-- </div> --}}

                    </div>

                </div>
                <div class="flex items-center mr-10">
                    <div class="hidden sm:flex sm:items-center sm:ms-6">

                        @auth
                            <x-dropdown width="w-auto" :contentClasses="'py-0 bg-gris text-white rounded-lg shadow-md'">
                                <x-slot name="trigger">
                                    <button class="bg-red-500 rounded-2xl w-10 h-10 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </button>


                                </x-slot>
                                <x-slot name="content">
                                    <div class="flex items-center  p-5 border-b">
                                        <!-- Mostrar nombre y correo del usuario -->
                                        <div class="text-start space-y-3">
                                            <p class="whitespace-nowrap">{{ Auth::user()->name }}</p>
                                            <p class="whitespace-nowrap">{{ Auth::user()->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-5 border-b">

                                        <div class="text-center space-y-3">
                                            <x-dropdown-link :href="route('profile.edit')">
                                                {{ __('Profile') }}
                                            </x-dropdown-link>
                                            <x-dropdown-link :href="route('profile.edit')">
                                                {{ __('Saved') }}
                                            </x-dropdown-link>
                                            <x-dropdown-link :href="route('profile.edit')">
                                                {{ __('Orders') }}
                                            </x-dropdown-link>

                                        </div>
                                    </div>
                                    <div class="flex items-start justify-start p-5 border-b">

                                        <div class="flex space-x-3">
                                            <x-dropdown-link :href="route('profile.edit')">
                                                {{ __('Theme') }}
                                            </x-dropdown-link>
                                            <div class="flex ">
                                                <button class="bg-red-500 w-[5rem] h-7 rounded-2xl"></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-start p-5 border-b ">
                                        <div class="space-y-3">
                                            <x-dropdown-link :href="route('profile.edit')">
                                                {{ __('Help') }}
                                            </x-dropdown-link>
                                            <!-- Formulario de autenticación para cerrar sesión -->
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <x-dropdown-link :href="route('logout')"
                                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                                    {{ __('Log Out') }}
                                                </x-dropdown-link>
                                            </form>
                                        </div>

                                    </div>
                                </x-slot>

                            </x-dropdown>
                        @endauth

                    </div>
                    @auth
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="open = ! open"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>


                </div>


            </div>
            
            <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden mt-0">
                <div class="pt-2 pb-3 space-y-1">
                    {{-- <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link> --}}
                    {{-- <x-responsive-nav-link :href="route('chirps.index')" :active="request()->routeIs('chirps.*')">
                        {{ __('chirps') }}
                    </x-responsive-nav-link> --}}
                </div>

                <!-- Responsive Settings Options -->
               

              
                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
                @else
                <div class=" ">

                    <button onclick="window.location.href='{{ route('login') }}'" class="flex items-center space-x-2 bg-transparent border-none cursor-pointer">
                      
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                      
                    </button>
                    </div>
                @endauth
            </div>




    </nav>
</div>
