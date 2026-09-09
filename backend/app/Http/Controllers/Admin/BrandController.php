<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = DB::table('brands')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'website' => 'nullable|string|max:255',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('brands')->insert($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit($id)
    {
        $brand = DB::table('brands')->where('id', $id)->first();
        if (!$brand) {
            return redirect()->route('admin.brands.index')->with('error', 'Brand not found.');
        }
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = DB::table('brands')->where('id', $id)->first();
        if (!$brand) {
            return redirect()->route('admin.brands.index')->with('error', 'Brand not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $id,
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'website' => 'nullable|string|max:255',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['updated_at'] = now();

        DB::table('brands')->where('id', $id)->update($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $brand = DB::table('brands')->where('id', $id)->first();
        if (!$brand) {
            return redirect()->route('admin.brands.index')->with('error', 'Brand not found.');
        }

        DB::table('brands')->where('id', $id)->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}
