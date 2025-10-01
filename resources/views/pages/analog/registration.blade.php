@extends('layout')
@section('content')

<form method="POST" action="{{ route('analog.registration.store', ['hallId' => request('hallId'),'hash' => request('hash')], false) }}"
      enctype="multipart/form-data"
      onsubmit="disableButton(this)">
    @csrf
    <p class="mg-b-20" style="font-size: 0.765rem;">
        Please provide your information.
        <br>
    </p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

{{--    @dd($_SESSION['register'])--}}

    <div class="form-group input-material">
        <input type="hidden" name="documentsError" value="{{$_SESSION['register']['documentsError'] ? 1 : 0}}">
        <input type="hidden" name="documentsErrorText"
               value="{{ $_SESSION['register']['documentsErrorText'] ?? ''}}">
        <input type="hidden" name="hash" value="{{request('hash')}}">
        <input type="hidden" name="tid" value="{{ $_SESSION['register']['tid'] ?? '' }}">
        <input type="hidden" name="payId" value="{{ $_SESSION['register']['pay_id'] ?? '' }}">
        <input type="hidden" name="type" value="{{ $_SESSION['register']['type'] ?? '' }}">
        <input type="hidden" name="like" value="{{ $_SESSION['register']['like'] ?? '' }}">
        <input type="hidden" name="tgName" value="{{ $_SESSION['register']['tg_name'] ?? '' }}">
        <input type="hidden" name="expiry_date" value="{{ $_SESSION['register']['expiry_date'] ?? '' }}">
        <input name="firstname" value="{{ old('firstname') ?? $_SESSION['register']['firstname'] ?? '' }}" class="form-control datarequired" type="text" placeholder="Mike"
               style="font-size:0.765rem;" required="required">
        <label class="form-label label-active" for="firstnamedata"
               style="color: red !important;">First Name*</label>
        <span class="d-flex error-block" id="firstname-error" style="font-size: smaller;"></span>
    </div>
    <div class="form-group input-material mg-t-20">
        <input name="middlename" value="{{ old('middlename') ?? $_SESSION['register']['middlename'] ?? ''}}" class="form-control" type="text" placeholder="Fitzgerald"
               style="font-size:0.765rem;">
        <label class="form-label label-active" id="middlenamelabel" for="middlenamedata">Middle Name</label>
        <span class="d-flex error-block" id="middlename-error" style="font-size: smaller;"></span>
    </div>
    <div class="form-group input-material mg-t-20">
        <input name="lastname" value="{{ old('lastname') ?? $_SESSION['register']['lastname'] ?? ''}}" class="form-control datarequired" type="text"
               placeholder="Mustermann" style="font-size:0.765rem;" required="required">
        <label class="form-label label-active" id="lastnamelabel" for="lastnamedata"
               style="color: red !important;">Last Name*</label>
        <span class="d-flex error-block" id="lastname-error" style="font-size: smaller;"></span>
    </div>
    <div class="form-row">
        <div class="form-group input-material mg-t-20 col-md-6">
                <input name="mobile" value="{{ old('mobile') ?? $_SESSION['register']['mobile'] ?? '' }}" placeholder="+61 7 3091 1305"
                       class="form-control datarequired" type="tel" required>
            <label class="form-label label-active" id="mobilelabel" for="mobiledata"
                   style="color: red !important;">Mobile*</label>
        </div>

        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="email" value="{{ old('email') ?? $_SESSION['register']['email'] ?? ''}}"
                   class="form-control datarequired" type="email"
                   placeholder="john.doe@gmail.com" style="font-size:0.765rem;"
                   @if($_SESSION['register']['type'] === 'update') readonly @endif>
            <label class="form-label label-active text-red">
                <x-icon.lock :type="$_SESSION['register']['type']">Email*</x-icon.lock>
            </label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group input-material mg-t-20 col-md-6" style="font-size:0.765rem !important;">
            <select name="gender" class="form-control datarequired" required="required">
                <option value="{{ old('gender') }}" selected="selected" style="color: lightgrey !important;">-- Choose gender --</option>
                <option value="Male" {{ old('gender') ?? isset($_SESSION['register']['gender']) && $_SESSION['register']['gender'] == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') ?? isset($_SESSION['register']['gender']) && $_SESSION['register']['gender'] == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
            <label class="form-label label-active" id="genderlabel" for="genderdata"
                   style="color: red !important;">Gender*</label>
        </div>
        <div class="form-group input-material mg-t-20 col-md-6" style="font-size:0.765rem !important;">
            <input name="dob"
                   value="{{ old('dob') ?? $_SESSION['register']['dob'] ?? ''}}"
                   class="form-control datarequired" type="date"
                   style="font-size:0.765rem;" min="1920-01-01" required="required"
{{--                   @if($_SESSION['register']['type'] === 'update') readonly @endif>--}}
                   @if($_SESSION['register']['type'] === 'update' && !is_null($_SESSION['register']['dob'])) readonly @endif>
                @if(isset($_SESSION['register']['dob']) && !is_null($_SESSION['register']['dob']))
                    <label class="form-label label-active text-red">
                        <x-icon.lock :type="$_SESSION['register']['type']">Date of Birth*</x-icon.lock>
                    </label>
                @endif
        </div>
    </div>
    <div class="form-row">
        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="address"
                   value="{{ old('address') ?? $_SESSION['register']['address'] ?? ''}}"
                   class="form-control datarequired" type="text"
                   placeholder="Home address" style="font-size:0.765rem;" required="required">
            <label class="form-label label-active" id="addresslabel" for="address" style="color: red !important;">Address*</label>
        </div>
        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="city"
                   value="{{ old('city') ?? $_SESSION['register']['city'] ?? ''}}"
                   class="form-control datarequired" type="text"
                   placeholder="Testville" style="font-size:0.765rem;" required="required">
            <label class="form-label label-active" id="citylabel" for="citydata" style="color: red !important;">City*</label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group input-material mg-t-20 col-md-6">
            <select name="state" class="form-control datarequired" required="required">
                <option value="" style="color: lightgrey !important;">-- Choose state --</option>
                <option value="NSW" {{ old('state') ?? isset($_SESSION['register']['state']) && $_SESSION['register']['state'] == 'NSW' ? 'selected' : '' }}>New South Wales</option>
                <option value="VIC" {{ old('state') ?? isset($_SESSION['register']['state']) && $_SESSION['register']['state'] == 'VIC' ? 'selected' : '' }}>Victoria</option>
                <option value="QLD" {{ old('state') ?? isset($_SESSION['register']['state']) && $_SESSION['register']['state'] == 'QLD' ? 'selected' : '' }}>Queensland</option>
                <option value="TAS" {{ old('state') ?? isset($_SESSION['register']['state']) && $_SESSION['register']['state'] == 'TAS' ? 'selected' : '' }}>Tasmania</option>
                <option value="SA" {{ old('state') ?? isset($_SESSION['register']['state']) && $_SESSION['register']['state'] == 'SA' ? 'selected' : '' }}>South Australia</option>
                <option value="WA" {{ old('state') ?? isset($_SESSION['register']['state']) && $_SESSION['register']['state'] == 'WA' ? 'selected' : '' }}>Western Australia</option>
                <option value="NT" {{ old('state') ?? isset($_SESSION['register']['state']) && $_SESSION['register']['state'] == 'NT' ? 'selected' : '' }}>Northern Territory</option>
                <option value="ACT" {{ old('state') ?? isset($_SESSION['register']['state']) && $_SESSION['register']['state'] == 'ACT' ? 'selected' : '' }}>Australian Capital Territory</option>
            </select>
            <label class="form-label label-active" id="statelabel" for="statedata"
                   style="color: red !important;">State*</label>
        </div>
        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="postcode" value="{{ old('postcode') ?? $_SESSION['register']['postcode'] ?? ''}}" class="form-control datarequired" type="number" min="0001"
                   max="9999999999" placeholder="1234" style="font-size:0.765rem;" required="required">
            <label class="form-label label-active" id="postcodelabel" for="postcodedata"
                   style="color: red !important;">Post Code*</label>
        </div>
    </div>
    <div class="flex items-center justify-content-end w-[100%] mt-[20px]">
        <button class="my-button primary" type="submit">Verify</button>
        <script>
            function disableButton(form) {
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerText = 'Sending...';
            }
        </script>
    </div>
</form>

@endsection
