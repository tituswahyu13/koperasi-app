<?php $title = 'Dashboard Admin - Koperasi Tirta Raharja'; ?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Ringkasan Statistik</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        {{-- Kotak Total Anggota --}}
                        <div class="bg-blue-100 p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-blue-800">Total Anggota</span>
                                <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-10a4 4 0 01-4-4V8a4 4 0 014-4h10a4 4 0 014 4v8a4 4 0 01-4 4zM12 11h.01M12 15h.01M16 11h.01M16 15h.01M8 11h.01M8 15h.01M12 7h.01" />
                                </svg>
                            </div>
                            <div class="mt-2 text-3xl font-bold text-blue-900">{{ number_format($totalAnggota) }}</div>
                        </div>

                        {{-- Kotak Total Simpanan --}}
                        <div class="bg-green-100 p-6 rounded-lg shadow-md border-l-4 border-green-500">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-green-800">Total Simpanan</span>
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zM12 16a7.7 7.7 0 01-5-1.78l-4 4V13a9 9 0 1118 0v4l-4-4a7.7 7.7 0 01-5 1.78z" />
                                </svg>
                            </div>
                            <div class="mt-2 text-3xl font-bold text-green-900">Rp {{ number_format($totalSimpanan, 2, ',', '.') }}</div>
                        </div>

                        {{-- Kotak Total Pinjaman --}}
                        <div class="bg-yellow-100 p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-yellow-800">Total Pinjaman</span>
                                <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8zM12 18V6" />
                                </svg>
                            </div>
                            <div class="mt-2 text-3xl font-bold text-yellow-900">Rp {{ number_format($totalPinjaman, 2, ',', '.') }}</div>
                        </div>

                        {{-- Kotak Pinjaman Jatuh Tempo --}}
                        <div class="bg-red-100 p-6 rounded-lg shadow-md border-l-4 border-red-500">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-red-800">Pinjaman Jatuh Tempo</span>
                                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-2 text-3xl font-bold text-red-900">{{ number_format($pinjamanJatuhTempo) }}</div>
                        </div>
                    </div>

                    {{-- Bagian untuk Grafik --}}
                    <h3 class="text-xl font-bold mb-4 mt-8">Analisis Keuangan</h3>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <canvas id="simpanan-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ambil data dari server yang dikirim oleh controller
            const chartData = @json($simpananBulanan);
            
            // Buat data yang diformat untuk Chart.js
            const labels = chartData.map(item => `Bulan ${item.bulan}`);
            const data = chartData.map(item => item.total);

            const ctx = document.getElementById('simpanan-chart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Simpanan per Bulan',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(200, 200, 200, 0.2)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>