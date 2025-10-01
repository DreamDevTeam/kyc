<?php

namespace App\Http\Controllers;

use App\Http\Requests\Analog\StepOneStoreRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Services\AnalogService;
use App\Http\Services\CustomerRegistration;
use Illuminate\Http\Request;

class AnalogController extends BaseController
{
    public function stepOne()
    {
        return view('pages.analog.stepOne');
    }

    public function stepOneStore(StepOneStoreRequest $request, AnalogService $analogService)
    {
        try {
            session_start();
            $_SESSION['register'] = $analogService->process($request);
        }catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function registration()
    {
        session_start();
        if(!$_SESSION){
            return response()->view('errors.404', [], 404);
        }
        return view('pages.analog.registration');
    }

    public function store($hallId, CustomerRequest $request)
    {
        $customer = match (true) {
            $request->type === 'update' => CustomerRegistration::updateCustomer(request: $request),
            default => CustomerRegistration::addCustomer(request: $request, ref: $hallId)
        };

        return redirect()->route('success.index', ['payid' => $customer->payId]);
    }

    public function getPhotos(Request $request, AnalogService $analogService)
    {
        try {
            return response()->json($analogService->getPhotos(regulaTid: $request->regulaTid, cid: $request->cid));
        }catch (\Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    public function getText(Request $request, AnalogService $analogService)
    {
        try {
            return response()->json($analogService->getText(request:$request));
        }catch (\Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
}
