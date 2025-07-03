@extends('dashboard.layouts.app')

@section('container')
    <div class="container mx-auto grid px-6">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Dashboard
        </h2>

        {{-- Card 1 --}}
        <div class="mb-8 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <!-- Card -->
            <div class="shadow-xs flex items-center rounded-lg bg-white p-4 dark:bg-gray-800">
                <div class="mr-4 rounded-full bg-orange-100 p-3 text-orange-500 dark:bg-orange-500 dark:text-orange-100">
                    <i class="ri-table-fill text-xl"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        Kriteria
                    </p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                        {{ $kriteria->count() }}
                    </p>
                </div>
            </div>

            <!-- Card -->
            <div class="shadow-xs flex items-center rounded-lg bg-white p-4 dark:bg-gray-800">
                <div class="mr-4 rounded-full bg-teal-100 p-3 text-teal-500 dark:bg-teal-500 dark:text-teal-100">
                    <i class="ri-braces-fill text-xl"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        Alternatif
                    </p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                        {{ $alternatif->count() }}
                    </p>
                </div>
            </div>

            <div class="flex space-x-3 items-center">
                <button id="btn-reset-alternatif"
                    class="btn text-white dark:text-gray-800 normal-case bg-teal-500 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-purple-300 dark:hover:bg-opacity-90">
                    <i class="ri-refresh-line text-lg"></i>
                    Reset Alternatif
                </button>

                <button id="btn-reset-semua"
                    class="btn text-white dark:text-gray-800 normal-case bg-red-600 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-red-300 dark:hover:bg-opacity-90">
                    <i class="ri-refresh-line text-lg"></i>
                    Reset Semua
                </button>
            </div>
        </div>
        {{-- Card 2 - Single Banner WP --}}
        <div class="mb-8">
            <div class="shadow-xs min-w-0 rounded-lg bg-gradient-to-r from-teal-600 to-green-600 p-8 text-white">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="lg:w-2/3">
                        <h4 class="mb-4 text-2xl font-bold">
                            Sistem Pendukung Keputusan - Weighted Product (WP)
                        </h4>
                        <p class="mb-4 text-lg text-indigo-100">
                            Metode WP adalah salah satu metode penyelesaian masalah Multi Attribute Decision Making (MADM)
                            yang menggunakan perkalian untuk menghubungkan rating atribut, dimana rating setiap atribut
                            harus dipangkatkan dulu dengan bobot atribut yang bersangkutan.
                        </p>

                        <div class="mb-6">
                            <h5 class="mb-3 text-lg font-semibold text-indigo-100">Keunggulan Metode WP:</h5>
                            <ul class="space-y-2 text-indigo-100">
                                <li class="flex items-start">
                                    <i class="ri-check-line mt-1 mr-2 text-green-300"></i>
                                    <span>Perhitungan matematis sederhana dan mudah dipahami</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line mt-1 mr-2 text-green-300"></i>
                                    <span>Proses perangkingan yang efektif dengan perkalian bobot</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line mt-1 mr-2 text-green-300"></i>
                                    <span>Dapat menangani kriteria benefit dan cost secara bersamaan</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="ri-check-line mt-1 mr-2 text-green-300"></i>
                                    <span>Hasil lebih akurat dengan normalisasi bobot yang tepat</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="lg:w-2/3 lg:text-left">
                        <div class="mb-6">
                            <h6 class="text-sm font-medium text-indigo-200 mb-2">Rumus WP:</h6>
                            <div class="bg-white text-black bg-opacity-10 rounded-lg p-3 text-sm font-mono">
                                S<sub>i</sub> = ∏<sup>n</sup><sub>j=1</sub> X<sub>ij</sub><sup>w<sub>j</sub></sup>
                            </div>
                        </div>

                        <a class="inline-flex items-center px-6 py-3 bg-white text-teal-600 font-semibold rounded-lg shadow-lg hover:bg-indigo-50 transition-all duration-200 group"
                            href="{{ route('kriteria') }}">
                            <span>Mulai Perhitungan</span>
                            <i
                                class="ri-arrow-right-line ml-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- Chart --}}
        <div class="mb-8 grid gap-6">
            <div class="shadow-xs min-w-0 rounded-lg bg-white p-4 dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                    Hasil Perhitungan WP
                </h4>
                <canvas id="line"></canvas>
                <div class="mt-4 flex justify-center space-x-3 text-sm text-gray-600 dark:text-gray-400">
                    <!-- Chart legend -->
                    <div class="flex items-center">
                        <span class="mr-1 inline-block h-3 w-3 rounded-full bg-[#14B8A6]"></span>
                        <span>Alternatif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let hasilSolusiData = [];
        @foreach ($hasilSolusi as $item)
            hasilSolusiData.push(' {{ $item->nama_alternatif }} ');
        @endforeach

        const lineConfig = {
            type: 'line',
            data: {
                labels: hasilSolusiData,
                datasets: [{
                    label: 'Nilai',
                    backgroundColor: '#14B8A6',
                    borderColor: '#14B8A6',
                    data: [{{ $hasilNilaiData }}],
                    fill: false,
                }, ],
            },
            options: {
                responsive: true,
                legend: {
                    display: false,
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,
                },
                scales: {
                    x: {
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Alternatif',
                        },
                    },
                    y: {
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value',
                        },
                    },
                },
            },
        }

        $('#btn-reset-alternatif').on('click', function() {
            Swal.fire({
                title: 'Reset Data Alternatif?',
                text: "Semua data alternatif dan penilaian akan dihapus. Data tidak dapat dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#14B8A6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Ya, reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('reset.alternatif') }}";
                }
            });
        });

        $('#btn-reset-semua').on('click', function() {
            Swal.fire({
                title: 'Reset Semua Data?',
                text: "Seluruh data (kriteria, alternatif, hasil) akan dihapus. Data tidak dapat dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#14B8A6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Ya, reset semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('reset.semua') }}";
                }
            });
        });

        // change this to the id of your chart element in HMTL
        const lineCtx = document.getElementById('line')
        window.myLine = new Chart(lineCtx, lineConfig)
    </script>
@endsection
