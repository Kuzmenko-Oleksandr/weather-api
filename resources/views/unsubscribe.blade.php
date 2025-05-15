<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md text-center">
    <h1 class="text-2xl font-bold mb-4">Unsubscribe from Weather Updates</h1>

    <!-- Email input field -->
    <input type="email" id="email" placeholder="Enter your email" class="border p-2 w-full mb-4" required>

    <!-- Display result messages (success or error) -->
    <div id="result" class="mb-4 text-red-500"></div>

    <!-- Unsubscribe button -->
    <button onclick="getTokenAndUnsubscribe()" class="bg-red-500 text-white px-4 py-2 rounded w-full">Unsubscribe</button>
</div>

<script>
    /**
     * Function to handle the unsubscribe process.
     * It fetches the token based on the provided email and performs the unsubscribe request.
     */
    async function getTokenAndUnsubscribe() {
        const email = document.getElementById('email').value; // Get the entered email
        const resultDiv = document.getElementById('result'); // Result div for feedback

        try {
            // Fetch the token based on email
            const response = await fetch('/api/get-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email })
            });

            // Read the response text
            const text = await response.text();
            console.log('Response Text:', text); // Debugging log

            let data;

            // Try parsing the response as JSON
            try {
                data = JSON.parse(text);
            } catch (error) {
                console.error('Error parsing JSON:', error);
                console.error('Received response:', text);
                resultDiv.innerHTML = `<p class="text-red-500">Unexpected response format. Expected JSON but received HTML.</p>`;
                return;
            }

            // If the response is not OK, display the error message
            if (!response.ok) {
                resultDiv.innerHTML = `<p class="text-red-500">${data.message}</p>`;
                return;
            }

            const token = data.token; // Extract the token

            // Send the unsubscribe request using the token
            const unsubscribeResponse = await fetch(`/api/unsubscribe/${token}`, {
                method: 'GET',
            });

            const unsubscribeText = await unsubscribeResponse.text();
            console.log('Unsubscribe Response:', unsubscribeText); // Debugging log

            let unsubscribeData;

            // Try parsing the unsubscribe response as JSON
            try {
                unsubscribeData = JSON.parse(unsubscribeText);
            } catch (error) {
                console.error('Error parsing unsubscribe JSON:', error);
                resultDiv.innerHTML = `<p class="text-red-500">Unexpected response format on unsubscribe.</p>`;
                return;
            }

            // Check the unsubscribe response status and display appropriate message
            if (!unsubscribeResponse.ok) {
                resultDiv.innerHTML = `<p class="text-red-500">${unsubscribeData.message}</p>`;
            } else {
                resultDiv.innerHTML = `<p class="text-green-500">Successfully unsubscribed!</p>`;
            }

        } catch (error) {
            console.error('Error:', error);
            resultDiv.innerHTML = `<p class="text-red-500">${error.message}</p>`;
        }
    }

</script>

</body>
</html>
