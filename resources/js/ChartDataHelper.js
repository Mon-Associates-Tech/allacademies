class ChartDataHelper {
    static colorSchemes = {
        default: [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16'
        ],
        gradient: [
            'rgba(59, 130, 246, 0.1)', 'rgba(239, 68, 68, 0.1)', 'rgba(16, 185, 129, 0.1)',
            'rgba(245, 158, 11, 0.1)', 'rgba(139, 92, 246, 0.1)', 'rgba(236, 72, 153, 0.1)',
            'rgba(20, 184, 166, 0.1)', 'rgba(249, 115, 22, 0.1)', 'rgba(99, 102, 241, 0.1)',
            'rgba(132, 204, 22, 0.1)'
        ]
    };

    static getDefaultOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Inter, system-ui, sans-serif'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    font: {
                        family: 'Inter, system-ui, sans-serif'
                    }
                }
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                },
                line: {
                    borderWidth: 2
                },
                bar: {
                    borderRadius: 4
                }
            }
        };
    }

    static generateLineChartData(labels, datasets, options = {}) {
        const processedDatasets = datasets.map((dataset, index) => ({
            label: dataset.label,
            data: dataset.data,
            borderColor: dataset.borderColor || this.colorSchemes.default[index % this.colorSchemes.default.length],
            backgroundColor: dataset.backgroundColor || this.colorSchemes.gradient[index % this.colorSchemes.gradient.length],
            tension: dataset.tension || 0.4,
            fill: dataset.fill || false,
            pointBackgroundColor: dataset.borderColor || this.colorSchemes.default[index % this.colorSchemes.default.length],
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
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
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Inter, system-ui, sans-serif'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                family: 'Inter, system-ui, sans-serif'
                            }
                        }
                    }
                },
                ...options
            }
        };
    }

    static generateBarChartData(labels, datasets, options = {}) {
        const processedDatasets = datasets.map((dataset, index) => ({
            label: dataset.label,
            data: dataset.data,
            backgroundColor: dataset.backgroundColor || this.colorSchemes.default[index % this.colorSchemes.default.length],
            borderColor: dataset.borderColor || this.colorSchemes.default[index % this.colorSchemes.default.length],
            borderWidth: 1,
            borderRadius: 4,
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
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Inter, system-ui, sans-serif'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                family: 'Inter, system-ui, sans-serif'
                            }
                        }
                    }
                },
                ...options
            }
        };
    }

    static generatePieChartData(labels, data, options = {}) {
        return {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: this.colorSchemes.default.slice(0, labels.length),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: 'Inter, system-ui, sans-serif'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        font: {
                            family: 'Inter, system-ui, sans-serif'
                        },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                },
                ...options
            }
        };
    }

    static addDataToChart(chart, label, data) {
        chart.data.labels.push(label);
        chart.data.datasets.forEach((dataset, index) => {
            dataset.data.push(data[index] || 0);
        });
        chart.update('none'); // Animation disabled for performance
    }

    static removeDataFromChart(chart) {
        chart.data.labels.pop();
        chart.data.datasets.forEach((dataset) => {
            dataset.data.pop();
        });
        chart.update('none');
    }

    static updateChartData(chart, newData) {
        chart.data.datasets.forEach((dataset, index) => {
            dataset.data = newData[index] || [];
        });
        chart.update('active');
    }

    static createAnimatedChart(canvas, config) {
        const chart = new Chart(canvas, config);

        // Add entrance animation
        chart.update('show');

        return chart;
    }

    static destroyChart(chartInstance) {
        if (chartInstance && typeof chartInstance.destroy === 'function') {
            chartInstance.destroy();
        }
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChartDataHelper;
}

// Make available globally
window.ChartDataHelper = ChartDataHelper;
