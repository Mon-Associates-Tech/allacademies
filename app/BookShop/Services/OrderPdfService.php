<?php

namespace App\BookShop\Services;

use App\BookShop\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPdfService
{
    public function receipt(Order $order)
    {
        $order->loadMissing(['items', 'branch', 'customer']);

        return Pdf::loadView('bookshop::customer.orders.receipt-pdf', ['order' => $order])
            ->setPaper('a4');
    }

    public function packingSlip(Order $order)
    {
        $order->loadMissing(['items.book', 'branch', 'customer']);

        return Pdf::loadView('bookshop::staff.orders.packing-slip-pdf', ['order' => $order])
            ->setPaper('a4');
    }
}
