{{-- resources/views/qr-code.blade.php --}}
<img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(100)->generate($code)) !!} ">
