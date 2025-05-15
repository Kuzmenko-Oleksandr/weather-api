<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast & Subscription</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md">

    <h1 class="text-2xl font-bold mb-4 text-center">Weather Search & Subscription</h1>
    <input
        type="email"
        id="email"
        class="border p-2 w-full mb-2"
        placeholder="Enter your email"
        aria-label="Email Input">

    <input
        type="text"
        id="search"
        class="border p-2 w-full mb-2"
        placeholder="Enter city"
        aria-label="City Search Input">

    <div id="suggestions" class="mt-2 text-gray-500">
        Start typing to see suggestions...
    </div>

    <select
        id="frequency"
        class="border p-2 w-full mb-2"
        aria-label="Frequency Selection">
        <option value="hourly">Hourly</option>
        <option value="daily">Daily</option>
    </select>

    <button
        onclick="subscribe()"
        class="bg-blue-500 text-white px-4 py-2 rounded w-full"
        aria-label="Subscribe Button">
        Subscribe
    </button>

    <a href="/unsubscribe" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded inline-block">Go to Unsubscribe page</a>


    <div id="result" class="mt-4 text-center"></div>
</div>

<script>
    let debounceTimeout;  // Holds the debounce timer
    let selectedCity = ''; // Holds the selected city name
    const suggestionsDiv = document.getElementById('suggestions'); // Suggestions container
    const searchInput = document.getElementById('search'); // City search input
    const resultDiv = document.getElementById('result'); // Result display container

    /**
     * Debounce function to delay API requests
     * @param {Function} func - The function to debounce
     * @param {number} delay - Delay in milliseconds
     * @returns {Function}
     */
    const debounce = (func, delay) => {
        return (...args) => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => func(...args), delay);
        };
    };

    /**
     * Fetches city suggestions based on user input
     * @param {string} query - The city query string
     */
    const fetchCities = async (query) => {
        try {
            const response = await fetch(`/get-cities?query=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.length > 0) {
                suggestionsDiv.innerHTML = data
                    .map(city => `<div class="p-1 cursor-pointer hover:bg-gray-200" onclick="selectCity('${city.name}')">${city.name} - ${city.region}</div>`)
                    .join('');
            } else {
                suggestionsDiv.innerHTML = `<p>No suggestions found.</p>`;
            }

        } catch (err) {
            console.error('Failed to load cities:', err);
            suggestionsDiv.innerHTML = `<p>Error loading suggestions.</p>`;
        }
    };

    /**
     * Handles the selection of a city
     * @param {string} city - The selected city name
     */
    function selectCity(city) {
        searchInput.value = city;
        selectedCity = city;
        suggestionsDiv.innerHTML = '';  // Clear suggestions after selection
    }

    /**
     * Debounced function to handle city input changes
     */
    searchInput.addEventListener('input', debounce(() => {
        const query = searchInput.value.trim();
        if (query.length > 1) {
            fetchCities(query);
        } else {
            suggestionsDiv.innerHTML = `<p>Start typing to see suggestions...</p>`;
        }
    }, 300));

    /**
     * Handles subscription request
     */
    async function subscribe() {
        const email = document.getElementById('email').value;
        const frequency = document.getElementById('frequency').value;

        // Check if a city has been selected
        if (!selectedCity) {
            resultDiv.innerText = 'Please select a city from the suggestions.';
            return;
        }

        try {
            const response = await fetch('/api/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, city: selectedCity, frequency })
            });

            const data = await response.json();

            if (response.ok) {
                resultDiv.innerHTML = `<p class="text-green-500">${data.message}</p>`;
            } else {
                resultDiv.innerHTML = `<p class="text-red-500">${data.message || 'Error occurred'}</p>`;
            }

        } catch (error) {
            console.error('Subscription Error:', error);
            resultDiv.innerHTML = `<p class="text-red-500">Error: ${error.message}</p>`;
        }
    }
</script>

</body>
</html>
