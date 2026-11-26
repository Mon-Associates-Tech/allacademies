<div>
    {{-- Debug strip --}}
    @if(config('app.debug'))
        <div class="mb-5 flex flex-wrap items-center gap-x-5 gap-y-1 rounded-md border border-warn/40 bg-warn/10 px-4 py-2.5 font-mono text-[10.5px] tracking-wide text-ink-soft">
            <span class="font-semibold uppercase tracking-[0.18em] text-warn">Debug</span>
            <span>USER: <b class="text-ink">{{ auth()->user()->name }}</b></span>
            <span>DATE: <b class="text-ink">{{ now()->format('l, F j, Y') }}</b></span>
        </div>
    @endif

    {{-- ============ Header ============ --}}
    <div class="mb-6">
        <p class="mb-2 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
            <span class="h-2 w-2 bg-accent"></span>REGISTRAR'S OFFICE · PLATFORM ADMINISTRATION
        </p>
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Administrative Dashboard</h1>
                <p class="mt-1 text-sm text-ink-soft">Welcome back, <span class="font-semibold text-ink">{{ auth()->user()->name }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <p class="font-mono text-[10.5px] tracking-wider text-muted">{{ now()->format('l, F j, Y') }}</p>
                <x-exam.badge tone="ok">
                    {{ $this->loginStats['active_sessions'] }} ONLINE
                </x-exam.badge>
            </div>
        </div>
    </div>

    {{-- ============ System Alerts ============ --}}
    @if(count($this->systemAlerts) > 0)
        <div class="mb-6 space-y-2.5">
            @foreach($this->systemAlerts as $alert)
                @php
                    $alertTone = match($alert['type'] ?? 'info') {
                        'warning' => 'warn',
                        'success' => 'ok',
                        'danger'  => 'danger',
                        default   => 'primary',
                    };
                    $alertIcon = match($alert['type'] ?? 'info') {
                        'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
                        'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'danger'  => 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        default   => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    };
                @endphp
                <x-exam.callout :tone="$alertTone">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $alertIcon }}"/></svg>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-ink">{{ $alert['message'] }}</p>
                        </div>
                        @if(!empty($alert['action']) && !empty($alert['route']))
                            <a href="{{ route($alert['route']) }}"
                               class="shrink-0 border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary transition hover:border-current hover:text-primary-hi">
                                {{ $alert['action'] }} →
                            </a>
                        @endif
                    </div>
                </x-exam.callout>
            @endforeach
        </div>
    @endif

    {{-- ============ Ledger Summary Strip ============ --}}
    <div class="mb-8 grid grid-cols-2 divide-x divide-line rounded-md border border-line bg-surface sm:grid-cols-4">
        <div class="px-2 py-3.5 text-center sm:px-4">
            <p class="font-display text-xl font-bold text-ink sm:text-2xl">{{ number_format($this->systemStats['total_users']) }}</p>
            <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">TOTAL USERS</p>
        </div>
        <div class="px-2 py-3.5 text-center sm:px-4">
            <p class="font-display text-xl font-bold text-ok sm:text-2xl">GH₵{{ number_format($this->paymentStats['total_revenue_period'], 0) }}</p>
            <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">REVENUE</p>
        </div>
        <div class="px-2 py-3.5 text-center sm:px-4">
            <p class="font-display text-xl font-bold text-primary sm:text-2xl">{{ number_format($this->loginStats['logins_today']) }}</p>
            <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">LOGINS TODAY</p>
        </div>
        <div class="px-2 py-3.5 text-center sm:px-4">
            <p class="font-display text-xl font-bold {{ $this->systemHealth['score'] >= 80 ? 'text-ok' : ($this->systemHealth['score'] >= 50 ? 'text-warn' : 'text-accent') }} sm:text-2xl">
                {{ $this->systemHealth['score'] }}/100
            </p>
            <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">SYSTEM HEALTH</p>
        </div>
    </div>

    {{-- ============ System Health Card ============ --}}
    <x-exam.card class="mb-6" :padded="false">
        <x-slot:header>
            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                <span class="h-2 w-2 bg-primary"></span>SYSTEM HEALTH
            </p>
            <x-exam.badge :tone="$this->systemHealth['score'] >= 80 ? 'ok' : ($this->systemHealth['score'] >= 50 ? 'warn' : 'danger')">
                {{ $this->systemHealth['status'] }}
            </x-exam.badge>
        </x-slot:header>
        <div class="p-5">
            <div class="mb-4 flex items-baseline justify-between font-mono text-[10px] tracking-wider text-muted">
                <span>HEALTH SCORE</span>
                <span class="font-semibold text-ink">{{ $this->systemHealth['score'] }}/100</span>
            </div>
            <x-exam.progress :value="$this->systemHealth['score']" size="lg" />

            @if(count($this->systemHealth['issues']) > 0)
                <div class="mt-4 border-t border-dashed border-line pt-3">
                    <p class="mb-2 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Issues requiring attention</p>
                    <ul class="space-y-1.5">
                        @foreach($this->systemHealth['issues'] as $issue)
                            <li class="flex items-center gap-2 text-xs text-ink-soft">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-accent"></span>
                                {{ str_replace('_', ' ', ucfirst($issue)) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </x-exam.card>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Login Activity Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Login Activity Trends</h3>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Logins</span>
                </div>
            </div>
            <div class="h-64" x-data="loginChart()" x-init="initChart()">
                <canvas x-ref="loginCanvas" class="w-full h-full"></canvas>
            </div>
            <div class="mt-4 flex justify-between text-sm text-gray-500 dark:text-gray-400">
                <span>Total: {{ number_format(array_sum($this->loginChartData['data'])) }} logins</span>
                <span>Peak: {{ number_format(max($this->loginChartData['data'] ?: [0])) }}</span>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Trends</h3>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Revenue (GH₵)</span>
                </div>
            </div>
            <div class="h-64" x-data="revenueChart()" x-init="initChart()">
                <canvas x-ref="revenueCanvas" class="w-full h-full"></canvas>
            </div>
            <div class="mt-4 flex justify-between text-sm text-gray-500 dark:text-gray-400">
                <span>Total: GH₵{{ number_format(array_sum($this->revenueChartData['data']), 2) }}</span>
                <span>Peak: GH₵{{ number_format(max($this->revenueChartData['data'] ?: [0]), 2) }}</span>
            </div>
        </div>

        <!-- User Distribution Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Distribution</h3>
                <a href="{{ route('users.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
            </div>
            <div class="h-64" x-data="userDistributionChart()" x-init="initChart()">
                <canvas x-ref="distributionCanvas" class="w-full h-full"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                @foreach($this->userDistributionChartData['labels'] as $index => $label)
                    @if($this->userDistributionChartData['data'][$index] > 0)
                        <div class="flex items-center">
                            <span class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $this->userDistributionChartData['colors'][$index] }}"></span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $label }}: {{ number_format($this->userDistributionChartData['data'][$index]) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function loginChart() {
            return {
                initChart() {
                    const canvas = this.$refs.loginCanvas;
                    const ctx = canvas.getContext('2d');
                    const labels = @json($this->loginChartData['labels']);
                    const data = @json($this->loginChartData['data']);

                    this.drawBarChart(ctx, canvas, labels, data, '#3B82F6', '#93C5FD');
                },
                drawBarChart(ctx, canvas, labels, data, barColor, barColorLight) {
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width * window.devicePixelRatio;
                    canvas.height = rect.height * window.devicePixelRatio;
                    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                    const width = rect.width;
                    const height = rect.height;
                    const padding = { top: 20, right: 20, bottom: 40, left: 50 };
                    const chartWidth = width - padding.left - padding.right;
                    const chartHeight = height - padding.top - padding.bottom;

                    const maxValue = Math.max(...data, 1);
                    const barWidth = (chartWidth / data.length) * 0.7;
                    const barGap = (chartWidth / data.length) * 0.3;

                    // Clear canvas
                    ctx.clearRect(0, 0, width, height);

                    // Draw grid lines
                    ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
                    ctx.lineWidth = 1;
                    for (let i = 0; i <= 5; i++) {
                        const y = padding.top + (chartHeight / 5) * i;
                        ctx.beginPath();
                        ctx.moveTo(padding.left, y);
                        ctx.lineTo(width - padding.right, y);
                        ctx.stroke();

                        // Y-axis labels
                        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                        ctx.font = '11px sans-serif';
                        ctx.textAlign = 'right';
                        const value = Math.round(maxValue - (maxValue / 5) * i);
                        ctx.fillText(value.toString(), padding.left - 8, y + 4);
                    }

                    // Draw bars
                    data.forEach((value, index) => {
                        const barHeight = (value / maxValue) * chartHeight;
                        const x = padding.left + (index * (barWidth + barGap)) + barGap / 2;
                        const y = padding.top + chartHeight - barHeight;

                        // Bar gradient
                        const gradient = ctx.createLinearGradient(x, y, x, y + barHeight);
                        gradient.addColorStop(0, barColor);
                        gradient.addColorStop(1, barColorLight);

                        ctx.fillStyle = gradient;
                        ctx.beginPath();
                        ctx.roundRect(x, y, barWidth, barHeight, 4);
                        ctx.fill();

                        // X-axis labels (show every nth label to avoid crowding)
                        const showEvery = Math.ceil(labels.length / 7);
                        if (index % showEvery === 0) {
                            ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                            ctx.font = '10px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.fillText(labels[index], x + barWidth / 2, height - padding.bottom + 20);
                        }
                    });
                }
            };
        }

        function revenueChart() {
            return {
                initChart() {
                    const canvas = this.$refs.revenueCanvas;
                    const ctx = canvas.getContext('2d');
                    const labels = @json($this->revenueChartData['labels']);
                    const data = @json($this->revenueChartData['data']);

                    this.drawBarChart(ctx, canvas, labels, data, '#10B981', '#6EE7B7');
                },
                drawBarChart(ctx, canvas, labels, data, barColor, barColorLight) {
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width * window.devicePixelRatio;
                    canvas.height = rect.height * window.devicePixelRatio;
                    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                    const width = rect.width;
                    const height = rect.height;
                    const padding = { top: 20, right: 20, bottom: 40, left: 60 };
                    const chartWidth = width - padding.left - padding.right;
                    const chartHeight = height - padding.top - padding.bottom;

                    const maxValue = Math.max(...data, 1);
                    const barWidth = (chartWidth / data.length) * 0.7;
                    const barGap = (chartWidth / data.length) * 0.3;

                    // Clear canvas
                    ctx.clearRect(0, 0, width, height);

                    // Draw grid lines
                    ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
                    ctx.lineWidth = 1;
                    for (let i = 0; i <= 5; i++) {
                        const y = padding.top + (chartHeight / 5) * i;
                        ctx.beginPath();
                        ctx.moveTo(padding.left, y);
                        ctx.lineTo(width - padding.right, y);
                        ctx.stroke();

                        // Y-axis labels with currency
                        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                        ctx.font = '10px sans-serif';
                        ctx.textAlign = 'right';
                        const value = maxValue - (maxValue / 5) * i;
                        ctx.fillText('₵' + this.formatNumber(value), padding.left - 8, y + 4);
                    }

                    // Draw bars
                    data.forEach((value, index) => {
                        const barHeight = (value / maxValue) * chartHeight;
                        const x = padding.left + (index * (barWidth + barGap)) + barGap / 2;
                        const y = padding.top + chartHeight - barHeight;

                        // Bar gradient
                        const gradient = ctx.createLinearGradient(x, y, x, y + barHeight);
                        gradient.addColorStop(0, barColor);
                        gradient.addColorStop(1, barColorLight);

                        ctx.fillStyle = gradient;
                        ctx.beginPath();
                        ctx.roundRect(x, y, barWidth, barHeight, 4);
                        ctx.fill();

                        // X-axis labels
                        const showEvery = Math.ceil(labels.length / 7);
                        if (index % showEvery === 0) {
                            ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                            ctx.font = '10px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.fillText(labels[index], x + barWidth / 2, height - padding.bottom + 20);
                        }
                    });
                },
                formatNumber(num) {
                    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
                    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
                    return num.toFixed(0);
                }
            };
        }

        function userDistributionChart() {
            return {
                initChart() {
                    const canvas = this.$refs.distributionCanvas;
                    const ctx = canvas.getContext('2d');
                    const labels = @json($this->userDistributionChartData['labels']);
                    const data = @json($this->userDistributionChartData['data']);
                    const colors = @json($this->userDistributionChartData['colors']);

                    this.drawDoughnutChart(ctx, canvas, labels, data, colors);
                },
                drawDoughnutChart(ctx, canvas, labels, data, colors) {
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width * window.devicePixelRatio;
                    canvas.height = rect.height * window.devicePixelRatio;
                    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

                    const width = rect.width;
                    const height = rect.height;
                    const centerX = width / 2;
                    const centerY = height / 2;
                    const radius = Math.min(width, height) / 2 - 20;
                    const innerRadius = radius * 0.6; // Doughnut hole

                    // Filter out zero values
                    const filteredData = [];
                    const filteredColors = [];
                    const filteredLabels = [];
                    data.forEach((value, index) => {
                        if (value > 0) {
                            filteredData.push(value);
                            filteredColors.push(colors[index]);
                            filteredLabels.push(labels[index]);
                        }
                    });

                    const total = filteredData.reduce((sum, val) => sum + val, 0);

                    if (total === 0) {
                        // Draw empty state
                        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
                        ctx.beginPath();
                        ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
                        ctx.arc(centerX, centerY, innerRadius, 0, Math.PI * 2, true);
                        ctx.fill();

                        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                        ctx.font = '14px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.fillText('No users', centerX, centerY);
                        return;
                    }

                    // Clear canvas
                    ctx.clearRect(0, 0, width, height);

                    let startAngle = -Math.PI / 2; // Start from top

                    filteredData.forEach((value, index) => {
                        const sliceAngle = (value / total) * Math.PI * 2;
                        const endAngle = startAngle + sliceAngle;

                        // Draw slice
                        ctx.beginPath();
                        ctx.moveTo(centerX, centerY);
                        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                        ctx.closePath();
                        ctx.fillStyle = filteredColors[index];
                        ctx.fill();

                        // Add subtle border between slices
                        ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF';
                        ctx.lineWidth = 2;
                        ctx.stroke();

                        startAngle = endAngle;
                    });

                    // Draw inner circle (doughnut hole)
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, innerRadius, 0, Math.PI * 2);
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF';
                    ctx.fill();

                    // Draw total in center
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#F9FAFB' : '#111827';
                    ctx.font = 'bold 24px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(this.formatNumber(total), centerX, centerY - 8);

                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
                    ctx.font = '12px sans-serif';
                    ctx.fillText('Total Users', centerX, centerY + 12);
                },
                formatNumber(num) {
                    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
                    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
                    return num.toString();
                }
            };
        }
    </script>

    {{-- ============ Trends Row ============ --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Login Activity Trends --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-primary"></span>LOGIN ACTIVITY TRENDS
                </p>
                <p class="hidden font-mono text-[10px] tracking-wider text-muted sm:block">
                    TOTAL {{ number_format(array_sum($this->loginChartData['data'])) }} · PEAK {{ number_format(max($this->loginChartData['data'] ?: [0])) }}
                </p>
            </x-slot:header>
            <div class="p-5">
                {{-- CHART: paste your login activity Livewire chart component here --}}
                <div class="grid min-h-[200px] place-items-center rounded-md border border-dashed border-line text-muted">
                    <p class="font-mono text-[10px] tracking-[0.14em]">CHART PLACEHOLDER</p>
                </div>
            </div>
        </x-exam.card>

        {{-- Revenue Trends --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-ok"></span>REVENUE TRENDS (GH₵)
                </p>
                <p class="hidden font-mono text-[10px] tracking-wider text-muted sm:block">
                    TOTAL GH₵{{ number_format(array_sum($this->revenueChartData['data']), 2) }} · PEAK GH₵{{ number_format(max($this->revenueChartData['data'] ?: [0]), 2) }}
                </p>
            </x-slot:header>
            <div class="p-5">
                {{-- CHART: paste your revenue Livewire chart component here --}}
                <div class="grid min-h-[200px] place-items-center rounded-md border border-dashed border-line text-muted">
                    <p class="font-mono text-[10px] tracking-[0.14em]">CHART PLACEHOLDER</p>
                </div>
            </div>
        </x-exam.card>
    </div>

    {{-- ============ Key Metrics Grid ============ --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-exam.stat label="Total Users" :value="number_format($this->systemStats['total_users'])" tone="primary">
            <x-slot:sub>+{{ number_format($this->systemStats['new_users_period']) }} this period</x-slot:sub>
        </x-exam.stat>
        <x-exam.stat label="Active Today" :value="number_format($this->systemStats['active_today'])" tone="ok">
            <x-slot:sub>{{ number_format($this->loginStats['logins_today']) }} logins today</x-slot:sub>
        </x-exam.stat>
        <x-exam.stat label="Revenue (Period)" :value="'GH₵' . number_format($this->paymentStats['total_revenue_period'], 2)" tone="primary">
            <x-slot:sub>{{ number_format($this->paymentStats['successful_payments']) }} successful payments</x-slot:sub>
        </x-exam.stat>
        <x-exam.stat label="Assessments" :value="number_format($this->academicStats['recent_assessments'])" tone="warn">
            <x-slot:sub>{{ number_format($this->academicStats['average_performance'], 1) }}% avg score</x-slot:sub>
        </x-exam.stat>
    </div>

    {{-- ============ Secondary Metrics ============ --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <x-exam.stat label="Students" :value="number_format($this->userBreakdown['students'])" />
        </div>
        <div>
            <x-exam.stat label="Teachers" :value="number_format($this->userBreakdown['teachers'])" />
        </div>
        <div>
            <x-exam.stat label="Messages" :value="number_format($this->messageStats['messages_period'])">
                <x-slot:sub>{{ number_format($this->messageStats['unread_messages']) }} unread</x-slot:sub>
            </x-exam.stat>
        </div>
        <div>
            <x-exam.stat label="Forum Posts" :value="number_format($this->forumStats['new_posts_period'])">
                <x-slot:sub>{{ number_format($this->forumStats['active_discussions']) }} active topics</x-slot:sub>
            </x-exam.stat>
        </div>
    </div>

    {{-- ============ Departmental Overviews ============ --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Payment Summary --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-ok"></span>PAYMENT SUMMARY
                </p>
                <a href="{{ route('admin.payments.index') }}"
                   class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">VIEW ALL →</a>
            </x-slot:header>
            <div class="space-y-3 p-5">
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>TOTAL TRANSACTIONS</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">{{ number_format($this->paymentStats['total_transactions']) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>SUCCESSFUL</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ok">{{ number_format($this->paymentStats['successful_payments']) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>PENDING</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-warn">{{ number_format($this->paymentStats['pending_payments']) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>FAILED</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-accent">{{ number_format($this->paymentStats['failed_payments']) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>PENDING AMOUNT</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">GH₵{{ number_format($this->paymentStats['pending_amount'], 2) }}</span>
                </div>
            </div>
        </x-exam.card>

        {{-- Login Activity --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-primary"></span>LOGIN ACTIVITY
                </p>
                <a href="{{ route('admin.logins') }}"
                   class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">VIEW ALL →</a>
            </x-slot:header>
            <div class="space-y-3 p-5">
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>TOTAL LOGINS (PERIOD)</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">{{ number_format($this->loginStats['total_logins_period']) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>LOGINS TODAY</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">{{ number_format($this->loginStats['logins_today']) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>ACTIVE SESSIONS</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ok">{{ number_format($this->loginStats['active_sessions']) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>UNIQUE USERS</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">{{ number_format($this->loginStats['unique_users_period']) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 font-mono text-[11px] tracking-wide text-muted">
                    <span>AVG SESSION DURATION</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">{{ $this->loginStats['avg_session_duration'] }} min</span>
                </div>
            </div>
        </x-exam.card>
    </div>

    {{-- ============ Academic & Library ============ --}}
    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Academic Overview --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-warn"></span>ACADEMIC OVERVIEW
                </p>
                <a href="{{ route('academic-groups.index') }}"
                   class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">MANAGE →</a>
            </x-slot:header>
            <div class="grid grid-cols-2 gap-3 p-5">
                <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                    <p class="font-display text-lg font-bold text-ink">{{ number_format($this->academicStats['total_groups']) }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-wider text-muted">ACADEMIC GROUPS</p>
                </div>
                <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                    <p class="font-display text-lg font-bold text-ink">{{ number_format($this->academicStats['total_levels']) }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-wider text-muted">ACADEMIC LEVELS</p>
                </div>
                <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                    <p class="font-display text-lg font-bold text-ink">{{ number_format($this->academicStats['total_subjects']) }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-wider text-muted">SUBJECTS</p>
                </div>
                <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                    <p class="font-display text-lg font-bold text-ink">{{ number_format($this->academicStats['total_assessments']) }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-wider text-muted">TOTAL ASSESSMENTS</p>
                </div>
            </div>
        </x-exam.card>

        {{-- Library Overview --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-primary"></span>LIBRARY OVERVIEW
                </p>
                <a href="{{ route('admin.book-management') }}"
                   class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">MANAGE →</a>
            </x-slot:header>
            <div class="grid grid-cols-2 gap-3 p-5">
                <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                    <p class="font-display text-lg font-bold text-ink">{{ number_format($this->libraryStats['published_books']) }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-wider text-muted">PUBLISHED BOOKS</p>
                </div>
                <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                    <p class="font-display text-lg font-bold text-warn">{{ number_format($this->libraryStats['pending_approval']) }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-wider text-muted">PENDING APPROVAL</p>
                </div>
                <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                    <p class="font-display text-lg font-bold text-ok">{{ number_format($this->libraryStats['active_borrowings']) }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-wider text-muted">ACTIVE BORROWINGS</p>
                </div>
                <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                    <p class="font-display text-lg font-bold text-accent">{{ number_format($this->libraryStats['overdue_books']) }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-wider text-muted">OVERDUE BOOKS</p>
                </div>
            </div>
        </x-exam.card>
    </div>

    {{-- ============ Recent Activity ============ --}}
    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Recent Users --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-primary"></span>RECENT USERS
                </p>
                <a href="{{ route('users.index') }}"
                   class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">VIEW ALL →</a>
            </x-slot:header>
            <ul class="divide-y divide-line">
                @forelse($this->recentActivity['new_users'] as $user)
                    <li class="flex items-center gap-3 px-5 py-3 transition hover:bg-surface-2/60">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-primary/10 font-mono text-xs font-bold text-primary ring-1 ring-line">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">{{ $user->name }}</p>
                            <p class="truncate font-mono text-[10px] tracking-wide text-muted">{{ $user->email }}</p>
                        </div>
                        <p class="shrink-0 font-mono text-[10px] tracking-wide text-muted">{{ $user->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center font-mono text-xs tracking-wider text-muted">NO RECENT USERS</li>
                @endforelse
            </ul>
        </x-exam.card>

        {{-- Recent Logins --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-ok"></span>RECENT LOGINS
                </p>
                <a href="{{ route('admin.logins') }}"
                   class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">VIEW ALL →</a>
            </x-slot:header>
            <ul class="divide-y divide-line">
                @forelse($this->recentActivity['recent_logins'] as $login)
                    <li class="flex items-center gap-3 px-5 py-3 transition hover:bg-surface-2/60">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-ok/10 font-mono text-xs font-bold text-ok ring-1 ring-line">
                            {{ strtoupper(substr($login->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">{{ $login->user->name ?? 'Unknown' }}</p>
                            <p class="truncate font-mono text-[10px] tracking-wide text-muted">
                                {{ $login->device_type ?? 'Unknown Device' }}@if($login->browser) · {{ $login->browser }}@endif
                            </p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="font-mono text-[10px] tracking-wide text-muted">{{ $login->login_at?->diffForHumans() }}</p>
                            @if(!$login->logout_at)
                                <x-exam.badge tone="ok">ACTIVE</x-exam.badge>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center font-mono text-xs tracking-wider text-muted">NO RECENT LOGINS</li>
                @endforelse
            </ul>
        </x-exam.card>

        {{-- Recent Payments --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-warn"></span>RECENT PAYMENTS
                </p>
                <a href="{{ route('admin.payments.index') }}"
                   class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">VIEW ALL →</a>
            </x-slot:header>
            <ul class="divide-y divide-line">
                @forelse($this->recentActivity['recent_payments'] as $payment)
                    @php
                        $payTone = match(strtolower($payment->status ?? '')) {
                            'completed', 'succeeded', 'paid' => 'ok',
                            'pending' => 'warn',
                            'failed' => 'danger',
                            default => 'muted',
                        };
                    @endphp
                    <li class="flex items-center gap-3 px-5 py-3 transition hover:bg-surface-2/60">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-warn/10 font-mono text-xs font-bold text-warn ring-1 ring-line">
                            GH₵
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">{{ $payment->student->user->name ?? 'Unknown' }}</p>
                            <p class="truncate font-mono text-[10px] tracking-wide text-muted">
                                {{ $payment->payment_type ?? 'Payment' }} · GH₵{{ number_format($payment->amount ?? 0, 2) }}
                            </p>
                        </div>
                        <x-exam.badge :tone="$payTone">{{ ucfirst($payment->status ?? 'unknown') }}</x-exam.badge>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center font-mono text-xs tracking-wider text-muted">NO RECENT PAYMENTS</li>
                @endforelse
            </ul>
        </x-exam.card>
    </div>

    {{-- ============ Quick Actions Toggle ============ --}}
    @if($showQuickActions)
        <div class="mb-8">
            <div class="mb-4 flex items-center justify-between">
                <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    <span class="h-2 w-2 bg-accent"></span>QUICK ACTIONS
                </p>
                <p class="font-mono text-[10px] tracking-wider text-muted">{{ count($this->quickActionItems) }} AVAILABLE</p>
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                @foreach($this->quickActionItems as $action)
                    <a href="{{ $action['route'] ?? '#' }}"
                       class="group relative flex flex-col items-center gap-2 rounded-md border border-line bg-surface p-4 text-center transition hover:-translate-y-0.5 hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">
                        <div class="grid h-10 w-10 place-items-center rounded-md bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-on-primary">
                            @if($action['icon'] === 'user-plus')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            @elseif($action['icon'] === 'academic-cap')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            @elseif($action['icon'] === 'check-circle')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($action['icon'] === 'exclamation-triangle')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            @elseif($action['icon'] === 'currency')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($action['icon'] === 'mail')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @elseif($action['icon'] === 'user-secret')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            @elseif($action['icon'] === 'shield-check')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            @elseif($action['icon'] === 'clipboard-list')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            @elseif($action['icon'] === 'user-group')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @elseif($action['icon'] === 'book-open')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @elseif($action['icon'] === 'cog')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-ink">{{ $action['title'] }}</span>
                        <span class="line-clamp-2 text-[10px] leading-tight text-muted">{{ $action['description'] }}</span>
                        @if(isset($action['badge']) && $action['badge'] > 0)
                            <span class="absolute right-1.5 top-1.5 grid h-5 min-w-[20px] place-items-center rounded-full bg-accent px-1 font-mono text-[9px] font-bold text-white">
                                {{ $action['badge'] > 99 ? '99+' : $action['badge'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
