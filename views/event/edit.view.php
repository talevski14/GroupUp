{% include "/partials/head.view.php" %}

<body class="h-full">
<div class="min-h-full">
    {% include "/partials/navigation.view.php" %}
    <main>
        <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <form class="space-y-6" action="/society/{{filled['society']}}/event/{{id}}/edit" method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name of the
                            event</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text" required
                                   value="{{filled['name']}}"
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
                        <label for="event-time">When is the event happening?</label>
                        <div class="mt-2">
                            <input
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                type="datetime-local"
                                id="event-time"
                                name="event-time"
                                min="{{ currentTime }}"
                                max="2050-12-31T23:59"
                                value="{{filled['date_and_time']}}"
                                required
                            />
                        </div>
                    </div>

                    <p class="text-left text-sm text-red-500">
                        {% if date-time %}
                        {% for error in date-time %}
                        {{ error }}
                        {% endfor %}
                        {% endif %}
                    </p>

                    <div>
                        <label>Where is the event happening?</label>
                        <div class="mt-2">
                            <div id="myMap" style="position:relative;width:450px;height:300px;"></div>
                            <br/>
                            <input type="hidden" name="_lat" id="_lat" value="{{filled['lat']}}">
                            <input type="hidden" name="_lon" id="_lon" value="{{filled['lon']}}">
                            <input type="hidden" name="_location" id="_location" value="{{filled['location']}}">
                        </div>
                        <p id="streetName" class="block text-sm font-small leading-6 text-gray-400">{{filled['location']}}</p>
                    </div>

                    <p class="text-left text-sm text-red-500">
                        {% if lat %}
                        {% for error in lat %}
                        {{ error }}
                        {% endfor %}
                        {% endif %}
                    </p>

                    <div>
                        <label for="description">Description</label>
                        <div class="mt-2">
                            <textarea id="description" name="description" required
                                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{filled['description']}}</textarea>
                        </div>
                    </div>

                    <p class="text-left text-sm text-red-500">
                        {% if description %}
                        {% for error in description %}
                        {{ error }}
                        {% endfor %}
                        {% endif %}
                    </p>

                    <div>
                        <button type="submit"
                                class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Edit
                        </button>
                    </div>
                </form>
            </div>
    </main>
</div>
</body>

<script type='text/javascript'>
    var map;
    var pushpin = null;
    var searchManager = null;

    function GetMap() {
        map = new Microsoft.Maps.Map(document.getElementById("myMap"), {
            center: new Microsoft.Maps.Location(document.getElementById("_lat").value, document.getElementById("_lon").value),
            zoom: 13
        });

        Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
            searchManager = new Microsoft.Maps.Search.SearchManager(map);
            reverseGeocode();
        });

        var location = new Microsoft.Maps.Location(document.getElementById("_lat").value, document.getElementById("_lon").value);

        pushpin = new Microsoft.Maps.Pushpin(location, null);

        map.entities.push(pushpin);

        Microsoft.Maps.Events.addHandler(map, 'click', function (e) {
            set_latitudes_and_longitude(e);
        });
    }

    function set_latitudes_and_longitude(e) {
        document.getElementById('_lat').value = e.location.latitude;
        document.getElementById('_lon').value = e.location.longitude;

        map.entities.remove(pushpin)

        var location = new Microsoft.Maps.Location(e.location.latitude, e.location.longitude);

        pushpin = new Microsoft.Maps.Pushpin(location, null);

        map.entities.push(pushpin);

        pushpin.setOptions({enableHoverStyle: true, enableClickedStyle: true});

        var searchRequest = {
            location: location,
            callback: function (r) {
                document.getElementById("streetName").innerHTML = r.name;
                document.getElementById("_location").value = r.name;
            },
            errorCallback: function (e) {
                alert("Unable to find desired location. Please try again.");
            }
        };

        searchManager.reverseGeocode(searchRequest);

    }

</script>

<script type='text/javascript'
        src='http://www.bing.com/api/maps/mapcontrol?key=Ajm32OrgAV_ptVbxzDsXNYgW0utJJy2rjLAN590nyJOu7aQ4VmZu7AZD-rj6zSas&callback=GetMap'
        async defer></script>

</html>