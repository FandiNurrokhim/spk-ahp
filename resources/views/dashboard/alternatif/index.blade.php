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
                                    class="btn  btn-sm text-white dark:text-gray-800 normal-case bg-teal-500 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-purple-300 dark:hover:bg-opacity-90">
                                    <i class="ri-add-fill"></i>
                                    Tambah {{ $judul }}
                                </label>
                                <label for="import_button"
                                    class="btn btn-sm text-white dark:text-gray-800 normal-case bg-green-600 hover:bg-green-600 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-green-300 dark:hover:bg-green-300 dark:hover:bg-opacity-90 dark:border-green-300">
                                    <i class="ri-file-excel-line"></i>
                                    Import Data
                                </label>
                                <a href="/dashboard/download-template-alternatif"
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
                                    @foreach($kriteria as $k)
                                        <th scope="col" class="px-4 py-3">{{ $k->kode }}</th>
                                    @endforeach
                                    <th scope="col" class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-semibold">{{ $item->kode }}</td>
                                        <td class="px-4 py-3">{{ $item->nama }}</td>
                                        @foreach($kriteria as $k)
                                            @php
                                                $penilaian = DB::table('penilaian')
                                                    ->where('alternatif_id', $item->id)
                                                    ->where('kriteria_id', $k->id)
                                                    ->first();
                                            @endphp
                                            <td class="px-4 py-3">{{ $penilaian ? $penilaian->nilai : 0 }}</td>
                                        @endforeach
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
                        </table>
                    </div>
                </div>
            </div>

            {{-- Form Tambah Data --}}
            <input type="checkbox" id="add_button" class="modal-toggle" />
            <div class="modal">
                <div class="modal-box max-w-4xl">
                    <form action="{{ route('alternatif.simpan') }}" method="post" enctype="multipart/form-data">
                        <h3 class="font-bold text-lg mb-4">Tambah {{ $judul }}</h3>
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Kode</span>
                                </label>
                                <input type="text" name="kode" placeholder="Type here"
                                    class="input input-bordered w-full text-gray-800 bg-slate-100"
                                    value="{{ $kode }}" readonly required />
                                <label class="label">
                                    @error('kode')
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Nama</span>
                                </label>
                                <input type="text" name="nama" placeholder="Masukkan nama alternatif"
                                    class="input input-bordered w-full text-gray-800" value="{{ old('nama') }}"
                                    required />
                                <label class="label">
                                    @error('nama')
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>

                        <div class="divider">Nilai Kriteria</div>

                        <div class="grid grid-cols-1 gap-4">
                            @foreach($kriteria as $k)
                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text">{{ $k->kode }} - {{ $k->nama }}</span>
                                        <span class="label-text-alt badge {{ $k->jenis == 'benefit' ? 'badge-success' : 'badge-error' }}">
                                            {{ ucfirst($k->jenis) }}
                                        </span>
                                    </label>
                                    <input type="number" name="kriteria[{{ $k->id }}]" 
                                        placeholder="Masukkan nilai"
                                        class="input input-bordered w-full text-gray-800" 
                                        value="{{ old('kriteria.'.$k->id, 0) }}"
                                        min="0" step="0.01" required />
                                    <label class="label">
                                        @error('kriteria.'.$k->id)
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        @enderror
                                    </label>
                                </div>
                            @endforeach
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
                <div class="modal-box max-w-4xl" id="edit_form">
                    <form action="{{ route('alternatif.perbarui') }}" method="post" enctype="multipart/form-data">
                        <h3 class="font-bold text-lg mb-4">Ubah {{ $judul }}: <span class="text-greenPrimary"
                                id="title_form"><span class="loading loading-dots loading-md"></span></span></h3>
                        @csrf
                        <input type="text" name="id" hidden />
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control w-full">
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
                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Nama</span>
                                    <span class="label-text-alt" id="loading_edit2"></span>
                                </label>
                                <input type="text" name="nama" placeholder="Masukkan nama alternatif"
                                    class="input input-bordered w-full text-gray-800" required />
                                <label class="label">
                                    @error('nama')
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>

                        <div class="divider">Nilai Kriteria</div>

                        <div class="grid grid-cols-1 gap-4" id="kriteria_edit">
                            @foreach($kriteria as $k)
                                <div class="form-control w-full">
                                    <label class="label">
                                        <span class="label-text">{{ $k->kode }} - {{ $k->nama }}</span>
                                        <span class="label-text-alt badge {{ $k->jenis == 'benefit' ? 'badge-success' : 'badge-error' }}">
                                            {{ ucfirst($k->jenis) }}
                                        </span>
                                    </label>
                                    <input type="number" name="kriteria[{{ $k->id }}]" 
                                        placeholder="Masukkan nilai"
                                        class="input input-bordered w-full text-gray-800" 
                                        min="0" step="0.01" required />
                                </div>
                            @endforeach
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
                    <form action="{{ route('alternatif.import') }}" method="post" enctype="multipart/form-data">
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
            let loading = `<span class="loading loading-dots loading-md text-purple-600"></span>`;
            $("#title_form").html(loading);
            $("#loading_edit1").html(loading);
            $("#loading_edit2").html(loading);

            $.ajax({
                type: "get",
                url: "{{ route('alternatif.ubah') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(data) {
                    $("#title_form").html(data.nama);
                    $("input[name='id']").val(data.id);
                    $("input[name='kode']").val(data.kode);
                    $("input[name='nama']").val(data.nama);

                    // Set nilai untuk setiap kriteria
                    if(data.penilaian) {
                        Object.keys(data.penilaian).forEach(function(kriteria_id) {
                            $(`input[name='kriteria[${kriteria_id}]']`).val(data.penilaian[kriteria_id]);
                        });
                    }

                    $("#loading_edit1").html("");
                    $("#loading_edit2").html("");
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
                        url: "{{ route('alternatif.hapus') }}",
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