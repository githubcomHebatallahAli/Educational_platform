<?php

namespace App\Http\Controllers\Admin;

use Basketin\Paymob\Pay;

use Basketin\Paymob\Configs\AmountToCent;
use Basketin\Paymob\Configs\PaymentMethod;
use App\Http\Controllers\Controller;

class PaymobController extends Controller
{
    public function getPaymentLink()
    {
        $pay = new Pay;
        $pay->setMethod(new PaymentMethod('wallet'));
        $pay->setAmount(new AmountToCent(1000));
        $pay->setMerchantOrderId(1234567);

        return $pay->getLink();
    }
}
