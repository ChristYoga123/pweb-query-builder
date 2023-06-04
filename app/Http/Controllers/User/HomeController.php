<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("pages.user.home.index")->with([
            "houses" => DB::table("venues")->where("category", "House")->get(),
            "hotels" => DB::table("venues")->where("category", "Hotel")->get(),
            "apartments" => DB::table("venues")->where("category", "Apartment")->get(),
        ]);
    }

    public function show()
    {
        return view("pages.user.home.show");
    }
}
