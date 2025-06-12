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
                <div class="flex space-x-3 items-center">
                    <button id="btn-reset-alternatif"
                        class="btn text-white dark:text-gray-800 normal-case bg-purple-600 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-purple-300 dark:hover:bg-opacity-90">
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
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif

        @if (session()->has('gagal'))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session('gagal') }}',
                icon: 'error',
                confirmButtonColor: '#6419E6',
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
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            })
        @endif

        $('#btn-reset-alternatif').on('click', function() {
            Swal.fire({
                title: 'Reset Data Alternatif?',
                text: "Semua data alternatif dan penilaian akan dihapus. Data tidak dapat dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6419E6',
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
                text: "Seluruh data (kriteria, alternatif, penilaian, hasil) akan dihapus. Data tidak dapat dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6419E6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Ya, reset semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('reset.semua') }}";
                }
            });
        });
    </script>
@endsection
