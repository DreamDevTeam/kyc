<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Services\CustomerRegistration;
use App\Models\Customers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DigitalController extends BaseController
{
    public function stepOne()
    {
        if(!Session::get('session_id')){
            Session::put('session_id', Str::random(32));
        }

        return view('pages.digital.stepOne');
    }

    public function registration($hallId, $hash)
    {
        session_start();
        $response  = Http::get(env('PAYMENTIQ_URL').'/api/link/unhash/'.$hash)->json();

        $type = $response['type'];
        $like = $response['like'];
        $tgName = $response['tg_name'] ?? null;

        $customer = match (true) {
            $type === 'update' => Customers::query()->selectRaw('firstName,middleName,lastName,mobile,dob,gender,email,payId')
                ->where('payId',$response['pay_id'])
                ->first(),
            default => []
        };

        return view('pages.digital.registration', compact('customer', 'type','like','tgName'));
    }

    public function store($hallId, CustomerRequest $request)
    {
        try {
            $request['tid'] = self::getTid(12);

            $customer = match (true) {
                $request->type === 'update' => CustomerRegistration::updateCustomer(request: $request),
                default => CustomerRegistration::addCustomer(request: $request, ref: $hallId)
            };

            return redirect()->route('success.digital', ['payid' => $customer->payId]);

        }catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function getImg(string $type,string $tid)
    {
        return response(Storage::get("$type/$tid"));
    }
}
