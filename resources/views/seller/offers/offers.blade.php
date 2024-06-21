@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Dashboard') }} </h1>
            </div>

        </div>
    </div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if ($seller->permission_add_offers == 2)
        <div class="card" id="containerAdd">
            <div class="card-body" style="padding: 10px; background-color: white">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="/add_offer">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Type</label>

                            <select class="form-control" name="type" id="type">
                                @foreach ($types_offers as $type)
                                    <?php
                                    $allowed_types = explode(',', $contract->offers);
                                    if (in_array($type->id, $allowed_types)) {
                                        echo "<option value='" . $type->id . "'>" . $type->name . '---' . $type->name_ar . '</option>';
                                    }
                                    ?>
                                @endforeach
                            </select>
                        </div>
                        <!--</div>-->
                        <!--<div class="form-row">-->


                        <!--<div class="form-group col-md-6">-->
                        <!--  <label for="name">Name</label>-->
                        <!--  <input type="text" class="form-control" id="name" name="name" placeholder="Name Offer" >-->
                        <!--</div>-->
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="description">Description</label>
                            <input type="text" required class="form-control" id="description" name="description"
                                placeholder="Description">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="description">Description Arabic</label>
                            <input type="text" required class="form-control" id="description" name="description_ar"
                                placeholder="Description Arabic">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="code">Code</label>
                            <input type="text" required class="form-control" id="code" name="code"
                                placeholder="xxxx">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="points">Number of points</label>
                            <input type="number" required class="form-control" id="points" name="points"
                                placeholder="points">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="limitless">Limitless</label>

                            <select class="form-control" id="limitless" name="limitless" required>
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="usage">Number Of Usage</label>
                            <input type="number" class="form-control" id="usage" name="usage" placeholder="usage">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Offer</button>
                    <button type="button" id="cancelForm" class="btn btn-default">Cancel</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="card" id="offers">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Offers</h5>
                        <a href="#" onclick="addOffer()">
                            <i class="las la-plus aiz-side-nav-icon"></i>
                            Add Offer
                        </a>

                    </div>
                    <div class="card-body">
                        <table class="table aiz-table mb-0">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Offer Name</th>
                                    <th>Points</th>
                                    <th>Limitless</th>
                                    <th>Actions</th>
                                    <th>Reports</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($offers as $offer)
                                    <tr>
                                        <td>{{ $offer->id }}</td>
                                        <td>{{ $offer->title }}</td>
                                        <td style="width: 100px">{{ $offer->nb_points }} points</td>
                                        <td>
                                            @if ($offer->ilimitless_usage == 1)
                                                YES
                                            @else
                                                NO
                                            @endif
                                        </td>
                                        <td style="width: 110px">
                                            <a href="/edit_offer/{{ $offer->id }}" title="Edit" class="">
                                                <i class="las la-edit aiz-side-nav-icon"></i>
                                            </a>
                                            <span> </span>
                                            <a href="#offers" title="Delete" onclick="deleteOffer({{ $offer->id }})"
                                                class="">
                                                <i class="las la-trash aiz-side-nav-icon"></i>
                                            </a>

                                        </td>
                                        <td>
                                            <span> </span>
                                            <a href="/offer-statistics/{{ $offer->id }}" title="reports" class="">
                                                {{ translate('get reports') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card p-5 text-center">
                    <div id="card_qr" class="mb-3">
                        <center>
                            <div id="parent">
                                <img class="print_img" id="print_img_header1" width="66%" class=""
                                src="data:image/png;base64,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"  />
                                <img class="print_img" id="print_img_header11" width="66%" class=""
                                src="data:image/png;base64,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" />
                                <img class="print_img" id="print_img_header2" width="43%"
                                    src="https://qrcode.tec-it.com/API/QRCode?data={{ url('/') . '/id/shop/' . $shop_name }}" />
                                <img class="print_img" id="print_img_header3" width="66%" class=""
                                    src="data:image/png;base64,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" />
                            </div>
                        </center>

                    </div>
                    <script>
                        function printDiv() {
                            var divContents = document.getElementById("card_qr").innerHTML;
                            var a = window.open('', '', 'height=500, width=500');
                            a.document.write(
                                '<html><style> html, body { width: 55mm; height: 9mm; } </style><style> .print_img { width:100%; } </style>'
                                );
                            a.document.write('<style> #print_img_header2{ height: 35mm;width: 35mm;} </style>')
                            a.document.write(
                                '<body style="display: flex;flex-direction: column;box-sizing: border-box;padding-top:0px;border:1px solid black;margin :0" >'
                                );
                            a.document.write(divContents);
                            a.document.write('</body></html>');
                            a.document.close();
                            setTimeout(() => {
                                a.print();
                            }, 500);
                        }
                    </script>
                    <button onclick="printDiv()" class="btn btn-primary">Print</button>
                </div>
            </div>
        </div>
    @elseif ($seller->permission_add_offers == 1)
        @if ($contract !== null)
            <div class="row">
                <div class="card" id="contract" style="width: 100%;">
                    <div class="card-header">
                        <h5 class="mb-0 h6">CONTRACT</h5>
                    </div>
                    <div class="card-body">
                        @if ($contract->status == 0)
                            <center>
                                <h4>Your contract under review | عقدك قيد المراجعة</h4>
                                <p>Please print it and reupload the contract to complete process subscribe to offers </p>
                                <p>يرجى طباعته وإعادة تحميل العقد لإتمام عملية الاشتراك بالعروض</p>
                            </center>
                            <center>
                                <a href="javascript:void(0);" onclick="printFile('printableArea')"
                                    class="btn btn-primary">Print</a>
                                <br /><br />
                                <span>Or | أو</span>
                                <br />
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form method="POST" action="/upload_contract" enctype='multipart/form-data'>
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="date_create">Upload File حمل الملف</label>
                                            <input type="file" class="form-control" id="contract_file"
                                                name="contract_file" accept="application/pdf">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send | ارسل</button>
                                </form>
                                <img src="https://greencard-sa.com/public/uploads/all/signiature_gc.png" width="100px"
                                    hidden />
                            </center>
                        @elseif ($contract->status == 1)
                            <center>
                                <h4>Your contract under review | عقدك قيد المراجعة</h4>
                                <a href="{{ asset('public/' . $contract->file_url) }}" target="_blanck"
                                    class="btn btn-primary">Download Contract | تنزيل الملف</a>
                            </center>
                        @elseif($contract->status == -1)
                            <center>
                                <h4>Your contract has rejected | تم رفض عقدك</h4>
                                <a href="{{ asset('public/' . $contract->file_url) }}" target="_blanck"
                                    class="btn btn-primary">Download Contract | تنزيل الملف</a>
                                <br /><br /><br />
                                <h5>Cause of rejection | سبب الرفض</h5>
                                <p>
                                    {{ $contract->message }}
                                </p>

                                <br /><br /><br />
                                <a href="/init_contract/{{ $contract->id }}" class="btn btn-primary">Send new request |
                                    إرسال طلب جديد</a>
                            </center>
                        @endif
                    </div>
                </div>
            </div>



            <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>-->
            <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>-->
            <!--<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>-->
            <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js"></script>

        @endif
    @else
        <div class="row">
            <div class="card" id="contract" style="width: 100%;">
                <div class="card-header">
                    <h5 class="mb-0 h6">CONTRACT</h5>
                </div>
                <div class="card-body">
                    @if (isset($errors_contract))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors_contract as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="/send_contract">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="date_create">On</label>
                                <input type="date" class="form-control" id="date_create" name="date_create">
                            </div>
                            <div class="form-group col-md-6" dir="rtl">
                                <label dir="auto" for="date_create_ar" class="labelAr">في تاریخ</label>
                                <input dir="RTL" type="date" class="form-control" id="date_create_ar"
                                    name="date_create_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="company_name">The Company s name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="company_name_ar" class="labelAr">اسم الشركة</label>
                                <input dir="RTL" type="text" class="form-control" id="company_name_ar"
                                    name="company_name_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="comm_reg_no">under Commercial Registration No</label>
                                <input type="number" class="form-control" id="comm_reg_no" name="comm_reg_no">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="comm_reg_no_ar" class="labelAr"> العاملة تحت السجل التجاري
                                    رقم</label>
                                <input dir="RTL" type="number" class="form-control" id="comm_reg_no_ar"
                                    name="comm_reg_no_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="vat_reg">VAT registration number</label>
                                <input type="number" class="form-control" id="vat_reg" name="vat_reg">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="vat_reg_ar" class="labelAr">رقم التسجیل في ضریبة القیمة
                                    المضافة</label>
                                <input dir="RTL" type="number" class="form-control" id="vat_reg_ar"
                                    name="vat_reg_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="opr_as">It operates as</label>
                                <input type="text" class="form-control" id="opr_as" name="opr_as">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="opr_as_ar" class="labelAr"> تمارس نشاطھا بصفة</label>
                                <input dir="RTL" type="text" class="form-control" id="opr_as_ar"
                                    name="opr_as_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="contact_person">Contact person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="contact_person_ar" class="labelAr">مسؤول الاتصال</label>
                                <input dir="RTL" type="text" class="form-control" id="contact_person_ar"
                                    name="contact_person_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="email_ar" class="labelAr"> البرید الإلكتروني</label>
                                <input dir="RTL" type="email" class="form-control" id="email_ar"
                                    name="email_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="phone">The phone</label>
                                <input type="number" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="phone_ar" class="labelAr">الھاتف</label>
                                <input dir="RTL" type="number" class="form-control" id="phone_ar"
                                    name="phone_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="types_offers">The offer category provided by the second party</label>

                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="types_offers_ar" class="labelAr">فئة العرض المقدمة من الطرف
                                    الثاني</label>

                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="types_offers" name="types_offers[]"
                                    placeholder="1-">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <input dir="RTL" type="text" class="form-control" id="types_offers_ar"
                                    name="types_offers_ar[]" placeholder="1-">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="types_offers" name="types_offers[]"
                                    placeholder="2-">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <input dir="RTL" type="text" class="form-control" id="types_offers_ar"
                                    name="types_offers_ar[]" placeholder="2-">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="types_offers" name="types_offers[]"
                                    placeholder="3-">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <input dir="RTL" type="text" class="form-control" id="types_offers_ar"
                                    name="types_offers_ar[]" placeholder="3-">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="types_offers" name="types_offers[]"
                                    placeholder="4-">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <input dir="RTL" type="text" class="form-control" id="types_offers_ar"
                                    name="types_offers_ar[]" placeholder="4-">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="types_offers" name="types_offers[]"
                                    placeholder="5-">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <input dir="RTL" type="text" class="form-control" id="types_offers_ar"
                                    name="types_offers_ar[]" placeholder="5-">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="offer_price">Offer price</label>
                                <input type="number" class="form-control" id="offer_price" name="offer_price">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="offer_price_ar" class="labelAr">سعر العروض</label>
                                <input dir="RTL" type="number" class="form-control" id="offer_price_ar"
                                    name="offer_price_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="average_price">Average price Redemption amount</label>
                                <input type="number" class="form-control" id="average_price" name="average_price">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="average_price_ar" class="labelAr">سعر المتوسط مبلغ
                                    الاسترداد</label>
                                <input dir="RTL" type="number" class="form-control" id="average_price_ar"
                                    name="average_price_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="offers">Offers</label>
                                <div>
                                    @foreach ($types_offers as $type)
                                        <span><input type="checkbox" value="{{ $type->id }}" name="offers[]" />
                                            {{ $type->name }} <br /></span>
                                    @endforeach

                                </div>
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="company_name_ar" class="labelAr">العروض</label>
                                <div dir="RTL">
                                    @foreach ($types_offers as $type)
                                        <span class="labelAr"><input type="checkbox" value="{{ $type->id }}"
                                                name="offers_ar[]" /> {{ $type->name }}</span>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="type_offer_discount">Offer type Discount rate</label>
                                <input type="number" class="form-control" id="type_offer_discount"
                                    name="type_offer_discount">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="type_offer_discount_ar" class="labelAr">نوع العرض نسبة
                                    خصم</label>
                                <input dir="RTL" type="number" class="form-control" id="type_offer_discount_ar"
                                    name="type_offer_discount_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="activation_peroid_from">Activation period starts from</label>
                                <input type="date" class="form-control" id="activation_peroid_from"
                                    name="activation_peroid_from">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="activation_peroid_from_ar" class="labelAr">الفترة الزمنیة
                                    التفعیل من</label>
                                <input dir="RTL" type="date" class="form-control" id="activation_peroid_from_ar"
                                    name="activation_peroid_from_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="activation_peroid_expr">Expiry date </label>
                                <input type="date" class="form-control" id="activation_peroid_expr"
                                    name="activation_peroid_expr">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="activation_peroid_expr_ar" class="labelAr">تاریخ الانتھاء
                                    الى</label>
                                <input dir="RTL" type="date" class="form-control" id="activation_peroid_expr_ar"
                                    name="activation_peroid_expr_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="fees">Fees</label>
                                <input type="number" class="form-control" id="fees" name="fees">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="fees_ar" class="labelAr">الرسوم</label>
                                <input dir="RTL" type="number" class="form-control" id="fees_ar"
                                    name="fees_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="entry_fee">Entry Fee </label>
                                <input type="number" class="form-control" id="entry_fee" name="entry_fee">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="entry_fee_ar" class="labelAr">رسوم الاشتراك</label>
                                <input dir="RTL" type="number" class="form-control" id="entry_fee_ar"
                                    name="entry_fee_ar">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="currency">The currency</label>
                                <input type="text" class="form-control" id="currency" name="currency"
                                    value="SAR" disabled>
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="currency_ar" class="labelAr">العملة</label>
                                <input dir="RTL" type="text" class="form-control" id="currency_ar"
                                    name="currency_ar" value="SAR" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="commission">Commission </label>
                                <div class="form-row">
                                    <input type="number" class="form-control" id="commission" name="commission">
                                    <span>Commission by %</span>
                                </div>
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="commission_ar" class="labelAr">العمولة</label>
                                <input dir="RTL" type="number" class="form-control" id="commission_ar"
                                    name="commission_ar">
                                <span class="labelAr">العمولة ب %</span>

                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="job_title">Job title</label>
                                <input type="text" class="form-control" id="job_title" name="job_title">
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                                <label dir="RTL" for="job_title_ar" class="labelAr">المسمى الوظیفي</label>
                                <input dir="RTL" type="text" class="form-control" id="job_title_ar"
                                    name="job_title_ar">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%">Send | ارسل</button>
                    </form>
                </div>
            </div>


        </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
        crossorigin="anonymous"></script>
    <script type="text/javascript">
        function printFile(printableArea) {

            var printContents = document.getElementById(printableArea).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;



        }
    </script>
    <script>
        function printImg(url) {
            var win = window.open('');
            win.document.write('<img src="' + url + '" onload="window.print();window.close()" />');
            win.focus();
        }


        $("#containerAdd").hide();

        $("#cancelForm").on("click", function() {
            $("#containerAdd").hide();
        });

        function deleteOffer(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/delete_offer/' + id;
                }
            })

        }

        function addOffer() {
            $("#containerAdd").toggle()
        }
    </script>

    @if ($errors->any())
        <script>
            $("#containerAdd").show();
        </script>
    @endif
    <script></script>

@endsection



<div id="printableArea" style="display:none;">
    <!DOCTYPE html>
    <html>
    @if (!empty($contract))

        <head>
            <title>GC CONTRACT</title>
            <meta charset="UTF-8">
            <style type="text/css">
                /* body {
                                                        width:794px; height:1122px,
                                                        padding: 10px;

                                                      } */
                table {
                    border-spacing: 15px;
                }

                table tbody tr td {
                    border: 1px solid #000 !important;
                }


                .containerx {
                    width: 50% !important;
                    /* border: 1px solid #000; */
                    margin-right: 10px;
                    margin-left: 10px;
                    padding-top: 5px;
                    padding-bottom: 5px;
                    margin-top: 10px;
                    font-size: 12px;
                    font-family: sans-serif;
                    /* line-height: 20px; */
                }

                .containerArx {
                    width: 50% !imprtant;
                    /* border: 1px solid #000; */
                    margin-right: 10px;
                    margin-left: 10px;
                    padding-top: 5px;
                    padding-bottom: 5px;
                    text-align: right;
                    margin-top: 10px;
                    font-size: 12px;
                    font-family: sans-serif;
                    /* line-height: 20px; */
                }

                .containerLogo {
                    display: flex !important;
                    justify-content: right !important;
                    /* padding-top: 10px !important; */
                    padding-bottom: 30px !important;
                }

                h4 {
                    font-family: sans-serif;
                }

                h1 {
                    font-family: sans-serif;
                    font-size: 50px;
                    font-style: italic;
                }

                span {
                    padding-top: 15px;
                    font-family: sans-serif;
                }

                p {
                    padding: 5px;
                    font-family: sans-serif;
                    page-break-inside: avoid;
                }

                td {
                    font-family: sans-serif;
                }

                .file {
                    margin: 10px 10px;
                    page-break-inside: avoid;
                }

                li {
                    /* padding-top: 5px;
                                                      padding-bottom: 5px; */
                    page-break-inside: avoid;
                }

                table {
                    page-break-inside: auto
                }

                tr {
                    page-break-inside: avoid;
                    page-break-after: auto
                }

                td {
                    page-break-inside: avoid;
                    page-break-after: auto;
                    word-wrap: break-word;
                }

                thead {
                    display: table-header-group;
                }

                tfoot {
                    display: table-footer-group;
                }

                table {
                    page-break-before: always;
                }

                @media print {
                    table {
                        page-break-before: always;
                    }

                    table {
                        page-break-after: auto
                    }

                    tr {
                        page-break-inside: avoid;
                        page-break-after: auto
                    }

                    td {
                        page-break-inside: avoid;
                        page-break-after: auto;
                        width: 50%;
                    }

                    thead {
                        display: table-header-group
                    }

                    tfoot {
                        display: table-footer-group
                    }
                }

                #slug {
                    font-size: 22px;
                    font-family: sans-serif;
                    color: gray;
                    font-weight: bold;
                }

                #page {
                    padding-top: 100px;
                    padding-left: 50px;
                    display: block;
                    /* background-color: red; */
                    height: 900px;
                }

                #infos {
                    position: absolute;
                    bottom: 20px;
                    left: 50px;
                }

                div {
                    page-break-inside: avoid;
                }

                @page {
                    size: 25cm 35.7cm;
                    margin: 5mm 5mm 5mm 5mm;
                    /* change the margins as you want them to be. */
                }
            </style>

        </head>

        <body>

            <div id="dvContainer" class="file">
                <div id="page">
                    <h1>CONTRACT</h1>
                    <span id="slug">Service Provider Name</span>
                    <div id="infos">
                        <img src="{{ uploaded_asset(get_setting('system_logo_white')) }}"
                            width="160px"><br />

                        <span style="margin-top: 20px;">PO.Box 23218 Jeddah 2265 KSA</span><br />
                        <span>+966 12 663 3442</span>
                    </div>
                </div>
                <div style="break-after:page"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th colspan="2">
                                <div class="containerLogo">
                                    <img src="{{ uploaded_asset(get_setting('system_logo_white')) }}"
                                        width="240px">
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width:50%" class="containerx">
                                <p>On: {{ $contract->create_date }} of each between made was contract this:</p>
                            </td>
                            <td style="width:50%" class="containerArx">
                                <p>:إنه في تاریخ {{ $contract->create_date }} حرر ھذا العقد بین كل من</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="container">
                                <p>
                                    The first party:<br /><br />
                                    Green Card Trading Corporation / GREEN CARD represented by all its
                                    affiliated services and represented by Mr. Khaled Salem Harraf
                                    Lasloom<br /><br />
                                    Headquartered in Jeddah - Kingdom of Saudi Arabia - Al-Khayyat
                                    Tower - Ash-Sharafiya, Madinah Road, Third Floor, Office No. 32,
                                    working under Commercial Registration No.: 4030390607, Tel:
                                    0126633442 / Consolidated 920009120,<br />
                                    VAT registration number / 310710332100003 / Email:<br />
                                    Contract@greencard-sa.com
                                </p>
                            </td>
                            <td class="containerArx">
                                <p>
                                    :الطرف الأول<br /><br />
                                    مؤسسة الكرت الأخضر للتجارة / CARD GREEN ممثلة بجمیع الخدمات التابعة لھا
                                    ویمثلھا السید/ خالد سالم حراف لسلوم
                                    <br /><br />
                                    مقرھا جدة - المملكة العربیة السعودیة - برج الخیاط – الشرفیة طریق المدینة الدور
                                    الثالث مكتب رقم 32 العاملة تحت السجل التجاري رقم: 4030390607 ،ھاتف:
                                    920009120 الموحد / 0126633442
                                    <br /><br />
                                    رقم التسجیل في ضریبة القیمة المضافة / 310710332100003 / ایمیل<br />
                                    Contract@greencard-sa.com
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>
                                    Second Party : <br />
                                    The Company's name : {{ $contract->company_name }}<br />
                                    under Commercial Registration No : {{ $contract->comm_reg_no }}<br />
                                    VAT registration number : {{ $contract->vat_no }}<br />
                                    It operates as : {{ $contract->operates_as }}<br />
                                    Contact person : {{ $contract->contact_persons }}<br />
                                    E-mail : {{ $contract->email_company }}<br />
                                    the phone : {{ $contract->phone_company }}<br />
                                </p>
                            </td>
                            <td class="containerArx">
                                <p>
                                    : الطرف الثاني<br />
                                    اسم الشركة
                                    : {{ $contract->company_name_ar }}<br />
                                    العاملة تحت السجل التجاري رقم
                                    : {{ $contract->comm_reg_no }}<br />
                                    رقم التسجیل في ضریبة القیمة المضافة
                                    : {{ $contract->vat_no }}<br />
                                    تمارس نشاطھا بصفة
                                    : {{ $contract->operates_as_ar }} <br />
                                    مسؤول الاتصال
                                    : {{ $contract->contact_person_ar }} <br />
                                    : البرید الإلكتروني<br />
                                    {{ $contract->email_company }} <br />
                                    الھاتف
                                    : {{ $contract->phone_company }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>
                                    The offer category provided by the second party:<br />
                                <ol>
                                    <?php
                                    $types_offers_c = [];
                                    if ($contract->type_offer != null) {
                                        $types_offers_c = explode(',', $contract->type_offer);
                                        foreach ($types_offers_c as $tf) {
                                            echo '<li>' . $tf . '</li>';
                                        }
                                    }
                                    ?>
                                </ol>

                                </p>
                            </td>
                            <td class="containerArx" dir="RTL">
                                <p>
                                    فئة العرض المقدمة من الطرف الثاني:<br />
                                <ol>
                                    <?php
                                    $types_offers_c = [];
                                    if ($contract->type_offer != null) {
                                        $types_offers_c = explode(',', $contract->type_offer);
                                        foreach ($types_offers_c as $tf) {
                                            echo '<li>' . $tf . '</li>';
                                        }
                                    }
                                    ?>
                                </ol>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>
                                    Offer price : {{ $contract->price_offer }}<br />
                                    Average price Redemption amount: {{ $contract->average_price_amount }}<br />
                                <ol>
                                    <?php
                                    $offers = [];
                                    if ($contract->offers != null) {
                                        $offers = explode(',', $contract->offers);
                                        foreach ($types_offers as $tf) {
                                            if (in_array($tf->id, $offers)) {
                                                echo '<li>' . $tf->name . '</li>';
                                            }
                                        }
                                    }
                                    ?>
                                </ol>
                                Offer type Discount rate {{ $contract->prices_offers }}<br />
                                Activation period starts from: {{ $contract->date_start }}<br />
                                Expiry date : {{ $contract->date_exp }}<br />
                                </p>
                            </td>
                            <td class="containerArx" dir="RTL">
                                <p>
                                    سعر العروض {{ $contract->price_offer }}<br />
                                    سعر المتوسط مبلغ الاسترداد
                                    {{ $contract->average_price_amount }}
                                    <br />
                                <ol>
                                    <?php
                                    $offers = [];
                                    if ($contract->offers != null) {
                                        $offers = explode(',', $contract->offers);
                                        foreach ($types_offers as $tf) {
                                            if (in_array($tf->id, $offers)) {
                                                echo '<li>' . $tf->name . '</li>';
                                            }
                                        }
                                    }
                                    ?>
                                </ol>
                                نوع العرض نسبة خصم {{ $contract->prices_offers }}<br />
                                الفترة الزمنیة التفعیل من
                                : {{ $contract->date_start }} <br />
                                تاریخ الانتھاء الى
                                : {{ $contract->date_exp }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>
                                    Fees : {{ $contract->fees }}<br />
                                    Entry Fee : {{ $contract->entry_fee }}<br />
                                    the currency : {{ $contract->currency }}<br />
                                    Commission : {{ $contract->commission }} %
                                </p>
                            </td>
                            <td class="containerArx">
                                <p>
                                    {{ $contract->fees }} : الرسوم<br />
                                    {{ $contract->entry_fee }} : رسوم الاشتراك<br />
                                    {{ $contract->currency }} : العملة<br />
                                    % {{ $contract->commission }} :العمولة<br />
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td class="containerx">
                                <p>
                                    The first party is the owner of the "Green Card" application. This
                                    application provides services represented in marketing and
                                    electronic sales, as well as electronic coupons, an electronic
                                    discount card and sales within the online store, and a desire to
                                    cooperate between the first and second parties. The two parties
                                    have acknowledged their full legal capacity to contract and act,
                                    and this replaces any previous written agreements between the
                                    two parties.
                                </p>
                            </td>
                            <td class="containerArx">
                                <p>
                                    الطرف الأول ھو المالك لتطبیق "جرین كارد". ھذا التطبیق یقدم خدمات تتمثل في
                                    التسویق والمبیعات الإلكترونیة وعبارة عن
                                    كوبونات الكترونیة. وبطاقة خصومات الكترونیة ومبیعات ضمن المتجر الإلكتروني
                                    ورغبة في التعاون بین الطرفین الأول والثاني فقد أقر الطرفان بكامل أھلیتھم القانونیة
                                    على التعاقد والتصرف ویحل ھذا محل أي اتفاقات سابقة بین الطرفین كتابیة
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>Terms and Conditions:<br />
                                    These terms and conditions have been concluded between the
                                    two parties, the Green Card Corporation and the merchant to
                                    benefit from the marketing services of the "Green Card" special
                                    offers.<br />
                                    Submitted by the second party (to the client under the terms and
                                    conditions below). The contract is renewed and an appendix is
                                    attached after the agreement between the two parties at the end
                                    of the mentioned period according to
                                    above terms and conditions that serve all parties.</p>
                                <p style="margin-left: 40px; padding: 0px !important;">
                                <ol>
                                    <li>Under this agreement, the second party authorizes the
                                        first party to carry out marketing and advertising
                                        according to the model - data of offers and discounts -
                                        clause . The first party also releases its responsibility for
                                        all damages and compensation resulting from the
                                        second party's non-compliance.</li>
                                    <li>
                                        The second party is obligated to provide the agreed
                                        offers and services to the subscribers of Green Card
                                        membership in the application of the first party, the first
                                        party, and in the event that the offer is not available, the
                                        second party is obligated to compensate the application
                                        holder by giving him an alternative offer on another
                                        product or service equivalent to the value of the
                                        previous offer, or by compensating him with an amount
                                        of money equivalent to the value of the previous offer
                                        for a maximum period of three days If this is not done,
                                        the first party compensates the customer and collects all
                                        the expenses incurred in compensating the customer
                                        from the second party.</li>
                                    <li>Placing the inductive billboard welcoming the users of
                                        the first party application at the (second party's)
                                        customer reception and sales outlets.</li>
                                    <li>The costs of all services requested by the application
                                        holder shall be collected by the second party, without
                                        any liability on the first party.</li>
                                </ol>
                                </p>
                            </td>
                            <td class="containerArx" dir="RTL">
                                <p>الشروط والأحكام:<br />
                                    تم إبرام ھذه الشروط والبنود بین الطرفین مؤسسة الكرت الأخضر" والتاجر للاستفادة
                                    <br /><br />
                                    المقدمة من الطرف الثاني (إلى العمیل بموجب الشروط والاحكام أدناه). ویتم تجدید العقد
                                    وإلحاق ملحق بعد الاتفاق بین الطرفین في نھایة المدة المذكورة أعلاه بالشروط التي
                                    تخدم كل الأطراف.
                                </p>
                                <p style="margin-left: 40px; ">
                                <ol>
                                    <li>فوض الطرف الثاني بموجب ھذه الاتفاقیة الطرف الأول بالقیام بالتسویق
                                        والإعلان حسب نموذج – بیانات العروض والخصومات – كما یخلي الطرف
                                        الأول مسؤولیته من كافة الأضرار والتعویضات الناتجة من عدم التزام الطرف
                                        الثاني.
                                    </li>
                                    <li>لتزم الطرف الثاني بتقدیم العروض والخدمات المتفق علیھا لمشتركي عضویة
                                        جرین كارد الخاصة في تطبیق الطرف الأول، وفي حال عدم توفر العرض یلتزم
                                        الطرف الثاني بتعویض حامل التطبیق بمنحه عرض بدیل على منتج أو خدمة
                                        أخرى تعادل قیمة العرض السابق أو بتعویضه بمبلغ مالي یعادل قیمة العرض
                                        السابق بمدة أقصاھا ثلاثة أیام عمل وفي حال لم یتم ذلك فأن الطرف الأول
                                        یعوض العمیل ویحصل جمیع ما تكبده في تعویض العمیل من الطرف الثاني.
                                    </li>
                                    <li>
                                        وضع اللوحة الإعلانیة الاستدلالیة الترحیبیة بمستخدمي تطبیق الطرف الأول في
                                        منافذ بیع واستقبال العملاء لدى (الطرف الثاني).
                                    </li>
                                    <li>
                                        یتم تحصیل تكالیف جمیع الخدمات التي یطلبھا حامل التطبیق من قبل الطرف
                                        الثاني وذلك دون أدنى مسؤولیة على الطرف الأول.

                                    </li>
                                    <li>
                                        على الطرف الثاني الالتزام الكامل بقواعد وأنظمة المملكة العربیة السعودیة
                                        لضمان الجودة وھو وحده المسؤول عن سلامة وجودة المنتجات أو الخدمات التي
                                        یتطلب تسویقھا ومن المفھوم من كلا الطرفین أن الطرف الأول لا یتدخل في
                                        طبیعة مھنة الطرف الثاني وبالتالي لا یمكن أن یكون مسؤول عن أي شكاوى ذات
                                        صلة على الإطلاق.
                                    </li>
                                    <li>
                                        الطرف الأول لدیه الحق في استخدام شعارات وصور والاسم والعلامات التجاریة
                                        للطرف الثاني على موقع وتطبیق ومنصات الطرف الأول وفي حملاته التسویقیة
                                        والترویجیة.
                                    </li>
                                </ol>

                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>
                                <ol start="5">


                                    <li>The second party must fully comply with the rules and
                                        regulations of the Kingdom of Saudi Arabia to ensure
                                        quality and is solely responsible for the safety and
                                        quality of the products or services that require
                                        marketing. It is understood by both parties that the first
                                        party does not interfere with the nature of the
                                        profession of the second party and therefore cannot be
                                        responsible for any complaints Relevant at all.</li>
                                    <li>The first party has the right to use the logos, images,
                                        name and trademarks of the second party on the
                                        website, application and platforms of the first party and
                                        in its marketing and promotional campaigns.</li>
                                    <li>The second party guarantees that it is registered,
                                        licensed and legally authorized to provide services.</li>
                                    <li>The second party is obligated to secure a tablet device
                                        and internet and train its employees in all its sales or
                                        reception outlets to receive (Green Card) membership
                                        holders and give them the same offers found on the
                                        Green Card application and please coordinate with Green
                                        Card IT for any further questions or concerns if needed.</li>
                                    <li>The first party is obligated to provide the second party
                                        with a monthly report on the movement of customers
                                        using the Green Card application at the second party.</li>
                                    <li>The term of this contract is one year (1 Gregorian)
                                        and it is valid and effective from the date of the
                                        conclusion of the contract or the date of the contract
                                        until its end, and the contract is renewed upon its expiry
                                        in the event that the first party is notified that the
                                        renewal of the contract has not occurred 15 days in
                                        writing before its expiry.</li>
                                    <li>The second party is responsible for the correctness of all
                                        data, images, prices, offers and numbers on the Green
                                        Card website and application.</li>
                                    <li>The terms of this agreement are subject to all applicable
                                        rules and regulations in force in the Kingdom of Saudi
                                        Arabia.</li>
                                    <li>This contract has been issued in two copies, and each
                                        party has kept a copy of it to work in accordance with
                                        and in both Arabic and English, and the Arabic language
                                        is the valid language for this contract.</li>
                                </ol>

                                </p>
                            </td>
                            <td class="containerArx" dir="RTL">
                                <p style="margin-left: 40px; ">
                                <ol start="7">


                                    <li>
                                        یضمن الطرف الثاني أنه مسجل ومرخص ومفوض بشكل نظامي من أجل أن
                                        یقوم بتقدیم الخدمات الخاصة بموضوع ھذا العرض وبأنه المالك القانوني لمواد
                                        الطرف الثاني وبأنه قد قام بتفویض الطرف الأول بشكل نظامي من أجل استخدام
                                        ھذه المواد.
                                    </li>
                                    <li>
                                        لتزم الطرف الثاني بتأمین جھاز لوحي مع انترنت وتدریب موظفیه في جمیع منافذ
                                        البیع أو الاستقبال لدیه على استقبال حاملي عضویة (جرین كارد) ومنحھم نفس
                                        العروض الموجودة على تطبیق جرین كارت وتنسیق مع قسم الاي تي الخاص
                                        بالكرت الأخضر للاحتیاجات ان تطلب الأمر.
                                    </li>
                                    <li>
                                        یلتزم الطرف الأول بتزوید الطرف الثاني بتقریر شھري عن حركة العملاء
                                        المستخدمین لتطبیق جرین كارد عند الطرف الثاني.
                                    </li>
                                    <li>
                                        مدة ھذا العقد ھیه سنة ( 1 میلادیة ) وتكون نافذة وساریة المفعول اعتبارا من
                                        تاریخ ابرام العقد او تاریخ تفعیل العقد حتى نھایته , ویجدد العقد تلقائیا عند
                                        انتھائه
                                        بحال لم یتم اخطار الطرف الأول بعدم الرغبة بتجدید العقد قبل انتھائه ب ( 15
                                        یوم كتابیا ) .
                                    </li>
                                    <li>
                                        یكون الطرف الثاني ھو المسؤول عن صحة جمیع البیانات والصور والأسعار
                                        والعروض والأرقام الخاصة به على موقع وتطبیق جرین كارد.

                                    </li>
                                    <li>
                                        تخضع بنود ھذه الاتفاقیة لكافة القواعد والأنظمة المرعیة المعمول بھا والمطبقة
                                        بالمملكة العربیة السعودیة.
                                    </li>
                                    <li>
                                        حرر ھذا العقد من نسختین احتفظ كل طرف بنسخة منھا للعمل بموجبھا وباللغتین
                                        العربیة والانجلیزیة وتكون اللغة العربیة ھي اللغة المعتبرة لھذا العقد.
                                    </li>
                                </ol>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>
                                    Note: The Green Card Corporation has provided a binding
                                    guarantee to all of its customers who have the application that all
                                    the offers listed in the application are real offers and the
                                    Foundation has pledged to its customers to compensate them in
                                    the event that they do not receive the offers listed in the
                                    application and accordingly the merchant or creator (the second
                                    party) is obligated under this contract Compensating the Green
                                    Card Corporation (the first party) for all its losses by which the
                                    Corporation compensates its customers who did not receive the
                                    offers included in this contract
                                </p>
                            </td>
                            <td class="containerArx" dir="RTL">
                                <p>
                                    ملحوظة: قدمت مؤسسة الكرت الأخضر" ضمان ملزم لھا إلى كل عملائھا حاملي
                                    التطبیق بأن جمیع العروض المدرجة في التطبیق عروض حقیقیة وتعھدت المؤسسة
                                    لعملائھا بتعویضھم في حال لم یحصلوا على العروض المدرجة في التطبیق وبناء علیه
                                    فإن التاجر أو المنشئ) الطرف الثاني (ملزم بموجب ھذا العقد بتعویض مؤسسة الكرت
                                    الأخضر (الطرف الأول) عن جمیع خسائرھا التي تعوض بھا المؤسسة عملائھا الذین لم
                                    یحصلوا على العروض المدرجة في ھذا العقد.

                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>
                                    Prosecution of the Green Card Foundation<br />
                                    The name : Khaled salem lasloom<br />
                                    Job title : CEO<br />
                                    Date: {{ $contract->create_date }}<br />
                                    Signature: <img src="https://greencard-sa.com/public/uploads/all/signiature_gc.png"
                                        width="100" />
                                </p>
                            </td>
                            <td class="containerArx" dir="RTL">
                                <p>
                                    النیابة عن مؤسسة الكرت الأخضر
                                    <br />الاسم : خالد سالم لسلوم<br />
                                    المسمى الوظیفي : المدیر التنفیذي<br />
                                    التاریخ
                                    : {{ $contract->create_date }}<br />
                                    التوقیع:<br />
                                    ختم المؤسسة:
                                    <img src="https://greencard-sa.com/public/uploads/all/signiature_gc.png"
                                        width="100" />
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="containerx">
                                <p>
                                    Prosecuting the merchant<br />
                                    The name : {{ Auth::user()->name }}<br />
                                    Job title : {{ $contract->job_name }}<br />
                                    Date: {{ $contract->create_date }}<br />
                                    Signature<br />
                                </p>
                            </td>
                            <td class="containerArx" dir="RTL">
                                <p>
                                    النیابة عن التاجر<br />
                                    الاسم
                                    : {{ Auth::user()->name }}<br />
                                    المسمى الوظیفي
                                    : {{ $contract->job_name }}<br />
                                    التاریخ
                                    : {{ $contract->create_date }}<br />
                                    التوقیع:<br />
                                    ختم الشركة:<br />
                                </p>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <span style="color: gray;">
                                    <strong style="color: #8ac349">Green Card Trading Corporation</strong><br />
                                    +966 12 633 3442<br />
                                    PO.Box 23218 Jeddah 2265 KSA<br />
                                    CR4030390607<br />
                                </span>
                            </td>
                            <td dir="RTL">
                                <h4 style="align-content: flex-end; justify-content: flex-end;">
                                    www.greencard-sa.com
                                </h4>
                            </td>
                        </tr>
                    </tfoot>

                </table>

            </div>
        </body>

    </html>
    @endif

</div>