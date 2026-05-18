<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $vendors = $this->filtered($request)->paginate(12)->withQueryString();
        $categories = Vendor::all()->pluck('category')->filter()->unique()->sort()->values();
        $locations = Vendor::all()->pluck('location')->filter()->unique()->sort()->values();

        return view('vendors.index', compact('vendors', 'categories', 'locations'));
    }

    public function show(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $related = Vendor::where('category', $vendor->category)->where('_id', '!=', $vendor->getKey())->limit(3)->get();

        return view('vendors.show', compact('vendor', 'related'));
    }

    public function apiFilter(Request $request)
    {
        return response()->json($this->filtered($request)->limit(24)->get());
    }

    public function addToFavorites(Request $request, string $id)
    {
        Favorite::firstOrCreate(['user_id' => (string) $request->user()->getKey(), 'vendor_id' => $id]);

        return back()->with('success', 'Vendor saved to your shortlist.');
    }

    public function removeFromFavorites(Request $request, string $id)
    {
        Favorite::where('user_id', (string) $request->user()->getKey())->where('vendor_id', $id)->delete();

        return back()->with('success', 'Vendor removed from favorites.');
    }

    public function favorites(Request $request)
    {
        $ids = Favorite::where('user_id', (string) $request->user()->getKey())->pluck('vendor_id')->all();
        $vendors = Vendor::whereIn('_id', $ids)->paginate(12);

        return view('vendors.favorites', compact('vendors'));
    }

    private function filtered(Request $request)
    {
        $query = Vendor::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($nested) => $nested->where('business_name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%"));
        }
        if ($request->filled('category')) {
            $query->whereIn('category', (array) $request->category);
        }
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }
        if ($request->filled('price_max')) {
            $query->where('price_min', '<=', (float) $request->price_max);
        }
        if ($request->filled('rating')) {
            $query->where('rating', '>=', (float) $request->rating);
        }

        return match ($request->get('sort')) {
            'price_low' => $query->orderBy('price_min'),
            'price_high' => $query->orderByDesc('price_max'),
            'reviews' => $query->orderByDesc('total_reviews'),
            default => $query->orderByDesc('rating'),
        };
    }
}
