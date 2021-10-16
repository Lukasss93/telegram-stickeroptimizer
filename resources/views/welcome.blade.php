<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} | Telegram Bot</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
          integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
          crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"
          integrity="sha512-NhSC1YmyruXifcj/KFRWoC561YpHpc5Jtzgvbuzx5VozKpWvQ+4nXhPdFgmx8xqexRcpAglTj9sIBWINXa8x5w=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <style>
        body {
            font-family: 'Nunito';
        }

        p {
            margin: 0;
        }
    </style>
</head>
<body class="antialiased">
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">

    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-center pt-8 sm:pt-0">
            <img src="{{ asset('img/banner-transparent.png') }}" alt="logo" class="h-16 w-auto sm:h-20"/>
        </div>

        <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="ml-4 text-lg leading-7 font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-info-circle text-gray-500" style="width: 26px;"></i>
                            Description
                        </div>
                    </div>

                    <div class="ml-12">
                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                            @lang('start.description')
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
                    <div class="flex items-center">
                        <div class="ml-4 text-lg leading-7 font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-code text-gray-500" style="width: 26px;"></i>
                            Version
                        </div>
                    </div>

                    <div class="ml-12">
                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                            <p>Name: {{ config('app.name') }}</p>
                            <p>Username: <a href="https://t.me/{{ config('bot.username') }}"
                                            class="ml-1 underline">
                                    {{ '@'.config('bot.username') }}
                                </a></p>
                            <p>Version: v{{ config('app.version') }}</p>
                            <p>Changelog: <a href="{{ config('app.changelog') }}">Telegraph</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
