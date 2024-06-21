import "./styles.css";

import printJS from "print-js";

function printRawHTML() {
  let rawHTML = `
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
                  
                        .container {
                          width: 50%;
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
                      .containerAr {
                          width: 50%;
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
                        td    { page-break-inside:avoid; page-break-after:auto }
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
                            <tr>
                              <td class="container"><p>On: {{ $contract->create_date }} of each between made was contract this:</p></td>
                              <td class="containerAr"><p>:إنه في تاریخ {{ $contract->create_date }} حرر ھذا العقد بین كل من</p></td>
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
                              <td class="containerAr">
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
                              <td class="container">
                                <p>
                                  Second Party : <br/>
                                  The Company s name : {{ $contract->company_name }}<br/>
                                  under Commercial Registration No : {{ $contract->comm_reg_no }}<br/>
                                  VAT registration number : {{ $contract->vat_no }}<br/>
                                  It operates as : {{ $contract->operates_as }}<br/>
                                  Contact person : {{ $contract->contact_persons }}<br/>
                                  E-mail : {{ $contract->email_company }}<br/>
                                  the phone : {{ $contract->phone_company }}<br/>
                                </p>
                              </td>
                              <td class="containerAr">
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
                              <td class="container">
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
                              <td class="containerAr" dir="RTL">
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
                              <td class="container" >
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
                              <td class="containerAr" dir="RTL">
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
                              <td class="container">
                                <p>
                                  Fees : {{ $contract->fees }}<br/>
                                  Entry Fee : {{ $contract->entry_fee }}<br/>
                                  the currency : {{ $contract->currency }}<br/>
                                  Commission : {{ $contract->commission }} %<br/>
                                </p>
                              </td>
                              <td class="containerAr">
                                <p>
                                  {{ $contract->fees }} : الرسوم<br/>
                                  {{ $contract->entry_fee }} : رسوم الاشتراك<br/>
                                  {{ $contract->currency }} : العملة<br/>
                                  % {{ $contract->commission }} :العمولة<br/>
                                </p>
                              </td>
                            </tr>
                          
                            <tr>
                              <td class="container">
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
                              <td class="containerAr">
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
                          <td class="container">
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
                                second party s non-compliance.</li>
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
                                    the first party application at the (second party s)
                                    customer reception and sales outlets.</li>
                                    <li>The costs of all services requested by the application
                                    holder shall be collected by the second party, without
                                    any liability on the first party.</li>
                              </ol>
                            </p>
                          </td>
                          <td class="containerAr" dir="RTL">
                            <p>الشروط والأحكام:<br/>
                              تم إبرام ھذه الشروط والبنود بین الطرفین مؤسسة الكرت الأخضر والتاجر للاستفادة
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
                          <td class="container">
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
                          <td class="containerAr" dir="RTL">
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
                              <td class="container">
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
                              <td class="containerAr" dir="RTL">
                                <p>
                                  ملحوظة: قدمت مؤسسة الكرت الأخضر ضمان ملزم لھا إلى كل عملائھا حاملي
                                  التطبیق بأن جمیع العروض المدرجة في التطبیق عروض حقیقیة وتعھدت المؤسسة
                                  لعملائھا بتعویضھم في حال لم یحصلوا على العروض المدرجة في التطبیق وبناء علیه
                                  فإن التاجر أو المنشئ) الطرف الثاني (ملزم بموجب ھذا العقد بتعویض مؤسسة الكرت
                                  الأخضر (الطرف الأول) عن جمیع خسائرھا التي تعوض بھا المؤسسة عملائھا الذین لم
                                  یحصلوا على العروض المدرجة في ھذا العقد.
                  
                                </p>
                              </td>
                            </tr>
                            <tr>
                              <td class="container">
                                <p>
                                  Prosecution of the Green Card Foundation<br/>
                                  The name : Khaled salem lasloom<br/>
                                  Job title : CEO<br/>
                                  Date: {{ $contract->create_date }}<br/>
                                  Signature: <img src="https://test01.greencard-sa.com/public/uploads/all/signiature_gc.png" width="100" />
                                </p>
                              </td>
                              <td class="containerAr" dir="RTL">
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
                              <td class="container">
                                <p>
                                  Prosecuting the merchant<br/>
                                  The name : {{ Auth::user()->name }}<br/>
                                  Job title : {{ $contract->job_name }}<br/>
                                  Date: {{ $contract->create_date }}<br/>
                                  Signature<br/>
                                </p>
                              </td>
                              <td class="containerAr" dir="RTL">
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
          

`;

  printJS({
    type: "raw-html",
    printable: rawHTML
  });
}

document.getElementById("print-button").addEventListener("click", printRawHTML);
