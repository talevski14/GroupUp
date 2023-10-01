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
    <input type="hidden" name="eventIDs" id="eventIDs" value="{{ eventIDs }}">
    {% include "/partials/navigation.view.php" %}
    <div class="mt-3 mx-auto max-w-7xl py-6 sm:px-6 lg:px-8"
         style="width: 2000px;">
        <div>
            <div style="height: 320px; border-bottom: gray solid 2px;">
                <div style="display: flex; align-items: center; justify-content: center; height: 95%; width: 25%; float:left; background: #f0f0f0; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2); transition: 0.3s; border-radius: 5px;">
                    <img src="{{society.banner}}" alt="Banner">
                </div>
                <div style=" height: 95%; width: 50%; float:left;">
                    <div style=" height: 25%;">
                        <h1 style="float:left;" class="ml-10 text-5xl tracking-tight text-gray-900">{{society.name}}</h1>
                    </div>
                    <div style=" height: 15%; width: 40%;" class="ml-10">
                        <a href="/society/{{society.id}}/leave">
                            <button
                                    class="flex w-full justify-center rounded-md bg-red-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                Leave
                            </button>
                        </a>
                    </div>
                    <div style=" height: 45%;" class="scroll">
                        <p class="ml-10 mt-4 text-l tracking-tight text-gray-400">{{society.description}}</p>
                    </div>
                    <div style="height:15%; width: 40%; float:left;" class="mt-2 ml-10">
                        <a href="/society/{{society.id}}/event/create">
                            <button
                                    style="float: left;"
                                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Create an event
                            </button>
                        </a>
                    </div>
                    <div style="height:15%; width: 40%; float:left;" class="mt-2 ml-5">
                        {% if not passed %}
                        <a href="?passed=true">
                            <button
                                    style="float: left;"
                                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Past events
                            </button>
                        </a>
                        {% endif %}
                        {% if passed %}
                        <a href="/society/{{society.id}}">
                            <button
                                    style="float: left;"
                                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                On-going events
                            </button>
                        </a>
                        {% endif %}
                    </div>
                </div>
                <div style=" height: 95%; width: 25%; float:left;">
                    <div style="border-bottom: gray solid 2px; height: 15%;" class="mt-2">
                        <h1 class="text-3xl tracking-tight text-gray-900">Members:</h1>
                    </div>
                    <div style=" height: 70%;" class="scroll">
                        {% for member in members %}
                        <div style="height: 70px; border-bottom: #b1b1b1 solid 1px;" class="mt-2">
                            <div style="width: 15%; height: 100%; float: left;">
                                <img src="{{member['photo']}}" alt="photo" class="h-10 w-10 rounded-full mr-2"
                                     style="float: right;">
                            </div>
                            <div style="width: 85%;  float: left;">
                                <div style=" height: 60%;">
                                    <h1 class="font-bold">{{member['name']}}</h1>
                                </div>
                                <div style=" height: 40%;">
                                    <p class="text-s text-gray-600">@{{member['username']}}</p>
                                </div>
                            </div>
                        </div>
                        {% endfor %}
                    </div>
                    <div style="height: 15%;" class="mb-2">
                        <a href="/society/{{ society.id }}/invite-link">
                            <button
                                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Invite people
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                {% if events %}
                {% for event in events %}
                <div style="height: 630px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.5); border-radius: 10px; background: #f0f0f0">
                    <div style="height: 64px; border-bottom: gray solid 2px;">
                        <div style="width: 100%; height: 100%; float: left;" class="mt-2">
                            <div style="float: left;" class="ml-6">
                                <img src="{{event['creator']['photo']}}" alt="photo" class="h-10 w-10 rounded-full"
                                     style="float: left;">
                            </div>
                            <div style=" float: left" class="ml-5 mt-2">
                                <p class="text-xl text-gray-500"><b>{{ event['creator']['name'] }}</b> posted an event.
                                </p>
                            </div>
                            {% if event['editable'] %}
                            <div style="float: right;" class="mr-3 mt-2">
                                <form method="POST" action="/society/{{society.id}}/event/{{event['id']}}/delete" style="float: left;">
                                    <button type="submit">
                                        <input type="hidden" name="_eventIDDelete" value="{{event['id']}}">
                                        <input type="hidden" name="_societyIDDelete" value="{{society.id}}">
                                        <p class="ml-2 text-gray-400 hover:text-gray-600">
                                            Delete
                                        </p>
                                    </button>
                                </form>
                                <a href="/society/{{society.id}}/event/{{event['id']}}/edit" style="float: left;">
                                    <p class="ml-2 text-gray-400 hover:text-gray-600">
                                        Edit
                                    </p>
                                </a>
                            </div>
                            {% endif %}
                            <div style="float: right;" class="mr-8 mt-2">
                                <p class="text-s text-gray-500"><i>posted on <b>{{ event['creation'] }}</b></i></p>
                            </div>
                        </div>
                    </div>
                    <div style="height: 256px;">
                        <div style=" height: 100%; width: 20%; float:left;" class="ml-6">
                            <div style="border-bottom: gray solid 2px; height: 15%;" class="mt-2">
                                <h1 class="text-xl tracking-tight text-gray-700">Who is going?</h1>
                            </div>
                            <div style="height: 85%;" class="scroll">
                                {% if event['attending'] %}
                                {% for attend in event['attending'] %}
                                <div style="height: 70px; border-bottom: #b1b1b1 solid 1px;" class="mt-2">
                                    <div style="width: 25%; height: 100%; float: left;">
                                        <img src="{{attend['photo']}}" alt="photo" class="h-10 w-10 rounded-full mr-2"
                                             style="float: right;">
                                    </div>
                                    <div style="width: 75%;  float: left;">
                                        <div style=" height: 60%;">
                                            <h1 class="font-bold">{{attend['name']}}</h1>
                                        </div>
                                        <div style=" height: 40%;">
                                            <p class="text-s text-gray-600">@{{attend['username']}}</p>
                                        </div>
                                    </div>
                                </div>
                                {% endfor %}
                                {% endif %}
                                {% if not event['attending'] %}
                                <div class="flex mt-10">
                                    <h1 class="text-s text-gray-400 text-center">Nobody has responded yet.<br>Be the
                                        first!</h1>
                                </div>
                                {% endif %}
                            </div>
                        </div>
                        <div style="height: 100%; width: 75%; float: left;" class="ml-3">
                            <div style="height: 15%; width: 100%;">
                                <p class="text-4xl tracking-tight text-gray-700 ml-10"><b>{{ event['name'] }}</b></p>
                            </div>
                            <div style="height: 80%; width: 100%;">
                                <div style="height: 100%; width: 40%; float:left;" class="scroll mt-1">
                                    <p class="text-s text-gray-500 ml-10 mt-3">{{ event['description'] }}</p>
                                </div>
                                <div style="height: 100%; width: 35%; float: left; border-left: #b1b1b1 solid 1px;"
                                     class="mt-1 ml-2">
                                    <div style="height: 50%; width: 100%; ">
                                        <div style="height: 30%; width: 100%; ">
                                            <h1 class="text-s text-gray-900 ml-3"><b>Where?</b></h1>
                                        </div>
                                        <div style="height: 70%; width: 100%; ">
                                            <p class="text-s text-gray-900 ml-5"><i>{{ event['location'] }} </i></p>
                                        </div>
                                    </div>
                                    <div style="height: 50%; width: 100%; ">
                                        <div style="height: 30%; width: 100%; ">
                                            <h1 class="text-s text-gray-900 ml-3"><b>When?</b></h1>
                                        </div>
                                        <div style="height: 70%; width: 100%;">
                                            <p class="text-s text-gray-900 ml-5"><b>Date:</b> <i>{{ event['date'] }}</i>
                                            </p>
                                            <p class="text-s text-gray-900 ml-5"><b>Time:</b> <i>{{ event['time'] }}</i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div style="height: 100%; width: 23%; float: left; border-left: #b1b1b1 solid 1px;"
                                     class="mt-1 ml-2">
                                    <div style="height: 80%; width: 100%;">
                                        <div style="height: 25%; width: 100%; ">
                                            <h1 class="text-s text-gray-900 ml-3"><b>The weather</b></h1>
                                        </div>
                                        {% if event['weather']['temperature'] %}
                                        <div style="height: 25%; width: 100%" class="ml-3">
                                            <img src="/images/weather/sun.png" alt="sun"
                                                 style="height: 25px; width: 25px; float: left;">
                                            <h1 style="float: left;" class="ml-3">{{event['weather']['temperature']}}
                                                °C</h1>
                                        </div>
                                        {% endif %}

                                        {% if not event['weather']['temperature'] %}
                                        <div style="height: 25%; width: 100%" class="ml-3">
                                            <h1 class="text-s text-gray-400">At this moment we don't have information
                                                about the weather. Come back another day!</h1>
                                        </div>
                                        {% endif %}

                                        {% if event['weather']['rain'] %}
                                        <div style="height: 25%; width: 100%" class="ml-3">
                                            <img src="/images/weather/heavy-rain.png" alt="sun"
                                                 style="height: 25px; width: 25px; float: left;">
                                            <h1 style="float: left;" class="ml-3">{{event['weather']['rain']}} mm</h1>
                                        </div>
                                        {% endif %}

                                        {% if event['weather']['snow'] %}
                                        <div style="height: 25%; width: 100%" class="ml-3">
                                            <img src="/images/weather/snowflake.png" alt="sun"
                                                 style="height: 25px; width: 25px; float: left;">
                                            <h1 style="float: left;" class="ml-3">{{event['weather']['snow']}} mm</h1>
                                        </div>
                                        {% endif %}

                                    </div>
                                    <div style="height: 20%; width: 100%;">
                                        {% if not passed %}
                                        <form action="/society/{{ society.id }}/event/response" method="POST">
                                            <input type="hidden" id="event" name="event" value="{{ event['id'] }}">
                                            {% if not event['attendBool'] %}
                                            <input type="hidden" id="_response" name="_response" value="true">
                                            {% endif %}
                                            {% if event['attendBool'] %}
                                            <input type="hidden" id="_response" name="_response" value="false">
                                            {% endif %}
                                            <button type="submit"
                                                    class="ml-3 flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                {% if not event['attendBool'] %}
                                                I will be there
                                                {% endif %}
                                                {% if event['attendBool'] %}
                                                I changed my mind
                                                {% endif %}
                                            </button>
                                        </form>
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="height:300px; width: 100%;"
                         class="mt-2">
                        <div style="width: 54%; height: 100%; float:left;">
                            <div style="height: 15%; width: 100%;">
                                <p class="text-4xl tracking-tight text-gray-700 ml-6 mt-3 mr-3"
                                   style="border-bottom: gray solid 2px;"><b>Discussion</b></p>
                            </div>
                            <div style="height: 50%; width:95%; border-radius: 5px; background: #fbfbfb; border: #b1b1b1 solid 1px;"
                                 class="scroll ml-6 mt-3">
                                {% if event['comments'] %}
                                {% for comment in event['comments'] %}
                                <div style="border-bottom: #b1b1b1 solid 1px;" class="flex mt-2">
                                    <div style="width: 10%; height: 100%; float: left;">
                                        <img src="{{comment['photo']}}" alt="photo" class="h-10 w-10 rounded-full"
                                             style="float: left;">
                                    </div>
                                    <div style="width: 90%;  height: 100%; float: left;">
                                        <div style="height: 20%;">
                                            <h1 class="text-s text-gray-900">@{{comment['username']}}</h1>
                                        </div>
                                        <div style=" height: 80%;">
                                            <p class="text-s text-gray-600 mt-1 mr-1 mb-2">{{comment['body']}}</p>
                                        </div>
                                    </div>
                                </div>
                                {% endfor %}
                                {% endif %}
                                {% if not event['comments'] %}
                                <div class="flex" style="position: relative; top: 40%; left: 23%;">
                                    <h1 class="text-s text-gray-400">There are no comments. Post one right now!</h1>
                                </div>
                                {% endif %}
                            </div>
                            <div style="height: 20%; width:95%;" class="ml-6 mt-3">
                                <div style="height: 100%; width: 10%; float:left;">
                                    <img src="{{ profileimg }}" alt="photo" class="h-12 w-12 rounded-full"
                                         style="float: right;">
                                </div>
                                <div style="height: 100%; width: 87%; float:left;" class="ml-3">
                                    <form action="/society/{{ society.id }}/event/comment" method="POST">
                                        <input type="hidden" name="_id" value="{{event['id']}}">
                                        <label>
                                            <input type="text"
                                                   {% if passed %} disabled {% endif %}
                                                   name="body"
                                                   style="width: 80%; height: 100%; border-radius: 3px; float: left;"
                                                   class="mt-1.5">
                                        </label>
                                        <button
                                                {% if passed %} disabled {% endif %}
                                                type="submit"
                                                style="float: left; width: 15%;"
                                                class="ml-3 mt-1.5 flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm {% if not passed %} hover:bg-indigo-500 {% endif %} focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                            Post
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div style="width: 45%; height: 100%; float: left;" class="ml-3">
                            <input type="hidden" value="{{ event['lat'] }}" name="lat" id="lat{{event['id']}}">
                            <input type="hidden" value="{{ event['lon'] }}" name="lon" id="lon{{event['id']}}">
                            {% if not event['lat'] %}
                            {% if not event['lon'] %}
                            <div class="flex" style="position: relative; top: 40%; left: 23%;">
                                <h1 class="text-s text-gray-400">There is not a map available for this event.</h1>
                            </div>
                            {% endif %}
                            {% endif %}
                            {% if event['lat'] %}
                            {% if event['lon'] %}
                            <div id="{{event['id']}}"
                                 style="position:relative;width:95%;height:95%;border: #b1b1b1 solid 1px;"></div>
                            {% endif %}
                            {% endif %}
                        </div>
                    </div>
                </div>
                <div style="width: 100%; height: 30px;">

                </div>
                {% endfor %}
                {% endif %}
                {% if not events %}
                <div class="ml-22 mt-24">
                    <h1 class="text-center text-gray-600 text-3xl">Currently, there are no events in this society.</h1>
                </div>
                {% endif %}
            </div>
        </div>
    </div>

</main>
</body>
{% if welcomeMessage %}
<input type="hidden" value="{{welcomeMessage[0]}}" id="msg">
<script>
    message = document.getElementById("msg").value;
    alert(message);
</script>
{% endif %}

<script type='text/javascript'>
    var map;
    var pushpin = null;
    var searchManager = null;

    function createMaps() {
        var eventIDs = document.getElementById("eventIDs").value;
        for (let i = 0; i < eventIDs.split(" ").length; i++) {
            GetMap(eventIDs.split(" ")[i]);
        }
    }

    function GetMap(id) {
        console.log("lat" + id);
        map = new Microsoft.Maps.Map(document.getElementById(id), {
            center: new Microsoft.Maps.Location(document.getElementById("lat" + id).value, document.getElementById("lon" + id).value),
            zoom: 13
        });

        var location = new Microsoft.Maps.Location(document.getElementById("lat" + id).value, document.getElementById("lon" + id).value);

        pushpin = new Microsoft.Maps.Pushpin(location, null);

        map.entities.push(pushpin);

        pushpin.setOptions({enableHoverStyle: true, enableClickedStyle: true});
    }
</script>

<script type='text/javascript'
        src='http://www.bing.com/api/maps/mapcontrol?key=Ajm32OrgAV_ptVbxzDsXNYgW0utJJy2rjLAN590nyJOu7aQ4VmZu7AZD-rj6zSas&callback=createMaps'
        async defer></script>
</html>