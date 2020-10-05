<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Product;

class ProductController extends Controller{
    /**
     * Show all products
     */
    public function products(Request $request){
    	return Product::orderBy('name','desc')->get();
    }
    /**
     * Show products by shop
     */
    public function byShop($shop,Request $request){
    	return Product::where('shop','=',$shop)
    			   	  ->orderBy('name','desc')
    			   	  ->get();
    }
    /**
     * Show a product
     */
    public function show($id){
        return Product::find($id);
    }
    /**
     * Create a product
     */
    public function create(Request $request){
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'string',
            'amount' => 'required',
            'price' => 'required'
        ]);
        if(auth()->user()->type != 'shop'){
        	return response()->json([
	            'message' => 'Cannot create a product!'
	        ], 201);
        }
        Product::create([
            'shop' => auth()->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image,
            'amount' => $request->amount,
            'price' => $request->price
        ]);

        return response()->json([
            'message' => 'Successfully created product!'
        ], 201);
    }
    /**
     * Edit a product
     */
    public function edit($id, Request $request){
    	$product = Product::find($id);
    	$request->validate([
            'name' => 'string',
            'description' => 'string',
            'image' => 'string'
        ]);
		if(isset($request->name)){
		    $product->name = $request->name; 
		}
		if(isset($request->description)){
		    $product->description = $request->description; 
		}
		if(isset($request->image)){
		    $product->image = $request->image; 
		}
		if(isset($request->amount) && $request->amount >= 0){
		    $product->amount = $request->amount; 
		}
		if(isset($request->price ) && $request->price >= 0){
		    $product->price = $request->price; 
		}
        if($product->shop = auth()->user()->id){
        	$product->save();
        }
        return $product;
    }

    /**
     * Delete a product
     */
    public function delete($id)
    {
    	$product = Product::find($id);
        if($product->shop === auth()->user()->id){
        	$product->delete();
	        return response()->json([
	            'message' => 'Successfully deleted product!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to deleted product!'
	        ], 401);
    	}
    }
}
