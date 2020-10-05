<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\Category;
use App\PrdtByCtgr;
use App\Offer;
use App\PrdtByOffer;

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
    	$product = Product::find($prdtByCtgr->product_id);
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
    //Offers
    /**
     * Create a offer
     */
    public function createOffer(Request $request){
        $request->validate([
            'until' => 'required',
            'price' => 'required'
        ]);
        if(auth()->user()->type != 'admin'){
        	return response()->json([
	            'message' => 'Cannot create a offer!'
	        ], 201);
        }
        Offer::create([
            'until' => $request->until,
            'price' => $request->price,
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Successfully created offer!'
        ], 201);
    }
    /**
     * Edit a offer
     */
    public function editOffer($id, Request $request){
    	$offer = Offer::find($id);
    	$request->validate([
            'status' => 'boolean'
        ]);
		if(isset($request->until)){
		    $offer->until = $request->until; 
		}
		if(isset($request->price) && $request->price >= 0){
		    $offer->price = $request->price; 
		}
		if(isset($request->status)){
		    $offer->status = $request->status; 
		}
        if(auth()->user()->type == 'admin'){
        	$offer->save();
        }
        return $offer;
    }
    /**
     * Delete a offer
     */
    public function deleteOffer($id){
    	$offer = Offer::find($id);
        if(auth()->user()->type == 'admin'){
        	$offer->delete();
	        return response()->json([
	            'message' => 'Successfully deleted offer!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to deleted offer!'
	        ], 401);
    	}
    }
    /**
     * Show all offers
     */
    public function offers(Request $request){
    	return Offer::where('status','=',1)
    				 ->orderBy('until','desc')
    				 ->get();
    }
    /**
     * Products by Offer
     */
    public function offersProducts($id, Request $request){
    	return Product::join('prdt_by_offer','prdt_by_offer.product_id','=','products.id')
    				  ->where('prdt_by_offer.offer_id','=',$id)
    				  ->select('products.*')
    				  ->orderBy('name','desc')
    				  ->get();
    }
    /**
     * Add a product to an offer
     */
    public function addToOffer(Request $request){
        $request->validate([
            'offer' => 'required',
            'product' => 'required'
        ]);
        $product = Product::find($request->product);
        $offer = Offer::find($request->offer);        
        if(!((isset($product) && isset($offer)) || auth()->user()->type == 'admin')){
        	return response()->json([
	            'message' => 'Cannot add!'
	        ], 201);
        }
        PrdtByOffer::create([
            'product_id' => $request->product,
            'offer_id' => $request->offer
        ]);

        return response()->json([
            'message' => 'Successfully added product!'
        ], 201);
    }
    /**
     * Remove a product to an Offer
     */
    public function removeToOffer($id){
    	$prdtByOffer = PrdtByOffer::find($id);
    	$product = Product::find($prdtByOffer->product_id);
        if(auth()->user()->type == 'admin'){
        	$prdtByOffer->delete();
	        return response()->json([
	            'message' => 'Successfully removed product!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to removed product!'
	        ], 401);
    	}
    }
}
