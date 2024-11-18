<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaboration Request</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#223662',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-2xl mx-auto my-8 bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="bg-primary p-6 text-center">
            <h1 class="text-3xl font-bold text-white">{{ $companyName }}</h1>
        </div>
        <div class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 p-3">
            <h2 class="text-xl font-semibold text-white text-center">Collaboration Request</h2>
        </div>
        <div class="p-6 space-y-6">
            <div class="space-y-4">
                <p class="text-gray-700">Dear {{ $collaboratorName }},</p>
                <p class="text-gray-700">
                    I hope this email finds you well. I'm reaching out to you personally because I believe your expertise would be invaluable to our research team.
                </p>
                <p class="text-gray-700">
                    I'm excited to invite you to collaborate on a research proposal that I think will pique your interest. The proposal is titled:
                </p>
                <p class="text-xl font-semibold text-primary text-center py-4">
                    "{{ $proposalTitle }}"
                </p>
                <p class="text-gray-700">
                    Your unique insights and experience would significantly enhance the quality and impact of our work. I'm particularly interested in your perspective on [specific aspect related to the collaborator's expertise].
                </p>
                <p class="text-gray-700">
                    If you're interested in joining us on this research journey or would like more information about the proposal, I'd be thrilled to discuss it further. Please don't hesitate to reach out to me directly or click the button below to respond.
                </p>
            </div>
            <div class="text-center">
                <a href="{{ config('app.frontend_url') }}/collaborator/accept?email={{ $collaborator->collaborator_email }}" class="inline-block bg-primary hover:bg-opacity-90 text-white font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">Accept Invitation</a>
            </div>
            <div class="text-center text-sm text-gray-600 mt-6">
                <p>I'm looking forward to the possibility of working together.</p>
                <p class="mt-4">Warm regards,</p>
                <p class="font-semibold">{{ $requesterFullName }}</p>
            </div>
        </div>
        <div class="bg-primary text-white text-center py-4 text-sm">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>