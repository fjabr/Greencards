<?php

namespace App\Exports;

use App\Models\CustomerPackagePayment;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerListExport implements FromQuery, WithMapping, WithHeadings
{
    protected $search;


    public function __construct($search)
    {
        $this->search = $search;
    }

    public function query()
    {
        $query = User::query();
        $query->where('user_type', 'customer')->orderBy('created_at', 'desc');

        // Apply custom parameters to the query
        if ($this->search) {
            $sort_search = $this->search;
            $query->where(function ($q) use ($sort_search) {
                $q->where('name', 'like', '%' . $sort_search . '%')
                    ->orWhere('email', 'like', '%' . $sort_search . '%')
                    ->orWhere('id', 'like', '%' . $sort_search . '%')
                    ->orWhere('phone', 'like', '%' . $sort_search . '%');
            });
        }

        return $query;
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->phone,
            $user->customer_package_id != null && $user->customer_package != null ? $user->customer_package->getTranslation('name') : "",
            $user->end_sub_date,
            $user->start_sub_date,
            $user->duration,
            single_price($user->balance)
        ];
    }

    public function headings(): array
    {
        return [
            'User Name',
            'User Email',
            'User phone',
            'Package',
            'Subscription starting date',
            'Subscription ending date',
            'Subscription Duration',
            'Balance',
        ];
    }
}
