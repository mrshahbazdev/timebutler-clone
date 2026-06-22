<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TimeCheck') }} - {{ $pageTitle ?? __('Login') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .auth-gradient {
            background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 50%, #3b82f6 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
        }
        .input-custom {
            transition: all 0.2s ease;
        }
        .input-custom:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="h-full bg-gray-50 antialiased">
    <div class="min-h-full flex">
        {{-- Left branding panel (hidden on mobile) --}}
        <div class="hidden lg:flex lg:w-1/2 auth-gradient relative overflow-hidden">
            {{-- Floating shapes --}}
            <div class="floating-shape bg-white" style="width: 300px; height: 300px; top: -50px; left: -50px; animation-delay: 0s;"></div>
            <div class="floating-shape bg-white" style="width: 200px; height: 200px; bottom: 100px; right: -30px; animation-delay: 2s;"></div>
            <div class="floating-shape bg-white" style="width: 150px; height: 150px; top: 40%; left: 60%; animation-delay: 4s;"></div>

            <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                {{-- Logo & brand --}}
                <div>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-white text-2xl font-bold tracking-tight">TimeCheck</span>
                    </div>
                </div>

                {{-- Main heading --}}
                <div class="space-y-8">
                    <div>
                        <h1 class="text-4xl font-extrabold text-white leading-tight">
                            {{ app()->getLocale() === 'de' ? 'Urlaubsverwaltung' : 'Vacation Management' }}<br>
                            <span class="text-blue-200">{{ app()->getLocale() === 'de' ? 'leicht gemacht.' : 'made simple.' }}</span>
                        </h1>
                        <p class="mt-4 text-blue-100 text-lg leading-relaxed max-w-md">
                            {{ app()->getLocale() === 'de'
                                ? 'Verwalten Sie Urlaub, Abwesenheiten und Teamkalender an einem Ort.'
                                : 'Manage vacations, absences, and team calendars in one place.' }}
                        </p>
                    </div>

                    {{-- Feature highlights --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="feature-icon bg-white/15 backdrop-blur-sm">
                                <svg class="h-5 w-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ app()->getLocale() === 'de' ? 'Teamkalender' : 'Team Calendar' }}</p>
                                <p class="text-blue-200 text-xs">{{ app()->getLocale() === 'de' ? 'Alle Abwesenheiten im Blick' : 'All absences at a glance' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="feature-icon bg-white/15 backdrop-blur-sm">
                                <svg class="h-5 w-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ app()->getLocale() === 'de' ? 'Genehmigungsworkflow' : 'Approval Workflow' }}</p>
                                <p class="text-blue-200 text-xs">{{ app()->getLocale() === 'de' ? 'Einfach beantragen & genehmigen' : 'Simple request & approve' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="feature-icon bg-white/15 backdrop-blur-sm">
                                <svg class="h-5 w-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ app()->getLocale() === 'de' ? 'Deutsche Feiertage' : 'German Holidays' }}</p>
                                <p class="text-blue-200 text-xs">{{ app()->getLocale() === 'de' ? '16 Bundesländer unterstützt' : '16 federal states supported' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between">
                    <p class="text-blue-200 text-sm">&copy; {{ date('Y') }} TimeCheck</p>
                    {{-- Language switcher --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ route('locale.switch', 'de') }}" class="px-2 py-1 rounded text-xs font-medium {{ app()->getLocale() === 'de' ? 'bg-white/20 text-white' : 'text-blue-200 hover:text-white' }} transition-colors">DE</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-1 rounded text-xs font-medium {{ app()->getLocale() === 'en' ? 'bg-white/20 text-white' : 'text-blue-200 hover:text-white' }} transition-colors">EN</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right form panel --}}
        <div class="w-full lg:w-1/2 flex flex-col">
            {{-- Mobile header --}}
            <div class="lg:hidden auth-gradient px-6 py-8 text-center">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <div class="h-9 w-9 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-white text-xl font-bold">TimeCheck</span>
                </div>
                <p class="text-blue-100 text-sm">
                    {{ app()->getLocale() === 'de'
                        ? 'Urlaubsverwaltung leicht gemacht'
                        : 'Vacation management made simple' }}
                </p>
                {{-- Mobile language switcher --}}
                <div class="flex items-center justify-center gap-2 mt-4">
                    <a href="{{ route('locale.switch', 'de') }}" class="px-3 py-1 rounded text-xs font-medium {{ app()->getLocale() === 'de' ? 'bg-white/20 text-white' : 'text-blue-200 hover:text-white' }} transition-colors">DE</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1 rounded text-xs font-medium {{ app()->getLocale() === 'en' ? 'bg-white/20 text-white' : 'text-blue-200 hover:text-white' }} transition-colors">EN</a>
                </div>
            </div>

            {{-- Form content area --}}
            <div class="flex-1 flex items-center justify-center px-6 py-12 sm:px-12">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>

            {{-- Mobile footer --}}
            <div class="lg:hidden py-4 text-center">
                <p class="text-gray-400 text-xs">&copy; {{ date('Y') }} TimeCheck</p>
            </div>
        </div>
    </div>
</body>
</html>
