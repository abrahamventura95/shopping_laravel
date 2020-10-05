<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\Category;
use App\PrdtByCtgr;

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
    	$product = Product::find($id);
    	$categories = Category::join('prdt_by_ctg','prdt_by_ctg.category_id','=','categories.id')
    						  ->where('prdt_by_ctg.product_id','=',$id)
    						  ->select('categories.*')
    						  ->get();
    	$result = array('product' => $product, 'categories' => $categories);
    	return $result;					  
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
    public function delete($id){
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
    //Categories
    /**
     * Create a category
     */
    public function createCtgr(Request $request){
        $request->validate([
            'name' => 'required|string|unique:categories',
            'description' => 'required|string'
        ]);
        if(auth()->user()->type != 'admin'){
        	return response()->json([
	            'message' => 'Cannot create a category!'
	        ], 201);
        }
        Category::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'Successfully created category!'
        ], 201);
    }
    /**
     * Edit a category
     */
    public function editCtgr($id, Request $request){
    	$category = Category::find($id);
    	$request->validate([
            'name' => 'string',
            'description' => 'string',
            'image' => 'string'
        ]);
		if(isset($request->description)){
		    $category->description = $request->description; 
		}
        if(auth()->user()->type == 'admin'){
        	$category->save();
        }
        return $category;
    }
    /**
     * Delete a category
     */
    public function deleteCtgr($id){
    	$category = Category::find($id);
        if(auth()->user()->type == 'admin'){
        	$category->delete();
	        return response()->json([
	            'message' => 'Successfully deleted category!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to deleted category!'
	        ], 401);
    	}
    }
    /**
     * Show all categories
     */
    public function categories(Request $request){
    	return Category::orderBy('name','desc')->get();
    }
    /**
     * Add a category to a product
     */
    public function addCtgr(Request $request){
        $request->validate([
            'category' => 'required',
            'product' => 'required'
        ]);
        $product = Product::find($request->product);
        $category = Category::find($request->category);        
        if(!((isset($product) && isset($category)) || $product->shop != auth()->user()->id)){
        	return response()->json([
	            'message' => 'Cannot add!'
	        ], 201);
        }
        PrdtByCtgr::create([
            'product_id' => $request->product,
            'category_id' => $request->category
        ]);

        return response()->json([
            'message' => 'Successfully added category!'
        ], 201);
    }
    /**
     * Remove a category
     */
    public function removeCtgr($id){
    	$prdtByCtgr = PrdtByCtgr::find($id);
    	$product = Product::find($prdtByCtgr->product->id);
        if($product->shop === auth()->user()->id){
        	$prdtByCtgr->delete();
	        return response()->json([
	            'message' => 'Successfully removed category!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to removed category!'
	        ], 401);
    	}
    }
}
