<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Face;
use Illuminate\Support\Facades\Auth;

class FaceController extends BaseController
{

    protected $face = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Face $face)
    {
        $this->middleware('auth:api');
        $this->face = $face;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::User();
        if ($user->type == 'admin') {
            $faces = $this->face->where('reported', 0)->latest()->with('category', 'tags')->paginate(10);
        } else {
            $faces = $this->face->where([['name', $user->name],['reported', 1]])->latest()->with('category', 'tags')->paginate(10);
        }
        return $this->sendResponse($faces, 'Face list');
    }
}
