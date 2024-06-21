@extends('frontend.layouts.app')

@section('content')


<!-- <script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId={{$checkoutId}}"></script> -->
<script src="https://oppwa.com/v1/paymentWidgets.js?checkoutId={{$checkoutId}}"></script>
<style>
  body {background-color:#f6f6f5;}
  /*.cnpBillingCheckoutWrapper {position:relative;}
  .cnpBillingCheckoutHeader {width:100%;border-bottom: 1px solid #c0c0c0;margin-bottom:10px;}
  .cnpBillingCheckoutLeft {width:240px;margin-left: 5px;margin-bottom: 10px;border: 1px solid #c0c0c0;display:inline-block;vertical-align: top;padding:10px;}
  .cnpBillingCheckoutRight {width:50%;margin-left: 5px;border: 1px solid #c0c0c0;display:inline-block;vertical-align: top;padding:10px;}
  .cnpBillingCheckoutOrange {font-size:110%;color: rgb(255, 60, 22);font-weight:bold;}
  div.wpwl-wrapper, div.wpwl-label, div.wpwl-sup-wrapper { width: 100% }
  div.wpwl-group-expiry, div.wpwl-group-brand { width: 30%; float:left }
  div.wpwl-group-cvv { width: 68%; float:left; margin-left:2% }
  div.wpwl-group-cardHolder, div.wpwl-sup-wrapper-street1, div.wpwl-group-expiry { clear:both }
  div.wpwl-sup-wrapper-street1 { padding-top: 1px }
  div.wpwl-wrapper-brand { width: auto }
  div.wpwl-sup-wrapper-state, div.wpwl-sup-wrapper-city { width:32%;float:left;margin-right:2% }
  div.wpwl-sup-wrapper-postcode { width:32%;float:left }
  div.wpwl-sup-wrapper-country { width: 66% }
  div.wpwl-wrapper-brand, div.wpwl-label-brand, div.wpwl-brand { display: none;}
  div.wpwl-group-cardNumber { width:60%; float:left; font-size: 20px;  }
  div.wpwl-group-brand { width:35%; float:left; margin-top:28px }
  div.wpwl-brand-card  { width: 65px }
  div.wpwl-brand-custom  { margin: 0px 5px; background-image: url("https://eu-test.oppwa.com/v1/paymentWidgets/img/brand.png") }*/

</style>
<script> 
  var wpwlOptions = {style:"card"}
  // var wpwlOptions = {
  //   style: "plain",
  //   billingAddress: {
  //     country: "US",
  //     state: "NY",
  //     city: "New York",
  //     street1: "111 6th Avenu",
  //     street2: "",
  //     postcode: "12312"
  //   },
  //   forceCardHolderEqualsBillingName: true,
  //   showCVVHint: true,
  //   brandDetection: true,
  //   onReady: function(){ 
  //     $(".wpwl-group-cardNumber").after($(".wpwl-group-brand").detach());
  //     $(".wpwl-group-cvv").after( $(".wpwl-group-cardHolder").detach());
  //     var visa = $(".wpwl-brand:first").clone().removeAttr("class").attr("class", "wpwl-brand-card wpwl-brand-custom wpwl-brand-VISA")
  //     var master = $(visa).clone().removeClass("wpwl-brand-VISA").addClass("wpwl-brand-MASTER");
  //     $(".wpwl-brand:first").after( $(master)).after( $(visa));
  //   },
  //   onChangeBrand: function(e){
  //     $(".wpwl-brand-custom").css("opacity", "0.3");
  //     $(".wpwl-brand-" + e).css("opacity", "1"); 
  //   }
  // }

</script>
<div class="payment_form m-5">
  <div class="container">
    <form action="{{ url('/hyperpay/payment/visaresponse') }}" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>
  </div>
</div>

@endsection