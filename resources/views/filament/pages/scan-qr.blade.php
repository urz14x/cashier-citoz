<x-filament::page>
    <div class="flex items-center justify-center bg-gray-900 text-gray-800">
        <div class="w-full max-w-xl space-y-6 p-6 rounded-2xl shadow-lg bg-gray-800 border border-gray-700">
            <h2 class="text-2xl font-bold text-center text-gray-800">🎥 Scan QR Pegawai</h2>

            <div id="reader" class="mx-auto border-4 border-green-500 rounded-lg shadow-lg" style="width: 500px;"></div>

            <div id="result" class="text-center text-lg font-semibold mt-4"></div>
        </div>
    </div>

    {{-- Suara --}}
    <audio id="soundCheckin" src="{{ asset('sounds/absen-berhasil.mp3') }}" preload="auto"></audio>
    <audio id="soundCheckout" src="{{ asset('sounds/selesai.mp3') }}" preload="auto"></audio>
    <audio id="soundDone" src="{{ asset('sounds/already.mp3') }}" preload="auto"></audio>
    <audio id="soundFail" src="{{ asset('sounds/gagal.mp3') }}" preload="auto"></audio>

    {{-- QR Code --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        const scanner = new Html5Qrcode("reader");
        const soundCheckin = document.getElementById("soundCheckin");
        const soundCheckout = document.getElementById("soundCheckout");
        const soundDone = document.getElementById("soundDone");
        const soundFail = document.getElementById("soundFail");

        function onScanSuccess(decodedText, decodedResult) {
            scanner.stop().then(() => {
                document.getElementById('result').innerHTML =
                    `<span class="bg-green-600 text-gray-800 px-4 py-2 rounded-full">QR Terbaca: ${decodedText}</span>`;

                fetch(`/scan/${decodedText}`)
                    .then(res => res.json())
                    .then(data => {
                        let message = "❌ Terjadi kesalahan";
                        if (data.message === 'Check-in berhasil') {
                            soundCheckin.play();
                            message = `✅ Masuk: ${data.employee}`;
                        } else if (data.message === 'Check-out berhasil') {
                            soundCheckout.play();
                            message = `✅ Pulang: ${data.employee}`;
                        } else if (data.message === 'Pegawai sudah absen dan checkout hari ini') {
                            soundDone.play();
                            message = `⚠️ Sudah absen dan sudah checkout: ${data.employee}`;
                        } else {
                            soundFail.play();
                            message = `❌ ${data.message}`;
                        }

                        alert(message);

                        setTimeout(() => {
                            window.location.href = "/app/attendances";
                        }, 1500);
                    })
                    .catch(err => {
                        soundFail.play();
                        alert("❌ Gagal absen: " + err.message);
                        location.reload();
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
