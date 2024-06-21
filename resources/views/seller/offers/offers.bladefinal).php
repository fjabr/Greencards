@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Dashboard') }}</h1>
            </div>
            
        </div>
    </div> 
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if($seller->permission_add_offers == 2)
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
                                      @foreach($types_offers as $type)
                                        <?php
                                            $allowed_types = explode(",", $contract->offers);
                                            if(in_array($type->id, $allowed_types)){
                                                echo "<option value='".$type->id."'>".$type->name."</option>";
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
                        <div class="form-group col-md-6">
                          <label for="description">Description</label>
                          <input type="text" required class="form-control" id="description" name="description" placeholder="Description">
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="code">Code</label>
                          <input type="text" required class="form-control" id="code" name="code" placeholder="xxxx">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="points">Number of points</label>
                          <input type="number" required class="form-control" id="points" name="points" placeholder="points">
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
                              <i class="las la-plus aiz-side-nav-icon" ></i>
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
                                            <a href="/edit_offer/{{ $offer->id }}" class="">
                                                <i class="las la-edit aiz-side-nav-icon"></i>
                                            </a>
                                            <span> </span>
                                            <a href="#offers" onclick="deleteOffer({{ $offer->id }})" class="">
                                                <i class="las la-trash aiz-side-nav-icon"></i>
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
                        <img loading="lazy"  src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=https://test01.greencard-sa.com/id/shop/{{ $shop_name }}&choe=UTF-8" alt="" width="80%">
                        <div style="margin-top: -5px">
                            <center>
                                <img src='{{ asset("public/".$shop->logo_url) }}' width="70px"  />
                                <img src='https://test01.greencard-sa.com/public/uploads/all/4FYHed0ZDO5msGHah3bGPo2BBJjzMu8Oj2Qfbtb0.png' width="70px" />
                            </center>
                        </div>
                        
                        <span style="margin-top: 14px">by Green Card</span>
                      </center>
                     
                  </div>
                  <script>
        function printDiv() {
            var divContents = document.getElementById("card_qr").innerHTML;
            var a = window.open('', '', 'height=500, width=500');
            a.document.write('<html>');
            a.document.write('<body > <h1>QR <br>');
            a.document.write(divContents);
            a.document.write('</body></html>');
            a.document.close();
            a.print();
        }
    </script>
                  <button onclick="printDiv()" class="btn btn-primary">Print</button>
              </div>
            </div>
        </div>
    @elseif ($seller->permission_add_offers == 1)
        @if($contract !== null)
        <div class="row">
            <div class="card" id="contract" style="width: 100%;">
                <div class="card-header">
                        <h5 class="mb-0 h6">CONTRACT  </h5>
                </div>
                <div class="card-body">
                    <!--changed by hassan  status == 0-->
                    @if($contract->status == 2)
                    <center>
                        <h4>Your contract under review | عقدك قيد المراجعة</h4>
                        <p>Please print it and reupload the contract to complete process subscribe to offers </p>
                        <p>يرجى طباعته وإعادة تحميل العقد لإتمام عملية الاشتراك بالعروض</p>
                    </center>
                    <center>
                       <!-- <button onclick="printFile('printableArea')" class="btn btn-primary" >Print | اطبعه</button>
                        <input type="button" onclick="printFile()" value="print a div!"  class="btn btn-primary">-->
                        <!--<input type="button" onclick="printDiv('printableArea')" value="print a div!" />-->
                     <a href="javascript:void(0);" onclick="printFile('printableArea')" class="btn btn-primary">Print</a>

                        <br/><br/>
                                               

                        <span>Or | أو</span>
                        <br/>
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
                                  <input type="file" class="form-control" id="contract_file" name="contract_file" accept="application/pdf"  >
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Send | ارسل</button>
                        </form>
                        <img src="https://test01.greencard-sa.com/public/uploads/all/signiature_gc.png" width="100px" hidden />
                    </center>
                    @elseif ($contract->status == 1)
                        <center>
                            <h4>Your contract under review | عقدك قيد المراجعة</h4>
                            <a href="{{ asset("public/".$contract->file_url) }}" target="_blanck" class="btn btn-primary">Download Contract | تنزيل الملف</a>
                        </center>
                    @elseif($contract->status == -1)
                        <center>
                            <h4>Your contract has rejected | تم رفض عقدك</h4>
                            <a href="{{ asset("public/".$contract->file_url) }}" target="_blanck" class="btn btn-primary">Download Contract | تنزيل الملف</a>
                            <br/><br/><br/>
                            <h5>Cause of rejection | سبب الرفض</h5>
                            <p>
                                {{ $contract->message }}
                            </p>
                            
                            <br/><br/><br/>
                             <a href="/init_contract/{{ $contract->id }}" class="btn btn-primary">Send new request | إرسال طلب جديد</a>
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
           
                <script type="text/javascript">
             
                    function printFile(printableArea) {
                        
                      var printContents = document.getElementById(printableArea).innerHTML;
                      var originalContents = document.body.innerHTML;
                
                     document.body.innerHTML = printContents;
                
                     window.print();
                
                     document.body.innerHTML = originalContents;
                        
                   
                   
                    }    
                 
                        
                </script>
                 
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
                              <input type="date" class="form-control" id="date_create" name="date_create"  >
                            </div>
                            <div class="form-group col-md-6" dir="rtl">
                              <label dir="auto" for="date_create_ar" class="labelAr">في تاریخ</label>
                              <input dir="RTL" type="date" class="form-control" id="date_create_ar" name="date_create_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="company_name">The Company s name</label>
                              <input type="text" class="form-control" id="company_name" name="company_name"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="company_name_ar" class="labelAr">اسم الشركة</label>
                              <input dir="RTL" type="text" class="form-control" id="company_name_ar" name="company_name_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="comm_reg_no">under Commercial Registration No</label>
                              <input type="number" class="form-control" id="comm_reg_no" name="comm_reg_no"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="comm_reg_no_ar" class="labelAr"> العاملة تحت السجل التجاري رقم</label>
                              <input dir="RTL" type="number" class="form-control" id="comm_reg_no_ar" name="comm_reg_no_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="vat_reg">VAT registration number</label>
                              <input type="number" class="form-control" id="vat_reg" name="vat_reg"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="vat_reg_ar" class="labelAr">رقم التسجیل في ضریبة القیمة المضافة</label>
                              <input dir="RTL" type="number" class="form-control" id="vat_reg_ar" name="vat_reg_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="opr_as">It operates as</label>
                              <input type="text" class="form-control" id="opr_as" name="opr_as"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="opr_as_ar" class="labelAr"> تمارس نشاطھا بصفة</label>
                              <input dir="RTL" type="text"  class="form-control" id="opr_as_ar" name="opr_as_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="contact_person">Contact person</label>
                              <input type="text" class="form-control" id="contact_person" name="contact_person"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="contact_person_ar" class="labelAr">مسؤول الاتصال</label>
                              <input dir="RTL" type="text"  class="form-control" id="contact_person_ar" name="contact_person_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="email">E-mail</label>
                              <input type="email" class="form-control" id="email" name="email"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="email_ar" class="labelAr"> البرید الإلكتروني</label>
                              <input dir="RTL" type="email"  class="form-control" id="email_ar" name="email_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="phone">The phone</label>
                              <input type="number" class="form-control" id="phone" name="phone"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="phone_ar" class="labelAr">الھاتف</label>
                              <input dir="RTL" type="number"  class="form-control" id="phone_ar" name="phone_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="types_offers">The offer category provided by the second party</label>
                              
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="types_offers_ar" class="labelAr">فئة العرض المقدمة من الطرف الثاني</label>
                              
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <input type="text" class="form-control" id="types_offers" name="types_offers[]" placeholder="1-"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <input dir="RTL" type="text" class="form-control" id="types_offers_ar" name="types_offers_ar[]" placeholder="1-" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <input type="text" class="form-control" id="types_offers" name="types_offers[]" placeholder="2-"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <input dir="RTL" type="text" class="form-control" id="types_offers_ar" name="types_offers_ar[]" placeholder="2-" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <input type="text" class="form-control" id="types_offers" name="types_offers[]" placeholder="3-"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <input dir="RTL" type="text" class="form-control" id="types_offers_ar" name="types_offers_ar[]" placeholder="3-" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <input type="text" class="form-control" id="types_offers" name="types_offers[]" placeholder="4-"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <input dir="RTL" type="text" class="form-control" id="types_offers_ar" name="types_offers_ar[]" placeholder="4-" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <input type="text" class="form-control" id="types_offers" name="types_offers[]" placeholder="5-"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <input dir="RTL" type="text" class="form-control" id="types_offers_ar" name="types_offers_ar[]" placeholder="5-" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="offer_price">Offer price</label>
                              <input type="number" class="form-control" id="offer_price" name="offer_price"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="offer_price_ar" class="labelAr">سعر العروض</label>
                              <input dir="RTL" type="number" class="form-control" id="offer_price_ar" name="offer_price_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="average_price">Average price Redemption amount</label>
                              <input type="number" class="form-control" id="average_price" name="average_price"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="average_price_ar" class="labelAr">سعر المتوسط مبلغ الاسترداد</label>
                              <input dir="RTL" type="number" class="form-control" id="average_price_ar" name="average_price_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="offers">Offers</label>
                                <div>
                                    @foreach($types_offers as $type)
                                        <span><input type="checkbox" value="{{$type->id}}" name="offers[]" /> {{$type->name}} <br/></span>
                                    @endforeach
                                    
                                </div>
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="company_name_ar" class="labelAr">العروض</label>
                              <div dir="RTL">
                                    @foreach($types_offers as $type)
                                         <span class="labelAr"><input type="checkbox" value="{{$type->id}}" name="offers_ar[]" /> {{$type->name}}</span>
                                    @endforeach
                              </div>
                              
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="type_offer_discount">Offer type Discount rate</label>
                              <input type="number" class="form-control" id="type_offer_discount" name="type_offer_discount"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="type_offer_discount_ar" class="labelAr">نوع العرض نسبة خصم</label>
                              <input dir="RTL" type="number" class="form-control" id="type_offer_discount_ar" name="type_offer_discount_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="activation_peroid_from">Activation period starts from</label>
                              <input type="date" class="form-control" id="activation_peroid_from" name="activation_peroid_from"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="activation_peroid_from_ar" class="labelAr">الفترة الزمنیة التفعیل من</label>
                              <input dir="RTL" type="date"  class="form-control" id="activation_peroid_from_ar" name="activation_peroid_from_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="activation_peroid_expr">Expiry date </label>
                              <input type="date" class="form-control" id="activation_peroid_expr" name="activation_peroid_expr"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="activation_peroid_expr_ar" class="labelAr">تاریخ الانتھاء الى</label>
                              <input dir="RTL" type="date" class="form-control" id="activation_peroid_expr_ar" name="activation_peroid_expr_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="fees">Fees</label>
                              <input type="number" class="form-control" id="fees" name="fees"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="fees_ar" class="labelAr">الرسوم</label>
                              <input dir="RTL" type="number" class="form-control" id="fees_ar" name="fees_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="entry_fee">Entry Fee </label>
                              <input type="number" class="form-control" id="entry_fee" name="entry_fee"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="entry_fee_ar" class="labelAr">رسوم الاشتراك</label>
                              <input dir="RTL" type="number" class="form-control" id="entry_fee_ar" name="entry_fee_ar" >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="currency">The currency</label>
                              <input type="text" class="form-control" id="currency" name="currency" value="SAR" disabled >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="currency_ar" class="labelAr">العملة</label>
                              <input dir="RTL" type="text" class="form-control" id="currency_ar" name="currency_ar" value="SAR" disabled >
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="commission">Commission </label>
                              <div class="form-row">
                                <input type="number" class="form-control" id="commission" name="commission"  >
                                <span>Commission by %</span>
                              </div>
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="commission_ar" class="labelAr">العمولة</label>
                              <input dir="RTL" type="number" class="form-control" id="commission_ar" name="commission_ar" > 
                              <span class="labelAr">العمولة ب %</span>
                              
                            </div>
                        </div>
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="job_title">Job title</label>
                              <input type="text" class="form-control" id="job_title" name="job_title"  >
                            </div>
                            <div class="form-group col-md-6" dir="RTL">
                              <label dir="RTL" for="job_title_ar" class="labelAr">المسمى الوظیفي</label>
                              <input dir="RTL" type="text" class="form-control" id="job_title_ar" name="job_title_ar" >
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%">Send | ارسل</button>
                    </form>
                </div>
            </div>
            
            
        </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script>

        function printImg(url) {
          var win = window.open('');
          win.document.write('<img src="' + url + '" onload="window.print();window.close()" />');
          win.focus();
        }

        
        $("#containerAdd").hide();
        
        $("#cancelForm").on("click", function(){
             $("#containerAdd").hide();
        });
        
        function deleteOffer(id){
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
                window.location.href = '/delete_offer/'+id;
              }
            })
            console.log(id);
        }
        
        function addOffer(){
            $("#containerAdd").toggle()
        }
    </script>
    
    @if ($errors->any())
        <script>
            $("#containerAdd").show();
        </script>
    @endif
       
        <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>  
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js"></script>
 
@endsection

  <div id="printableArea"  style="display:none;">
      <!DOCTYPE html>
                                <html>
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
                                                      page-break-inside:avoid;
                                                    }
                                                    td {
                                                      font-family: sans-serif;
                                                    }
                                                    .file {
                                                      margin: 10px 10px;
                                                      page-break-inside:avoid;
                                                    }
                                                    li {
                                                      /* padding-top: 5px;
                                                      padding-bottom: 5px; */
                                                      page-break-inside:avoid;
                                                    }
                                                    table { page-break-inside:auto }
                                                      tr    { page-break-inside:avoid; page-break-after:auto }
                                                    td    { 
                                                      page-break-inside:avoid; 
                                                      page-break-after:auto;
                                                      word-wrap:break-word;
                                                    }
                                                    thead
                                                        {
                                                            display: table-header-group;
                                                        }
                                                        tfoot
                                                        {
                                                            display: table-footer-group;
                                                        }
                                                    table {page-break-before: always;}
                                                    @media print
                                                    {
                                                      table {page-break-before: always;}
                                                      table { page-break-after:auto }
                                                      tr    { page-break-inside:avoid; page-break-after:auto }
                                                      td    { page-break-inside:avoid; page-break-after:auto ; width:50%; }
                                                      thead { display:table-header-group }
                                                      tfoot { display:table-footer-group }
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
                                                    div   { page-break-inside:avoid; }
                                                    @page {
                                                            size: 25cm 35.7cm;
                                                            margin: 5mm 5mm 5mm 5mm; /* change the margins as you want them to be. */
                                                        }
                                                    </style>
                                                </head>
                                                <body>
                                                      <div id="dvContainer" class="file">
                                                      <div id="page">
                                                        <h1>CONTRACT</h1>
                                                        <span id="slug">Service Provider Name</span>
                                                        <div id="infos">
                                                          <img src="https://test01.greencard-sa.com/public/uploads/all/4FYHed0ZDO5msGHah3bGPo2BBJjzMu8Oj2Qfbtb0.png" width="160px"><br/>
                                                          
                                                          <span style="margin-top: 20px;">PO.Box 23218 Jeddah 2265 KSA</span><br/>
                                                          <span>+966 12 663 3442</span>
                                                        </div>
                                                      </div>
                                                      <div style="break-after:page"></div>
                                                        <table width="100%">
                                                        <thead>
                                                          <tr>
                                                            <th colspan="2" >
                                                              <div class="containerLogo">
                                                                <img src="https://test01.greencard-sa.com/public/uploads/all/4FYHed0ZDO5msGHah3bGPo2BBJjzMu8Oj2Qfbtb0.png" width="240px">
                                                              </div>
                                                            </th>
                                                          </tr>
                                                        </thead>
                                                          <tbody>
                                                          <tr >
                                                            <td style="width:50%" class="containerx"><p>On: {{ $contract->create_date }} of each between made was contract this:</p></td>
                                                            <td style="width:50%" class="containerArx"><p>:إنه في تاریخ {{ $contract->create_date }} حرر ھذا العقد بین كل من</p></td>
                                                          </tr>
                                                          <tr>
                                                            <td class="container">
                                                              <p>
                                                                The first party:<br/><br/>
                                                                Green Card Trading Corporation / GREEN CARD represented by all its
                                                                affiliated services and represented by Mr. Khaled Salem Harraf
                                                                Lasloom<br/><br/>
                                                                Headquartered in Jeddah - Kingdom of Saudi Arabia - Al-Khayyat
                                                                Tower - Ash-Sharafiya, Madinah Road, Third Floor, Office No. 32,
                                                                working under Commercial Registration No.: 4030390607, Tel:
                                                                0126633442 / Consolidated 920009120,<br/>
                                                                VAT registration number / 310710332100003 / Email:<br/>
                                                                Contract@greencard-sa.com
                                                              </p>
                                                            </td>
                                                            <td class="containerArx">
                                                              <p>
                                                                :الطرف الأول<br/><br/>
                                                                مؤسسة الكرت الأخضر للتجارة / CARD GREEN ممثلة بجمیع الخدمات التابعة لھا
                                                                ویمثلھا السید/ خالد سالم حراف لسلوم
                                                                <br/><br/>
                                                                مقرھا جدة - المملكة العربیة السعودیة - برج الخیاط – الشرفیة طریق المدینة الدور
                                                                الثالث مكتب رقم 32 العاملة تحت السجل التجاري رقم: 4030390607 ،ھاتف:
                                                                920009120 الموحد / 0126633442
                                                                <br/><br/>
                                                                رقم التسجیل في ضریبة القیمة المضافة / 310710332100003 / ایمیل<br/>
                                                                Contract@greencard-sa.com
                                                              </p>
                                                            </td>
                                                          </tr>
                                                          <tr>
                                                            <td class="containerx">
                                                              <p>
                                                                Second Party : <br/>
                                                                The Company's name : {{ $contract->company_name }}<br/>
                                                                under Commercial Registration No : {{ $contract->comm_reg_no }}<br/>
                                                                VAT registration number : {{ $contract->vat_no }}<br/>
                                                                It operates as : {{ $contract->operates_as }}<br/>
                                                                Contact person : {{ $contract->contact_persons }}<br/>
                                                                E-mail : {{ $contract->email_company }}<br/>
                                                                the phone : {{ $contract->phone_company }}<br/>
                                                              </p>
                                                            </td>
                                                            <td class="containerArx">
                                                              <p>
                                                                : الطرف الثاني<br/>
                                                                  اسم الشركة
                                                                 : {{ $contract->company_name_ar }}<br/>
                                                                  العاملة تحت السجل التجاري رقم
                                                                : {{ $contract->comm_reg_no }}<br/>
                                                                   رقم التسجیل في ضریبة القیمة المضافة
                                                                : {{ $contract->vat_no }}<br/>
                                                                 تمارس نشاطھا بصفة
                                                                 : {{ $contract->operates_as_ar }} <br/>
                                                                مسؤول الاتصال
                                                                : {{ $contract->contact_person_ar }} <br/>
                                                                : البرید الإلكتروني<br/>
                                                                  {{ $contract->email_company }} <br/>
                                                                 الھاتف
                                                                 : {{ $contract->phone_company }}
                                                              </p>
                                                            </td>
                                                          </tr>
                                                          <tr>
                                                            <td class="containerx">
                                                              <p>
                                                                The offer category provided by the second party:<br/>
                                                                <ol>
                                                                <?php
                                                                    $types_offers_c = [];
                                                                    if($contract->type_offer != null){
                                                                        $types_offers_c = explode(",", $contract->type_offer);
                                                                        foreach($types_offers_c as $tf){
                                                                            echo "<li>".$tf."</li>";
                                                                        }
                                                                    }
                                                                ?>
                                                                </ol>
                                                                
                                                              </p>
                                                            </td>
                                                            <td class="containerArx" dir="RTL">
                                                              <p>
                                                                فئة العرض المقدمة من الطرف الثاني:<br/>
                                                                <ol>
                                                                <?php
                                                                    $types_offers_c = [];
                                                                    if($contract->type_offer != null){
                                                                        $types_offers_c = explode(",", $contract->type_offer);
                                                                        foreach($types_offers_c as $tf){
                                                                            echo "<li>".$tf."</li>";
                                                                        }
                                                                    }
                                                                ?>
                                                                </ol>
                                                              </p>
                                                            </td>
                                                          </tr>
                                                          <tr>
                                                            <td class="containerx" >
                                                              <p>
                                                                Offer price : {{ $contract->price_offer }}<br/>
                                                                Average price Redemption amount: {{ $contract->average_price_amount }}<br/>
                                                                <ol>
                                                                <?php
                                                                    $offers = [];
                                                                    if($contract->offers != null){
                                                                        $offers = explode(",", $contract->offers);
                                                                        foreach($types_offers as $tf){
                                                                            if(in_array($tf->id, $offers)) echo "<li>".$tf->name."</li>";
                                                                        }
                                                                    }
                                                                ?>
                                                                </ol>
                                                                Offer type Discount rate {{ $contract->prices_offers }}<br/>
                                                                Activation period starts from: {{ $contract->date_start }}<br/>
                                                                Expiry date : {{ $contract->date_exp }}<br/>
                                                              </p>
                                                            </td>
                                                            <td class="containerArx" dir="RTL">
                                                              <p>
                                                                 سعر العروض {{ $contract->price_offer }}<br/>
                                                                 سعر المتوسط مبلغ الاسترداد
                                                                 {{ $contract->average_price_amount }}
                                                                 <br/>
                                                                <ol>
                                                                <?php
                                                                    $offers = [];
                                                                    if($contract->offers != null){
                                                                        $offers = explode(",", $contract->offers);
                                                                        foreach($types_offers as $tf){
                                                                            if(in_array($tf->id, $offers)) echo "<li>".$tf->name."</li>";
                                                                        }
                                                                    }
                                                                ?>
                                                                </ol>
                                                                نوع العرض نسبة خصم {{ $contract->prices_offers }}<br/>
                                                                  الفترة الزمنیة التفعیل من
                                                                : {{ $contract->date_start }} <br/>
                                                                تاریخ الانتھاء الى
                                                                : {{ $contract->date_exp }} 
                                                              </p>
                                                            </td>
                                                          </tr>
                                                          <tr>
                                                            <td class="containerx">
                                                              <p>
                                                                Fees : {{ $contract->fees }}<br/>
                                                                Entry Fee : {{ $contract->entry_fee }}<br/>
                                                                the currency : {{ $contract->currency }}<br/>
                                                                Commission : {{ $contract->commission }} %
                                                              </p>
                                                            </td>
                                                            <td class="containerArx">
                                                              <p>
                                                                {{ $contract->fees }} : الرسوم<br/>
                                                                {{ $contract->entry_fee }} : رسوم الاشتراك<br/>
                                                                {{ $contract->currency }} : العملة<br/>
                                                                % {{ $contract->commission }} :العمولة<br/>
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
                                                          <p>Terms and Conditions:<br/>
                                                          These terms and conditions have been concluded between the
                                                          two parties, the Green Card Corporation and the merchant to
                                                          benefit from the marketing services of the "Green Card" special
                                                          offers.<br/>
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
                                                          <p>الشروط والأحكام:<br/>
                                                            تم إبرام ھذه الشروط والبنود بین الطرفین مؤسسة الكرت الأخضر" والتاجر للاستفادة
                                                            <br/><br/>
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
                                                                تاریخ ابرام العقد او تاریخ تفعیل العقد حتى نھایته , ویجدد العقد تلقائیا عند انتھائه
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
                                                                Prosecution of the Green Card Foundation<br/>
                                                                The name : Khaled salem lasloom<br/>
                                                                Job title : CEO<br/>
                                                                Date: {{ $contract->create_date }}<br/>
                                                                Signature: <img src="https://test01.greencard-sa.com/public/uploads/all/signiature_gc.png" width="100" />
                                                              </p>
                                                            </td>
                                                            <td class="containerArx" dir="RTL">
                                                              <p>
                                                                النیابة عن مؤسسة الكرت الأخضر
                                                                <br/>الاسم : خالد سالم لسلوم<br/>
                                                                المسمى الوظیفي : المدیر التنفیذي<br/>
                                                                التاریخ
                                                                : {{ $contract->create_date }}<br/>
                                                                التوقیع:<br/>
                                                                ختم المؤسسة:
                                                                <img src="https://test01.greencard-sa.com/public/uploads/all/signiature_gc.png" width="100" />
                                                              </p>
                                                            </td>
                                                          </tr>
                                                          <tr>
                                                            <td class="containerx">
                                                              <p>
                                                                Prosecuting the merchant<br/>
                                                                The name : {{ Auth::user()->name }}<br/>
                                                                Job title : {{ $contract->job_name }}<br/>
                                                                Date: {{ $contract->create_date }}<br/>
                                                                Signature<br/>
                                                              </p>
                                                            </td>
                                                            <td class="containerArx" dir="RTL">
                                                              <p>
                                                                النیابة عن التاجر<br/>
                                                                الاسم
                                                                : {{ Auth::user()->name }}<br/>
                                                                المسمى الوظیفي
                                                                : {{ $contract->job_name }}<br/>
                                                                التاریخ
                                                                : {{ $contract->create_date }}<br/>
                                                                التوقیع:<br/>
                                                                ختم الشركة:<br/>
                                                              </p>
                                                            </td>
                                                          </tr>
                                                        </tbody>
                                                        <tfoot >
                                                          <tr>
                                                            <td >
                                                              <span style="color: gray;">
                                                                <strong style="color: #8ac349">Green Card Trading Corporation</strong><br/>
                                                                +966 12 633 3442<br/>
                                                                PO.Box 23218 Jeddah 2265 KSA<br/>
                                                                CR4030390607<br/>
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
                                        
</div>
