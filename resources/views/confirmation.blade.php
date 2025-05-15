<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Confirmation</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md text-center">
    <h1 class="text-2xl font-bold mb-4">Subscription Status</h1>

    @if (session('message'))
        <p class="text-lg {{ session('status') === 'success' ? 'text-green-600' : (session('status') === 'info' ? 'text-blue-600' : 'text-red-600') }}">
            {{ session('message') }}
        </p>
    @else
        <p class="text-gray-600">No status available.</p>
    @endif

    <a href="/weather" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded inline-block">Back to Weather</a>
</div>
</body>
</html>
