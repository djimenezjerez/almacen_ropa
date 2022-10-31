<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de categorías',
                'payload' => [
                    'data' => DB::table('categories')->select('id', 'name')->where('active', '=', true)->where('deleted_at', null)->orderBy('name')->get(),
                ],
            ];
        }

        $query = DB::table('categories')->where('deleted_at', null);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('name', 'ASC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de categorías',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->only('name'));
        return [
            'message' => 'Categoría registrada',
        ];
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->all());
        return [
            'message' => 'Datos de categoría actualizados',
        ];
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
