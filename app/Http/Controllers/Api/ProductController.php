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
    public function index(Request $request)
    {
        // Start with a base query
        $query = Product::query();

        // Check if discount filter is applied
        if ($request->has('discount')) {
            $query->where('discount', '>', 0);
        }

        // Apply min price filter
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Apply max price filter
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Apply price sorting
        if ($request->has('sort_price')) {
            $direction = strtolower($request->sort_price) === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $direction);
        }

        $products = $query->get();

        return new ProductResource(true, 'List Data Products', $products);
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
