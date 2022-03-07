<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $product = Product::all()->last();
        $products = Product::all()->take(-10);
        $users = User::all();

        if($request->input('id'))
        {
            Product::where('id', $request->input('id'))->update(['reported' => 0]);
            return back();
        }

        return view('welcome',compact('product','products','users'));
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Verfication  $product
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $product = Product::where('id', $id)->update(['reported' => 0]);
        return $this->sendResponse($product, 'Face Information has been reported');
    }
}

