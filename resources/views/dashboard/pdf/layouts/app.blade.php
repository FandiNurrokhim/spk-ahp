<!DOCTYPE html>
<html lang="en" data-theme="light" :class="{ 'theme-dark': dark }" x-data="data()" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $judul }}</title>

    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/logo.png') }}" />
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}" />

    <style>
        .judul-laporan {
            text-align: center;
            margin-bottom: 30px;
        }

        .table-pdf table {
            font-size: 24px;
            font-weight: normal;
            table-layout: auto;
            width: 100% !important;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .table-pdf table th,
        .table-pdf table td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            text-align: center;
            vertical-align: middle;
        }

        .rounded-full {
            border-radius: 50% !important;
        }

        .table-pdf table th:first-child,
        .table-pdf table td:first-child {
            text-align: left;
            padding-left: 12px;
        }

        .table-pdf table tr {
            background-color: white;
        }

        .table-pdf table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .table-pdf table th {
            font-size: 24px;
            font-weight: bold;
            color: white !important;
            background-color: #0d9488 !important;
            /* Teal-600 */
            padding: 10px 12px;
            text-align: center;
        }

        .table-pdf table th:first-child {
            border-top-left-radius: 6px;
        }

        .table-pdf table th:last-child {
            border-top-right-radius: 6px;
        }

        /* Styling untuk tabel hasil ranking */
        .table-pdf .bg-teal-500 {
            background-color: #14b8a6 !important;
            /* Teal-500 */
            color: white !important;
        }

        .table-pdf .bg-teal-100 {
            background-color: #ccfbf1 !important;
            /* Teal-100 */
        }

        /* Styling untuk total/summary rows */
        .table-pdf .bg-teal-100.font-bold {
            background-color: #99f6e4 !important;
            /* Teal-200 */
            font-weight: bold;
            border-top: 2px solid #0d9488;
        }

        /* Responsive text size untuk PDF */
        @media print {
            .table-pdf table {
                font-size: 24px;
            }

            .table-pdf table th {
                font-size: 11px;
            }
        }

        /* Styling untuk highlight boxes */
        .bg-teal-50 {
            background-color: #f0fdfa !important;
        }

        .border-teal-400 {
            border-color: #2dd4bf !important;
        }

        /* Ranking badges */
        .bg-yellow-500 {
            background-color: #eab308 !important;
        }

        .bg-gray-400 {
            background-color: #9ca3af !important;
        }

        .bg-orange-600 {
            background-color: #ea580c !important;
        }

        .bg-gray-200 {
            background-color: #e5e7eb !important;
        }

        /* Section spacing */
        .mb-6 {
            margin-bottom: 24px;
        }

        .mb-7 {
            margin-bottom: 28px;
        }

        /* Text styling */
        .text-lg {
            font-size: 18px;
            line-height: 28px;
        }

        .text-xl {
            font-size: 24px;
            line-height: 28px;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 700;
        }
    </style>
</head>

<body class="font-workSans">
    <div class="flex h-screen bg-gray-50">
        <div class="flex flex-col flex-1 w-full">
            <main class="h-full overflow-y-auto">
                @yield('container')
            </main>
        </div>
    </div>
</body>

</html>
