// Chart.js data utilities and configurations
export class ChartDataHelper {
    // Color palette for consistent styling
    static colors = {
        primary: '#3B82F6',
        secondary: '#6B7280',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        info: '#06B6D4',
        light: '#F3F4F6',
        dark: '#1F2937',
        purple: '#8B5CF6',
        pink: '#EC4899',
        indigo: '#6366F1',
        teal: '#14B8A6'
    };

    // Chart color schemes
    static colorSchemes = {
        default: [
            this.colors.primary,
            this.colors.success,
            this.colors.warning,
            this.colors.danger,
            this.colors.info,
            this.colors.purple,
            this.colors.pink,
            this.colors.indigo,
            this.colors.teal
        ],
        gradient: [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(6, 182, 212, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(99, 102, 241, 0.8)',
            'rgba(20, 184, 166, 0.8)'
        ]
    };

    // Default chart options
    static getDefaultOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    cornerRadius: 6
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    beginAtZero: true
                }
            }
        };
    }

    // Generate bar chart data
    static generateBarChartData(labels, datasets, options = {}) {
        const processedDatasets = datasets.map((dataset, index) => ({
            label: dataset.label,
            data: dataset.data,
            backgroundColor: dataset.backgroundColor || this.colorSchemes.default[index % this.colorSchemes.default.length],
            borderColor: dataset.borderColor || this.colorSchemes.default[index % this.colorSchemes.default.length],
            borderWidth: dataset.borderWidth || 1,
            ...dataset
        }));

        return {
            type: 'bar',
            data: {
                labels: labels,
                datasets: processedDatasets
            },
            options: {
                ...this.getDefaultOptions(),
                ...options
            }
        };
    }

    // Generate line chart data
    static generateLineChartData(labels, datasets, options = {}) {
        const processedDatasets = datasets.map((dataset, index) => ({
            label: dataset.label,
            data: dataset.data,
            borderColor: dataset.borderColor || this.colorSchemes.default[index % this.colorSchemes.default.length],
            backgroundColor: dataset.backgroundColor || this.colorSchemes.gradient[index % this.colorSchemes.gradient.length],
            tension: dataset.tension || 0.4,
            fill: dataset.fill || false,
            ...dataset
        }));

        return {
            type: 'line',
            data: {
                labels: labels,
                datasets: processedDatasets
            },
            options: {
                ...this.getDefaultOptions(),
                ...options
            }
        };
    }

    // Generate pie chart data
    static generatePieChartData(labels, data, options = {}) {
        return {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: this.colorSchemes.default.slice(0, labels.length),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 6
                    }
                },
                ...options
            }
        };
    }

    // Generate doughnut chart data
    static generateDoughnutChartData(labels, data, options = {}) {
        return {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: this.colorSchemes.default.slice(0, labels.length),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 6
                    }
                },
                cutout: '60%',
                ...options
            }
        };
    }

    // Generate radar chart data
    static generateRadarChartData(labels, datasets, options = {}) {
        const processedDatasets = datasets.map((dataset, index) => ({
            label: dataset.label,
            data: dataset.data,
            borderColor: dataset.borderColor || this.colorSchemes.default[index % this.colorSchemes.default.length],
            backgroundColor: dataset.backgroundColor || this.colorSchemes.gradient[index % this.colorSchemes.gradient.length],
            ...dataset
        }));

        return {
            type: 'radar',
            data: {
                labels: labels,
                datasets: processedDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                ...options
            }
        };
    }

    // Sample data generators for testing
    static generateSampleBarData() {
        return this.generateBarChartData(
            ['January', 'February', 'March', 'April', 'May', 'June'],
            [
                {
                    label: 'Sales',
                    data: [12, 19, 3, 5, 2, 3]
                },
                {
                    label: 'Revenue',
                    data: [2, 3, 20, 5, 1, 4]
                }
            ]
        );
    }

    static generateSampleLineData() {
        return this.generateLineChartData(
            ['January', 'February', 'March', 'April', 'May', 'June'],
            [
                {
                    label: 'Website Visitors',
                    data: [65, 59, 80, 81, 56, 55],
                    fill: true
                },
                {
                    label: 'Unique Visitors',
                    data: [28, 48, 40, 19, 86, 27],
                    fill: true
                }
            ]
        );
    }

    static generateSamplePieData() {
        return this.generatePieChartData(
            ['Desktop', 'Mobile', 'Tablet'],
            [55, 35, 10]
        );
    }

    static generateSampleDoughnutData() {
        return this.generateDoughnutChartData(
            ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            [12, 19, 3, 5, 2, 3]
        );
    }

    static generateSampleRadarData() {
        return this.generateRadarChartData(
            ['Eating', 'Drinking', 'Sleeping', 'Designing', 'Coding', 'Cycling', 'Running'],
            [
                {
                    label: 'User 1',
                    data: [65, 59, 90, 81, 56, 55, 40]
                },
                {
                    label: 'User 2',
                    data: [28, 48, 40, 19, 96, 27, 100]
                }
            ]
        );
    }

    // Utility method to create chart instance
    static createChart(canvasId, config) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.error(`Canvas element with id '${canvasId}' not found`);
            return null;
        }
        return new Chart(ctx, config);
    }

    // Method to update chart data
    static updateChart(chart, newData) {
        chart.data = newData;
        chart.update();
    }

    // Method to add data to existing chart
    static addDataToChart(chart, label, data) {
        chart.data.labels.push(label);
        chart.data.datasets.forEach((dataset, index) => {
            dataset.data.push(data[index] || 0);
        });
        chart.update();
    }

    // Method to remove data from chart
    static removeDataFromChart(chart) {
        chart.data.labels.pop();
        chart.data.datasets.forEach((dataset) => {
            dataset.data.pop();
        });
        chart.update();
    }
}

// Export for use in other modules
window.ChartDataHelper = ChartDataHelper;
