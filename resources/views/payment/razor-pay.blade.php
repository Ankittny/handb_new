@extends('payment.layouts.master')

@section('content')
    <div class="over_image mt-5">
        <h1 class="text-center text-uppercase cpm">{{ "Please do not refresh this page..." }}</h1>
    </div>

    <div class=content>
  <div class="wrapper-1">
    <div class="wrapper-2">
      <h1>PAY HERE !</h1>
      <p>Thanks for Purchasing to our Site.  </p>
      <p>you should receive a confirmation email soon.  </p>
      @php
        $numberOnly = preg_replace('/\D/', '', webCurrencyConverter($data->payment_amount));
        $numberOnly1 = substr($numberOnly, 0, -2);
    @endphp
    <form action="{!!route('razor-pay.payment',['payment_id'=>$data->id])!!}" id="form" method="POST">
    @csrf
        <script src="https://checkout.razorpay.com/v1/checkout.js"
                data-key="{{ config()->get('razor_config.api_key') }}"
                data-amount="{{round($numberOnly1, 2)*100}}"
                data-buttontext="Pay {{ round($numberOnly1, 2) . ' ' . '₹' }}"
                {{-- data-buttontext="Pay {{ round($numberOnly1, 2) . ' ' . $data->currency_code }}" --}}
                data-name={{ $business_name }}
                data-description="{{$numberOnly1}}"
                data-image={{ $business_logo }}
                data-prefill.name="{{$payer->name ?? ''}}"
                data-prefill.email="{{$payer->email ?? ''}}"
                data-theme.color="#ff7529">
        </script>
        <button class="btn btn-block" id="pay-button" type="submit" style="display:none"></button>
    </form>
      </button>
    </div>
    
</div>




    <style>
        .cpm{
            text-shadow:
                0px 3px 4px ;
                font-size: 35px;
                color: #1a0a00;
            }
            .razorpay-payment-button{
                font-weight: 700;
                background: #1a0a00;
                color: #fff;
                border: none;
                padding: 15px 35px;
                border-radius:30px;
            }

            form{
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 40px 0px;
            }
            body{
            background: #ffffff;
            background: linear-gradient(to bottom, #ffffff 0%,#e1e8ed 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e1e8ed',GradientType=0 );
            height: 100%;
            margin: 0;
            background-repeat: no-repeat;
            background-attachment: fixed;
  
}

.wrapper-1{
  width:100%;
  height:100vh;
  display: flex;
flex-direction: column;
}
.wrapper-2{
  padding :30px;
  text-align:center;
}
h1{
   
  font-size:4em;
  letter-spacing:3px;
  color:#5892FF ;
  margin:0;
  margin-bottom:20px;
  color: #1a0a00;
}
.wrapper-2 p{
  margin:0;
  font-size:1.3em;
  color:#aaa;
  font-family: 'Open Sans', sans-serif;
  letter-spacing:1px;
}
.go-home{
  color:#fff;
  background:#5892FF;
  border:none;
  padding:10px 50px;
  margin:30px 0;
  border-radius:30px;
  text-transform:capitalize;
  box-shadow: 0 10px 16px 1px rgba(174, 199, 251, 1);
}
.footer-like{
  margin-top: auto; 
  background:#D7E6FE;
  padding:6px;
  text-align:center;
}
.footer-like p{
  margin:0;
  padding:4px;
  color:#5892FF;
  font-family: 'Open Sans', sans-serif;
  letter-spacing:1px;
}
.footer-like p a{
  text-decoration:none;
  color:#5892FF;
  font-weight:600;
}

@media (min-width:360px){
  h1{
    font-size:4.5em;
  }
  .go-home{
    margin-bottom:20px;
  }
}

@media (min-width:600px){
  .content{
  max-width:1000px;
  margin:0 auto;
}
  .wrapper-1{
  height: initial;
  max-width:620px;
  margin:0 auto;
  margin-top:50px;
  box-shadow:4px 8px 40px 8px rgba(26, 10, 0,  0.2);
}
  
}
        
        
</style>
    <script type="text/javascript">
        "use strict";
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("pay-button").click();
        });
    </script>
@endsection