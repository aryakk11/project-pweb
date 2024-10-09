<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\ProductResource;

//import facade Validator
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        //get all posts
        $Products = Product::latest()->paginate(5);

        //return collection of posts as a resource
        return new ProductResource(true, 'List Data Posts', $Products);
    }


    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'image'          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'     => 'required',
            'name'           => 'required',
            'stock'         => 'required',
            'discount'      => 'required',
            'price'         => 'required',
        ]);
            //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        
        //create post
        $product = Product::create([
            'image'     => $image->hashName(),
            'name'     => $request->name,
            'stock'         => $request->stock,
            'description'     => $request->description,
            'price'   => $request->price,
            'discount'     => $request->discount,
        ]);
            //return response
        return new ProductResource(true, 'Data Post Berhasil Ditambahkan!', $product);
    }

	
}
