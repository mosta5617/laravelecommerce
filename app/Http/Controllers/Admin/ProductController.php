<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        $products=Product::latest()->get();
        return view('admin.allproduct', compact('products'));
    }
    public function AddProduct(){
        $categories=Category::latest()->get();
        $subcategories=SubCategory::latest()->get();
        return view('admin.addproduct', compact('categories','subcategories'));
    }

    public function StoreProduct(Request $request){
        $request->validate([
            'product_name' => 'required|unique:products|max:255',
            'product_short_desc' => 'required|max:500',
            'product_long_desc' => 'required',
            'product_price' => 'required',
            'product_category_id' => 'required',
            'product_subcategory_id' => 'required',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $image=$request->file('product_image');
        $image_name=hexdec(uniqid()).'.'. $image->getClientOriginalExtension();
        $request->product_image->move(public_path('upload'),$image_name);
        $image_url= 'upload/'. $image_name;

        $category_id=$request->product_category_id;
        $subcategory_id=$request->product_subcategory_id;

        $category_name= Category::where('id', $category_id )->value('category_name');
        $subcategory_name= SubCategory::where('id', $subcategory_id )->value('subcategory_name');

        Product::insert([
            'product_name'=>$request->product_name,
            'product_short_desc'=>$request->product_short_desc,
            'product_long_desc'=>$request->product_long_desc,
            'product_price'=>$request->product_price,
            'product_quantity'=>$request->product_quantity,
            'product_category_id'=>$request->product_category_id,
            'product_subcategory_id'=>$request->product_subcategory_id,
            'product_category_name'=>$category_name,
            'product_subcategory_name'=>$subcategory_name,
            'product_image'=>$image_url,
            'slug'=>strtolower(str_replace(' ','-',$request->product_name)),
        ]);

       Category::where('id',  $category_id)->increment('product_count', 1);
       SubCategory::where('id',  $subcategory_id)->increment('product_count', 1);

        return redirect()->route('allproduct')->with('message', 'Product Added Successfully!'); 
    }



}