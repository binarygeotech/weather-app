<div
    class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
    <x-application-logo class="block h-20 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
        Welcome to your Weather Companion!
    </h1>

    <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
        Subsribe to get notified about the harmful weather in your city.
    </p>
</div>

<div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 gap-6 lg:gap-8 p-6 lg:p-8">
    <div>
        <div class="flex items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Subscribe
            </h2>
        </div>

        <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
            You can select your city and subscribe to get notified about the harmful weather in your city.
        </p>

        <div class="mt-4">
            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select your
                city:</label>
            <div class="flex flex-col md:flex-row items-center mt-2">
                <button id="detect-city"
                    class="mr-0 md:mr-4 md:w-auto w-full px-4 py-2 bg-blue-500 text-white rounded-md flex-nowrap">Detect
                    City</button>
                <select id="city" name="city"
                    class="mt-1 block w-full md:max-w-64 pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="new-york">New York</option>
                    <option value="los-angeles">Los Angeles</option>
                    <option value="chicago">Chicago</option>
                    <option value="houston">Houston</option>
                    <option value="miami">Miami</option>
                </select>
            </div>
        </div>
        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">
            Selected City: <span id="selected-city-text"></span>
        </h4>
    </div>
</div>

<script>
    const detectButton = document.querySelector('#detect-city');
    const status = document.querySelector("#selected-city-text");

    function detectCity() {
        axios.get('https://ipapi.co/json/')
            .then(data => {
                const locationData = data.data;

                console.log('City detected:', locationData.city);

                document.getElementById('selected-city-text').innerText = locationData.city;

                /** const citySelect = document.getElementById('city');
                for (let i = 0; i < citySelect.options.length; i++) {
                    if (citySelect.options[i].text.toLowerCase() === city.toLowerCase()) {
                        citySelect.selectedIndex = i;
                        break;
                    }
                } **/
            })
            .catch(error => console.error('Error detecting city:', error));
    }

    function getCityFromLocation() {

        const success = (position) => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            status.textContent = `Latitude: ${latitude} °, Longitude: ${longitude} °`;
        }

        const error = () => {
            status.textContent = "Unable to retrieve your location";
        }

        if (!navigator.geolocation) {
            status.textContent = "Geolocation is not supported by your browser";
        } else {
            status.textContent = "Locating…";
            navigator.geolocation.getCurrentPosition(success, error);
        }
    }

    detectButton?.addEventListener('click', function() {
        getCityFromLocation();
    });
</script>
