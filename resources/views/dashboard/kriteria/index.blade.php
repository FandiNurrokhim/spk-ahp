@extends('dashboard.layouts.app')

@section('container')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ $judul }}
        </h2>
    </div>

    <div>
        <section class="mt-10">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex justify-start items-center d p-4">
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <label for="add_button"
                                    class="btn  btn-sm text-white dark:text-gray-800 normal-case bg-teal-500 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-teal-300 dark:hover:bg-opacity-90">
                                    <i class="ri-add-fill"></i>
                                    Tambah {{ $judul }}
                                </label>
                                <label for="import_button"
                                    class="btn btn-sm text-white dark:text-gray-800 normal-case bg-green-600 hover:bg-green-600 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-green-300 dark:hover:bg-green-300 dark:hover:bg-opacity-90 dark:border-green-300">
                                    <i class="ri-file-excel-line"></i>
                                    Import Data
                                </label>
                                <a href="/dashboard/download-template-kriteria"
                                    class="btn btn-sm text-white dark:text-gray-800 normal-case bg-blue-600 hover:bg-blue-600 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-blue-300 dark:hover:bg-blue-300 dark:hover:bg-opacity-90 dark:border-blue-300">
                                    <i class="ri-file-download-line"></i>
                                    Unduh Template Excel
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3">
                        <table id="tabel_data"
                            class="w-full text-sm text-left text-gray-500 dark:text-gray-400 stripe hover"
                            style="width:100%; padding-top: 1em; padding-bottom: 1em;">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Kode</th>
                                    <th scope="col" class="px-4 py-3">Nama</th>
                                    <th scope="col" class="px-4 py-3">Bobot</th>
                                    <th scope="col" class="px-4 py-3">Jenis</th>
                                    <th scope="col" class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $item->kode }}</td>
                                        <td class="px-4 py-3">{{ $item->nama }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $item->bobot }}</td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-sm font-semibold rounded-full 
                                                {{ $item->jenis == 'benefit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($item->jenis) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <label for="edit_button" class="btn btn-sm btn-warning text-white"
                                                onclick="return edit_button('{{ $item->id }}')">
                                                <i class="ri-pencil-line"></i>
                                            </label>
                                            <button class="btn btn-sm btn-error text-white"
                                                onclick="return delete_button('{{ $item->id }}', '{{ $item->nama }}');">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="text-xs text-gray-700 uppercase bg-gray-100 font-bold">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center">Total Bobot</td>
                                    <td class="px-4 py-3 text-lg">{{ $data->sum('bobot') }}</td>
                                    <td colspan="2" class="px-4 py-3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Tabel Bobot Relatif Kriteria --}}
                @if ($data->count() > 0)
                    <div class="mb-7 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden mt-6">
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
                                        @foreach ($data as $item)
                                            <th scope="col" class="px-4 py-3">{{ $item->kode ?? 'C' . $loop->iteration }}
                                            </th>
                                        @endforeach
                                        <th scope="col" class="px-4 py-3">Σ wj</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b dark:border-gray-700">
                                        @php
                                            $totalBobot = $data->sum('bobot');
                                        @endphp
                                        @foreach ($data as $item)
                                            @php
                                                $bobotRelatif = $totalBobot > 0 ? $item->bobot / $totalBobot : 0;
                                            @endphp
                                            <td class="px-4 py-3 text-lg font-semibold">
                                                {{ number_format($bobotRelatif, 9) }}
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-3 text-lg font-bold bg-blue-50">1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-7 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center p-4 mb-5">
                            <div class="flex space-x-3">
                                <div class="flex space-x-3 items-center">
                                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">2. Pangkat (wj untuk Cost
                                        = negatif)</h2>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto p-3">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Pangkat</th>
                                        @foreach ($data as $item)
                                            <th scope="col" class="px-4 py-3">{{ $item->kode ?? 'C' . $loop->iteration }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-semibold">Nilai Pangkat</td>
                                        @php
                                            $totalBobot = $data->sum('bobot');
                                        @endphp
                                        @foreach ($data as $item)
                                            @php
                                                $bobotRelatif = $totalBobot > 0 ? $item->bobot / $totalBobot : 0;
                                                if ($item->jenis == 'cost') {
                                                    $bobotRelatif = -$bobotRelatif;
                                                }
                                            @endphp
                                            <td
                                                class="px-4 py-3 {{ $item->jenis == 'cost' ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }}">
                                                {{ number_format($bobotRelatif, 9) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Form Tambah Data --}}
            <input type="checkbox" id="add_button" class="modal-toggle" />
            <div class="modal">
                <div class="modal-box">
                    <form action="{{ route('kriteria.simpan') }}" method="post" enctype="multipart/form-data">
                        <h3 class="font-bold text-lg">Tambah {{ $judul }}</h3>
                        @csrf
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Kode</span>
                            </label>
                            <input type="text" name="kode" placeholder="Type here"
                                class="input input-bordered w-full max-w-xs text-gray-800 bg-slate-100"
                                value="{{ $kode }}" readonly required />
                            <label class="label">
                                @error('kode')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Nama</span>
                            </label>
                            <input type="text" name="nama" placeholder="Type here"
                                class="input input-bordered w-full max-w-xs text-gray-800" value="{{ old('nama') }}"
                                required />
                            <label class="label">
                                @error('nama')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Bobot</span>
                            </label>
                            <input type="number" name="bobot" placeholder="1-10" min="1" max="10"
                                class="input input-bordered w-full max-w-xs text-gray-800" value="{{ old('bobot') }}"
                                required />
                            <label class="label">
                                @error('bobot')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Jenis</span>
                            </label>
                            <select name="jenis" class="select select-bordered w-full max-w-xs text-gray-800" required>
                                <option value="">Pilih Jenis</option>
                                <option value="benefit" {{ old('jenis') == 'benefit' ? 'selected' : '' }}>Benefit</option>
                                <option value="cost" {{ old('jenis') == 'cost' ? 'selected' : '' }}>Cost</option>
                            </select>
                            <label class="label">
                                @error('jenis')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="modal-action">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <label for="add_button" class="btn">Batal</label>
                        </div>
                    </form>
                </div>
                <label class="modal-backdrop" for="add_button">Close</label>
            </div>

            {{-- Form Ubah Data --}}
            <input type="checkbox" id="edit_button" class="modal-toggle" />
            <div class="modal">
                <div class="modal-box" id="edit_form">
                    <form action="{{ route('kriteria.perbarui') }}" method="post" enctype="multipart/form-data">
                        <h3 class="font-bold text-lg">Ubah {{ $judul }}: <span class="text-greenPrimary"
                                id="title_form"><span class="loading loading-dots loading-md"></span></span></h3>
                        @csrf
                        <input type="text" name="id" hidden />
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Kode</span>
                                <span class="label-text-alt" id="loading_edit1"></span>
                            </label>
                            <input type="text" name="kode" placeholder="Type here"
                                class="input input-bordered w-full text-gray-800" required />
                            <label class="label">
                                @error('kode')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Nama</span>
                                <span class="label-text-alt" id="loading_edit2"></span>
                            </label>
                            <input type="text" name="nama" placeholder="Type here"
                                class="input input-bordered w-full text-gray-800" required />
                            <label class="label">
                                @error('nama')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Bobot</span>
                                <span class="label-text-alt" id="loading_edit3"></span>
                            </label>
                            <input type="number" name="bobot" placeholder="1-10" min="1" max="10"
                                class="input input-bordered w-full text-gray-800" required />
                            <label class="label">
                                @error('bobot')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Jenis</span>
                                <span class="label-text-alt" id="loading_edit4"></span>
                            </label>
                            <select name="jenis" class="select select-bordered w-full text-gray-800" required>
                                <option value="">Pilih Jenis</option>
                                <option value="benefit">Benefit</option>
                                <option value="cost">Cost</option>
                            </select>
                            <label class="label">
                                @error('jenis')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="modal-action">
                            <button type="submit" class="btn btn-success">Perbarui</button>
                            <label for="edit_button" class="btn">Batal</label>
                        </div>
                    </form>
                </div>
                <label class="modal-backdrop" for="edit_button">Close</label>
            </div>

            {{-- Import Data --}}
            <input type="checkbox" id="import_button" class="modal-toggle" />
            <div class="modal">
                <div class="modal-box">
                    <form action="{{ route('kriteria.import') }}" method="post" enctype="multipart/form-data">
                        <h3 class="font-bold text-lg">Import {{ $judul }}</h3>
                        @csrf
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Import File</span>
                            </label>
                            <input type="file" name="import_data"
                                class="file-input file-input-bordered w-full max-w-xs"
                                accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                                required />
                            <label class="label">
                                @error('import_data')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="modal-action">
                            <button type="submit" class="btn btn-success">Import</button>
                            <label for="import_button" class="btn">Batal</label>
                        </div>
                    </form>
                </div>
                <label class="modal-backdrop" for="import_button">Close</label>
            </div>
        </section>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#tabel_data').DataTable({
                    responsive: true,
                    order: [],
                })
                .columns.adjust()
                .responsive.recalc();
        });

        @if (session()->has('berhasil'))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session('berhasil') }}',
                icon: 'success',
                confirmButtonColor: '#14B8A6',
                confirmButtonText: 'OK',
            });
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

        window.edit_button = function(id) {
            let loading = `<span class="loading loading-dots loading-md text-teal-600"></span>`;
            $("#title_form").html(loading);
            $("#loading_edit1").html(loading);
            $("#loading_edit2").html(loading);
            $("#loading_edit3").html(loading);
            $("#loading_edit4").html(loading);

            $.ajax({
                type: "get",
                url: "{{ route('kriteria.ubah') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(data) {
                    let items = [];
                    $.each(data, function(key, val) {
                        items.push(val);
                    });

                    $("#title_form").html(`${items[2]}`);
                    $("input[name='id']").val(items[0]);
                    $("input[name='kode']").val(items[1]);
                    $("input[name='nama']").val(items[2]);
                    $("input[name='bobot']").val(items[3]);
                    $("select[name='jenis']").val(items[4]);

                    $("#loading_edit1").html("");
                    $("#loading_edit2").html("");
                    $("#loading_edit3").html("");
                    $("#loading_edit4").html("");
                }
            });
        }

        window.delete_button = function(id, nama) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: "<p>Data tidak dapat dipulihkan kembali!</p>" +
                    "<div class='divider'></div>" +
                    "<b>Data: " + nama + "</b>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#14B8A6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Hapus Data!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{{ route('kriteria.hapus') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Data berhasil dihapus!',
                                icon: 'success',
                                confirmButtonColor: '#14B8A6',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data gagal dihapus!',
                            })
                        }
                    });
                }
            })
        }
    </script>
@endsection
