{% include "/partials/head.view.php" %}
<style>
    div.scroll {
        width: 100%;
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        text-align: left;
        padding: 10px;
    }
</style>
<body class="h-full">
<main class="min-h-full">
    {% include "/partials/navigation.view.php" %}

    <main>
        <div class="mt-3 mx-auto max-w-7xl py-6 sm:px-6 lg:px-8"
             style="width: 1000px;">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-800" style="text-align: center;">Your
                    societies</h1>
                {% if not societies %}
                <h1 class="text-xl tracking-tight text-gray-500" style="text-align: center;">You are currently
                    not part of any societies. Wanna create one?</h1>
                {% endif %}
            </div>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8"
                 style="height: 60px; width: 400px;">
                <a href="/society/create">
                    <button
                            class="mt-0 mb-5 flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Create a society
                    </button>
                </a>
            </div>
            <div class="ml-10 mr-10 mt-5 flex gap-12 flex-wrap">
                {% if societies %}
                {% for society in societies %}
                <a href="/society/{{society.id}}">
                    <div style="display: inline-block; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2); transition: 0.3s; border-radius: 5px; height: 300px; width: 250px;">
                        <div style="height: 150px; width: 250px;">
                            <img src="{{ society.banner }}" alt="Banner"
                                 style="width:100%; height: 100%; border-radius: 5px 5px 0 0; ">
                        </div>
                        <div style="padding: 2px 16px; ">
                            <h4><b>{{ society.name }}</b></h4>
                            <div class="scroll">
                                <p>{{ society.description }}</p>
                            </div>
                        </div>
                    </div>
                </a>
                {% endfor %}
                {% endif %}
            </div>
        </div>
</main>
    {% if activationMessage %}
    <input type="hidden" value="{{activationMessage[0]}}" id="msg">
    <script>
        message = document.getElementById("msg").value;
        alert(message);
    </script>
    {% endif %}
</body>
</html>