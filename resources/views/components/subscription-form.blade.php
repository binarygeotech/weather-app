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
                class="md:w-auto w-full px-4 py-2 bg-gray-500 text-white rounded-md flex-nowrap">Detect
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
            <button id="add-city" class="md:w-auto w-full px-4 py-2 bg-blue-500 text-white rounded-md flex-nowrap">
                Add City
            </button>
        </div>
    </div>
    <div class="mt-4 lg:max-w-[50%]">
        <form method="POST" wire:submit="subscribe">
            <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">
                Selected City: <span id="selected-city-text"></span>
            </h4>
            <ul id="selected-cities-list" class="mt-2 list-none p-0">
                <li>None</li>
            </ul>
            <template id="city-template">
                <li class="flex items-center justify-between bg-gray-200 rounded-full px-4 py-2 mb-2">
                    <span class="city-name">City Name</span>
                    <button type="button" class="remove-city text-red-500 hover:text-red-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </li>
            </template>

            <div class="mt-4">
                <label for="threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300">UV Index
                    Threshold:</label>
                <input id="threshold" name="threshold" wire:model="threshold" type="text" value="6"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" />
            </div>

            <input type="hidden" name="selected_cities" id="selected-cities" wire:model.defer="selected_cities"
                value="" />

            <button id="save-btn" type="submit"
                class="md:w-auto w-full px-4 py-2 mt-5 bg-blue-500 text-white rounded-md flex-nowrap">
                Update Subscription
            </button>
        </form>
    </div>


    <script>
        const initialSubscribedCities = [{
                city: 'Lagos',
                country: 'Nigeria',
                longitude: '3.3792',
                latitude: '6.5244'
            },
            {
                city: 'New York',
                country: 'United States',
                longitude: '-74.0060',
                latitude: '40.7128'
            },
            {
                city: 'London',
                country: 'United Kingdom',
                longitude: '-0.1276',
                latitude: '51.5072'
            }
        ];

        const detectButton = document.querySelector('#detect-city');
        const addButton = document.querySelector('#add-city');
        const saveButton = document.querySelector('#save-btn');
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

        const selectionListElement = document.querySelector('#selected-cities-list');
        const selectedCitiesInput = document.querySelector('#selected-cities');

        const selectedCities = [];

        const geocodeCity = () => {
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

        citySelect.addEventListener('change', function() {
            const currentCity = cities.find(city => city.name.toLowerCase() === citySelect.value
                .toLowerCase());

            selection.city = currentCity.name;
            selection.longitude = currentCity.longitude;
            selection.latitude = currentCity.latitude;
        });

        const displaySelection = () => {
            status.textContent = `${selection.city}, ${selection.country}`;
        }

        const getCurrentWeather = async (latitude, longitude) => {
            const url =
                `https://api.open-meteo.com/v1/forecast?latitude=${selection.latitude}&longitude=${selection.longitude}&hourly=apparent_temperature`;
            const response = await fetch(url);
            const weatherData = await response.json();
            return weatherData;
        }

        const setCityInputValue = () => {
            setTimeout(() => {
                selectedCitiesInput.value = JSON.stringify(selectedCities);
                selectedCitiesInput.dispatchEvent(new Event(
                'input')); // required for Livewire to detect changes
            }, 100); // Delay to allow Livewire to update the value
        }

        const renderSelection = () => {
            const itemTemplate = document.querySelector('#city-template').content;

            selectionListElement.innerHTML = '';

            setCityInputValue();

            selectedCities.forEach(city => {
                const clone = document.importNode(itemTemplate, true);
                clone.querySelector('.city-name').textContent = `${city.city}, ${city.country}`;
                clone.querySelector('.remove-city').addEventListener('click', removeCity);
                clone.querySelector('.remove-city').setAttribute('data-city-name', city.city);

                selectionListElement.appendChild(clone);
            });
        }

        const removeCity = (ev) => {
            const button = ev.target.closest('button');
            const listItem = button.closest('li');
            const cityName = button.attributes['data-city-name'].value;

            const index = selectedCities.findIndex(city => city.city === cityName);

            if (index > -1) {
                selectedCities.splice(index, 1);
            }

            renderSelection();
        }

        const updateSaveButton = () => {
            if (initialSubscribedCities.length > 0) {
                saveButton.innerHTML = 'Update Subscription';
            } else {
                saveButton.innerHTML = 'Save Subscription';
            }
        }

        detectButton?.addEventListener('click', function() {
            geocodeCity();
        });

        addButton?.addEventListener('click', function() {
            if (selectedCities.find(city => city.city === selection.city)) {
                return;
            }

            selectedCities.push({
                ...selection
            });

            selection.city = '';
            selection.country = '';
            selection.longitude = '';
            selection.latitude = '';

            citySelect.selectedIndex = 0;
            countrySelect.selectedIndex = 0;

            renderSelection();
        });

        const handleCitySelectionEvent = function(event) {
            const data = event.detail;
            if (data.country && data.city && data.latitude && data.longitude) {
                addButton.disabled = false;
            } else {
                addButton.disabled = true;
            }
        }

        const selectionChangedEvent = new CustomEvent('CitySelectionChanged', {
            detail: selection
        });

        document.addEventListener('CitySelectionChanged', handleCitySelectionEvent);

        (() => {
            initialSubscribedCities.forEach(city => {
                selectedCities.push(city);
            });

            renderSelection();
            updateSaveButton();
        })();

        const selectionWatcher = setInterval(function() {
            document.dispatchEvent(selectionChangedEvent);
        }, 500);
    </script>
