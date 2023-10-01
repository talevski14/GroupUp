{% include "/partials/head.view.php" %}

<body class="h-full">
<div class="min-h-full">
    {% include "/partials/navigation.view.php" %}

    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Edit your password</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="/account/edit/password" method="POST">

                <div>
                    <div class="flex items-center justify-between">
                        <label for="oldpassword" class="block text-sm font-medium leading-6 text-gray-900">Old password</label>
                    </div>
                    <div class="mt-2">
                        <input id="oldpassword" name="oldpassword" type="password" autocomplete="current-password" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <p class="text-left text-sm text-red-500">
                    {% if oldpassword %}
                    {% for error in oldpassword %}
                    {{ error }}
                    {% endfor %}
                    {% endif %}
                </p>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="newpassword" class="block text-sm font-medium leading-6 text-gray-900">New password</label>
                    </div>
                    <div class="mt-2">
                        <input id="newpassword" name="newpassword" type="password" autocomplete="current-password" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <p class="text-left text-sm text-red-500">
                    {% if newpassword %}
                    {% for error in newpassword %}
                    {{ error }}
                    {% endfor %}
                    {% endif %}
                </p>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="repeatpassword" class="block text-sm font-medium leading-6 text-gray-900">Repeat the new password</label>
                    </div>
                    <div class="mt-2">
                        <input id="repeatpassword" name="repeatpassword" type="password" autocomplete="current-password" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <p class="text-left text-sm text-red-500">
                    {% if repeatpassword %}
                    {% for error in repeatpassword %}
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
                        Change
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>