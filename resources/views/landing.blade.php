<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ app()->getLocale() === 'de' ? 'TimeButler - Urlaubsverwaltung und Abwesenheitsmanagement für Unternehmen. Deutsche Feiertage, Teamkalender, Genehmigungsworkflow.' : 'TimeButler - Vacation and absence management for businesses. German holidays, team calendar, approval workflow.' }}">
    <title>TimeButler - {{ app()->getLocale() === 'de' ? 'Urlaubsverwaltung leicht gemacht' : 'Vacation Management Made Simple' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 30%, #1e40af 60%, #3b82f6 100%);
        }
        .feature-gradient {
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        }
        .pricing-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.08;
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .btn-glow {
            transition: all 0.3s ease;
        }
        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        }
        .grid-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="bg-white antialiased" x-data="{ mobileMenu: false }">

    {{-- ==================== NAVBAR ==================== --}}
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" x-data="{ scrolled: false }"
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="scrolled ? 'bg-white/95 backdrop-blur-lg shadow-sm border-b border-gray-100' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl flex items-center justify-center" :class="scrolled ? 'bg-blue-600' : 'bg-white/20 backdrop-blur-sm'">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold" :class="scrolled ? 'text-gray-900' : 'text-white'">TimeButler</span>
                </a>

                {{-- Desktop nav --}}
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#features" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-gray-900' : 'text-blue-100 hover:text-white'">
                        {{ app()->getLocale() === 'de' ? 'Funktionen' : 'Features' }}
                    </a>
                    <a href="#pricing" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-gray-900' : 'text-blue-100 hover:text-white'">
                        {{ app()->getLocale() === 'de' ? 'Preise' : 'Pricing' }}
                    </a>
                    <a href="#testimonials" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-gray-900' : 'text-blue-100 hover:text-white'">
                        {{ app()->getLocale() === 'de' ? 'Referenzen' : 'Testimonials' }}
                    </a>
                    <a href="#faq" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-gray-900' : 'text-blue-100 hover:text-white'">
                        FAQ
                    </a>
                </div>

                {{-- Right side --}}
                <div class="hidden lg:flex items-center gap-4">
                    {{-- Language --}}
                    <div class="flex items-center gap-1 rounded-lg px-1 py-0.5" :class="scrolled ? 'bg-gray-100' : 'bg-white/10'">
                        <a href="{{ route('locale.switch', 'de') }}" class="px-2 py-1 rounded text-xs font-semibold transition-colors {{ app()->getLocale() === 'de' ? 'bg-white text-blue-700 shadow-sm' : '' }}" :class="scrolled ? '{{ app()->getLocale() !== 'de' ? 'text-gray-500 hover:text-gray-700' : '' }}' : '{{ app()->getLocale() !== 'de' ? 'text-blue-200 hover:text-white' : '' }}'">DE</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-1 rounded text-xs font-semibold transition-colors {{ app()->getLocale() === 'en' ? 'bg-white text-blue-700 shadow-sm' : '' }}" :class="scrolled ? '{{ app()->getLocale() !== 'en' ? 'text-gray-500 hover:text-gray-700' : '' }}' : '{{ app()->getLocale() !== 'en' ? 'text-blue-200 hover:text-white' : '' }}'">EN</a>
                    </div>
                    <a href="{{ route('login') }}" class="text-sm font-semibold transition-colors" :class="scrolled ? 'text-gray-700 hover:text-gray-900' : 'text-white hover:text-blue-100'">
                        {{ app()->getLocale() === 'de' ? 'Anmelden' : 'Sign In' }}
                    </a>
                    <a href="{{ route('register-company') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 btn-glow shadow-sm">
                        {{ app()->getLocale() === 'de' ? 'Kostenlos starten' : 'Get Started Free' }}
                    </a>
                </div>

                {{-- Mobile menu button --}}
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg" :class="scrolled ? 'text-gray-700' : 'text-white'">
                    <svg x-show="!mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileMenu" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileMenu" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="lg:hidden bg-white border-t border-gray-100 shadow-lg">
            <div class="px-4 py-4 space-y-2">
                <a href="#features" @click="mobileMenu = false" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">{{ app()->getLocale() === 'de' ? 'Funktionen' : 'Features' }}</a>
                <a href="#pricing" @click="mobileMenu = false" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">{{ app()->getLocale() === 'de' ? 'Preise' : 'Pricing' }}</a>
                <a href="#testimonials" @click="mobileMenu = false" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">{{ app()->getLocale() === 'de' ? 'Referenzen' : 'Testimonials' }}</a>
                <a href="#faq" @click="mobileMenu = false" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">FAQ</a>
                <hr class="my-2">
                <div class="flex items-center gap-2 px-4 py-2">
                    <a href="{{ route('locale.switch', 'de') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ app()->getLocale() === 'de' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">DE</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ app()->getLocale() === 'en' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">EN</a>
                </div>
                <a href="{{ route('login') }}" class="block px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ app()->getLocale() === 'de' ? 'Anmelden' : 'Sign In' }}</a>
                <a href="{{ route('register-company') }}" class="block px-4 py-3 rounded-xl text-sm font-semibold text-center text-white bg-blue-600 hover:bg-blue-700">{{ app()->getLocale() === 'de' ? 'Kostenlos starten' : 'Get Started Free' }}</a>
            </div>
        </div>
    </nav>

    {{-- ==================== HERO ==================== --}}
    <section class="hero-gradient relative overflow-hidden min-h-[100vh] flex items-center">
        <div class="grid-pattern absolute inset-0"></div>
        <div class="floating-shape bg-white" style="width: 400px; height: 400px; top: -100px; right: -100px; animation-delay: 0s;"></div>
        <div class="floating-shape bg-white" style="width: 250px; height: 250px; bottom: 50px; left: -50px; animation-delay: 3s;"></div>
        <div class="floating-shape bg-white" style="width: 180px; height: 180px; top: 30%; left: 50%; animation-delay: 5s;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 lg:py-40">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                {{-- Left content --}}
                <div class="text-center lg:text-left animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 px-4 py-1.5 mb-6">
                        <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-sm font-medium text-blue-100">
                            {{ app()->getLocale() === 'de' ? 'Jetzt verfügbar - Kostenlos testen' : 'Now available - Try for free' }}
                        </span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
                        {{ app()->getLocale() === 'de' ? 'Urlaubs-' : 'Vacation' }}<br>
                        {{ app()->getLocale() === 'de' ? 'verwaltung' : 'Management' }}
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-cyan-200">
                            {{ app()->getLocale() === 'de' ? 'leicht gemacht' : 'Made Simple' }}
                        </span>
                    </h1>

                    <p class="mt-6 text-lg sm:text-xl text-blue-100 leading-relaxed max-w-lg mx-auto lg:mx-0">
                        {{ app()->getLocale() === 'de'
                            ? 'Verwalten Sie Urlaub, Abwesenheiten und Teamkalender an einem Ort. Mit deutschen Feiertagen, Genehmigungsworkflows und mehr.'
                            : 'Manage vacations, absences, and team calendars in one place. With German holidays, approval workflows, and more.' }}
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('register-company') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-blue-700 hover:bg-blue-50 btn-glow shadow-lg">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                            </svg>
                            {{ app()->getLocale() === 'de' ? 'Firma registrieren' : 'Register Company' }}
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-white/30 px-6 py-3.5 text-sm font-bold text-white hover:bg-white/10 transition-all">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                            {{ app()->getLocale() === 'de' ? 'Demo ansehen' : 'View Demo' }}
                        </a>
                    </div>

                    {{-- Stats --}}
                    <div class="mt-12 grid grid-cols-3 gap-6 max-w-md mx-auto lg:mx-0">
                        <div>
                            <p class="text-2xl sm:text-3xl font-extrabold text-white">16</p>
                            <p class="text-xs sm:text-sm text-blue-200 mt-1">{{ app()->getLocale() === 'de' ? 'Bundesländer' : 'Federal States' }}</p>
                        </div>
                        <div>
                            <p class="text-2xl sm:text-3xl font-extrabold text-white">9+</p>
                            <p class="text-xs sm:text-sm text-blue-200 mt-1">{{ app()->getLocale() === 'de' ? 'Abwesenheits&shy;arten' : 'Absence Types' }}</p>
                        </div>
                        <div>
                            <p class="text-2xl sm:text-3xl font-extrabold text-white">DE/EN</p>
                            <p class="text-xs sm:text-sm text-blue-200 mt-1">{{ app()->getLocale() === 'de' ? 'Mehrsprachig' : 'Multilingual' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Right: Dashboard preview --}}
                <div class="hidden lg:block animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-400/20 to-cyan-400/20 rounded-3xl blur-2xl"></div>
                        <div class="relative bg-white/10 backdrop-blur-lg rounded-2xl border border-white/20 p-6 shadow-2xl">
                            {{-- Mock dashboard header --}}
                            <div class="flex items-center gap-3 mb-5">
                                <div class="flex gap-1.5">
                                    <div class="h-3 w-3 rounded-full bg-red-400/60"></div>
                                    <div class="h-3 w-3 rounded-full bg-yellow-400/60"></div>
                                    <div class="h-3 w-3 rounded-full bg-green-400/60"></div>
                                </div>
                                <div class="flex-1 h-7 rounded-lg bg-white/10"></div>
                            </div>

                            {{-- Mock vacation balance cards --}}
                            <div class="grid grid-cols-3 gap-3 mb-5">
                                <div class="rounded-xl bg-white/15 p-3 text-center">
                                    <p class="text-2xl font-bold text-white">30</p>
                                    <p class="text-xs text-blue-200 mt-1">{{ app()->getLocale() === 'de' ? 'Gesamt' : 'Total' }}</p>
                                </div>
                                <div class="rounded-xl bg-white/15 p-3 text-center">
                                    <p class="text-2xl font-bold text-green-300">12</p>
                                    <p class="text-xs text-blue-200 mt-1">{{ app()->getLocale() === 'de' ? 'Genommen' : 'Used' }}</p>
                                </div>
                                <div class="rounded-xl bg-white/15 p-3 text-center">
                                    <p class="text-2xl font-bold text-cyan-300">18</p>
                                    <p class="text-xs text-blue-200 mt-1">{{ app()->getLocale() === 'de' ? 'Verbleibend' : 'Remaining' }}</p>
                                </div>
                            </div>

                            {{-- Mock calendar grid --}}
                            <div class="rounded-xl bg-white/10 p-3">
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    @foreach(['Mo','Di','Mi','Do','Fr','Sa','So'] as $d)
                                        <div class="text-center text-xs font-medium text-blue-200 py-1">{{ $d }}</div>
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-7 gap-1">
                                    @for($i = 1; $i <= 28; $i++)
                                        <div class="aspect-square rounded-lg flex items-center justify-center text-xs font-medium
                                            @if(in_array($i, [6,7,13,14,20,21,27,28])) bg-white/5 text-blue-300
                                            @elseif(in_array($i, [15,16,17])) bg-blue-500/40 text-white
                                            @elseif(in_array($i, [22,23])) bg-green-500/40 text-white
                                            @elseif($i == 10) bg-red-500/40 text-white
                                            @else bg-white/10 text-blue-100
                                            @endif">{{ $i }}</div>
                                    @endfor
                                </div>
                                <div class="flex items-center gap-4 mt-3 px-1">
                                    <div class="flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded bg-blue-500/60"></div><span class="text-xs text-blue-200">{{ app()->getLocale() === 'de' ? 'Urlaub' : 'Vacation' }}</span></div>
                                    <div class="flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded bg-green-500/60"></div><span class="text-xs text-blue-200">{{ app()->getLocale() === 'de' ? 'Genehmigt' : 'Approved' }}</span></div>
                                    <div class="flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded bg-red-500/60"></div><span class="text-xs text-blue-200">{{ app()->getLocale() === 'de' ? 'Krank' : 'Sick' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wave divider --}}
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 120V60C240 20 480 0 720 20C960 40 1200 80 1440 60V120H0Z" fill="white"/>
            </svg>
        </div>
    </section>

    {{-- ==================== TRUSTED BY ==================== --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-8">
                {{ app()->getLocale() === 'de' ? 'Entwickelt für deutsche Unternehmen' : 'Built for German Businesses' }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12">
                <div class="flex items-center gap-2 text-gray-300">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                    <span class="text-lg font-bold">{{ app()->getLocale() === 'de' ? 'KMU-gerecht' : 'SMB Ready' }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-300">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                    <span class="text-lg font-bold">{{ app()->getLocale() === 'de' ? 'DSGVO-konform' : 'GDPR Compliant' }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-300">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" /></svg>
                    <span class="text-lg font-bold">{{ app()->getLocale() === 'de' ? 'Made in Germany' : 'Made in Germany' }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-300">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-lg font-bold">{{ app()->getLocale() === 'de' ? 'Schnell einsatzbereit' : 'Quick Setup' }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== FEATURES ==================== --}}
    <section id="features" class="py-20 lg:py-28 feature-gradient">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">
                    {{ app()->getLocale() === 'de' ? 'Funktionen' : 'Features' }}
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">
                    {{ app()->getLocale() === 'de' ? 'Alles, was Sie brauchen' : 'Everything You Need' }}
                </h2>
                <p class="mt-4 text-lg text-gray-500">
                    {{ app()->getLocale() === 'de'
                        ? 'Eine Plattform für die komplette Abwesenheitsverwaltung Ihres Unternehmens'
                        : 'One platform for your complete absence management needs' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1: Vacation --}}
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 card-hover">
                    <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center mb-5">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Urlaubsverwaltung' : 'Vacation Management' }}</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? 'Urlaub beantragen oder blockieren, Restanspruch im Blick behalten, automatische Berechnung der Urlaubstage.'
                            : 'Request or block vacation, track remaining balance, automatic calculation of vacation days.' }}
                    </p>
                </div>

                {{-- Feature 2: Holidays --}}
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 card-hover">
                    <div class="h-12 w-12 rounded-xl bg-green-100 flex items-center justify-center mb-5">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Deutsche Feiertage' : 'German Holidays' }}</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? 'Alle 16 Bundesländer mit Feiertagen und Schulferien. Automatischer Import von schulferien.org.'
                            : 'All 16 federal states with public holidays and school breaks. Auto-import from schulferien.org.' }}
                    </p>
                </div>

                {{-- Feature 3: Team Calendar --}}
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 card-hover">
                    <div class="h-12 w-12 rounded-xl bg-purple-100 flex items-center justify-center mb-5">
                        <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Teamkalender' : 'Team Calendar' }}</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? 'Übersichtlicher Kalender mit farbcodierten Abwesenheiten aller Teammitglieder. PDF-Export für den Druck.'
                            : 'Clear calendar with color-coded absences for all team members. PDF export for printing.' }}
                    </p>
                </div>

                {{-- Feature 4: Approval --}}
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 card-hover">
                    <div class="h-12 w-12 rounded-xl bg-amber-100 flex items-center justify-center mb-5">
                        <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Genehmigungsworkflow' : 'Approval Workflow' }}</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? 'Abteilungsleiter genehmigen oder lehnen Anträge ab. Automatische E-Mail-Benachrichtigungen bei jedem Schritt.'
                            : 'Department heads approve or reject requests. Automatic email notifications at every step.' }}
                    </p>
                </div>

                {{-- Feature 5: Reports --}}
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 card-hover">
                    <div class="h-12 w-12 rounded-xl bg-rose-100 flex items-center justify-center mb-5">
                        <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Berichte & Export' : 'Reports & Export' }}</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? 'Umfangreiche Berichte zu Abwesenheiten und Zeiterfassung. Export als PDF oder Excel.'
                            : 'Comprehensive absence and time tracking reports. Export as PDF or Excel.' }}
                    </p>
                </div>

                {{-- Feature 6: Multi-language --}}
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 card-hover">
                    <div class="h-12 w-12 rounded-xl bg-cyan-100 flex items-center justify-center mb-5">
                        <svg class="h-6 w-6 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Deutsch & Englisch' : 'German & English' }}</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? 'Vollständig in Deutsch und Englisch verfügbar. Jeder Benutzer kann seine bevorzugte Sprache wählen.'
                            : 'Fully available in German and English. Each user can choose their preferred language.' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== HOW IT WORKS ==================== --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">
                    {{ app()->getLocale() === 'de' ? 'So funktioniert es' : 'How It Works' }}
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ app()->getLocale() === 'de' ? 'In 3 Schritten startklar' : 'Get Started in 3 Steps' }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                <div class="text-center">
                    <div class="mx-auto h-16 w-16 rounded-2xl bg-blue-600 flex items-center justify-center mb-5 shadow-lg shadow-blue-600/30">
                        <span class="text-2xl font-extrabold text-white">1</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Firma registrieren' : 'Register Company' }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ app()->getLocale() === 'de'
                            ? 'Erstellen Sie Ihr Firmenkonto mit Bundesland-Auswahl und Administrator-Profil.'
                            : 'Create your company account with federal state selection and admin profile.' }}
                    </p>
                </div>
                <div class="text-center">
                    <div class="mx-auto h-16 w-16 rounded-2xl bg-blue-600 flex items-center justify-center mb-5 shadow-lg shadow-blue-600/30">
                        <span class="text-2xl font-extrabold text-white">2</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Team einrichten' : 'Set Up Team' }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ app()->getLocale() === 'de'
                            ? 'Abteilungen anlegen, Mitarbeiter hinzufügen und Urlaubskontingente zuweisen.'
                            : 'Create departments, add employees, and assign vacation quotas.' }}
                    </p>
                </div>
                <div class="text-center">
                    <div class="mx-auto h-16 w-16 rounded-2xl bg-blue-600 flex items-center justify-center mb-5 shadow-lg shadow-blue-600/30">
                        <span class="text-2xl font-extrabold text-white">3</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ app()->getLocale() === 'de' ? 'Urlaub verwalten' : 'Manage Vacations' }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ app()->getLocale() === 'de'
                            ? 'Mitarbeiter beantragen Urlaub, Vorgesetzte genehmigen - alles automatisiert.'
                            : 'Employees request vacation, managers approve - everything automated.' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== PRICING ==================== --}}
    <section id="pricing" class="py-20 lg:py-28 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">
                    {{ app()->getLocale() === 'de' ? 'Preise' : 'Pricing' }}
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ app()->getLocale() === 'de' ? 'Einfache, transparente Preise' : 'Simple, Transparent Pricing' }}
                </h2>
                <p class="mt-4 text-lg text-gray-500">
                    {{ app()->getLocale() === 'de'
                        ? 'Keine versteckten Kosten. Jederzeit kündbar.'
                        : 'No hidden fees. Cancel anytime.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                {{-- Starter --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 card-hover">
                    <h3 class="text-lg font-bold text-gray-900">Starter</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'de' ? 'Für kleine Teams' : 'For small teams' }}</p>
                    <div class="mt-6">
                        <span class="text-4xl font-extrabold text-gray-900">{{ app()->getLocale() === 'de' ? '0 €' : '€0' }}</span>
                        <span class="text-sm text-gray-500 ml-1">/ {{ app()->getLocale() === 'de' ? 'Monat' : 'month' }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale() === 'de' ? 'Bis zu 5 Benutzer' : 'Up to 5 users' }}</p>
                    <ul class="mt-8 space-y-3">
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Urlaubsverwaltung' : 'Vacation management' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Teamkalender' : 'Team calendar' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Deutsche Feiertage' : 'German holidays' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'E-Mail-Benachrichtigungen' : 'Email notifications' }}
                        </li>
                    </ul>
                    <a href="{{ route('register-company') }}" class="mt-8 block w-full text-center rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 hover:border-gray-300 hover:bg-gray-50 transition-all">
                        {{ app()->getLocale() === 'de' ? 'Kostenlos starten' : 'Get Started Free' }}
                    </a>
                </div>

                {{-- Professional --}}
                <div class="bg-white rounded-2xl p-8 shadow-xl border-2 border-blue-600 card-hover relative">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                        <span class="inline-block bg-blue-600 text-white text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full shadow-lg">
                            {{ app()->getLocale() === 'de' ? 'Beliebt' : 'Popular' }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Professional</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'de' ? 'Für wachsende Teams' : 'For growing teams' }}</p>
                    <div class="mt-6">
                        <span class="text-4xl font-extrabold text-gray-900">{{ app()->getLocale() === 'de' ? '29 €' : '€29' }}</span>
                        <span class="text-sm text-gray-500 ml-1">/ {{ app()->getLocale() === 'de' ? 'Monat' : 'month' }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale() === 'de' ? 'Bis zu 35 Benutzer' : 'Up to 35 users' }}</p>
                    <ul class="mt-8 space-y-3">
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Alles aus Starter' : 'Everything in Starter' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'PDF/Excel-Berichte' : 'PDF/Excel reports' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Zeiterfassung' : 'Time tracking' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Abteilungsverwaltung' : 'Department management' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Schulferien-Import' : 'School break import' }}
                        </li>
                    </ul>
                    <a href="{{ route('register-company') }}" class="mt-8 block w-full text-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 btn-glow shadow-sm">
                        {{ app()->getLocale() === 'de' ? 'Jetzt starten' : 'Get Started' }}
                    </a>
                </div>

                {{-- Enterprise --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 card-hover">
                    <h3 class="text-lg font-bold text-gray-900">Enterprise</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'de' ? 'Für große Unternehmen' : 'For large organizations' }}</p>
                    <div class="mt-6">
                        <span class="text-4xl font-extrabold text-gray-900">{{ app()->getLocale() === 'de' ? '99 €' : '€99' }}</span>
                        <span class="text-sm text-gray-500 ml-1">/ {{ app()->getLocale() === 'de' ? 'Monat' : 'month' }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale() === 'de' ? 'Unbegrenzte Benutzer' : 'Unlimited users' }}</p>
                    <ul class="mt-8 space-y-3">
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Alles aus Professional' : 'Everything in Professional' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Prioritäts-Support' : 'Priority support' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'SSO-Integration' : 'SSO integration' }}
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            {{ app()->getLocale() === 'de' ? 'Individuelle Anpassungen' : 'Custom integrations' }}
                        </li>
                    </ul>
                    <a href="{{ route('register-company') }}" class="mt-8 block w-full text-center rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 hover:border-gray-300 hover:bg-gray-50 transition-all">
                        {{ app()->getLocale() === 'de' ? 'Kontakt aufnehmen' : 'Contact Sales' }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== TESTIMONIALS ==================== --}}
    <section id="testimonials" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">
                    {{ app()->getLocale() === 'de' ? 'Referenzen' : 'Testimonials' }}
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ app()->getLocale() === 'de' ? 'Was unsere Kunden sagen' : 'What Our Customers Say' }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 rounded-2xl p-7 border border-gray-100">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? '"TimeButler hat unsere Urlaubsplanung komplett vereinfacht. Die automatische Feiertags-Integration für NRW spart uns enorm viel Zeit."'
                            : '"TimeButler completely simplified our vacation planning. The automatic holiday integration for NRW saves us a lot of time."' }}
                    </p>
                    <div class="mt-5 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold">MS</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Maria Schmidt</p>
                            <p class="text-xs text-gray-500">HR Manager, TechCorp GmbH</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-7 border border-gray-100">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? '"Der Teamkalender ist fantastisch. Auf einen Blick sehen wir, wer da ist und wer nicht. Die PDF-Export-Funktion nutzen wir täglich."'
                            : '"The team calendar is fantastic. At a glance we can see who\'s in and who\'s not. We use the PDF export feature daily."' }}
                    </p>
                    <div class="mt-5 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-sm font-bold">TW</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Thomas Weber</p>
                            <p class="text-xs text-gray-500">{{ app()->getLocale() === 'de' ? 'Geschäftsführer' : 'Managing Director' }}, Weber & Partner</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-7 border border-gray-100">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? '"Endlich eine Lösung, die die deutschen Schulferien kennt! Die Genehmigungsworkflows sind genau das, was wir gebraucht haben."'
                            : '"Finally a solution that knows German school holidays! The approval workflows are exactly what we needed."' }}
                    </p>
                    <div class="mt-5 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white text-sm font-bold">LK</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Lisa Krause</p>
                            <p class="text-xs text-gray-500">{{ app()->getLocale() === 'de' ? 'Teamleiterin' : 'Team Lead' }}, Digital Solutions AG</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== FAQ ==================== --}}
    <section id="faq" class="py-20 lg:py-28 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">FAQ</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ app()->getLocale() === 'de' ? 'Häufig gestellte Fragen' : 'Frequently Asked Questions' }}
                </h2>
            </div>

            <div class="space-y-4" x-data="{ active: null }">
                @php
                $faqs = app()->getLocale() === 'de' ? [
                    ['q' => 'Welche Bundesländer werden unterstützt?', 'a' => 'Alle 16 deutschen Bundesländer mit ihren jeweiligen Feiertagen und Schulferien. Die Feiertage werden automatisch von schulferien.org importiert.'],
                    ['q' => 'Können Mitarbeiter selbst Urlaub beantragen?', 'a' => 'Ja! Mitarbeiter können Urlaub beantragen oder blockieren, ihren Resturlaub einsehen und den Teamkalender nutzen. Blockierter Urlaub kann auch wieder storniert werden.'],
                    ['q' => 'Wie funktioniert der Genehmigungsworkflow?', 'a' => 'Wenn ein Mitarbeiter Urlaub beantragt, wird der zuständige Abteilungsleiter per E-Mail benachrichtigt. Er kann den Antrag genehmigen oder ablehnen. Der Mitarbeiter wird über die Entscheidung per E-Mail informiert.'],
                    ['q' => 'Welche Abwesenheitsarten gibt es?', 'a' => 'Urlaub, Krankheit mit/ohne Attest, Kind krank, Homeoffice, Dienstreise, Elternzeit, Sonderurlaub und Weiterbildung. Jede Art hat eine eigene Farbe im Kalender.'],
                    ['q' => 'Kann ich Berichte exportieren?', 'a' => 'Ja, Abwesenheits- und Zeiterfassungsberichte können als PDF oder Excel-Datei exportiert werden. Der Teamkalender kann ebenfalls als PDF gedruckt werden.'],
                ] : [
                    ['q' => 'Which federal states are supported?', 'a' => 'All 16 German federal states with their respective public holidays and school breaks. Holidays are automatically imported from schulferien.org.'],
                    ['q' => 'Can employees request vacation themselves?', 'a' => 'Yes! Employees can request or block vacation, view their remaining balance, and use the team calendar. Blocked vacation can also be cancelled.'],
                    ['q' => 'How does the approval workflow work?', 'a' => 'When an employee requests vacation, their department head is notified via email. They can approve or reject the request. The employee is informed about the decision via email.'],
                    ['q' => 'What absence types are available?', 'a' => 'Vacation, sick leave with/without note, sick child, home office, business trip, parental leave, special leave, and continuing education. Each type has its own color in the calendar.'],
                    ['q' => 'Can I export reports?', 'a' => 'Yes, absence and time tracking reports can be exported as PDF or Excel files. The team calendar can also be printed as a PDF.'],
                ];
                @endphp

                @foreach($faqs as $index => $faq)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button @click="active = active === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between px-6 py-4 text-left">
                        <span class="text-sm font-semibold text-gray-900">{{ $faq['q'] }}</span>
                        <svg class="h-5 w-5 text-gray-400 flex-shrink-0 transition-transform duration-200" :class="active === {{ $index }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="active === {{ $index }}" x-cloak x-collapse>
                        <div class="px-6 pb-4">
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== CTA ==================== --}}
    <section class="py-20 lg:py-28 pricing-gradient relative overflow-hidden">
        <div class="floating-shape bg-white" style="width: 300px; height: 300px; top: -80px; right: -80px; opacity: 0.06;"></div>
        <div class="floating-shape bg-white" style="width: 200px; height: 200px; bottom: -40px; left: -40px; opacity: 0.06; animation-delay: 3s;"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight">
                {{ app()->getLocale() === 'de' ? 'Bereit, loszulegen?' : 'Ready to Get Started?' }}
            </h2>
            <p class="mt-4 text-lg text-blue-100 max-w-2xl mx-auto">
                {{ app()->getLocale() === 'de'
                    ? 'Registrieren Sie Ihre Firma kostenlos und starten Sie noch heute mit der Urlaubsverwaltung.'
                    : 'Register your company for free and start managing vacations today.' }}
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register-company') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-8 py-4 text-sm font-bold text-blue-700 hover:bg-blue-50 btn-glow shadow-lg">
                    {{ app()->getLocale() === 'de' ? 'Kostenlos registrieren' : 'Register for Free' }}
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-white/30 px-8 py-4 text-sm font-bold text-white hover:bg-white/10 transition-all">
                    {{ app()->getLocale() === 'de' ? 'Demo ansehen' : 'View Demo' }}
                </a>
            </div>
        </div>
    </section>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="bg-gray-900 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                {{-- Brand --}}
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center">
                            <svg class="h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">TimeButler</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        {{ app()->getLocale() === 'de'
                            ? 'Urlaubsverwaltung und Abwesenheitsmanagement für deutsche Unternehmen.'
                            : 'Vacation and absence management for German businesses.' }}
                    </p>
                    {{-- Language --}}
                    <div class="flex items-center gap-2 mt-4">
                        <a href="{{ route('locale.switch', 'de') }}" class="px-2.5 py-1 rounded text-xs font-semibold {{ app()->getLocale() === 'de' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }} transition-colors">DE</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="px-2.5 py-1 rounded text-xs font-semibold {{ app()->getLocale() === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }} transition-colors">EN</a>
                    </div>
                </div>

                {{-- Product --}}
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ app()->getLocale() === 'de' ? 'Produkt' : 'Product' }}</h4>
                    <ul class="space-y-2.5">
                        <li><a href="#features" class="text-sm hover:text-white transition-colors">{{ app()->getLocale() === 'de' ? 'Funktionen' : 'Features' }}</a></li>
                        <li><a href="#pricing" class="text-sm hover:text-white transition-colors">{{ app()->getLocale() === 'de' ? 'Preise' : 'Pricing' }}</a></li>
                        <li><a href="{{ route('login') }}" class="text-sm hover:text-white transition-colors">Demo</a></li>
                        <li><a href="#faq" class="text-sm hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>

                {{-- Company --}}
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ app()->getLocale() === 'de' ? 'Unternehmen' : 'Company' }}</h4>
                    <ul class="space-y-2.5">
                        <li><a href="#" class="text-sm hover:text-white transition-colors">{{ app()->getLocale() === 'de' ? 'Über uns' : 'About Us' }}</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">{{ app()->getLocale() === 'de' ? 'Kontakt' : 'Contact' }}</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ app()->getLocale() === 'de' ? 'Rechtliches' : 'Legal' }}</h4>
                    <ul class="space-y-2.5">
                        <li><a href="#" class="text-sm hover:text-white transition-colors">{{ app()->getLocale() === 'de' ? 'Datenschutz' : 'Privacy Policy' }}</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">{{ app()->getLocale() === 'de' ? 'Impressum' : 'Imprint' }}</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">{{ app()->getLocale() === 'de' ? 'AGB' : 'Terms' }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm">&copy; {{ date('Y') }} TimeButler. {{ app()->getLocale() === 'de' ? 'Alle Rechte vorbehalten.' : 'All rights reserved.' }}</p>
                <p class="text-sm">{{ app()->getLocale() === 'de' ? 'Mit Liebe in Deutschland entwickelt' : 'Made with care in Germany' }}</p>
            </div>
        </div>
    </footer>

</body>
</html>
