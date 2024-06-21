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
                    @if($contract->status == 0)
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

@include('contractPdf.tarmatos')
