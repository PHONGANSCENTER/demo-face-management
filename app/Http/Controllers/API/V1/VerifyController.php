<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;

class VerifyController extends BaseController
{
    protected $product = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->product->where('reported', 1)->latest()->with('category', 'tags')->paginate(10);
        return $this->sendResponse($products, 'Face list');
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
        $products = $this->product->where('id', $id)->update(['reported' => 0]);
        return $this->sendResponse($products, 'Face Information has been reported');
    }

    public function destroy($id)
    {
        $products = $this->product->where('id', $id)->update(['reported' => 1]);
        return $this->sendResponse($products, 'Face Information has been reported');
    }
}