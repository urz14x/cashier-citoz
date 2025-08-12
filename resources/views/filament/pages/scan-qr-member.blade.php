<x-filament::page>
    <div class="flex items-center justify-center bg-gray-900 text-gray-800">
        <div class="w-full max-w-xl space-y-6 p-6 rounded-2xl shadow-lg bg-gray-800 border border-gray-700">
            <h2 class="text-2xl font-bold text-center text-gray-100">🎥 Scan QR Member</h2>

            <div id="reader" class="mx-auto border-4 border-green-500 rounded-lg shadow-lg" style="width: 500px;"></div>
        </div>
    </div>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        const scanner = new Html5Qrcode("reader");

        function onScanSuccess(decodedText, decodedResult) {
            scanner.stop().then(() => {
                fetch(`/scan/member/${decodedText}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: '✅ Absen Berhasil',
                                text: `Member: ${data.member}`,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '❌ Gagal Absen',
                                text: data.message,
                            }).then(() => {
                                location.reload();
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: '❌ Error',
                            text: err.message,
                        }).then(() => {
                            location.reload();
                        });
                    });
            });
        }

        scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 300 },
            onScanSuccess
        );
    </script>
</x-filament::page>
