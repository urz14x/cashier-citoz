{{-- resources/views/qr-code.blade.php --}}
<img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->generate($code)) !!} ">
