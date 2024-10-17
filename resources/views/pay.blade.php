{{-- {{ $payLink }} --}}
{{-- <a href="{{ $paylink }}">pay</a> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدفع</title>
    <script src="https://cdn.paddle.com/paddle/paddle.js"></script>
</head>
<body>

    <h1>شراء الدورة</h1>

    <?php
    $items = $checkout->getItems();
    $customer = $checkout->getCustomer();
    $custom = $checkout->getCustomData();
    ?>

<a
href="#!"
id="buy-button"
class="paddle_button"
data-items='@json($items)'
@if ($customer) data-customer-id="{{ $customer->paddle_id }}" @endif
@if ($custom) data-custom-data='@json($custom)' @endif
@if ($returnUrl = $checkout->getReturnUrl()) data-success-url="{{ $returnUrl }}" @endif
>
شراء الدورة
</a>


    <script>
        // إعداد Paddle باستخدام معرف البائع الخاص بك
        Paddle.Setup({ vendor: 23623 }); // معرف البائع

        // التعامل مع حدث النقر على زر الدفع
        document.getElementById('buy-button').addEventListener('click', function (e) {
            e.preventDefault();
            console.log('زر الشراء تم النقر عليه');   // منع السلوك الافتراضي للرابط

            // تأكد من تحديد معرف المنتج الصحيح هنا
            var productId = 'pro_01ja025249nf0axeqgj2xskef8'; // استبدل بهذا المعرف إذا كان لديك معرف منتج مختلف

            // فتح نافذة الدفع الخاصة بـ Paddle
            Paddle.Checkout.open({
                product: productId,  // استخدم معرف المنتج الصحيح
                successCallback: function(data) {
                    console.log('الدفع ناجح', data);
                    // يمكنك هنا التعامل مع نجاح الدفع
                },
                closeCallback: function() {
                    console.log('تم إغلاق نافذة الدفع');
                }
            });
        });
    </script>

</body>
</html>










