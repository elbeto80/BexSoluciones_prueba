<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

use Illuminate\Support\Facades\Validator;

use Exception;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $products = Product::all();

            return response()->json([
                'success' => true,
                'products' => $products
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                // 'description' => 'string',
                'email' => 'required|string|email|max:255',
                'quantity' => 'required|integer',
                'active' => 'required|boolean',
                'created_at' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error'   => $validator->messages()->all(),
                ], 400);
            }

            $input = $request->all();

            $product = new Product();
            $product->name = $input['name'];
            $product->description = $input['description'];
            $product->email = $input['email'];
            $product->quantity = $input['quantity'];
            $product->active = $input['active'];
            $product->created_at = $input['created_at'];
            $product->save();

            return response()->json([
                'success' => true,
                'data' => $product,
            ], 201);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product = Product::where('id', $id)->first();

            if (empty($product->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $product,
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                // 'description' => 'string',
                'email' => 'required|string|email|max:255',
                'quantity' => 'required|integer',
                'active' => 'required|boolean',
                'created_at' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error'   => $validator->messages()->all(),
                ], 400);
            }

            $input = $request->all();

            $product = Product::where('id', $id)->first();
            $product->name = $input['name'];
            $product->description = $input['description'];
            $product->email = $input['email'];
            $product->quantity = $input['quantity'];
            $product->active = $input['active'];
            $product->created_at = $input['created_at'];
            $product->save();

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product updated successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::where('id', $id)->first();

            if (empty($product->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found',
                ], 400);
            }

            Product::where('id', $product->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
