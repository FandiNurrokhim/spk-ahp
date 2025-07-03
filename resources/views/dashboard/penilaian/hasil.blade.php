@extends('dashboard.layouts.app')

@section('container')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ $judul }}
        </h2>
    </div>

    <div>
        <section class="mt-3">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
                <div class="flex justify-start items-center mb-5">
                    <div class="flex space-x-3">
                        <div class="flex justify-start items-center mb-5">
                            <div class="flex space-x-3">
                                <div class="flex space-x-3 items-center">
                                    <form action="{{ route('wp.hitung') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 rounded-lg text-white dark:text-gray-800 normal-case bg-teal-500 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-teal-300 dark:hover:bg-opacity-90 font-medium text-sm transition duration-150 ease-in-out">
                                            <i class="ri-calculator-fill mr-2 text-lg"></i>
                                            Hitung Ulang Weighted Product
                                        </button>
                                    </form>
                                </div>
                                <div class="flex space-x-3 items-center">
                                    <form action="{{ route('penilaian.pdf_hasil') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg text-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                            <i class="ri-file-pdf-2-fill mr-2 text-lg"></i>
                                            Unduh Hasil PDF
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel 1: Bobot Relatif Kriteria --}}
                <div class="mb-7 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex justify-between items-center p-4 mb-5">
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">1. Bobot Relatif Kriteria
                                    (wj)</h2>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    @foreach ($kriteria as $item)
                                        <th scope="col" class="px-4 py-3">{{ $item->kode ?? 'C' . $loop->iteration }}
                                        </th>
                                    @endforeach
                                    <th scope="col" class="px-4 py-3">Σ wj</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-gray-700">
                                    @foreach ($kriteria as $item)
                                        <td class="px-4 py-3 text-lg font-semibold">
                                            {{ isset($bobotRelatif[$item->id]) ? number_format($bobotRelatif[$item->id], 9) : '0' }}
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 text-lg font-bold bg-blue-50">1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabel 2: Matriks Perbandingan Alternatif dan Kriteria --}}
                <div class="mb-7 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex justify-between items-center p-4 mb-5">
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">2. Matriks Perbandingan
                                    Alternatif dan Kriteria</h2>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Alternatif / Kriteria</th>
                                    @foreach ($kriteria as $item)
                                        <th scope="col" class="px-4 py-3">{{ $item->kode ?? 'C' . $loop->iteration }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alternatif as $alt)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-semibold">{{ $alt->kode ?? 'A' . $loop->iteration }}</td>
                                        @foreach ($kriteria as $krit)
                                            @php
                                                $penilaian = DB::table('penilaian')
                                                    ->where('alternatif_id', $alt->id)
                                                    ->where('kriteria_id', $krit->id)
                                                    ->first();
                                            @endphp
                                            <td class="px-4 py-3">{{ $penilaian ? $penilaian->nilai : 0 }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabel 3: Pangkat (wj untuk cost = negatif) --}}
                <div class="mb-7 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex justify-between items-center p-4 mb-5">
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">3. Pangkat (wj untuk Cost =
                                    negatif)</h2>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Pangkat</th>
                                    @foreach ($kriteria as $item)
                                        <th scope="col" class="px-4 py-3">{{ $item->kode ?? 'C' . $loop->iteration }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3 font-semibold">Nilai Pangkat</td>
                                    @foreach ($kriteria as $item)
                                        @php
                                            $bobot = $bobotRelatif[$item->id] ?? 0;
                                            if ($item->jenis == 'cost') {
                                                $bobot = -$bobot;
                                            }
                                        @endphp
                                        <td class="px-4 py-3 {{ $item->jenis == 'cost' ? 'text-red-600 font-bold' : '' }}">
                                            {{ number_format($bobot, 9) }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabel 4: Perhitungan Nilai Vektor S --}}
                <div class="mb-7 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex justify-between items-center p-4 mb-5">
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">4. Perhitungan Nilai Vektor S
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Alternatif</th>
                                    <th scope="col" class="px-4 py-3">Nilai S</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alternatif as $alt)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-semibold">{{ $alt->kode ?? 'A' . $loop->iteration }}</td>
                                        <td class="px-4 py-3">
                                            {{ isset($nilaiS[$alt->id]) ? number_format($nilaiS[$alt->id], 9) : '0' }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-blue-50 font-bold">
                                    <td class="px-4 py-3">Jumlah</td>
                                    <td class="px-4 py-3">{{ number_format(array_sum($nilaiS ?? []), 9) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabel 5: Perhitungan Nilai Preferensi Relatif (Vektor V) --}}
                <div class="mb-7 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex justify-between items-center p-4 mb-5">
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">5. Perhitungan Nilai
                                    Preferensi Relatif (Vektor V)</h2>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Alternatif</th>
                                    <th scope="col" class="px-4 py-3">Nilai V</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alternatif as $alt)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-semibold">{{ $alt->kode ?? 'A' . $loop->iteration }}</td>
                                        <td class="px-4 py-3">
                                            {{ isset($nilaiV[$alt->id]) ? number_format($nilaiV[$alt->id], 9) : '0' }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-blue-50 font-bold">
                                    <td class="px-4 py-3">Jumlah</td>
                                    <td class="px-4 py-3">1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabel 6: Ranking Alternatif --}}
                <div class="mb-7 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex justify-between items-center p-4 mb-5">
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">6. Ranking Alternatif</h2>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Alternatif</th>
                                    <th scope="col" class="px-4 py-3">Nama</th>
                                    <th scope="col" class="px-4 py-3">Nilai V</th>
                                    <th scope="col" class="px-4 py-3">Ranking</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sortedAlternatif = $alternatif->sortBy(function ($alt) use ($ranking) {
                                        return $ranking[$alt->id] ?? 999;
                                    });
                                @endphp
                                @foreach ($sortedAlternatif as $alt)
                                    <tr
                                        class="border-b dark:border-gray-700 {{ isset($ranking[$alt->id]) && $ranking[$alt->id] == 1 ? 'bg-green-50' : '' }}">
                                        <td class="px-4 py-3 font-semibold">{{ $alt->kode ?? 'A' . $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-3">{{ $alt->nama }}</td>
                                        <td class="px-4 py-3">
                                            {{ isset($nilaiV[$alt->id]) ? number_format($nilaiV[$alt->id], 9) : '0' }}</td>
                                        <td class="px-4 py-3">
                                            @if (isset($ranking[$alt->id]))
                                                <span
                                                    class="px-2 py-1 text-sm font-semibold rounded-full 
                                                    {{ $ranking[$alt->id] == 1
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($ranking[$alt->id] == 2
                                                            ? 'bg-blue-100 text-blue-800'
                                                            : 'bg-gray-100 text-gray-800') }}">
                                                    {{ $ranking[$alt->id] }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@section('js')
    <script>
        @if (session()->has('berhasil'))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session('berhasil') }}',
                icon: 'success',
                confirmButtonColor: '#14B8A6',
                confirmButtonText: 'OK',
            })
        @endif

        @if (session()->has('gagal'))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session('gagal') }}',
                icon: 'error',
                confirmButtonColor: '#14B8A6',
                confirmButtonText: 'OK',
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                title: 'Gagal',
                text: @foreach ($errors->all() as $error)
                    '{{ $error }}'
                @endforeach ,
                icon: 'error',
                confirmButtonColor: '#14B8A6',
                confirmButtonText: 'OK',
            })
        @endif
    </script>
@endsection
