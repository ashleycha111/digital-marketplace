<?php

namespace App\Http\Controllers\Vendor;

use App\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;

class NewProductController extends Controller
{
    public function showDetailsStep()
    {
        return view('vendor.new-product.details-step');
    }
    public function processDetailsStep(Request $request)
    {
        $this->validate($request, [
            'title' => 'required', // todo: specify min and max for product title
            'body' => 'required',
            'price' => 'required|numeric|min:0', // todo: specify min and max for product price
            'category_id' => 'required|exists:categories,id'
        ]);

        session()->put('new_product.details_step', $request->only([
            'title',
            'body',
            'price',
            'category_id'
        ]));

        return redirect('/vendor/new-product/cover');
    }

    public function showCoverStep()
    {
        if (! session()->has('new_product.details_step'))
        {
            return redirect('/vendor/new-product/');
        }

        return view('vendor.new-product.cover-step');
    }

    public function processCoverStep(Request $request)
    {
        if (! session()->has('new_product.details_step'))
        {
            return redirect('/vendor/new-product');
        }

        $this->validate($request, [
            'cover' => 'required|file|mimes:jpeg|dimensions:min_width=640,min_height=480,ratio=4/3'
        ]);

        $uploadedCover = $request->file('cover');

        $coverFile = $uploadedCover->move(
            // todo: consider moving uploads dir into storage dir
            // todo: consider uuid
            public_uploads_path('product_covers'),
            uniqid().'.'.$uploadedCover->extension()
        );

        $coverModel = File::create([
            'path' => Str::after(
                $coverFile->getRealPath(),
                public_path().DIRECTORY_SEPARATOR
            ),
            'assoc' => 'cover',
        ]);

        session()->put('new_product.cover_step.cover.id', $coverModel->id);

        return redirect('/vendor/new-product/sample');
    }
}