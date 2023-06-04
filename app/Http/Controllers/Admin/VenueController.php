<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("pages.admin.venue.index")->with([
            "venues" => DB::table("venues")->select()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("pages.admin.venue.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|unique:venues,name",
            "category" => "required|in:House,Hotel,Apartment",
            "price_per_night" => "required|integer|min:0",
            "location" => "required",
            "description" => "required",
            "hero_image" => "required|image|mimes:png,jpg,jpeg",
            "gallery_venue" => "required|array",
            "gallery_venue.*" => "image|mimes:png",
        ]);
        DB::beginTransaction();
        try {
            $hero_image = $request->file("hero_image")->store("venue-hero-image", "public");
            // insert venue data
            $venue = DB::table("venues")->insertGetId([
                "name" => $request->name,
                "slug" => Str::slug($request->name),
                "category" => $request->category,
                "location" => $request->location,
                "price_per_night" => $request->price_per_night,
                "hero_image" => $hero_image,
                "description" => $request->description
            ]);

            // insert venue gallery
            foreach ($request->gallery_venue as $item) {
                $gallery = $item->store("venue-gallery", "public");
                DB::table("venue_galleries")->insert([
                    "venue_id" => $venue,
                    "venue_gallery" => $gallery
                ]);
            }
            DB::commit();
            return redirect()->route("admin.venue.index")->with("success", "Data berhasil ditambah");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venue $venue)
    {
        return view("pages.admin.venue.edit")->with([
            "venue" => $venue
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venue $venue)
    {
        $request->validate([
            "name" => "required|unique:venues,name," . $venue->id,
            "category" => "required|in:House,Hotel,Apartment",
            "price_per_night" => "required|integer|min:0",
            "location" => "required",
            "description" => "required",
            "hero_image" => "image|mimes:png,jpg,jpeg",
            "gallery_venue" => "array",
            "gallery_venue.*" => "image|mimes:png",
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile("hero_image")) {
                Storage::delete("public/" . $request->old_image);
                $new_hero_image = $request->file("hero_image")->store("venue-hero-image", "public");
            } else {
                $new_hero_image = $request->old_image;
            }

            DB::table('venues')->where("id", $venue->id)->update([
                "name" => $request->name,
                "slug" => Str::slug($request->name),
                "category" => $request->category,
                "location" => $request->location,
                "price_per_night" => $request->price_per_night,
                "hero_image" => $new_hero_image,
                "description" => $request->description
            ]);

            if ($request->gallery_venue) {
                // select all gallery file and delete the real file
                $venue_galleries = DB::table("venue_galleries")->where("venue_id", $venue->id)->select()->get();
                foreach ($venue_galleries as $gallery) {
                    Storage::delete("public/" . $gallery->venue_gallery);
                }
                // delete venue gallery from db
                DB::table("venue_galleries")->where("venue_id", $venue->id)->delete();
                // insert venue gallery
                foreach ($request->gallery_venue as $item) {
                    $gallery = $item->store("venue-gallery", "public");
                    DB::table("venue_galleries")->insert([
                        "venue_id" => $venue->id,
                        "venue_gallery" => $gallery
                    ]);
                }
            }
            DB::commit();
            return redirect()->route("admin.venue.index")->with("success", "Data berhasil diubah");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue)
    {
        // select all gallery file and delete the real file
        $venue_galleries = DB::table("venue_galleries")->where("venue_id", $venue->id)->select()->get();
        foreach ($venue_galleries as $gallery) {
            Storage::delete("public/" . $gallery->venue_gallery);
        }
        // select hero image and delete the real file
        $venue_hero_image = DB::table("venues")->where("id", $venue->id)->select("hero_image")->first();
        Storage::delete("public/" . $venue_hero_image->hero_image);
        DB::table("venue_galleries")->where("venue_id", $venue->id)->delete();
        DB::table("venues")->where("id", $venue->id)->delete();
        return redirect()->back()->with("success", "Data berhasil dihapus");
    }
}
