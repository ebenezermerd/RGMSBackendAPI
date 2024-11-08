<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <style>
            .custom-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 10px 20px rgba(0, 0, 0, 0.1);
            }
            .custom-text {
                font-family: 'Poppins', sans-serif;
                transition: transform 0.3s ease, color 0.3s ease;
            }
            .custom-text:hover {
                transform: scale(1.1);
                color: #ffeb3b;
            }
        </style>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    </head>
    <body class="flex items-center justify-center h-screen custom-bg text-white overflow-hidden">
        <div class="text-center text-2xl font-bold animate-bounce custom-text">
            Addis Ababa Science And Technology University Research Grant Management System
        </div>
    </body>
</html>
