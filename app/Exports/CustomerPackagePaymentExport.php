<?php

namespace App\Exports;

use App\Models\CustomerPackagePayment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerPackagePaymentExport implements FromQuery, WithMapping, WithHeadings
{
    protected $search;
    protected $isOfflinepayment;
    protected $startDate;
    protected $endDate;
    protected $paymentMethod;


    public function __construct($search, $paymentMethod, $startDate, $endDate, $isOfflinepayment = null)
    {
        $this->search = $search;
        $this->isOfflinepayment = $isOfflinepayment;
        $this->paymentMethod = $paymentMethod;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = CustomerPackagePayment::query();

        $query->join("users", "customer_package_payments.user_id", "=", "users.id")
            ->join("customer_packages", "customer_package_payments.customer_package_id", "=", "customer_packages.id")
            ->select("customer_package_payments.*", "customer_packages.name as packName", "users.name as userName", "users.email as userEmail");

        if ($this->paymentMethod) {
            $query->where("payment_method", $this->paymentMethod);
        }

        if ($this->endDate && $this->startDate) {

            $query->where('customer_package_payments.created_at', '>=', $this->startDate)
                ->where('customer_package_payments.created_at', '<=', $this->endDate);
        }
        // Apply custom parameters to the query
        if ($this->search) {

            $sort_search = $this->search;
            $query->where(function ($q) use ($sort_search) {
                $q->where('users.name', 'like', '%' . $sort_search . '%')
                    ->orWhere('users.email', 'like', '%' . $sort_search . '%')
                    ->orWhere('users.id', 'like', '%' . $sort_search . '%')
                    ->orWhere('users.phone', 'like', '%' . $sort_search . '%');
            });
        }

        if ($this->isOfflinepayment) {
            $query->where('offline_payment', $this->isOfflinepayment);
        }

        return $query;
    }

    public function map($customerPackagePayment): array
    {
        return [
            $customerPackagePayment->userName,
            $customerPackagePayment->userName,
            $customerPackagePayment->amount,
            $customerPackagePayment->vat_total,
            $customerPackagePayment->created_at,
            $customerPackagePayment->payment_method,
        ];
    }

    public function headings(): array
    {
        return [
            'User Name',
            'User Email',
            'Amount',
            'VAT Amount',
            'invoice date',
            'Payment method'
        ];
    }
}
