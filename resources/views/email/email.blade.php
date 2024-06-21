<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            /* font-weight: bold; */
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
            text-align: center;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .infos {
            font-size: 11px;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>

<body dir="auto">

    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <p class="infos">
                                    Green Card for Trading Ets.<br />
                                    ALjamjoom Center , AlHamra district, Palestine st.<br />
                                    CR No.: 4030390607<br />
                                    Phone: 920009120<br />
                                </p>
                            </td>

                            <td class="title">
                                <img src="{{ uploaded_asset(get_setting('system_logo_white')) }}" alt="Green Card SA"
                                    style="width: 120px; max-width: 200px" />
                            </td>

                            <td dir="rtl">
                                <p class="infos" dir="auto" style="text-align: right;">
                                    مؤسسة الكرت الاخضر للتجارة<br />
                                    مركز الجمجوم - حي الحمرا , شارع فلسطين<br />
                                    السجل التجاري رقم: 4030390607 <br />
                                    920009120 : ھاتف<br />
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="details">
                <td colspan="2">Simplified VAT Invoice فاتوره ضريبه مبسطه</td>
                <br /><br />
            </tr>

            <tr class="heading">
                <td>Invoice Number</td>
                <td style="text-align: right;">رقم الفاتورة</td>
            </tr>

            <tr class="details">
                <td colspan="2">{{ $invoice_id }}</td>
            </tr>

            <tr class="heading">
                <td>Seller Name</td>
                <td style="text-align: right;">اسم البائع</td>
            </tr>

            <tr class="details">
                <td colspan="2">Green Card SA</td>
            </tr>

            <tr class="heading">
                <td>VAT registration number</td>
                <td style="text-align: right;">رقم التسجیل في ضریبة القیمة المضافة</td>
            </tr>

            <tr class="details">
                <td colspan="2">310710332100003</td>
            </tr>

            <tr class="heading">
                <td>Invoice Date</td>
                <td style="text-align: right;">تاريخ الفاتورة</td>
            </tr>

            <tr class="details">
                <td colspan="2">{{ $date }}</td>
            </tr>

            <tr class="heading">
                <td>Invoice Total (with VAT)</td>
                <td style="text-align: right;">إجمالي الفاتورة (مع ضريبة القيمة المضافة)</td>
            </tr>

            <tr class="details">
                <td colspan="2">{{ $package_price }} SAR</td>
            </tr>

            <tr class="heading">
                <td>VAT Total</td>
                <td style="text-align: right;">إجمالي ضريبة القيمة المضافة</td>
            </tr>

            <tr class="details">
                <td colspan="2">{{ $vat_val }} SAR</td>
            </tr>

            <tr class="heading">
                <td>Customer name</td>
                <td style="text-align: right;">اسم الزبون</td>
            </tr>

            <tr class="details">
                <td colspan="2">{{ $user_name }}</td>
            </tr>

            <tr class="heading">
                <td>Customer Email</td>
                <td style="text-align: right;">البريد الإلكتروني للعميل</td>
            </tr>

            <tr class="details">
                <td colspan="2">{{ $email }}</td>
            </tr>

            <tr class="heading">
                <td>Package Name</td>
                <td style="text-align: right;">اسم الحزمة</td>
            </tr>

            <tr class="details">
                <td colspan="2">{{ $package_name }}</td>
            </tr>

            <tr class="heading">
                <td>INVOICE QR CODE</td>
                <td style="text-align: right;">رمز الاستجابة السريعة للفواتير</td>
            </tr>

            <tr class="details">
                <td colspan="2">
                    <img src="https://qrcode.tec-it.com/API/QRCode?data={{ $qr_code }}"
                        width="200px" height="200px" />
                </td>
            </tr>

        </table>
    </div>

    <div class="invoice-box" style="margin-top: 15px;">
        <table>
            <tr>
                <td>
                    <img src="{{ static_asset('assets/img/greencardlogo.png') }}" style ="width: 80%; max-width: 100px;">
                </td>
                <td>
                    Greencard SA<br/>
                    تطبيق الخصومات
                </td>
                <td>
                    <a href="{{ get_setting('greenCard_play_store_link') }}" target="_blank">
                        <img src="{{ static_asset('assets/img/play.png') }}" style ="width: 80%; max-width: 200px;">
                    </a>
                    <a href="{{ get_setting('greenCard_app_store_link') }}" target="_blank">
                        <img src="{{ static_asset('assets/img/app.png') }}" style ="width: 80%; max-width: 200px;">
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{{ static_asset('assets/img/greencartlogo.png') }}" style ="width: 80%; max-width: 100px;">

                </td>
                <td>
                    Greencart SA	<br/>
                    تطبيق المتجر الإلكتروني
                </td>
                <td>
                    <a href="{{ get_setting('greenCart_play_store_link') }}" target="_blank">
                        <img src="{{ static_asset('assets/img/play.png') }}" style ="width: 80%; max-width: 200px;">
                    </a>
                    <a href="{{ get_setting('greenCard_app_store_link') }}" target="_blank">
                        <img src="{{ static_asset('assets/img/app.png') }}" style ="width: 80%; max-width: 200px;">
                    </a>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>
