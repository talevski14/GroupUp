{% include "/partials/head.view.php" %}
<body class="h-full">
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-8xl font-bold leading-9 tracking-tight text-indigo-600" style="font-family: 'Brush Script MT', cursive;">GroupUp</h2>
    </div>
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Create a new
            account</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="/signup" method="POST">
            <div>
                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full name</label>
                <div class="mt-2">
                    <input id="name" name="name" type="text" required
                           value="{% if filled['name'] %} {{ filled['name'] }} {% endif %}"
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
                           value="{% if filled['username'] %} {{ filled['username'] }} {% endif %}"
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
                    <input id="email" name="email" type="email" autocomplete="email" required
                           value="{% if filled['email'] %} {{ filled['email'] }} {% endif %}"
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <p class="text-left text-sm text-red-500">
                {% if email %}
                {% for error in email %}
                {{ error }}
                {% endfor %}
                {% endif %}
            </p>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                </div>
                <div class="mt-2">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <p class="text-left text-sm text-red-500">
                {% if password %}
                {% for error in password %}
                {{ error }}
                {% endfor %}
                {% endif %}
            </p>

            <p class="text-center text-sm text-red-500">
                {% if error %}
                {{ error }}
                {% endif %}
            </p>

            <div>
                <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Sign up
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>