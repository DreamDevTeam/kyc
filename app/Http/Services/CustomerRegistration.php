<?php


namespace App\Http\Services;


use App\Helpers\Image;
use App\Helpers\Str;
use App\Helpers\Tier;
use App\Models\Customers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CustomerRegistration
{
    public static function addCustomer(object $request, string $ref): object
    {
        $isKyc = $request->like === 'analog' && $request->documentsError === '0';

        $customer = Customers::create([
            'payId' => Str::generatePayId(request: $request),
            'ref' => Str::checkRef(ref: $ref),
            'regulaTid' => $request->tid,
            'email' => $request->email,
            'firstName' => $request->firstname,
            'middleName' => $request->middlename,
            'lastName' => $request->lastname,
            'mobile' => $request->mobile,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'city' => $request->city,
            'state' => $request->state,
            'postcode' => $request->postcode,
            'address' => $request->address,
            'mobilePrefix' => $request->mobile_prefix,
            'hash' => $request->hash,
            'tg_name' => $request->tgName,
            'kyc' => $isKyc,
            'register_date' => Carbon::now(),
            'docs_update' => Carbon::now()
        ]);

        if ($request->has('photo')) {
            $documents = [
                'frontSide' => $request->photo[0],
                'backSide' => $request->photo[1],
                'selfie' => $request->photo[2],
            ];
            S3Class::addPhotos(path: "documents/{$customer->id}", documents: $documents);
        }

        if ($request->has('tgName') && !is_null($request->tgName)) {
            DB::update('update links set is_registered = 1 where tg_name = ?', [$customer->tg_name]);
        }

        if ($request->has('key')) {
            DB::update('update links set is_registered = 1 where hash = ?', [$request->key]);
        }

        Tier::change(kycStatus: $customer->kyc, cid: $customer->id);

        if ($request->like === 'analog') {
            self::forAnalog(customerId: $customer->id,customerKyc: $customer->kyc,request: $request);
        }

        DB::table('links')->where('hash', $request->hash)->update([
            'is_registered' => 1
        ]);


        Http::post(env("PAYMENTGURU_URL") . "/api/psp", [
            'type' => 'newCustomer', 'payId' => $customer->payId
        ]);


        if ($request->type === 'chat') {
            Http::post(env("CHAT_PAYMENTGURU_URL") . "/api/new-customer", [
                'customerId' => $customer->id,
            ]);
        }

        Session::forget('session_id');
        return $customer;
    }

    public static function updateCustomer(object $request): object
    {
        if ($request->like === 'digital' && !is_null($request->payId)) {
            $regulaTid = Customers::where('payId', $request->payId)->first()->regulaTid;
            if($regulaTid) {
                self::deleteOldImg(tid:$regulaTid);
            }
        }

        Customers::where('payId', $request->payId)->update([
            'regulaTid' => $request->tid,
            'firstName' => $request->firstname,
            'middleName' => $request->middlename,
            'lastName' => $request->lastname,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'city' => $request->city,
            'state' => $request->state,
            'postcode' => $request->postcode,
            'address' => $request->address,
            'kyc' => $request->documentsError != 1,
            'register_date' => Carbon::now(),
            'checked' => 0,
            'docs_update' => Carbon::now()
        ]);

        $customer = Customers::where('payId', $request->payId)->first();

        if ($request->has('photo')) {
            $documents = [
                'frontSide' => $request->photo[0],
                'backSide' => $request->photo[1],
                'selfie' => $request->photo[2],
            ];
            S3Class::addPhotos(path: "documents/{$customer->id}", documents: $documents);
        }

        if ($request->like === 'analog') {
            self::forAnalog(customerId: $customer->id,customerKyc: $customer->kyc,request: $request);
        }

        DB::table('links')->where('hash', $request->hash)->update([
            'is_registered' => 1
        ]);

        //If tge kyc needed, the level should not be lowered
        if(is_null($customer->kyc_needed_label)) {
            Tier::change(kycStatus: $customer->kyc, cid: $customer->id);
        }

        return $customer;
    }


    private static function forAnalog(int $customerId, string $customerKyc, object $request): void
    {
        $images = (new AnalogService())->getPhotos(payId:null, regulaTid:$request->tid,cid:$customerId);

        $documents = [
            'frontSide'         => Image::base64Decode(base64String:$images[1]),
            'backSide'          => Image::base64Decode(base64String:$images[2]),
            'selfie'            => Image::base64Decode(base64String:$images[0]),
        ];

        S3Class::addPhotos(path: "documents/{$customerId}", documents: $documents);

        $error = $request->documentsErrorText;
        if (!$request->expire_date || Carbon::parse($request->expire_date)->lte(now()->addMonths(3))) {
            $error .= "\nLess than 3 months left until the ID expires.";
        }

        DB::table('customers_regula')->insert([
            'tid'               => $request->tid,
            'firstName'         => $request->firstname,
            'lastName'          => $request->lastname,
            'dob'               => $request->dob,
            'documents_error'   => $error,
            'expire'            => $request->documentsError != 1 ? $request->expiry_date : null,
            'created_at'        => Carbon::now(),
        ]);
    }

    private static function deleteOldImg(string $tid): void
    {
        $dirs = [
            'IDCARD' => 'idcard',
            'IDBACK' => 'idback',
            'SELFIE' => 'selfie'
        ];

        foreach ($dirs as $dir) {
            $path = storage_path('app/' . $dir);
            $file = $path . '/' . $tid;
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
