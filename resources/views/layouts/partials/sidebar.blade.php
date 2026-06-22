<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-b from-slate-900 to-slate-800 px-6 pb-4">
    {{-- Logo --}}
    <div class="flex h-16 shrink-0 items-center">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-x-3">
            <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-xl font-bold text-white tracking-tight">Time<span class="text-blue-400">Check</span></span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            {{-- Main navigation --}}
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('dashboard') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            {{ __('app.dashboard') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('absences.index') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('absences.*') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            {{ __('app.absences') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('time-tracking.index') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('time-tracking.*') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('app.time_tracking') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('calendar') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('calendar') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                            {{ __('app.calendar') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('overtime.index') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('overtime.*') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            {{ __('app.overtime') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('calendar.team') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('calendar.team*') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                            {{ __('app.team_calendar') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('holidays.index') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('holidays.*') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                            {{ __('app.holidays') }}
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Management Section (admin/manager only) --}}
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
            <li>
                <div class="text-xs font-semibold leading-6 text-slate-400 uppercase tracking-wider">{{ app()->getLocale() === 'de' ? 'Verwaltung' : 'Management' }}</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    @if(auth()->user()->hasRole('admin'))
                    <li>
                        <a href="{{ route('employees.index') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('employees.*') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            {{ __('app.employees') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('departments.index') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('departments.*') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                            </svg>
                            {{ __('app.departments') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('overtime.admin') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('overtime.admin') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                            </svg>
                            {{ app()->getLocale() === 'de' ? 'Überstunden verwalten' : 'Manage Overtime' }}
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('absences.team') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('absences.team') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                            {{ __('app.team_requests') }}
                        </a>
                    </li>
                    @if(auth()->user()->hasRole('admin'))
                    <li>
                        <a href="{{ route('reports.index') }}"
                           class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                                  {{ request()->routeIs('reports.*') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            {{ __('app.reports') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- Settings at bottom (admin only) --}}
            <li class="mt-auto">
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('settings') }}"
                   class="group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-150
                          {{ request()->routeIs('settings') ? 'bg-blue-600/20 text-blue-300' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __('app.settings') }}
                </a>
                @endif
            </li>
        </ul>
    </nav>
</div>
