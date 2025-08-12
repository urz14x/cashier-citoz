<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Member</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 font-sans text-gray-800 min-h-screen p-6">

    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-lg p-6 space-y-10">

        {{-- Header --}}
        <div class="text-center space-y-2">
            <img src="{{ asset('images/logo.svg') }}" class="w-20 h-20 mx-auto" />
            <h1 class="text-3xl font-extrabold text-slate-800 ">Citoz Sport Center</h1>
            <h2 class="text-sm  text-gray-500 tracking-wide">Dashboard Membership GYM</h2>
        </div>

        {{-- Profile Card --}}
        <div class="grid grid-cols-1 items-center md:grid-cols-3 gap-6">

            {{-- QR Code --}}
            <div class="text-center bg-gray-100 p-6 mx-auto rounded-2xl shadow-inner">
                {!! QrCode::size(180)->generate($member->qr_code) !!}
            </div>

            {{-- Biodata --}}
            <div class="md:col-span-2 space-y-4 bg-gray-50 p-6 rounded-2xl border">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nama</p>
                        <p class="font-semibold">{{ $member->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Gender</p>
                        <p class="font-semibold">{{ $member->gender->value === 'M' ? 'Laki-laki' : 'Perempuan' }}</p>

                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <span
                            class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $member->status->value === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($member->status->value) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-500">Bergabung</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($member->joined)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Berakhir</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($member->expired)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat Latihan --}}
        <div x-data="{ tab: 'tabel' }" class="space-y-4">
            <h3 class="text-lg font-bold text-gray-700">
                Riwayat Latihan
                {{ \Carbon\Carbon::parse($member->joined)->translatedFormat('d M Y') }} -
                {{ \Carbon\Carbon::parse($member->expired)->translatedFormat('d M Y') }}
            </h3>

            {{-- Tab Buttons --}}
            <div class="flex space-x-4 border-b border-gray-200">
                <button @click="tab = 'tabel'"
                    :class="tab === 'tabel' ? 'border-b-2 border-green-500 text-green-600' : 'text-gray-500'"
                    class="py-2 px-4 text-sm font-semibold focus:outline-none">
                    📋 Tabel
                </button>
                <button @click="tab = 'chart'"
                    :class="tab === 'chart' ? 'border-b-2 border-green-500 text-green-600' : 'text-gray-500'"
                    class="py-2 px-4 text-sm font-semibold focus:outline-none">
                    📊 Grafik
                </button>
            </div>

            {{-- Tabel Latihan --}}
            <div x-show="tab === 'tabel'" class="w-full overflow-x-auto rounded-xl border border-gray-200 bg-white">
                <table class="min-w-[600px] w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="px-4 py-2">Tanggal</th>
                            <th class="px-4 py-2">Jumlah Latihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latihanSelamaAktif as $tanggal => $jumlah)
                            <tr class="border-t hover:bg-gray-100 odd:bg-white even:bg-gray-50">
                                <td class="px-4 py-2">{{ $tanggal }}</td>
                                <td class="px-4 py-2 font-semibold">{{ $jumlah }}x</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-gray-400">Tidak ada data latihan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Grafik Latihan --}}
            <div x-show="tab === 'chart'" class="w-full rounded-xl border border-gray-200 bg-white p-4">
                <canvas id="attendanceChart" height="100"></canvas>
            </div>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Jumlah Latihan',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(34,197,94,0.6)', // hijau
                    borderColor: 'rgba(34,197,94,1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 90,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
