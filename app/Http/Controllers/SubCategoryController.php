<?php

namespace App\Http\Controllers;

use App\Models\sub_category;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sub_category =  sub_category::all();
        return $sub_category;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id)
    {
       
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return sub_category::where('category_id',"=",$id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sub_category $sub_category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, sub_category $sub_category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(sub_category $sub_category)
    {
        //
    }
}
