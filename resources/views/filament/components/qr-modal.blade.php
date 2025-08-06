<div class="text-center space-y-4">
    <div id="qr-container" class="bg-white inline-block p-4 rounded shadow">
        {!! $qrImage !!}
    </div>

    <p class="text-lg font-semibold mt-2">{{ $employeeName }}</p>

    <button
        onclick="downloadQR()"
        class="mt-4 px-4 py-2 bg-primary-600 text-white rounded shadow hover:bg-primary-700 transition"
    >
        Download QR
    </button>

    <script>
        function downloadQR() {
            const qr = document.querySelector('#qr-container').innerHTML;
            const blob = new Blob([qr], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = 'qr-pegawai.svg';
            a.click();

            URL.revokeObjectURL(url);
        }
    </script>
</div>
