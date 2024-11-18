<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Request Response</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .float { animation: float 6s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-400 to-red-500 p-6 md:p-12">
    <div class="max-w-4xl w-full mx-auto" x-data="{ response: '' }">
        <div class="bg-gradient-to-r from-blue-800 to-blue-500 rounded-lg shadow-2xl overflow-hidden md:grid md:grid-cols-2 float">
            <!-- Left side with title and description -->
            <div class="p-8 text-white space-y-6">
                <h1 class="text-3xl font-bold">Review Request Response</h1>
                <div class="space-y-3">
                    <p class="text-white/90"><span class="font-semibold">Proposal Title:</span> {{ $assignment->proposal->proposal_title }}</p>
                    <p class="text-white/90"><span class="font-semibold">Start Time:</span> {{ $assignment->start_time }}</p>
                    <p class="text-white/90"><span class="font-semibold">End Time:</span> {{ $assignment->end_time }}</p>
                    <p class="text-white/90"><span class="font-semibold">Status:</span> {{ $assignment->request_status }}</p>
                </div>
            </div>

            <!-- Right side with form -->
            <div class="bg-white p-8 shadow-[0_0_50px_rgba(0,0,0,0.1)] relative">
                <form action="{{ url('/review-request/' . $assignment->id . '/response') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center space-x-2 cursor-pointer group">
                                <input type="radio" name="response" value="accepted" required x-model="response"
                                    class="w-5 h-5 text-green-600 border-gray-300 focus:ring-green-500 transition-all duration-300 ease-in-out transform group-hover:scale-110">
                                <span class="text-lg group-hover:text-green-600 transition-colors duration-300">Accept</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer group">
                                <input type="radio" name="response" value="rejected" required x-model="response"
                                    class="w-5 h-5 text-red-600 border-gray-300 focus:ring-red-500 transition-all duration-300 ease-in-out transform group-hover:scale-110">
                                <span class="text-lg group-hover:text-red-600 transition-colors duration-300">Reject</span>
                            </label>
                        </div>

                        <div>
                            <textarea name="comment" placeholder="Optional comment"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 ease-in-out"
                                rows="4"></textarea>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-800 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        x-bind:disabled="!response"
                        x-bind:class="{ 'bg-green-600 hover:bg-green-700': response === 'accepted', 'bg-red-600 hover:bg-red-700': response === 'rejected' }">
                        Submit Response
                    </button>
                </form>
            </div>
        </div>

        <!-- Enhanced mirror reflection effect -->
        <div class="mt-1 mx-4">
            <div class="h-32 bg-gradient-to-b from-blue-600/30 to-transparent rounded-b-lg blur-md transform scale-y-[-0.5] translate-y-[-20px] opacity-60"></div>
        </div>
    </div>
</body>
</html>