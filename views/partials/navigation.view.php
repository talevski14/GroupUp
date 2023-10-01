<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 ">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center" style="width: 800px;">
                <div class="sm:mx-auto sm:w-full sm:max-w-sm" style="width: 120px;">
                    <a href="/home"><h2
                                class="mt-10 text-center text-3xl font-bold leading-9 tracking-tight text-indigo-600"
                                style="margin: auto; width: 50%; padding: 10px; font-family: 'Brush Script MT', cursive;">
                            GroupUp</h2></a>
                </div>
                <div class="ml-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8" style="width: 880px;">
                    <h1 class="text-3xl font-bold tracking-tight text-white">{{ header }}</h1>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <div style="float:left;" class="flex">
                        <p class="text-s font-bold tracking-tight text-gray-400">@{{username}}</p>
                    </div>
                    <div class="relative ml-3">
                        <div style="float: left;" class="ml-3">
                            <a href="/account/edit">
                                <button type="button"
                                        class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                        id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="absolute -inset-1.5"></span>
                                    <span class="sr-only">Open user menu</span>
                                    <img class="h-8 w-8 rounded-full" src=
                                    "{{ profileimg }}"
                                         alt="slika">
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="ml-3">
                        <a href="/logout">
                            <h1 class="text-s font-bold tracking-tight text-gray-400 hover:text-white">Log out</h1>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>