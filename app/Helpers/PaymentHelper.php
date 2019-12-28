<?php

namespace App\Helpers;

/**
 * Class PaymentHelper
 * @package App\Helpers
 */
class PaymentHelper
{
    /**
     * @param $paymentMethodCode
     * @return null|string
     */
    public static function getPaymentMethod($paymentMethodCode)
    {
        $paymentMethod = null;

        switch ($paymentMethodCode) {
            case 'authorized':
                $paymentMethod = "Credit Card";
                break;
            case 'paypal':
                $paymentMethod = "Paypal";
                break;
            case 'pay_by_invoice':
                $paymentMethod = "Pay by Invoice";
                break;
        }

        return $paymentMethod;
    }

    /**
     * @param $paymentStatusCode
     * @return string
     */
    public static function getPaymentStatus($paymentStatusCode)
    {
        $paymentStatus = null;

        switch ($paymentStatusCode) {
            case 0:
                $paymentMethod = "Payment Due";
                break;
            case 2:
                $paymentMethod = "Payment Received";
                break;
            case 3:
                $paymentMethod = "Payment Pending";
                break;
            case 4:
                $paymentMethod = "Declined";
                break;
            case 7:
                $paymentMethod = "Payment Due";
                break;
        }

        return $paymentMethod;
    }
}
