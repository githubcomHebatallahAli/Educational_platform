{{-- {{ $payLink }} --}}
{{-- <a href="{{ $paylink }}">pay</a> --}}

<!-- resources/views/buy.blade.php -->
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شراء المنتج</title>
    @paddleJS <!-- تحميل مكتبة Paddle -->
</head>
<body>
    <h1>شراء المنتجات</h1>

    <x-paddle-button :checkout="$checkout" class="px-8 py-4">
        شراء
    </x-paddle-button>
</body>
</html>






