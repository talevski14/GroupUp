<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <title>GroupUp</title>
    <script src="https://cdn.tailwindcss.com/?plugins=forms"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function copyEvent() {
            let value = document.getElementById('copy');
            let content = document.getElementById('copy').textContent;
            window.getSelection().selectAllChildren(value);
            let clipboard = navigator.clipboard;

            if (clipboard === undefined) {
                document.execCommand("copy");
            } else {
                navigator.clipboard.writeText(content);
            }
        }
    </script>
</head>
<body class="h-full">
<main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
    <div class="text-center">
        <p class="text-base font-semibold text-indigo-600">Invite link</p>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">{{society}}</h1>
        <p class="mt-6 text-base leading-7 text-gray-600" id="copy">http://localhost/invite-link?id={{link}}</p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <button onclick="copyEvent()" class="mr-5 rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Copy link</button>
            <a href="/society/{{societyID}}" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Go to society</a>
        </div>
    </div>
</main>
</body>
</html>
