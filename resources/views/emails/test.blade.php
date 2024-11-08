<!DOCTYPE html>
<html>
<head>
    <title>Laravel Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-green-600">{{ $data['title'] }}</h1>
        </div>
        <div class="mb-6">
            <p class="text-gray-700 text-lg">{{ $data['body'] }}</p>
        </div>
        <div class="text-center">
            <p class="text-gray-500">Thank you</p>
        </div>
    </div>
</body>
</html>