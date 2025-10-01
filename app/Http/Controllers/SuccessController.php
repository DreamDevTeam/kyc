<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;

class SuccessController extends Controller
{
    public function index($payid)
    {
        $customer = Customers::where('payId', $payid)->first(['email', 'firstName', 'middleName', 'lastName']);
        $fullname = $customer->firstName . ($customer->middleName ? ' ' . $customer->middleName : '') . ' ' . $customer->lastName;
        $email = $customer->email;

        return view('success', compact('fullname', 'email'));
    }

    public function digital($payid)
    {
        $customer = Customers::where('payId', $payid)->first(['email', 'firstName', 'middleName', 'lastName']);
        $fullname = $customer->firstName . ($customer->middleName ? ' ' . $customer->middleName : '') . ' ' . $customer->lastName;
        $email = $customer->email;

        return view('successdigital', compact('payid', 'fullname', 'email'));
    }
}
