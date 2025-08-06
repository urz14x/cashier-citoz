<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>QR Code Pegawai</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md">

        <img src="{{ asset('images/logo.svg') }}" alt="Citoz Sport Center Logo"
            class="w-36 h-36 flex items-center justify-center mx-auto" />
        <h3 class="text-xl font-bold text-center italic">Citoz Sport Center</h3>
        <h1 class="text-2xl font-bold text-center text-orange-600 mb-4">QR Code Pegawai</h1>

        <div class="flex justify-center mb-6">
            {!! QrCode::size(200)->generate($employee->qr_code) !!}
        </div>

        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Nama</span>
                <span class="font-semibold text-gray-800">{{ $employee->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Jabatan</span>
                <span
                    class="font-semibold text-gray-800">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $employee->position)) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal Lahir</span>
                <span class="font-semibold text-gray-800">
                    {{ \Carbon\Carbon::parse($employee->date_of_birth)->translatedFormat('d F Y') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Nomor HP</span>
                <span class="font-semibold text-gray-800">{{ $employee->phone }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">UUID</span>
                <span class="font-mono text-xs text-gray-600 break-all">{{ $employee->qr_code }}</span>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ url('/app') }}" class="text-indigo-600 hover:underline text-sm">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>
</body>

</html>
