@extends('layout')
@section('content')
    <div class='containerLoader' id='myloader'>
        <span class="loader"></span>
        <span class="textLoad">
            Data processing may take some time...
        </span>
    </div>


    <div class='dialog' id='dialog'>
        <div class='dialogBox rounded-10 shadow-sm'>
            <div class='header'>
                <span>Photo upload information</span>
                <svg xmlns="http://www.w3.org/2000/svg" id='btn-close-dialog' class="icon icon-tabler icon-tabler-x" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M18 6l-12 12" />
                <path d="M6 6l12 12" />
                </svg>
            </div>
            <div class='container'>
                <div class='boxImage'>
                    <img src="{{asset('info.png')}}" alt='info image'/>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body flex-column gap-3 pb-0" style="display: flex;">
        <!-- START DIALOG -->
        <div class='start-container' id='start'>
            <div class='info'>
                <div class='info-title'>
                    <h5>Confirm your identity</h5>
                    <div>
                        Our easy-to-follow verification process allows you to verify your identity safely and easily. Before clicking "Get Started," please select the type of documents you will be uploading.
                    </div>
                </div>
                <div class='info-action'>
                    <div class='title-action'>Select the type of ID</div>
                    <div class='pre-title-action'>Make sure you select the correct ID type so that the confirmation goes through correctly</div>
                    <div class='box-change'>
                        <button class='switch active' onclick="changeType(event, 'analog')">Analog ID</button>
                        <button class='switch' onclick="changeType(event, 'digital')">Digital ID</button>
                    </div>
                </div>
            </div>
            <button class="my-button primary" id="start-btn">
                Get Started
            </button>
        </div>
        <!-- FIRST STEP ANALOG-->
        <div class='step-one' id="step-one-analog" style="display: none;">
            <div class='first-info'>
                <h5>Upload your ID photo</h5>
                <div class='text'>
                    Please take a clear photo (or scan) of your <b>driver licence (both sides), passport or proof of age card.</b>
                </div>
                <div class='progress-container'>
                    <div class='progress'>
                        <div class='progress-wrapper'>
                            <div class='progress-bar'></div>
                        </div>
                    </div>
                    <div class='label'>Step 1</div>
                </div>
            </div>
            <div class='second-info'>
                <div class='error-message' id='error-message-one' >Uploaded photos do not match!</div>
                <div class='label'>Front side of the ID Card</div>
                <div class='box-image'>
                    <img class='image' id="idcardphoto" src="{{asset('card-front.jpg')}}" alt="idcard"/>
                </div>
                <div class='box-upload'>
                    <input type="file" id="idcard" onchange="openPhoto(event)" accept="image/*" style="display: none"/>
                    <button class='button-input' onclick="selectPhoto('idcard')">
                        click to upload
                    </button>
                </div>
                <div class='label'>Back side of the ID Card</div>
                <div class='box-image' style='display: none'>
                    <img class='image' id="idcardbackphoto" src="{{asset('card-back.jpg')}}"  alt="idcardback"/>
                </div>
                <div  class='box-upload'>
                    <input type="file" id="idcardback" onchange="openPhoto(event)" accept="image/*" style="display: none"/>
                    <button class='button-input' onclick="selectPhoto('idcardback')">
                        click to upload
                    </button>
                </div>
                <ul class='list'>
                    <li>Use a color image not a black and white</li>
                    <li>Show all four corners of document</li>
                    <li>Make sure your face shows</li>
                    <li>Don`t use photocopies or screenshots</li>
                </ul>
                <div class='rules'>
                    <p><span>ACCEPTED IDENTIFICATION</span></p>
                    <p>We`ll accept only valid documents (passports, ids, etc.).</p>
                    <p>Foreign passports must be current and, if not written in English, will need to be translated by an accredited translator.</p>
                    <p>We’ll accept an original document or certified copy of the translation.</p>
                    <p>A Proof of Age card must be issued by an Australian state or territory with your name, photo and signature.</p>
                </div>
            </div>
            <button class="my-button primary" id="step-one-btn-analog" onclick="processStepOnePhotos()" disabled>
                Submit
            </button>
        </div>
        <!-- FIRST STEP DIGITAL-->
        <div class='step-one' id="step-one-digital" style="display: none;">
            <div class='first-info'>
                <h5>Upload your ID screenshot</h5>
                <div class='text'>
                Please take a screenshot of your ID screen. We need this to verify your identity
                </div>
                <div class='progress-container'>
                    <div class='progress'>
                        <div class='progress-wrapper'>
                            <div class='progress-bar'></div>
                        </div>
                    </div>
                    <div class='label'>Step 1</div>
                </div>
            </div>
            <div class='second-info'>
                <div class='error-message' id='error-message-one' >Uploaded photos do not match!</div>
                <div class='label'>ID screenshot</div>
                <div class='box-image'>
                    <img class='image' id="digitalphoto" src="{{asset('digital.png')}}" alt="digital"/>
                </div>
                <div class='box-upload'>
                    <input type="file" id="digital" onchange="openPhoto(event)" accept="image/*" style="display: none"/>
                    <button class='button-input' onclick="selectPhoto('digital')">
                        click to upload
                    </button>
                </div>
                <ul class='list'>
                    <li>Use a color image not a black and white</li>
                    <li>Show all four corners of document</li>
                    <li>Make sure your face shows</li>
                    <li>Don`t use photocopies or screenshots</li>
                </ul>
            </div>
            <button class="my-button primary" id="step-one-btn-digital" onclick="saveIdScreenshot()" disabled>
                Submit
            </button>
        </div>
        <!-- SECOND STEP -->
        <div class='step-two' id="step-two" style="display: none;">
        <div class='first-info'>
                <h5>Upload your photo of you holding your ID</h5>
                <div class='text'>
                    <p><b>Take a well-lit, clear photo of you holding your chosen ID.</b></p>
                    <p>Please make sure we can clearly see your face and  easily read the details on your ID. </p>
                    <p><b>Handy tip:</b> Despite being called ‘selfie ID’ it’s best (and easier) to have someone else to take this photo of you</p>
                </div>
                <div class='progress-container'>
                    <div class='progress'>
                        <div class='progress-wrapper'>
                            <div class='progress-bar'></div>
                        </div>
                    </div>
                    <div class='label'>Step 2</div>
                </div>
            </div>
            <div class='second-info'>
                <div class='error-message' id='error-message-two' >Uploaded photos do not match!</div>
                <div class='label'>Selfie</div>
                <div class='box-image'>
                    <img class='image' id="selfiephoto" src="{{asset('selfie.jpg')}}" alt="idcard"/>
                </div>
                <div  class='box-upload'>
                    <input type="file" id="selfie" onchange="openPhoto(event)" accept="image/*" style="display: none"/>
                    <button class='button-input' onclick="selectPhoto('selfie')">
                        click to upload
                    </button>
                </div>
                <ul class='list'>
                    <li>Keep a neutral expression</li>
                    <li>Remove dark glasses or tinted lenses</li>
                    <li>Ensure both eyes are open</li>
                    <li>Take a well-lighted photo</li>
                    <li>No filters needed</li>
                    <li>No photos of photos</li>
                </ul>
                <div class='rules'>
                    <p><b>A photo will be rejected if your face or information on your ID are not clearly visible.</b></p>
                    <p>Please avoid photos where your face or ID are blurred, out of focus, obscured or cropped off.</p>
                </div>
            </div>
            <button class="my-button primary" id="step-two-btn" onclick="processStepTwoPhotos()" disabled>
                Submit
            </button>
        </div>
        <!-- FORM DIALOG -->
        <div class='form' id="form" style="display: none;">
        <form id="profile-kyc-frm" enctype="multipart/form-data">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                class="mg-t-15 mg-b-15 bi bi-person-fill" viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
            </svg>
            <p class="mg-b-20" style="font-size: 0.765rem;">
                Please provide your information.
                <br>
            </p>

            <div class='edit-box' id='edit-box'>
                <span class='edit-text'>Click to correct name or date of birth</span>
                <button class='btn-edit' id="edit-input" onclick="cancelDisable(event)">Edit</button>
            </div>

            <input id="tid" type="hidden" name="tid" value="">
            <input id="merchant-hidden" type="hidden" name="merchant" value="{{$token}}">
            <input id="ref" type="hidden" name="ref" value="{{$hallId}}">
            <input id="merchant-requireId" type="hidden" name="requireId">
            <div class="form-group input-material">
                <input class="form-control datarequired" id="firstnamedata" type="text" placeholder="Mike" disabled
                       style="font-size:0.765rem;" required="required">
                <label class="form-label label-active" id="firstnamelabel" for="firstnamedata"
                       style="color: red !important;">First Name*</label>
                <span class="d-flex error-block" id="firstname-error" style="font-size: smaller;"></span>
            </div>
            <div class="form-group input-material mg-t-20">
                <input class="form-control" id="middlenamedata" type="text" placeholder="Fitzgerald" disabled
                       style="font-size:0.765rem;">
                <label class="form-label label-active" id="middlenamelabel" for="middlenamedata">Middle
                    Name</label>
                <span class="d-flex error-block" id="middlename-error" style="font-size: smaller;"></span>
            </div>
            <div class="form-group input-material mg-t-20">
                <input class="form-control datarequired" id="lastnamedata" type="text" disabled
                       placeholder="Mustermann" style="font-size:0.765rem;" required="required">
                <label class="form-label label-active" id="lastnamelabel" for="lastnamedata"
                       style="color: red !important;">Last Name*</label>
                <span class="d-flex error-block" id="lastname-error" style="font-size: smaller;"></span>
            </div>
            <div class="form-row">
                <div class="form-group input-material mg-t-20 col-md-6">
                    <div class="iti iti--allow-dropdown iti--separate-dial-code">
                        <div class="iti__flag-container" id="login" onsubmit="process(event)"></div>
                        <input class="form-control datarequired" id="mobiledata" type="tel" name="mobile"
                               required="required" autocomplete="off">
                    </div>

                    <label class="form-label label-active" id="mobilelabel" for="mobiledata"
                           style="color: red !important;">Mobile*</label>
                </div>

                <div class="form-group input-material mg-t-20 col-md-6">
                    <input class="form-control datarequired" id="emaildata" type="email"
                           placeholder="john.doe@gmail.com" style="font-size:0.765rem;" required="required">
                    <label class="form-label label-active" id="emaillabel" for="emaildata"
                           style="color: red !important;">Email*</label>
                    <span class="d-flex error-block" id="emaildata-error"
                          style="font-size: smaller;">Email is invalid</span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group input-material mg-t-20 col-md-6"
                     style="font-size:0.765rem !important;">
                    <select class="form-control datarequired" id="genderdata" required="required">
                        <option value="" selected="selected" style="color: lightgrey !important;">-- Choose
                            gender --
                        </option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <label class="form-label label-active" id="genderlabel" for="genderdata"
                           style="color: red !important;">Gender*</label>
                </div>
                <div class="form-group input-material mg-t-20 col-md-6"
                     style="font-size:0.765rem !important;">
                    <input class="form-control datarequired" id="dobdata" type="date" disabled
                           style="font-size:0.765rem;" min="1920-01-01" required="required">
                    <label class="form-label label-active" id="doblabel" for="dobdata"
                           style="color: red !important;">Date of Birth*</label>
                </div>
            </div>
            <div class="form-row">
                <!-- {{-- <div class="form-group input-material mg-t-20 col-md-6">
                    <input class="form-control datarequired" id="streetdata" type="text"
                           placeholder="12 Test Lane" style="font-size:0.765rem;" required="required">
                    <label class="form-label label-active" id="streetlabel" for="streetdata"
                           style="color: red !important;">Street*</label>
                </div> --}} -->
                <div class="form-group input-material mg-t-20 col-md-6">
                    <input class="form-control datarequired" id="addressdata" type="text"
                           placeholder="Home address" style="font-size:0.765rem;" required="required">
                    <label class="form-label label-active" id="addresslabel" for="address"
                           style="color: red !important;">Address*</label>
                </div>
                <div class="form-group input-material mg-t-20 col-md-6">
                    <input class="form-control datarequired" id="citydata" type="text"
                           placeholder="Testville" style="font-size:0.765rem;" required="required">
                    <label class="form-label label-active" id="citylabel" for="citydata"
                           style="color: red !important;">City*</label>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group input-material mg-t-20 col-md-6">
                    <select class="form-control datarequired" id="statedata" required="required">
                        <option value="" selected="selected" style="color: lightgrey !important;">-- Choose
                            state --
                        </option>
                        <option value="NSW">New South Wales</option>
                        <option value="VIC">Victoria</option>
                        <option value="QLD">Queensland</option>
                        <option value="TAS">Tasmania</option>
                        <option value="SA">South Australia</option>
                        <option value="WA">Western Australia</option>
                        <option value="NT">Northern Territory</option>
                        <option value="ACT">Australian Capital Territory</option>
                    </select>
                    <label class="form-label label-active" id="statelabel" for="statedata"
                           style="color: red !important;">State*</label>
                </div>
                <div class="form-group input-material mg-t-20 col-md-6">
                    <input class="form-control datarequired" id="postcodedata" type="number" min="0001"
                           max="9999999999" placeholder="1234" style="font-size:0.765rem;" required="required">
                    <label class="form-label label-active" id="postcodelabel" for="postcodedata"
                           style="color: red !important;">Post Code*</label>
                </div>
            </div>
            <div class="mg-t-20" style="font-size:0.765rem;">
                <button class="my-button primary" id="verify-btn" type="submit">
                    Verify
                </button>
                <div class="loader-wr" id="loader" style="display: none;">
                    <svg class="loader-sm">
                        <path fill="#122a49"
                              d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                            <animateTransform attributeName="transform" attributeType="XML" type="rotate"
                                              dur="1s" from="0 50 50" to="360 50 50"
                                              repeatCount="indefinite"></animateTransform>
                        </path>
                    </svg>
                </div>
                <p class="mg-b-20" id="loader-text" style="font-size: 0.765rem; display: none;">We are
                    processing your data...<br>This could take up to 1 minute, please do not refresh this
                    page.</p>
            </div>
        </form>
        </div>
    </div>
    </div>
@endsection
