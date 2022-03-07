<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;

class VerifyController extends BaseController
{
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