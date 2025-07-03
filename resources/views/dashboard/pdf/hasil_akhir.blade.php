@extends('dashboard.pdf.layouts.app')

@section('container')
    <div class="container mx-auto grid px-6">
        <h2 class="judul-laporan my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ $judul }}
        </h2>
    </div>

    <section class="mt-3">
        <div class="table-pdf mx-auto max-w-screen-xl px-4 lg:px-12">
            {{-- Tabel Hasil Akhir --}}
            <div class="relative mb-7 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                <div class="d mb-5 flex items-center justify-between p-4">
                    <div class="flex space-x-3">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Hasil Perhitungan WP -
                                Perangkingan</h2>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto p-3">
                    <table id="tabel_data_hasil"
                        class="nowrap w-full text-lg text-left text-gray-500 dark:text-gray-400 stripe hover"
                        style="width:100%; padding-top: 1em; padding-bottom: 1em;">
                        <thead class="text-xs text-gray-700 uppercase bg-teal-500 text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-center">Ranking</th>
                                <th scope="col" class="px-4 py-3">Alternatif</th>
                                <th scope="col" class="px-4 py-3 text-center">Nilai WP</th>
                                <th scope="col" class="px-4 py-3 text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil as $index => $item)
                                <tr class="border-b dark:border-gray-700 {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-400 font-semibold">
                                        {{ $item->nama_alternatif }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-400 font-semibold text-center">
                                        {{ number_format($item->nilai, 4) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-400 font-semibold text-center">
                                        {{ number_format(($item->nilai / $hasil->sum('nilai')) * 100, 2) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Detail Perhitungan WP --}}
            <div class="relative mb-7 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                <div class="d mb-5 flex items-center justify-between p-4">
                    <div class="flex space-x-3">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Detail Perhitungan Metode WP</h2>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    {{-- Kriteria dan Bobot --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">1. Kriteria dan Bobot</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-lg text-left text-gray-500 dark:text-gray-400 border border-gray-300">
                                <thead class="text-xs text-gray-700 uppercase bg-teal-500 text-white">
                                    <tr>
                                        <th class="px-4 py-3 border-r border-teal-300 text-center">Kode</th>
                                        <th class="px-4 py-3 border-r border-teal-300">Kriteria</th>
                                        <th class="px-4 py-3 border-r border-teal-300 text-center">Bobot (%)</th>
                                        <th class="px-4 py-3 border-r border-teal-300 text-center">Bobot Normalisasi (W)
                                        </th>
                                        <th class="px-4 py-3 text-center">Jenis</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kriteria as $k)
                                        <tr class="border-b {{ $loop->index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                            <td class="px-4 py-3 font-semibold text-center border-r border-gray-300">
                                                {{ $k->kode }}</td>
                                            <td class="px-4 py-3 border-r border-gray-300">{{ $k->nama }}</td>
                                            <td class="px-4 py-3 text-center border-r border-gray-300">{{ $k->bobot }}%
                                            </td>
                                            <td class="px-4 py-3 text-center border-r border-gray-300">
                                                {{ number_format($k->bobot / 100, 3) }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full 
                                                    {{ $k->jenis == 'benefit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($k->jenis) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Matriks Penilaian --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">2. Matriks Penilaian
                            Alternatif</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-lg text-left text-gray-500 dark:text-gray-400 border border-gray-300">
                                <thead class="text-xs text-gray-700 uppercase bg-teal-500 text-white">
                                    <tr>
                                        <th class="px-4 py-3 border-r border-teal-300">Alternatif</th>
                                        @foreach ($kriteria as $k)
                                            <th
                                                class="px-4 py-3 border-r border-teal-300 text-center {{ $loop->last ? '' : 'border-r' }}">
                                                {{ $k->kode }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penilaianMatrix as $altNama => $nilai)
                                        <tr class="border-b {{ $loop->index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                            <td class="px-4 py-3 font-semibold border-r border-gray-300">
                                                {{ $altNama }}</td>
                                            @foreach ($kriteria as $k)
                                                <td class="px-4 py-3 text-center border-r border-gray-300">
                                                    {{ $nilai[$k->id] ?? '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Perhitungan S (Vector S) --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">3. Perhitungan Vector S</h3>
                        <div class="bg-teal-50 border-l-4 border-teal-400 p-4 rounded-lg mb-4">
                            <p class="text-lg text-gray-600">
                                <strong>Rumus:</strong> S<sub>i</sub> = ∏<sup>n</sup><sub>j=1</sub>
                                X<sub>ij</sub><sup>W<sub>j</sub></sup>
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Dimana W<sub>j</sub> bernilai positif untuk kriteria benefit dan negatif untuk kriteria cost
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-lg text-left text-gray-500 dark:text-gray-400 border border-gray-300">
                                <thead class="text-xs text-gray-700 uppercase bg-teal-500 text-white">
                                    <tr>
                                        <th class="px-4 py-3 border-r border-teal-300">Alternatif</th>
                                        <th class="px-4 py-3 border-r border-teal-300">Perhitungan</th>
                                        <th class="px-4 py-3 text-center">Nilai S</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vectorS as $altNama => $sValue)
                                        <tr class="border-b {{ $loop->index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                            <td class="px-4 py-3 font-semibold border-r border-gray-300">
                                                {{ $altNama }}</td>
                                            <td class="px-4 py-3 text-xs border-r border-gray-300">
                                                @php
                                                    $calculation = [];
                                                    foreach ($kriteria as $k) {
                                                        $nilai = $penilaianMatrix[$altNama][$k->id] ?? 0;
                                                        $bobot = ($k->jenis == 'benefit' ? 1 : -1) * ($k->bobot / 100);
                                                        $calculation[] = $nilai . '^(' . number_format($bobot, 3) . ')';
                                                    }
                                                @endphp
                                                {{ implode(' × ', $calculation) }}
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-center">{{ number_format($sValue, 6) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-teal-100 font-bold border-t-2 border-teal-500">
                                        <td class="px-4 py-3 text-center border-r border-gray-300" colspan="2">Total ∑S
                                        </td>
                                        <td class="px-4 py-3 text-center">{{ number_format($vectorS->sum(), 6) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Perhitungan V (Vector V) --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">4. Perhitungan Vector V
                            (Nilai Preferensi)</h3>
                        <div class="bg-teal-50 border-l-4 border-teal-400 p-4 rounded-lg mb-4">
                            <p class="text-lg text-gray-600">
                                <strong>Rumus:</strong> V<sub>i</sub> = S<sub>i</sub> / ∑S<sub>i</sub>
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Total ∑S = {{ number_format($vectorS->sum(), 6) }}
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-lg text-left text-gray-500 dark:text-gray-400 border border-gray-300">
                                <thead class="text-xs text-gray-700 uppercase bg-teal-500 text-white">
                                    <tr>
                                        <th class="px-4 py-3 border-r border-teal-300">Alternatif</th>
                                        <th class="px-4 py-3 border-r border-teal-300">Perhitungan</th>
                                        <th class="px-4 py-3 border-r border-teal-300 text-center">Nilai V</th>
                                        <th class="px-4 py-3 text-center">Ranking</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hasil as $index => $item)
                                        <tr class="border-b {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                            <td class="px-4 py-3 font-semibold border-r border-gray-300">
                                                {{ $item->nama_alternatif }}</td>
                                            <td class="px-4 py-3 text-xs border-r border-gray-300">
                                                {{ number_format($vectorS[$item->nama_alternatif], 6) }} /
                                                {{ number_format($vectorS->sum(), 6) }}
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-center border-r border-gray-300">
                                                {{ number_format($item->nilai, 6) }}</td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $index + 1 }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-teal-100 font-bold border-t-2 border-teal-500">
                                        <td class="px-4 py-3 text-center border-r border-gray-300" colspan="2">Total</td>
                                        <td class="px-4 py-3 text-center border-r border-gray-300">1.000000</td>
                                        <td class="px-4 py-3 text-center">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Simpulan --}}
            <div class="relative mb-7 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Simpulan</h2>
                    <div class="bg-teal-50 border-l-4 border-teal-400 p-4">
                        <p class="text-gray-700">
                            Berdasarkan hasil perhitungan menggunakan metode <strong>Weighted Product (WP)</strong>,
                            alternatif terbaik adalah <strong>{{ $hasil->first()->nama_alternatif }}</strong>
                            dengan nilai preferensi <strong>{{ number_format($hasil->first()->nilai, 4) }}</strong>
                            atau
                            <strong>{{ number_format(($hasil->first()->nilai / $hasil->sum('nilai')) * 100, 2) }}%</strong>
                            dari total keseluruhan.
                        </p>

                        @if ($hasil->count() > 1)
                            <p class="text-gray-700 mt-3">
                                Urutan ranking alternatif adalah:
                            </p>
                            <ol class="mt-2 text-gray-700">
                                @foreach ($hasil as $index => $item)
                                    <li class="flex items-center mt-1">
                                        <strong>{{ $item->nama_alternatif }}</strong>
                                        ({{ number_format($item->nilai, 4) }})
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
