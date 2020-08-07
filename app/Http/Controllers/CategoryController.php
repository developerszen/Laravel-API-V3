<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function index (Request $request) {
        $categories = Category::
            when($request->has('name'), function ($query) use ($request) {
                $name = $request->query('name');
                $query->where('name', 'like', '%' . $name . '%');
            })
        ->get(['id', 'name', 'created_at']);

        return $categories;
    }

    function store (Request $request) {
        $request->validate([
            'name' => 'required|string',
        ]);

        $category = Category::create([
            'name' => $request->input('name'),
        ]);

        return $category;
    }

    function show ($id) {
        $category = Category::findOrFail($id);

        return $category;
    }

    function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|string',
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'name' => $request->input('name'),
        ]);

        return $category;
    }

    function destroy ($id) {
        $category = Category::findOrFail($id);

        if($category->book()->exists()) {
            return response()->json([
                'error' => 'integrity violation'
            ], 500);
        }

        $category->delete();

        return response([], 204);
    }
}
