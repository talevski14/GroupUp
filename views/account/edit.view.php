{% include "/partials/head.view.php" %}

<body class="h-full">
<div class="min-h-full">
    {% include "/partials/navigation.view.php" %}

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-sm">
    <div class="mt-1">
        <a href="/account/remove">
            <button
                    class=" flex w-full justify-center rounded-md bg-red-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                Deactivate your account
            </button>
        </a>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-5 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Edit your account</h2>
    </div>
    </div>
    <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="/account/edit" method="POST">
            <div>
                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full name</label>
                <div class="mt-2">
                    <input id="name" name="name" type="text" required
                           value="{% if filled['name'] %}{{filled['name']}}{% endif %}"
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <p class="text-left text-sm text-red-500">
                {% if name %}
                {% for error in name %}
                {{ error }}
                {% endfor %}
                {% endif %}
            </p>

            <div>
                <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Username</label>
                <div class="mt-2">
                    <input id="username" name="username" type="text" required
                           value="{% if filled['username'] %}{{filled['username']}}{% endif %}"
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <p class="text-left text-sm text-red-500">
                {% if username %}
                {% for error in username %}
                {{ error }}
                {% endfor %}
                {% endif %}
            </p>

            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                <div class="mt-2">
                    <input id="email" name="email" type="email" autocomplete="email"
                           disabled
                           value="{% if filled['email'] %}{{filled['email']}}{% endif %}"
                           class="block w-full bg-gray-200 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <p class="text-center text-sm text-red-500">
                {% if error %}
                {{ error }}
                {% endif %}
            </p>

            <div>
                <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Edit
                </button>
            </div>
        </form>

            <div class="mt-3">
                <a href="/account/edit/password">
                    <button
                            class=" flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Change password
                    </button>
                </a>
            </div>

            <div class="mt-3">
                <a href="/photo">
                    <button
                            class=" flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Change profile picture
                    </button>
                </a>
            </div>
    </div>
</div>
</div>
</body>
</html>