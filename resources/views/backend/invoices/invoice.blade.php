<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{  translate('Tax Invoice') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <style media="all">
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 0.875rem;
            font-family: <?php echo ($font_family) ?>;
            font-weight: normal;
            direction: <?php echo  $direction ?>;
            text-align: <?php echo  $text_align ?>;
            padding: 0;
            margin: 0;
        }

        .gry-color *,
        .gry-color {
            color: #000;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: .25rem .7rem;
        }

        table.padding td {
            padding: .25rem .7rem;
        }

        table.sm-padding td {
            padding: .1rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .border-right {
            border: 1px solid #f3f3f3;
        }

        .text-left {
            text-align: <?php echo  $text_align ?>;
        }

        .text-right {
            text-align: <?php echo  $not_text_align ?>;
        }
    </style>
</head>
<body>
{{--	<div>--}}

@php
    $logo = get_setting('header_logo');
@endphp

<div style="background: #eceff4;padding: 1rem;">
    <table>
        <tr>
            <td>
                <img src="{{ static_asset('uploads/all/cJuu75M9DSZo83xdBxXcv8OOpDSiv8iIJOE3HAL8.jpg') }}" height="80" style="display:inline-block;">
            </td>
            <td style="font-size: 1.5rem;" class="text-right strong">فاتورة ضريبه مبسطة</td>
        </tr>
        <tr>
            <td>
            </td>
            <td style="font-size: 1.5rem;" class="text-right strong">{{ translate('Simplified VAT invoice ') }}</td>

        </tr>

    </table>
    <table>
        <tr>
            <td style="font-size: 1rem;" class="strong">{{ get_setting('site_name') }}</td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td class="gry-color small">{{ get_setting('contact_address') }}</td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td class="gry-color small">{{  translate('Email') }}: {{ get_setting('contact_email') }}</td>
            <td class="text-right small"><span class="gry-color small">{{  translate('Invoice no','ar') }}:</span> <span
                    class="strong">{{ $order->code }}</span></td>
        </tr>
        <tr>
            <td class="gry-color small">{{  translate('Phone') }}: 920009120</td>
            <td class="text-right small"><span class="gry-color small">{{  translate('Invoice Issue Date','ar') }}:</span>
                <span class=" strong">{{ date('d-m-Y', $order->date) }}</span></td>
        </tr>

    </table>

</div>

{{--		<div style="padding: 1rem;padding-bottom: 0">--}}
{{--            <table>--}}
{{--				@php--}}
{{--					$shipping_address = json_decode($order->shipping_address);--}}
{{--				@endphp--}}
{{--				<tr><td class="strong small gry-color">{{ translate('Bill to') }}:</td></tr>--}}
{{--				<tr><td class="strong">{{ $shipping_address->name }}</td></tr>--}}
{{--				<tr><td class="gry-color small">{{ $shipping_address->address }}, {{ $shipping_address->city }}, {{ $shipping_address->postal_code }}, {{ $shipping_address->country }}</td></tr>--}}
{{--				<tr><td class="gry-color small">{{ translate('Email') }}: {{ $shipping_address->email }}</td></tr>--}}
{{--				<tr><td class="gry-color small">{{ translate('Phone') }}: {{ $shipping_address->phone }}</td></tr>--}}
{{--			</table>--}}
{{--		</div>--}}

<div style="padding: 1rem;">


    <table class="padding text-left small border-bottom table-striped">
        <thead>
        <tr class="gry-color" style="background: #eceff4;">
            <th width="25%" colspan="2" class="text-left">{{ translate('Seller','ar') }}</th>
            <th width="25%" class="text-right" colspan="2">البائع</th>
            <th width="25%" colspan="2" class="text-left">{{ translate('Buyer','ar') }}</th>
            <th width="25%" colspan="2" class="text-right">المشتري</th>
        </tr>
        </thead>
        <tbody class="strong">


        <tr class="border-right">
            <td class="border-right" width="12%">Name</td>
            <td class="border-right" width="13%">Green Card for Trading Ets.</td>
            <td class="border-right text-right" width="13%">مؤسسه الكرت الأخضر للتجارة</td>
            <td class="border-right text-right" width="12%">:اسم</td>
            <td class="border-right" width="12%">Name</td>
            <td class="border-right" width="26%">{{ $buyerData['name'] ?? ''}}</td>
            <td class="border-right text-right" width="12%">اسم</td>
        </tr>
        <tr class="border-right">
            <td class="border-right" width="12%">Building No.</td>
            <td class="border-right" width="13%">2724</td>
            <td class="border-right text-right" width="13%">2724</td>
            <td class="border-right text-right" width="12%">:رقم المبني</td>
            <td class="border-right" width="12%">Email/Mobile</td>
            <td class="border-right" width="26%">{{ $buyerData['email'] ?? $buyerData['phone'] ?? ''}}</td>
            <td class="border-right text-right" width="12%">: الايميل  / رقم الجوال </td>
        </tr>
        <tr>
            <td class="border-right" width="12%">Street Name</td>
            <td class="border-right" width="13%">Palestine st.</td>
            <td class="border-right text-right" width="13%">شارع فلسطين</td>
            <td class="border-right text-right" width="12%">:اسم الشارع</td>
        </tr>

        <tr class="border-right">
            <td class="border-right" width="12%">District</td>
            <td class="border-right" width="13%">AlHamra district, </td>
            <td class="border-right text-right" width="13%">حي الحمراء</td>
            <td class="border-right text-right" width="12%">:الحى</td>

        </tr>
        <tr class="border-right">
            <td class="border-right" width="12%">City</td>
            <td class="border-right" width="13%">Jeddah</td>
            <td class="border-right text-right" width="13%">جده</td>
            <td class="border-right text-right" width="12%">:المدينة</td>
        </tr>
        <tr class="border-right">
            <td class="border-right" width="12%">Country</td>
            <td class="border-right" width="13%">Saudi Arabia</td>
            <td class="border-right text-right" width="13%">السعوديه</td>
            <td class="border-right text-right" width="12%">:البلد</td>
        </tr>
        <tr class="border-right">
            <td class="border-right" width="12%">Postal Code</td>
            <td class="border-right" width="13%">{{ get_setting('owner_postal_code') ?? '23321'}}</td>
            <td class="border-right text-right" width="13%">{{ get_setting('owner_postal_code') ?? '23321'}}</td>
            <td class="border-right text-right" width="12%">الرمز البريدي</td>
        </tr>
        <tr class="border-right">
            <td class="border-right" width="12%">Additional No.</td>
            <td class="border-right" width="13%">{{ get_setting('owner_additional_no')?? '' }}</td>
            <td class="border-right text-right" width="13%"></td>
            <td class="border-right text-right" width="12%">:الرقم الاضافى للعنوان</td>
        </tr>
        <tr class="border-right">
            <td class="border-right" width="12%">VAT No.</td>
            <td class="border-right" width="13%">{{ get_setting('owner_vat_no') ?? '310710332100003'}}</td>
            <td class="border-right text-right" width="13%">{{ get_setting('owner_vat_no') ?? '310710332100003'}}</td>
            <td class="border-right text-right" width="12%">رقم تسجيل : ضريبه القيمة المضافه</td>
        </tr>
        <tr class="border-right">
            <td class="border-right" width="12%">Company Registration</td>
            <td class="border-right" width="13%">4030390607</td>
            <td class="border-right text-right" width="13%">4030390607</td>
            <td class="border-right text-right" width="12%">رقم السجل التجاري</td>
        </tr>


        </tbody>
    </table>
</div>

<div style="padding: 1rem;">
    <table class="padding text-left small border-bottom">
        <thead>
        <tr class="gry-color" style="background: #eceff4;">
            <th width="35%" class="text-left">{{ translate('Product Name','ar') }}</th>
            <th width="15%" class="text-left">{{ translate('Delivery Type','ar') }}</th>
            <th width="10%" class="text-left">{{ translate('Qty','ar') }}</th>
            <th width="15%" class="text-left">{{ translate('Unit Price','ar') }}</th>
            <th width="15%" class="text-left">{{ translate('Total Amount','ar') }}</th>
            <th width="10%" class="text-left">{{ translate('VAT','ar') }}</th>
            <th width="15%" class="text-right">{{ translate('Total','ar') }}</th>
        </tr>
        </thead>
        <tbody class="strong">
        @foreach ($order->orderDetails as $key => $orderDetail)
            @if ($orderDetail->product != null)
                <tr class="">
                    <td>{{ $orderDetail->product->name }} @if($orderDetail->variation != null)
                            ({{ $orderDetail->variation }}) @endif</td>
                    <td>
                        @if ($order->shipping_type != null && $order->shipping_type == 'home_delivery')
                            {{ translate('Home Delivery') }}
                        @elseif ($order->shipping_type == 'pickup_point')
                            @if ($order->pickup_point != null)
                                {{ $order->pickup_point->getTranslation('name','ar') }} ({{ translate('Pickip Point','ar') }})
                            @endif
                        @endif
                    </td>
                    <td class="">{{ $orderDetail->quantity }}</td>
                    @php
                        $unitPrice = $orderDetail->price / $orderDetail->quantity;
                    @endphp
                    <td class="currency">{{ single_price($unitPrice) }}</td>
                    <td class="currency">{{ single_price($unitPrice * $orderDetail->quantity) }}</td>
                    <td class="currency">{{ single_price($orderDetail->tax) }}</td>
                    <td class="text-right currency">{{ single_price($orderDetail->price+$orderDetail->tax) }}</td>
                </tr>
            @endif
        @endforeach

        </tbody>
    </table>
</div>

<div style="padding:0 1.5rem;">
    <table class="text-right sm-padding small strong">
        <thead>
        <tr>
            <th width="60%"></th>
            <th width="40%"></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-left">
                @php
                    $removedXML = '<?xml version="1.0" encoding="UTF-8"?>';
                @endphp
                {!! str_replace($removedXML,"", $qrcode) !!}
            </td>
            <td>
                <table class="text-right sm-padding small strong">
                    <tbody>
                    <tr>
                        <th class="gry-color text-left">{{ translate('Sub Total','ar') }}</th>
                        <td class="currency">{{ single_price($order->orderDetails->sum('price')) }}</td>
                    </tr>
                    <tr>
                        <th class="gry-color text-left">{{ translate('Shipping Cost','ar') }}</th>
                        <td class="currency">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</td>
                    </tr>
                    <tr class="border-bottom">
                        <th class="gry-color text-left">{{ translate('Total VAT','ar') }}</th>
                        <td class="currency">{{ single_price($order->orderDetails->sum('tax')) }}</td>
                    </tr>
                    <tr class="border-bottom">
                        <th class="gry-color text-left">{{ translate('Coupon Discount','ar') }}</th>
                        <td class="currency">{{ single_price($order->coupon_discount) }}</td>
                    </tr>
                    <tr>
                        <th class="text-left strong">{{ translate('Grand Total','ar') }}</th>
                        <td class="currency">{{ single_price($order->grand_total) }}</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

</div>
</body>
</html>
