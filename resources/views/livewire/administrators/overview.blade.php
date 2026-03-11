<div class="bg-gray-50 dark:bg-gray-900 p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Administrative Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 px-3 py-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-sm font-medium text-green-700 dark:text-green-300">{{ $this->loginStats['active_sessions'] }} Online</span>
            </div>
            <select wire:model.live="selectedPeriod" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>
        </div>
    </div>

    <!-- System Alerts -->
    @if(count($this->systemAlerts) > 0)
        <div class="mb-8 space-y-3">
            @foreach($this->systemAlerts as $alert)
                <div class="flex items-center justify-between p-4 rounded-lg border
                    {{ $alert['type'] === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-700 dark:text-yellow-200' : '' }}
                    {{ $alert['type'] === 'info' ? 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-700 dark:text-blue-200' : '' }}
                    {{ $alert['type'] === 'success' ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-700 dark:text-green-200' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            @if($alert['type'] === 'warning')
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            @elseif($alert['type'] === 'success')
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                        <span class="font-medium">{{ $alert['message'] }}</span>
                    </div>
                    <a href="{{ route($alert['route']) }}" class="text-sm font-semibold hover:underline">
                        {{ $alert['action'] }} →
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- System Health Overview -->
    <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">System Health</h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Score:</span>
                <span class="text-lg font-bold {{ $this->systemHealth['status'] === 'excellent' ? 'text-green-600' : ($this->systemHealth['status'] === 'good' ? 'text-blue-600' : ($this->systemHealth['status'] === 'fair' ? 'text-yellow-600' : 'text-red-600')) }}">
                    {{ $this->systemHealth['score'] }}/100
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="h-3 rounded-full {{ $this->systemHealth['status'] === 'excellent' ? 'bg-green-500' : ($this->systemHealth['status'] === 'good' ? 'bg-blue-500' : ($this->systemHealth['status'] === 'fair' ? 'bg-yellow-500' : 'bg-red-500')) }}"
                     style="width: {{ $this->systemHealth['score'] }}%"></div>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $this->systemHealth['status'] }}</span>
        </div>
        @if(count($this->systemHealth['issues']) > 0)
            <div class="mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Issues requiring attention:</p>
                <ul class="mt-2 space-y-1">
                    @foreach($this->systemHealth['issues'] as $issue)
                        <li class="text-sm text-red-600 dark:text-red-400">• {{ str_replace('_', ' ', ucfirst($issue)) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

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

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->systemStats['total_users']) }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400">+{{ number_format($this->systemStats['new_users_period']) }} this period</p>
                </div>
            </div>
        </div>

        <!-- Active Today -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Active Today</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->systemStats['active_today']) }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400">{{ number_format($this->loginStats['logins_today']) }} logins today</p>
                </div>
            </div>
        </div>

        <!-- Revenue This Period -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-emerald-100 dark:bg-emerald-900">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Revenue (Period)</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">GH₵{{ number_format($this->paymentStats['total_revenue_period'], 2) }}</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ number_format($this->paymentStats['successful_payments']) }} successful payments</p>
                </div>
            </div>
        </div>

        <!-- Assessments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Assessments</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->academicStats['recent_assessments']) }}</p>
                    <p class="text-xs text-purple-600 dark:text-purple-400">{{ number_format($this->academicStats['average_performance'], 1) }}% avg score</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Students -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Students</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->userBreakdown['students']) }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.student-management') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Teachers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Teachers</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->userBreakdown['teachers']) }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.teacher-management') }}" class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Messages -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-cyan-100 dark:bg-cyan-900">
                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Messages</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->messageStats['messages_period']) }}</p>
                        <p class="text-xs text-cyan-600 dark:text-cyan-400">{{ number_format($this->messageStats['unread_messages']) }} unread</p>
                    </div>
                </div>
                <a href="{{ route('admin.messages.index') }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Forum Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-rose-100 dark:bg-rose-900">
                        <svg class="w-6 h-6 text-rose-600 dark:text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Forum Posts</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->forumStats['new_posts_period']) }}</p>
                        <p class="text-xs text-rose-600 dark:text-rose-400">{{ number_format($this->forumStats['active_discussions']) }} active topics</p>
                    </div>
                </div>
                <a href="{{ route('forums') }}" class="text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- User Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Distribution</h3>
                <a href="{{ route('users.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                @foreach($this->userBreakdown as $role => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $role) }}</span>
                        <div class="flex items-center">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white mr-2">{{ number_format($count) }}</span>
                            <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $this->systemStats['total_users'] > 0 ? min(100, ($count / $this->systemStats['total_users']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Summary</h3>
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Transactions</span>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($this->paymentStats['total_transactions']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Successful</span>
                    <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ number_format($this->paymentStats['successful_payments']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                    <span class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ number_format($this->paymentStats['pending_payments']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Failed</span>
                    <span class="text-lg font-semibold text-red-600 dark:text-red-400">{{ number_format($this->paymentStats['failed_payments']) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Amount</span>
                        <span class="text-lg font-bold text-yellow-600 dark:text-yellow-400">GH₵{{ number_format($this->paymentStats['pending_amount'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Login Activity</h3>
                <a href="{{ route('admin.logins') }}" class="text-sm text-amber-600 dark:text-amber-400 hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Logins (Period)</span>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($this->loginStats['total_logins_period']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Logins Today</span>
                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ number_format($this->loginStats['logins_today']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Active Sessions</span>
                    <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ number_format($this->loginStats['active_sessions']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Unique Users</span>
                    <span class="text-lg font-semibold text-purple-600 dark:text-purple-400">{{ number_format($this->loginStats['unique_users_period']) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Session Duration</span>
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $this->loginStats['avg_session_duration'] }} min</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic & Library Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Academic Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Overview</h3>
                <a href="{{ route('academic-groups.index') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">Manage</a>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->academicStats['total_groups']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Academic Groups</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->academicStats['total_levels']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Academic Levels</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->academicStats['total_subjects']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Subjects</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->academicStats['total_assessments']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Assessments</p>
                </div>
            </div>
        </div>

        <!-- Library Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Library Overview</h3>
                <a href="{{ route('admin.book-management') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">Manage</a>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($this->libraryStats['published_books']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Published Books</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($this->libraryStats['pending_approval']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Approval</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->libraryStats['active_borrowings']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Active Borrowings</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($this->libraryStats['overdue_books']) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Overdue Books</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Feeds -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Users</h3>
                <a href="{{ route('users.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($this->recentActivity['new_users'] as $user)
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent users</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Logins -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Logins</h3>
                <a href="{{ route('admin.logins') }}" class="text-sm text-amber-600 dark:text-amber-400 hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($this->recentActivity['recent_logins'] as $login)
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $login->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $login->device_type ?? 'Unknown Device' }} • {{ $login->browser ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $login->login_at?->diffForHumans() }}</span>
                            @if(!$login->logout_at)
                                <span class="block text-xs text-green-500">Active</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent logins</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Payments</h3>
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($this->recentActivity['recent_payments'] as $payment)
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            {{ $payment->status === 'succeeded' ? 'bg-gradient-to-br from-green-500 to-green-600' : ($payment->status === 'pending' ? 'bg-gradient-to-br from-yellow-500 to-yellow-600' : 'bg-gradient-to-br from-red-500 to-red-600') }}">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $payment->student->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->payment_type ?? 'Payment' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">GH₵{{ number_format($payment->amount ?? 0, 2) }}</span>
                            <span class="block text-xs {{ $payment->status === 'succeeded' ? 'text-green-500' : ($payment->status === 'pending' ? 'text-yellow-500' : 'text-red-500') }}">
                                {{ ucfirst($payment->status ?? 'unknown') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent payments</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if($showQuickActions)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($this->quickActionItems) }} actions available</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($this->quickActionItems as $action)
                    <a href="{{ route($action['route']) }}" class="relative p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all duration-200 group bg-white dark:bg-gray-800">
                        <div class="flex items-start space-x-3">
                            <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($action['icon'] === 'user-plus')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    @elseif($action['icon'] === 'academic-cap')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                                    @elseif($action['icon'] === 'check-circle')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @elseif($action['icon'] === 'exclamation-triangle')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    @elseif($action['icon'] === 'currency')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @elseif($action['icon'] === 'mail')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    @elseif($action['icon'] === 'user-secret')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    @elseif($action['icon'] === 'shield-check')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    @elseif($action['icon'] === 'clipboard-list')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    @elseif($action['icon'] === 'user-group')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    @elseif($action['icon'] === 'book-open')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    @elseif($action['icon'] === 'cog')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    @endif
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $action['title'] }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $action['description'] }}</p>
                            </div>
                        </div>
                        @if(isset($action['badge']) && $action['badge'] > 0)
                            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
                                {{ $action['badge'] > 99 ? '99+' : $action['badge'] }}
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
