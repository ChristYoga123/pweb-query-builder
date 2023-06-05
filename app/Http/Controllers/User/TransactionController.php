<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Venue;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Str;
use Midtrans;

class TransactionController extends Controller
{
    public function __construct()
    {
        Midtrans\Config::$serverKey = env("MIDTRANS_SERVERKEY");
        Midtrans\Config::$clientKey = env("MIDTRANS_CLIENTKEY");
        Midtrans\Config::$isProduction = env("MIDTRANS_IS_PRODCUCTION");
        Midtrans\Config::$isSanitized = env("MIDTRANS_IS_SANITIZED");
        Midtrans\Config::$is3ds = env("MIDTRANS_IS_3DS");
    }

    public function store(Request $request, Venue $venue)
    {
        $request->validate([
            "night" => "required|integer",
            "start_date" => "required|date",
            "end_date" => "required|date",
        ]);

        $transaction = DB::table("transactions")->insertGetId([
            "user_id" => Auth::user()->id,
            "venue_id" => $venue->id,
            "night" => $request->night,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date
        ]);

        $transaction = DB::table("transactions")->where("id", $transaction)->first();

        // Buat instance dari Transaction model
        $converted_transaction = new Transaction();
        $converted_transaction->user_id = $transaction->user_id;
        $converted_transaction->venue_id = $transaction->venue_id;
        $converted_transaction->night = $transaction->night;
        $converted_transaction->start_date = $transaction->start_date;
        $converted_transaction->end_date = $transaction->end_date;

        $this->getSnapRedirect($converted_transaction);
        return "Pembayaran sukses";
    }

    public function success()
    {
        return view("pages.user.home.success");
    }

    public function getSnapRedirect(Transaction $transaction)
    {
        // get Transaction Data
        $transaction = DB::table("transactions")
            ->join("venues", "venues.id", "=", "transactions.venue_id")
            ->join("users", "users.id", "=", "transactions.user_id")
            ->select(
                "transactions.*",
                "venues.name as venue_name",
                "venues.price_per_night as venue_price",
                "venues.id as venue_id",
                "users.name as user_name",
                "users.email as user_email"
            )->first();

        // filling request body midtrans
        $transaction_details = [
            "order_id" => $transaction->id . "-" . Str::random(5),
            "gross_ammount" => $transaction->venue_price * $transaction->night,
        ];

        $item_details[] = [
            "id" => $transaction->venue_id,
            "price" => $transaction->venue_price,
            "quantity" => 1,
            "name" => $transaction->venue_name,
        ];

        $user_data = [
            "first_name" => $transaction->user_name,
            "last_name" => "",
            "email" => $transaction->user_email,
            "phone" => "",
            "address" => "",
            "city" => "",
            "postal_code" => "",
            "country_code" => "IDN"
        ];

        $customer_details = [
            "first_name" => $transaction->user_name,
            "last_name" => "",
            "email" => $transaction->user_email,
            "phone" => "",
            "customer_details" => $user_data,
            "shipping_address" => $user_data,
        ];

        // send to midtrans
        $midtrans_params = [
            "transaction_details" => $transaction_details,
            "item_details" => $item_details,
            "customer_details" => $customer_details
        ];

        try {
            $payment_url = \Midtrans\Snap::createTransaction($midtrans_params)->redirect_url;
            DB::table("transactions")->where("id", $transaction->id)
                ->update([
                    "midtrans_url" => $payment_url
                ]);

            return $payment_url;
        } catch (Exception $e) {
            //throw $th;
            return false;
        }
    }

    public function midtransCallback(Request $request)
    {
        $notif = $request->method() == 'POST' ? new Midtrans\Notification() : Midtrans\Transaction::status($request->order_id);

        $transaction_status = $notif->transaction_status;
        $fraud = $notif->fraud_status;

        // get checkout id
        $transaction_id = explode("-", $notif->order_id)[0];
        $transaction = DB::table("transactions")->where("id", $transaction_id)->first();
        if ($transaction_status == 'capture') {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'challenge'
                $transaction->payment_status = "pending";
            } else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'success'
                $transaction->payment_status = "paid";
            }
        } else if ($transaction_status == 'cancel') {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'failure'
                $transaction->payment_status = "failed";
            } else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'failure'
                $transaction->payment_status = "failed";
            }
        } else if ($transaction_status == 'deny') {
            // TODO Set payment status in merchant's database to 'failure'
            $transaction->payment_status = "failed";
        } else if ($transaction_status == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $transaction->payment_status = "paid";
        } else if ($transaction_status == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            $transaction->payment_status = "pending";
        } else if ($transaction_status == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            $transaction->payment_status = "failed";
        }

        $transaction->save();
        return redirect()->route("transaction.success.index");
    }
}
