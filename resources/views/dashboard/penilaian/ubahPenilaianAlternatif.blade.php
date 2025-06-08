@extends('dashboard.layouts.app')

@section('container')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ $judul }} <span class="text-purple-600 dark:text-purple-300"></span>
        </h2>
    </div>

    <div>
        <section class="mt-3">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden py-5 pl-10">
                    <form
                        action="{{ route('penilaian.perbarui', ['alternatif_id' => request('alternatif_id') ?? ($data->first()->alternatif_id ?? '')]) }}"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="number" name="alternatif_id"
                            value="{{ request('alternatif_id') ?? ($data->first()->alternatif_id ?? '') }}" hidden>
                        @foreach ($criterias as $item)
                            <div class="form-control w-full max-w-md">
                                <div class="form-control w-full max-w-xs mb-3">
                                    <label class="label">
                                        <span
                                            class="label-text font-bold text-purple-700 dark:text-purple-300">{{ $item->nama }}</span>
                                    </label>
                                    @php
                                        // Ambil nilai dari data jika ada, jika tidak kosong
                                        $nilai = $data->where('kriteria_id', $item->id)->first()->nilai ?? '';
                                    @endphp
                                    <input type="number" name="nilai[{{ $item->id }}]" class="input input-bordered"
                                        min="0" max="100" value="{{ $nilai }}" required
                                        placeholder="Masukkan nilai 0-100">
                                </div>
                            </div>
                        @endforeach
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ route('penilaian') }}"
                                class="btn normal-case bg-gray-300 hover:bg-gray-400 hover:border-gray-400 hover:text-white">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
