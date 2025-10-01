@extends('layout')
@section('content')
    <div class="card-body d-flex flex-column" id="not_paid">
        <div class="pay-now my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <div class="h4 text-dark my-4"><strong>Personal data received</strong></div>
                </div>
                <div class="col-12 text-center">
                    <div class="text-dark my-2">Thank you for uploading your ID. Your documents have been sent to our support team for review. Once approved you will be able to deposit and play.<br><br></div>
                </div>
                <div class="col-12 text-center">
                    <div class="h5 text-dark my-3"><strong>Please copy the data below and send it to your admin chat.</strong></div>
                    <div class="d-flex payid-value my-2 gap-2 align-items-center" style='word-break: break-all;'>
                        <span id="copy21" style="width: 100%;">{{$fullname}}<br>{{$email}}</span>

                        <svg id="copy22" width="30px" height="30px" viewBox="0 0 24 24" fill="none" style="cursor: pointer">
                            <path d="M20.9983 10C20.9862 7.82497 20.8897 6.64706 20.1213 5.87868C19.2426 5 17.8284 5 15 5H12C9.17157 5 7.75736 5 6.87868 5.87868C6 6.75736 6 8.17157 6 11V16C6 18.8284 6 20.2426 6.87868 21.1213C7.75736 22 9.17157 22 12 22H15C17.8284 22 19.2426 22 20.1213 21.1213C21 20.2426 21 18.8284 21 16V15" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M3 10V16C3 17.6569 4.34315 19 6 19M18 5C18 3.34315 16.6569 2 15 2H11C7.22876 2 5.34315 2 4.17157 3.17157C3.51839 3.82475 3.22937 4.69989 3.10149 6" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay" id="success-overlay">
        <div class="row overlay-topjustify-content-center">
            <div class="col align-self-center px-5">
                <div class="svg-box mx-auto">
                    <svg class="circle">
                        <circle class="path" cx="75" cy="75" r="50" fill="none" stroke-width="3"
                                stroke-miterlimit="10"></circle>
                    </svg>
                    <svg class="checkmark">
                        <g transform="matrix(0.79961,8.65821e-32,8.39584e-32,0.79961,-489.57,-205.679)">
                            <path class="checkmark__check" fill="none"
                                  d="M616.306,283.025L634.087,300.805L673.361,261.53"></path>
                        </g>
                    </svg>
                </div>
                <h2 class="text-white fw-lighter text-center">Payment Successful</h2>
                <p class="lead text-white text-opacity-50 text-center">We have received your funds</p>
                <div class="payment-remaining-time my-4">
                    <div class="row g-3 justify-content-center">
                        <div class="col-12 text-center">
                            <strong>
                                <span class="fs-1 text-white fw-lighter" id="time"></span>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="is_paid" style="display:none;">
        <div class="card-body">
            <div class="payment-summary my-3">
                <div class="row g-1">
                    <div class="col-12 text-center">
                        <span class="text-dark">You have successfully used</span>
                    </div>
                    <div class="col-12 text-center">
                        <span class="text-primary fw-bold fs-3" id="head_amount"></span>
                    </div>
                    <div class="col-12 text-center">
                        <span class="text-dark">to buy a Voucher for</span>
                        <span class="text-dark" id="head_amount_to"></span>
                    </div>
                    <div class="col-12 text-center mt-4 mb-2">
                        <span>Voucher Reference: </span>
                        <span class="text-primary" id="shortid"></span>
                    </div>
                </div>
            </div>
            <hr>
            <div class="payment-confirmation my-4">
                <div class="row g-3 align-items-center">
                    <div class="col-12">
                        <h6 class="small text-uppercase text-gray-600 fw-bold">Payment Details</h6>
                    </div>
                    <div class="col-3 text-start">Purchased by</div>
                    <div class="col-9 text-end" id="name"></div>
                    <div class="col-6 text-start">Paid by</div>
                    <div class="col-6 text-end">
                        <img class="method-icon" src="{{asset('payid.png')}}">
                    </div>
                    <div class="col-6 text-start">Amount</div>
                    <div class="col-6 text-end">
                        <span id="from_amount"></span>
                    </div>
                    <div class="col-6 text-start">Bought</div>
                    <div class="col-6 text-end">
                        <span id="to_asset"></span>
                    </div>
                    <div class="col-6 text-start">Rate</div>
                    <div class="col-6 text-end">
                        <span id="rate"> </span>
                    </div>
                    <div class="col-6 text-start">Amount Received</div>
                    <div class="col-6 text-end">
                        <span id="to_amount"></span>
                    </div>
                    <div class="col-6 text-start">Date</div>
                    <div class="col-6 text-end">
                        <span id="time_deposit"></span>
                    </div>
                    <div class="col-6 text-start">Status</div>
                    <div class="col-6 text-end">
                        <i class="far fa-check-circle text-success"></i>
                        <span class="text-dark">
                                            <strong> Complete</strong>
                                        </span>
                    </div>
                    <div class="col-12 text-center">
                        <small class="form-text text-muted">
                            You will receive an email receipt to
                            <strong id="email2">{{$email}}</strong> on this purchase and any future
                            transaction
                        </small>
                    </div>
                </div>
            </div>
            <hr>
            <div class="pay-now my-4">
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="text-success">Your Link is now enabled for automatic purchases</p>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <div class="payid-value my-2" id="pay_id"><i
                            class="far fa-copy fs-6 text-primary" id="copy2" data-toggle="tooltip"
                            title="Link Copied!"></i>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <p class="text-dark" id="footer_detail">Sending any amount of in future will automatically
                        buy <strong></strong> at the current day rate when received</p>
                </div>
            </div>
        </div>
        <div class="card-footer border-0">
            <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit">Return to the site</button>
            </div>
        </div>
    </div>
    <script type="module">
        import { clearData } from '/js/IndexedDB.js';

        const item = document.querySelector("#copy21");
        const item2 = document.querySelector("#copy22");
        item.onclick = function () {document.execCommand("copy");}
        item2.onclick = function () {document.execCommand("copy");}


        item.addEventListener("copy", function (event) {
            event.preventDefault();
            if (event.clipboardData) {
                event.clipboardData.setData("text/plain", "{{$fullname}}\n{{$email}}");
                Swal.fire("Personal data has been copied!", "success", "success");
            }
        });

        item2.addEventListener("copy", function (event) {
            event.preventDefault();
            if (event.clipboardData) {
                event.clipboardData.setData("text/plain", "{{$fullname}}\n{{$email}}");
                Swal.fire("Personal data has been copied!", "success", "success");
            }
        });

        clearData().then(() => {
            console.log('clear')
        })
    </script>
@endsection
