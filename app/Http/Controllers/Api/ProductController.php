<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
//import facade Validator
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        //get all posts
        $Products = Product::all();

        //return collection of posts as a resource
        return new ProductResource(true, 'List Data Posts', $Products);
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'image'          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'     => 'nullable',
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
        $image->storeAs('public/products', $image->hashName());

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


    public function show($id)
    {
        //find post by ID
        $product = Product::find($id);

        //return single post as a resource
        return new ProductResource(true, 'Detail Data Product!', $product);
    }


    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'     => 'nullable',
            'name'           => 'required',
            'stock'         => 'required',
            'discount'      => 'required',
            'price'         => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $product = Product::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            //delete old image
            Storage::delete('public/products/' . basename($product->image));

            //update post with new image
            $product->update([
                'image'     => $image->hashName(),
                'name'     => $request->name,
                'stock'         => $request->stock,
                'description'     => $request->description,
                'price'   => $request->price,
                'discount'     => $request->discount,
            ]);
        } else {

            //update post without image
            $product->update([
                'name'     => $request->name,
                'stock'         => $request->stock,
                'description'     => $request->description,
                'price'   => $request->price,
                'discount'     => $request->discount,
            ]);
        }

        //return response
        return new ProductResource(true, 'Data Product Berhasil Diubah!', $product);
    }


    public function destroy($id)
    {

        //find post by ID
        $product = Product::find($id);

        //delete image
        Storage::delete('public/products/' . basename($product->image));

        //delete post
        $product->delete();

        //return response
        return new ProductResource(true, 'Data Product Berhasil Dihapus!', null);
    }
}
