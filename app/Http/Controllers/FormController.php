<?php

namespace App\Http\Controllers;

use App\Helpers\Tier;
use App\Http\Requests\CustomerRequest;
use App\Models\Customers;
use App\Models\TaggedCustomers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class FormController extends Controller
{
    public function index($token, $hallId)
    {
        return view('form', compact('token', 'hallId'));
    }

    public function add(CustomerRequest $request)
    {
        try {
//            if($request->key && DB::table('links')->where('hash', $request->key)->get()->isEmpty()){
//                throw new \Exception('The link was not found');
//            }
            $customer = self::addCustomer($request);

            $url = env("PAYMENTGURU_URL") . "/api/psp";

            $response = Http::post($url, [
                'type' => 'newCustomer',
                'payId' => $customer->payId,
            ]);


            if ($response->successful()) {

            } elseif ($response->found()) {
                Log::debug('Webhook customer creation failed ID ' . $customer->id);
            } else {
                Log::debug('Webhook customer creation failed ID ' . $customer->id . "\n" . var_dump($response));
            }

            if (strlen($request->tid) === 12) {
                return response()->json(['route' => route('success.digital',[trim(base64_encode($customer->payId),'=')])]);
            } else {
                return response()->json(['route' => route('success.index',[trim(base64_encode($customer->payId),'=')])]);
            }
        }catch (Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    function addCustomer(object $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $payId = str_replace(' ', '_', strtolower($request->firstname)) . '_' . $this->generateRandomString(7) . '@paymentiq.cc';

                $customer = Customers::create([
                    'payId' => $payId,
                    'ref' => $this->checkRef($request->ref),
                    'regulaTid' => $request->tid,
                    'email' => $request->email,
                    'firstName' => $request->firstname,
                    'middleName'=> $request->middlename,
                    'lastName'=> $request->lastname,
                    'mobile'=> $request->mobile,
                    'dob' => $request->dob,
                    'gender' => $request->gender,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postcode' => $request->postcode,
                    'address' => $request->address,
                    'mobilePrefix' => $request->mobile_prefix,
                    'hash' => $this->checkHash($request->ref),
                    'tg_name' => $this->unHash($request->ref)['tgName'],
                    'kyc' => (strlen($request->tid) === 24) ? TRUE : FALSE,
                    'register_date' => Carbon::now()
                ]);

                if($request->has('tg_name') && !is_null($request->tg_name)){
                    DB::update('update links set is_registered = 1 where tg_name = ?', [$customer->tg_name]);
                }

                if ($request->has('key')){
                    DB::update('update links set is_registered = 1 where hash = ?', [$request->key]);
                }


                Tier::change(kycStatus:$customer->kyc,cid: $customer->id);

                return $customer;

            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        });
    }

    function unHash(string $hash) : array
    {
        if ($this->checkHash($hash)) {
            $str = explode('BCC', $hash);
            return [
                'hallId' => $str[0],
                'inviting' => base64_decode($str[1]),
                'tgName' => base64_decode($str[2]),
                'realName' => base64_decode($str[3]),
            ];
        }
        else {
            return [
                'inviting' => null,
                'tgName' => null,
                'realName' => null,
            ];
        }

    }

    function checkRef(string $ref): int
    {
        $findHash = strpos($ref, 'BCC');
        if ($findHash) {
            $hallId = explode('BCC', $ref)[0];
        } else {
            $hallId = $ref;
        }
        return (int)$hallId;
    }

    function checkHash(string $ref) :mixed
    {
        $findHash = strpos($ref,'BCC');
        if ($findHash){
            $hash = $ref;
        }else{
            $hash = null;
        }
        return $hash;
    }

    function generateRandomString(int $n = 0)
    {
        $al = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k'
            , 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u',
            'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '2', '3', '4', '5', '6', '7', '8', '9'];

        $len = !$n ? random_int(7, 12) : $n; // Chose length randomly in 7 to 12

        $ddd = array_map(function ($a) use ($al) {
            $key = random_int(0, 60);
            return $al[$key];
        }, array_fill(0, $len, 0));
        return implode('', $ddd);
    }
}
