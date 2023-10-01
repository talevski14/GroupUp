{% include "/partials/head.view.php" %}
<body>
<div id="content" style="margin: auto; width: 50%; padding: 10px;">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-8xl font-bold leading-9 tracking-tight text-indigo-600" style="font-family: 'Brush Script MT', cursive;">GroupUp</h2>
    </div>
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Change your profile picture</h2>
    </div>
    <form method="POST" action="/photo" enctype="multipart/form-data" class="mt-5">
        <div class="form-group" style="margin: auto; width: 50%; padding: 10px;">
            <input class="form-control" type="file" name="uploadfile" value=""/>
        </div>
        <div style="margin: auto; width: 50%; padding: 10px;">
            <button type="submit"
                    name="upload"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Upload photo
            </button>
            <button type="submit"
                    class="mt-2 flex w-full justify-center rounded-md bg-gray-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                Later
            </button>
        </div>
    </form>
</div>
</body>

</html>