<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailVerification extends Controller
{
    public function emailVerification(string $payId, string $email)
    {
        $payId = base64_decode($payId);
        $email = base64_decode($email);

        $customer = Customers::where('payId', $payId)->first();

        if (!$customer) {
            return response(404);
        }

        if ($customer->email === $email) {
            $customer->emailVerified = TRUE;
            $customer->save();
        }
        else {
            try {
                $customer->email = $email;
                $customer->emailVerified = TRUE;
                $customer->save();
            }
            catch (Exception $e) {
                abort(403, "This email is already registered with another customer");
            }
        }

        return view('emailsuccess');
    }
}
