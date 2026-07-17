<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()->withCount('books')->orderBy('name')->paginate(20);

        return view('bookshop::staff.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:bookshop_categories,name'],
        ]);

        Category::create($data);

        return back()->with('status', "Category \"{$data['name']}\" created.");
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:bookshop_categories,name,'.$category->id],
        ]);

        $category->update($data);

        return back()->with('status', "Category \"{$category->name}\" updated.");
    }

    public function toggleActive(Category $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);

        return back()->with('status', "Category \"{$category->name}\" ".($category->is_active ? 'activated.' : 'deactivated.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->books()->exists()) {
            return back()->withErrors(['category' => 'Cannot delete a category that still has books. Deactivate it instead.']);
        }

        $category->delete();

        return back()->with('status', 'Category deleted.');
    }
}
