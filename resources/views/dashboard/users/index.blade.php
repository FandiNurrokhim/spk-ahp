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
                    <div class="flex justify-end items-center d p-4">
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <label for="add_button"
                                    class="btn btn-primary btn-sm text-white dark:text-gray-800 normal-case bg-purple-600 hover:bg-opacity-70 hover:border-opacity-70 dark:bg-purple-300 dark:hover:bg-opacity-90">
                                    <i class="ri-add-fill"></i>
                                    Tambah {{ $judul }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto p-3">
                        <table id="tabel_data"
                            class="w-full text-sm text-left text-gray-500 dark:text-gray-400 stripe hover"
                            style="width:100%; padding-top: 1em; padding-bottom: 1em;">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Nama</th>
                                    <th scope="col" class="px-4 py-3">Email</th>
                                    <th scope="col" class="px-4 py-3">Role</th>
                                    <th scope="col" class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $item->name }}</td>
                                        <td class="px-4 py-3">{{ $item->email }}</td>
                                        <td class="px-4 py-3">
                                            @if ($item->role === 'admin')
                                                <span class="badge bg-purple-600 text-white font-bold">Admin</span>
                                            @elseif ($item->role === 'user')
                                                <span class="badge bg-gray-400 text-white font-bold">User</span>
                                            @else
                                                <span
                                                    class="badge bg-gray-200 text-gray-700 font-bold">{{ ucfirst($item->role) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <label for="edit_button" class="btn btn-sm btn-warning text-white"
                                                onclick="return edit_button('{{ $item->id }}')">
                                                <i class="ri-pencil-line"></i>edit
                                            </label>
                                            <button class="btn btn-sm btn-error text-white"
                                                onclick="return delete_button('{{ $item->id }}', '{{ $item->nama }}');">
                                                <i class="ri-delete-bin-line"></i>hapus
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
                <div class="modal-box">
                    <form action="{{ route('user.simpan') }}" method="post" enctype="multipart/form-data">
                        <h3 class="font-bold text-lg">Tambah {{ $judul }}</h3>
                        @csrf
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Nama</span>
                            </label>
                            <input type="text" name="name" placeholder="Masukkan nama"
                                class="input input-bordered w-full max-w-xs text-gray-800" value="{{ old('name') }}"
                                required />
                            <label class="label">
                                @error('name')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Email</span>
                            </label>
                            <input type="email" name="email" placeholder="Masukkan email"
                                class="input input-bordered w-full max-w-xs text-gray-800" value="{{ old('email') }}"
                                required />
                            <label class="label">
                                @error('email')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Password</span>
                            </label>
                            <input type="password" name="password" id="password" placeholder="Masukkan password"
                                class="input input-bordered w-full max-w-xs text-gray-800" autocomplete="password" required />
                            <label class="label">
                                @error('password')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span class="label-text-alt text-error" id="min-char"></span>
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Role</span>
                            </label>
                            <select name="role" class="select select-bordered w-full max-w-xs" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            <label class="label">
                                @error('role')
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
                    <form action="{{ route('user.perbarui') }}" method="post" enctype="multipart/form-data">
                        <h3 class="font-bold text-lg">Ubah {{ $judul }}: <span class="text-greenPrimary"
                                id="title_form"><span class="loading loading-dots loading-md"></span></span></h3>
                        @csrf
                        <input type="text" name="id" hidden />
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Nama</span>
                                <span class="label-text-alt" id="loading_edit1"></span>
                            </label>
                            <input type="text" name="name" placeholder="Masukkan nama"
                                class="input input-bordered w-full text-gray-800" required />
                            <label class="label">
                                @error('name')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Email</span>
                            </label>
                            <input type="email" name="email" placeholder="Masukkan email"
                                class="input input-bordered w-full text-gray-800" required />
                            <label class="label">
                                @error('email')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Password Baru (kosongkan jika tidak diubah)</span>
                            </label>
                            <input type="password" name="password" id="new-password" placeholder="Masukkan password"
                                class="input input-bordered w-full max-w-xs text-gray-800" autocomplete="new-password" required />
                            <label class="label">
                                @error('password')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span class="label-text-alt text-error" id="new-min-char"></span>
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Konfirmasi Password</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Konfirmasi password"
                                class="input input-bordered w-full max-w-xs text-gray-800" required />
                            <label class="label">
                                <span class="label-text-alt text-error" id="password-match-message"></span>
                            </label>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Role</span>
                            </label>
                            <select name="role" class="select select-bordered w-full" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                            <label class="label">
                                @error('role')
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

        $('form[action="{{ route('user.simpan') }}"]').on('submit', function(e) {
            let pass = $('#password').val();
            let conf = $('#password_confirmation').val();

            if (pass.length < 6) {
                $('#min-char').text('Password minimal 6 karakter.');
                $('#password').addClass('input-error');
                e.preventDefault();
                return;
            } else {
                $('#password').removeClass('input-error');
            }

            if (pass !== conf) {
                $('#password-match-message').text('Konfirmasi password tidak sesuai.');
                $('#password_confirmation').addClass('input-error');
                e.preventDefault();
            } else {
                $('#password-match-message').text('');
                $('#password_confirmation').removeClass('input-error');
            }
        });
        // Live check saat mengetik
        $('#password, #new-password, #password_confirmation').on('keyup', function() {
            let pass = $('#password').val();
            let newPass = $('#new-password').val();
            let conf = $('#password_confirmation').val();

            if ((pass.length > 0 || newPass.length > 0) && pass.length < 6) {
                $('#min-char').text('Password minimal 6 karakter.');
                $('#new-min-char').text('Password minimal 6 karakter.');
                $('#new-password').addClass('input-error');
                $('#password').addClass('input-error');
            } else if (conf.length > 0 && pass !== conf) {
                $('#password-match-message').text('Konfirmasi password tidak sesuai.');
                $('#password_confirmation').addClass('input-error');
                $('#new-password').removeClass('input-error');
            } else {
                $('#password-match-message').text('');
                $('#password_confirmation').removeClass('input-error');
                $('#password').removeClass('input-error');
                $('#new-password').removeClass('input-error');
            }
        });
        window.edit_button = function(id) {
            // Loading effect start
            let loading = `<span class="loading loading-dots loading-md text-purple-600"></span>`;
            $("#title_form").html(loading);
            $("#loading_edit1").html(loading);

            $.ajax({
                type: "get",
                url: "{{ route('user.ubah') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(data) {
                    let items = [];
                    $.each(data, function(key, val) {
                        items.push(val);
                    });

                    $("#title_form").html(`${items[1]}`);
                    $("input[name='id']").val(items[0]);
                    $("input[name='name']").val(items[1]);
                    $("input[name='email']").val(items[2]);
                    $("select[name='role']").val(items[3]);
                    $("input[name='role']").val(items[3]);
                    $("input[name='password']").val("");

                    // Loading effect end
                    loading = "";
                    $("#loading_edit1").html(loading);
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
                confirmButtonColor: '#6419E6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Hapus Data!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{{ route('user.hapus') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Data berhasil dihapus!',
                                icon: 'success',
                                confirmButtonColor: '#6419E6',
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
