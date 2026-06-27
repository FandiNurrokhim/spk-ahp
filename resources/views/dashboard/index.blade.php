@extends('dashboard.layouts.app')

@section('container')
    {{-- Hero Banner --}}
    <div class="relative bg-gradient-to-br from-teal-800 via-teal-700 to-green-600 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-48 -translate-y-48"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-white rounded-full translate-x-40 translate-y-40"></div>
        </div>
        <div class="relative flex items-center justify-center py-16 px-6 text-center">
            <div>
                <p class="text-teal-200 text-sm font-medium tracking-widest uppercase mb-2">Selamat Datang di</p>
                <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight">Profil Desa Sangen</h1>
                <div class="mt-4 w-16 h-1 bg-teal-300 mx-auto rounded-full"></div>
            </div>
        </div>
    </div>

    {{-- Profile Desa Section --}}
    <div class="bg-white py-16 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col lg:flex-row items-center lg:items-start gap-12">
                {{-- Foto Kepala Desa --}}
                <div class="flex-shrink-0">
                    <div class="w-full rounded-2xl overflow-hidden shadow-lg bg-gray-100 border-4 border-teal-100">
                        <img
                            src="{{ asset('img/foto-desa.jpeg') }}"
                            alt="Kepala Desa"
                            class="w-full h-full object-cover"
                            onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'flex flex-col items-center justify-center h-full text-gray-400\'><svg xmlns=\'http://www.w3.org/2000/svg\' class=\'w-20 h-20 mb-2\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\' /></svg><span class=\'text-xs\'>Foto Kepala Desa</span></div>'"
                        />
                    </div>
                </div>

                {{-- Info Desa --}}
                <div class="flex-1 text-center lg:text-left">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 leading-tight mb-1">
                        DESA SANGEN
                    </h2>
                    <p class="text-teal-600 font-medium mb-5">Sangen Kec. Geger, Kabupaten Madiun, Jawa Timur</p>

                    <div class="space-y-3 text-gray-600 leading-relaxed text-base">
                        <p>
                            Dengan memanjatkan puji syukur kepada Tuhan Yang Maha Esa, kami menyambut kehadiran Anda
                            di halaman resmi Desa kami. Desa ini merupakan bagian dari wilayah administrasi Kecamatan
                            yang terus berkembang dengan potensi sumber daya alam dan manusia yang luar biasa.
                        </p>
                        <p>
                            Sebagai kepala desa, kami berkomitmen untuk terus meningkatkan pelayanan kepada masyarakat,
                            mendorong pembangunan yang berkelanjutan, serta mempererat tali silaturahmi antar warga desa.
                            Semoga website ini dapat menjadi media informasi yang bermanfaat bagi seluruh masyarakat.
                        </p>
                    </div>

                    {{-- Sosial Media --}}
                    <div class="mt-6 flex gap-3 justify-center lg:justify-start">
                        <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-teal-600 text-white hover:bg-teal-700 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-teal-600 text-white hover:bg-teal-700 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-teal-600 text-white hover:bg-teal-700 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Divider --}}
    <div class="bg-gray-50 h-2"></div>

    {{-- Contact Section --}}
    <div class="bg-gray-50 py-16 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <span class="inline-block bg-teal-50 text-teal-700 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide mb-3">Hubungi Kami</span>
                <h2 class="text-3xl font-bold text-gray-800">Kontak & Informasi</h2>
                <p class="text-gray-500 mt-2">Kami siap melayani Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Telepon --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition-shadow duration-200">
                    <div class="w-14 h-14 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">Telepon</h3>
                    <p class="text-teal-600 font-medium">(0xxx) xxx-xxxx</p>
                    <p class="text-gray-500 text-sm mt-1">Senin – Jumat, 08.00 – 16.00</p>
                </div>

                {{-- Email --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition-shadow duration-200">
                    <div class="w-14 h-14 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">Email</h3>
                    <p class="text-teal-600 font-medium">desa@email.com</p>
                    <p class="text-gray-500 text-sm mt-1">Balas dalam 1x24 jam</p>
                </div>

                {{-- Alamat --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center hover:shadow-md transition-shadow duration-200">
                    <div class="w-14 h-14 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">Alamat</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Jl. Nama Jalan No. XX,<br>Desa, Kecamatan,<br>Kabupaten, Provinsi</p>
                </div>
            </div>
        </div>
    </div>
@endsection
