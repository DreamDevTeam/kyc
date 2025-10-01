@extends('layout')
@section('content')

<form method="POST" action="{{ route('digital.registration.store', ['hallId' => request('hallId'),'hash' => request('hash')], false) }}" enctype="multipart/form-data">
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
    <input type="hidden" name="hash" value="{{request('hash')}}">
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="like" value="{{ $like }}">
    <input type="hidden" name="tgName" value="{{ $tgName }}">
{{--    <input type="hidden" name="realName" value="{{ $realName }}">--}}
    <input type="hidden" name="payId" value="{{ $customer->payId ?? null}}">
    <div class="form-group input-material">
        <input name="firstname" value="{{ old('firstname') ?? $customer->firstName ?? '' }}"
               class="form-control @if($type === 'update') text-darkgray @endif" type="text" placeholder="Mike"
                required @if($type === 'update') readonly @endif>
        <label class="form-label label-active text-red">
           <x-icon.lock :type="$type">First Name*</x-icon.lock>
        </label>
    </div>
    <div class="form-group input-material mg-t-20">
        <input name="middlename"  value="{{ old('middlename') ?? $customer->middleName ?? '' }}" class="form-control"
               type="text" placeholder="Fitzgerald">
        <label class="form-label label-active text-red">Middle Name</label>

    </div>
    <div class="form-group input-material mg-t-20">
        <input name="lastname" value="{{ old('lastname') ?? $customer->lastName ?? '' }}" class="form-control @if($type === 'update') text-darkgray @endif" type="text"
               placeholder="Mustermann" required @if($type === 'update') readonly @endif>
        <label class="form-label label-active text-red">
            <x-icon.lock :type="$type">Last Name*</x-icon.lock>
        </label>
    </div>
    <div class="form-row">
        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="mobile" value="{{ old('mobile') ?? $customer->mobile ?? '' }}" class="form-control @if($type === 'update') text-darkgray @endif" type="tel"
                   required @if($type === 'update') readonly @endif placeholder="+61 7 3091 1305">
            <label class="form-label label-active text-red">
                <x-icon.lock :type="$type">Mobile*</x-icon.lock>
            </label>
        </div>

        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="email" value="{{ old('email') ?? $customer->email ?? '' }}" class="form-control @if($type === 'update') text-darkgray @endif" type="email"
                   placeholder="john.doe@gmail.com" >
            <label class="form-label label-active text-red">
                <x-icon.lock :type="$type">Email*</x-icon.lock>
            </label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group input-material mg-t-20 col-md-6">
            <select name="gender" class="form-control datarequired" required>
                <option value="{{ old('gender') ?? $customer->gender ?? '' }}" class="text-lightgrey" selected>
                    -- Choose gender --
                </option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
            <label class="form-label label-active text-red">Gender*</label>
        </div>

        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="dob" value="{{ old('dob') ?? $customer->dob ?? ''}}" class="form-control @if($type === 'update') text-darkgray @endif" type="date" min="1920-01-01" required @if($type === 'update') readonly @endif>
            <label class="form-label label-active text-red">
                <x-icon.lock :type="$type">Date of Birth*</x-icon.lock>
            </label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="address" value="{{ old('address') }}" class="form-control" type="text"
                   placeholder="Home address" required>
            <label class="form-label label-active text-red">Address* </label>
        </div>
        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="city" value="{{ old('city') }}" class="form-control" type="text"
                   placeholder="Testville" required>
            <label class="form-label label-active text-red">City*</label>
        </div>

    </div>
    <div class="form-row">
        <div class="form-group input-material mg-t-20 col-md-6">
            <select name="state" class="form-control" required>
                <option class="text-lightgrey" value="">-- Choose state --</option>
                <option value="NSW" {{ old('state') == 'NSW' ? 'selected' : '' }}>New South Wales</option>
                <option value="VIC" {{ old('state') == 'VIC' ? 'selected' : '' }}>Victoria</option>
                <option value="QLD" {{ old('state') == 'QLD' ? 'selected' : '' }}>Queensland</option>
                <option value="TAS" {{ old('state') == 'TAS' ? 'selected' : '' }}>Tasmania</option>
                <option value="SA" {{ old('state') == 'SA' ? 'selected' : '' }}>South Australia</option>
                <option value="WA" {{ old('state') == 'WA' ? 'selected' : '' }}>Western Australia</option>
                <option value="NT" {{ old('state') == 'NT' ? 'selected' : '' }}>Northern Territory</option>
                <option value="ACT" {{ old('state') == 'ACT' ? 'selected' : '' }}>Australian Capital Territory</option>
            </select>
            <label class="form-label label-active text-red">State*</label>
        </div>
        <div class="form-group input-material mg-t-20 col-md-6">
            <input name="postcode" value="{{ old('postcode') }}" class="form-control" type="number" min="0001"
                   max="9999999999" placeholder="1234" style="font-size:0.765rem;" required>
            <label class="form-label label-active text-red">Post Code*</label>
        </div>
    </div>
    <div class="flex items-center justify-between w-[100%] mt-[20px]">
{{--        <a href="{{route('digital.stepOne', [--}}
{{--                                'hallId' => request('hallId'),--}}
{{--                                'hash' => request('hash')], false)}}">--}}
{{--            <x-svg.left></x-svg.left>--}}
{{--        </a>--}}

        <button class="my-button primary" type="submit">Verify</button>
    </div>
    <input name="photo[]" type="file" id="fileInput" multiple hidden>

    <script type="module">
        import { getData, openDatabase } from '/js/IndexedDB.js';

        window.sessionId = "{{ session()->getId() }}";

        openDatabase().then(() => {
            let stepOne;
            let stepTwo;
            let stepThree;
            Promise.all([
                getData('st_1' + window.sessionId),
                getData('st_2' + window.sessionId),
                getData('st_3' + window.sessionId)])
                .then((values) => {
                    const fileInput = document.getElementById("fileInput");
                    const dataTransfer = new DataTransfer();

                    if(values[0]){
                        stepOne = JSON.parse(values[0].data)
                        console.log(stepOne)
                        const file1 = new File([dataURItoBlob(stepOne?.preview)], "step_1", {type: "text/plain"});
                        dataTransfer.items.add(file1);
                    }

                    if(values[1]){
                        stepTwo = JSON.parse(values[1].data)
                        const file2 = new File([dataURItoBlob(stepTwo?.preview)], "step_2", {type: "text/plain"});
                        dataTransfer.items.add(file2);
                    }

                    if(values[2]){
                        stepThree = JSON.parse(values[2].data)
                        const file3 = new File([dataURItoBlob(stepThree?.preview)], "step_3", {type: "text/plain"});
                        dataTransfer.items.add(file3);
                    }
                    fileInput.files = dataTransfer.files;
            });

        }).catch((error) => {
            console.error('Error initializing the database:', error);
        })

        function dataURItoBlob(dataURI) {
            const byteString = atob(dataURI.split(',')[1]);
            const ab = new ArrayBuffer(byteString.length);
            const ua = new Uint8Array(ab);
            for (let i = 0; i < byteString.length; i++) {
                ua[i] = byteString.charCodeAt(i);
            }
            return new Blob([ab], { type: 'image/png' });
        }

    </script>
</form>

@endsection
