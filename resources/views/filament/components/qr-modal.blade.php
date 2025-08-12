<div
    x-data="{
        downloadQR() {
            const svgElement = document.querySelector('#qr-container svg');
            if (!svgElement) {
                alert('QR Code tidak ditemukan.');
                return;
            }

            const svgData = new XMLSerializer().serializeToString(svgElement);
            const blob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = 'qr-pegawai-{{ Str::slug($employeeName) }}.svg';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    }"
    class="text-center space-y-4"
>
    <div id="qr-container" class="bg-white inline-block p-4 rounded shadow">
        {!! $qrSvg !!}
    </div>

    <p class="text-lg font-semibold mt-2">{{ $employeeName }}</p>

    <button
        @click="downloadQR"
        class="mt-4 px-4 py-2 bg-primary-600 text-white rounded shadow hover:bg-primary-700 transition"
    >
        Download QR
    </button>
</div>
