{% include "/partials/head.view.php" %}

<body class="h-full">
<div class="min-h-full">
    {% include "/partials/navigation.view.php" %}
    <main>
        <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <form class="space-y-6" action="/society/create" method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name of the
                            society</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text" required
                                   value="{% if filled['name'] %} {{ filled['name'] }} {% endif %}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                        <div class="mt-2">
                            <input id="description" name="description" type="text" required
                                   value="{% if filled['description'] %}{{ filled['description'] }}{% endif %}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div>
                        <label for="uploadfile" class="block text-sm font-medium leading-6 text-gray-900">The banner of
                            your society</label>
                        <p class="block text-sm font-small leading-6 text-gray-400">*you can use a default banner by
                            simply avoiding this step</p>
                        <div class="mt-2">
                            <input class="form-control" type="file" name="uploadfile" value=""/>
                        </div>
                    </div>

                    <p class="text-left text-sm text-red-500">
                        {% if error %}
                        {{ error }}
                        {% endif %}
                    </p>

                    <div>
                        <button type="submit"
                                name="upload"
                                class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Create
                        </button>
                    </div>
                </form>
            </div>
    </main>
</div>
</body>
</html>