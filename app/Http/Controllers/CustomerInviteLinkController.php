<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvitationLink;
use App\Models\CustomerPackage;
use App\Models\Shop;
use Hash;
use DB;

class CustomerInviteLinkController extends Controller
{
    //
    public function index()
    {
        $invitationLinks = InvitationLink::where("deleted",0)->paginate(10);

        //return InvitationLink::all();
        return view('backend.customer.invitation_link.index', compact('invitationLinks'));
    }

    public function create(Request $request)
    {
        $packages = CustomerPackage::all();

        return view('backend.customer.invitation_link.create', compact('packages'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $invitationLink = new InvitationLink();

            $invitationLink->nb_members = $request->input("number_of_invitaion");
            $invitationLink->description = $request->input("description");
            $invitationLink->package_id = $request->input("package_id");
            $invitationLink->partner = $request->input("partner");
            $invitationLink->logo = $request->input("logo");


            $invitationLink->save();

            $hashedId = encrypt($invitationLink->id);
            $invitaionPath = "/customers/links/subscription/".$hashedId;
            $invitationLink->link = $invitaionPath;
            $invitationLink->save();
            DB::commit();
            return redirect()->route("customers.links.index");
        } catch (\Throwable $th) {
            flash(translate('Problem while creating invitaion link'))->error();
            return redirect()->back();
        }

    }

    public function edit(Request $request, InvitationLink $invitationLink)
    {
        $packages = CustomerPackage::all();

        return view('backend.customer.invitation_link.edit', compact('invitationLink','packages'));
    }

    public function update(Request $request, InvitationLink $invitationLink)
    {
        DB::beginTransaction();
        try {

            $invitationLink->nb_members = $request->input("number_of_invitaion");
            $invitationLink->description = $request->input("description");
            $invitationLink->package_id = $request->input("package_id");
            $invitationLink->partner = $request->input("partner");
            $invitationLink->logo = $request->input("logo");

            if ($request->hasFile('image')) {
                $defaultImage = $request->file('image');
                $imageName = date('YmdHi') . "." . $defaultImage->getClientOriginalExtension();
                move_uploaded_file($defaultImage, public_path() . '/image/' . $imageName);
                $invitationLink->image = "/image/" . $imageName;
            }

            $invitationLink->save();

            $hashedId = encrypt($invitationLink->id);
            $invitaionPath = "/customers/links/subscription/".$hashedId;
            $invitationLink->link = $invitaionPath;
            $invitationLink->save();
            DB::commit();
            return redirect()->route("customers.links.index");
        } catch (\Throwable $th) {
            flash(translate('Problem while creating invitaion link'))->error();
            return redirect()->back();
        }

    }



    public function delete(Request $request, InvitationLink $invitationLink)
    {
        $invitationLink->deleted = 1;
        $invitationLink->save();
        return redirect()->back();
    }
}
