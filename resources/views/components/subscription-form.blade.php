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
        <div class="flex flex-col md:flex-row gap-4 items-center mt-2">
            <button id="detect-city"
                class="md:w-auto w-full px-4 py-2 bg-blue-500 text-white rounded-md flex-nowrap">Detect
                City
            </button>
            <select id="country" name="country"
                class="mt-1 block w-full md:max-w-64 pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Select Country</option>
                @foreach ($countries as $country)
                    <option value="{{ $country }}">{{ $country }}</option>
                @endforeach
            </select>
            <select id="city" name="city"
                class="mt-1 block w-full md:max-w-64 pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Select City</option>
            </select>
        </div>
    </div>
    <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">
        Selected City: <span id="selected-city-text"></span>
    </h4>
</div>


<script>
    const detectButton = document.querySelector('#detect-city');
    const status = document.querySelector("#selected-city-text");
    const cities = {!! json_encode($cities->values()->toArray()) !!};
    const countries = {!! json_encode($countries->values()->toArray()) !!};
    const citySelect = document.querySelector('#city');
    const countrySelect = document.querySelector('#country');
    const selection = {
        city: '',
        country: '',
        longitude: '',
        latitude: ''
    };

    function geocodeCity() {
        axios.get('https://ipapi.co/json/')
            .then(data => {
                const locationData = data.data;

                const currentCity = cities.find(city => city.name.toLowerCase() === locationData.city
                    .toLowerCase());

                selection.city = currentCity.name;
                selection.country = currentCity.country
                selection.longitude = currentCity.longitude;
                selection.latitude = currentCity.latitude;

                for (let i = 0; i < countrySelect.options.length; i++) {
                    if (countrySelect.options[i].text.toLowerCase() === selection.country.toLowerCase()) {
                        countrySelect.selectedIndex = i;
                        break;
                    }
                }

                renderCitiesOptions(selection.country);

                for (let i = 0; i < citySelect.options.length; i++) {
                    if (citySelect.options[i].text.toLowerCase() === locationData.city.toLowerCase()) {
                        citySelect.selectedIndex = i;
                        break;
                    }
                }

                displaySelection()
            })
            .catch(error => console.error('Error detecting city:', error));
    }

    const renderCitiesOptions = (country) => {
        citySelect.innerHTML = '<option value="">Select City</option>';
        cities.filter(city => city.country === country).forEach(city => {
            const option = document.createElement('option');
            option.value = city.name;
            option.textContent = city.name;
            citySelect.appendChild(option);
        });
    }

    countrySelect.addEventListener('change', function() {
        selection.country = countrySelect.value;
        renderCitiesOptions(selection.country);
    });

    detectButton?.addEventListener('click', function() {
        geocodeCity();
    });

    const displaySelection = () => {
        status.textContent = `${selection.city}, ${selection.country}`;

        getCurrentWeather().then(weatherData => {
            console.log(weatherData);
        });
    }

    async function getCurrentWeather(latitude, longitude) {
        const url =
            `https://api.open-meteo.com/v1/forecast?latitude=${selection.latitude}&longitude=${selection.longitude}&hourly=apparent_temperature`;
        const response = await fetch(url);
        const weatherData = await response.json();
        return weatherData;
    }
</script>
