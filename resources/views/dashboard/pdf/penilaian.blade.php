@extends("dashboard.pdf.layouts.app")

@section("container")
    <div class="container mx-auto grid px-6">
        <h2 class="judul-laporan my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ $judul }}
        </h2>
    </div>

    <section class="mt-3">
        <div class="table-pdf mx-auto max-w-screen-xl px-4 lg:px-12">
            {{-- Tabel Penilaian Alternatif --}}
            <div class="relative mb-7 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                <div class="d mb-5 flex items-center justify-between p-4">
                    <div class="flex space-x-3">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Penilaian Alternatif</h2>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto p-3">
                    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-top: 1em; padding-bottom: 1em;">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3">Alternatif</th>
                                @foreach ($kriteria as $item)
                                    <th scope="col" class="px-4 py-3">{{ $item->nama }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->unique("alternatif_id") as $item)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3 font-semibold uppercase text-gray-700 dark:text-gray-400">
                                        {{ $item->alternatif->nama }}
                                    </td>
                                     @foreach ($kriteria as $k)
                                            @php
                                                $nilai =
                                                    $data
                                                        ->where('alternatif_id', $item->alternatif_id)
                                                        ->where('kriteria_id', $k->id)
                                                        ->first()->nilai ?? '-';
                                            @endphp
                                            <td class="px-4 py-3 text-center">{{ $nilai }}</td>
                                        @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Prioritas Kriteria --}}
            <div class="relative mb-7 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                <div class="d mb-5 flex items-center justify-between p-4">
                    <div class="flex space-x-3">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Prioritas Kriteria</h2>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto p-3">
                    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-top: 1em; padding-bottom: 1em;">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3">Kriteria</th>
                                <th scope="col" class="px-4 py-3">Prioritas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($matriksNilaiKriteria as $item)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3 font-semibold uppercase text-gray-700 dark:text-gray-400">
                                        {{ $item->nama_kriteria }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold uppercase text-gray-700 dark:text-gray-400">
                                        {{ round($item->prioritas, 3) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Hasil AHP --}}
            <div class="relative mb-7 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                <div class="d mb-5 flex items-center justify-between p-4">
                    <div class="flex space-x-3">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Hasil Perhitungan</h2>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto p-3">
                    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; padding-top: 1em; padding-bottom: 1em;">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3">Alternatif</th>
                                <th scope="col" class="px-4 py-3">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil as $item)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3 font-semibold uppercase text-gray-700 dark:text-gray-400">
                                        {{ $item->nama_alternatif }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold uppercase text-gray-700 dark:text-gray-400">
                                        {{ round($item->nilai, 3) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
