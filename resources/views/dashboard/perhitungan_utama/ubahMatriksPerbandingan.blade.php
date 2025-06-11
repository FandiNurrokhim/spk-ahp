@extends('dashboard.layouts.app')

@section('container')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ $judul }} <span class="text-purple-600 dark:text-purple-300">{{ $namaKriteria->nama }}</span>
        </h2>
    </div>

    <div>
        <section class="mt-3">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden py-5 pl-10">
                    <form action="/dashboard/kriteria/matriks_perbandingan" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="number" name="kriteria_id" value="{{ $namaKriteria->id }}" hidden>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3">Kriteria Pertama</th>
                                        <th class="px-4 py-3">Penilaian</th>
                                        <th class="px-4 py-3">Kriteria Kedua</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($matriksPerbandingan as $item)
                                        <tr>
                                            <td class="px-4 py-2 w-1/3">
                                                <input type="text" class="input input-bordered w-full bg-gray-100"
                                                    value="{{ $item->nama_kriteria }}" readonly>
                                            </td>
                                            <td class="px-4 py-2 w-1/3">
                                                @if ($item->kriteria_id == $item->kriteria_id_banding)
                                                    <input type="number" name="{{ $item->kriteria_id_banding }}"
                                                        class="input input-bordered w-full text-gray-800 bg-gray-100"
                                                        value="{{ $item->nilai }}" required readonly />
                                                @else
                                                    <select name="{{ $item->kriteria_id_banding }}"
                                                        class="select select-bordered w-full" required>
                                                        <option value=""
                                                            {{ is_null($item->nilai) || floatval($item->nilai) == 0.0 ? 'selected' : '' }}>
                                                            -- Pilih Nilai --
                                                        </option>
                                                        @php
                                                            $nilaiOptions = [
                                                                "0.111" => '1/9 - Kebalikan dari nilai 9',
                                                                "0.125" => '1/8 - Kebalikan dari nilai 8',
                                                                "0.142" => '1/7 - Kebalikan dari nilai 7',
                                                                "0.166" => '1/6 - Kebalikan dari nilai 6',
                                                                "0.2" => '1/5 - Kebalikan dari nilai 5',
                                                                "0.25" => '1/4 - Kebalikan dari nilai 4',
                                                                "0.333" => '1/3 - Kebalikan dari nilai 3',
                                                                "0.5" => '1/2 - Kebalikan dari nilai 2',
                                                                1 => '1 - Sama penting',
                                                                2 => '2 - Nilai antara sama dan sedikit lebih penting',
                                                                3 => '3 - Sedikit lebih penting',
                                                                4 => '4 - Nilai antara 3 dan 5',
                                                                5 => '5 - Lebih penting',
                                                                6 => '6 - Nilai antara 5 dan 7',
                                                                7 => '7 - Sangat penting',
                                                                8 => '8 - Nilai antara 7 dan 9',
                                                                9 => '9 - Mutlak lebih penting',
                                                            ];
                                                        @endphp
                                                        @foreach ($nilaiOptions as $nilai => $label)
                                                            <option value={{ $nilai }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 w-1/3">
                                                <input type="text" class="input input-bordered w-full bg-gray-100"
                                                    value="{{ $item->nama_kriteria_banding }}" readonly>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ route('perhitungan_utama') }}"
                                class="btn normal-case bg-gray-300 hover:bg-gray-400 hover:border-gray-400 hover:text-white">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
