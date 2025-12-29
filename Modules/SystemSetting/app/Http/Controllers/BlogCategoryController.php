<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\SystemSetting\app\Http\Requests\BlogCategoryRequest;
use Modules\SystemSetting\app\Models\BlogCategory;
use Modules\SystemSetting\app\Services\BlogCategoryService;
use Yajra\DataTables\Facades\DataTables;

class BlogCategoryController extends Controller
{

    public function __construct(
        protected BlogCategoryService $blogCategoryService,
        protected LanguageService $languageService
    ) {
        $this->middleware('can:blog-categories.index')->only(['index', 'datatable']);
        $this->middleware('can:blog-categories.create')->only(['create', 'store']);
        $this->middleware('can:blog-categories.edit')->only(['edit', 'update']);
        $this->middleware('can:blog-categories.delete')->only(['destroy']);
        $this->middleware('can:blog-categories.toggle-status')->only(['toggleStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('systemsetting::blog-categories.index');
    }

    /**
     * Get datatable data.
     */
    public function datatable(Request $request)
    {
        $query = $this->blogCategoryService->getAllBlogCategories();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query = $query->filter(function($item) use ($search) {
                return stripos($item->title, $search) !== false;
            });
        }

        if ($request->filled('active') && $request->active !== '') {
            $query = $query->where('active', $request->active);
        }

        if ($request->filled('created_date_from')) {
            $query = $query->where('created_at', '>=', $request->created_date_from);
        }

        if ($request->filled('created_date_to')) {
            $query = $query->where('created_at', '<=', $request->created_date_to);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('title_display', function ($blogCategory) {
                $html = '<div class="userDatatable-content">';
                $html .= '<div class="mb-1"><strong>' . ($blogCategory->title ?? '-') . '</strong></div>';
                if ($blogCategory->description) {
                    $html .= '<div class="text-muted small">' . Str::limit($blogCategory->description, 50) . '</div>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('image_preview', function ($blogCategory) {
                if ($blogCategory->mainImage && $blogCategory->mainImage->path) {
                    $imagePath = asset('storage/' . $blogCategory->mainImage->path);
                    return '<img src="' . $imagePath . '" alt="Blog Category Image" class="img-thumbnail" style="width: 60px; height: 40px; border-radius: 4px;" onerror="this.src=\'' . asset('images/no-image.png') . '\'">';
                }
                return '<div class="text-center"><i class="uil uil-image-slash text-muted" style="font-size: 24px;"></i></div>';
            })
            ->addColumn('status_badge', function ($blogCategory) {
                if (auth()->user()->can('blog-categories.toggle-status')) {
                    $isChecked = $blogCategory->active ? 'checked' : '';
                    $switchId = 'status-switch-' . $blogCategory->id;
                    return '<div class="userDatatable-content">
                        <div class="form-switch">
                            <input class="form-check-input status-switcher"
                                   type="checkbox"
                                   id="' . $switchId . '"
                                   data-id="' . $blogCategory->id . '"
                                   ' . $isChecked . '
                                   style="cursor: pointer; width: 40px; height: 20px;">
                            <label class="form-check-label" for="' . $switchId . '"></label>
                        </div>
                    </div>';
                } else {
                    if ($blogCategory->active) {
                        return '<span class="badge badge-round badge-lg badge-success">' . __('systemsetting::blog_categories.active') . '</span>';
                    }
                    return '<span class="badge badge-round badge-lg badge-danger">' . __('systemsetting::blog_categories.inactive') . '</span>';
                }
            })
            ->addColumn('created_date', function ($blogCategory) {
                return $blogCategory->created_at;
            })
            ->addColumn('action', function ($blogCategory) {
                $actions = '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';
                if (auth()->user()->can('blog-categories.edit')) {
                    $actions .= '<a href="' . route('admin.system-settings.blog-categories.edit', $blogCategory->id) . '" class="edit btn btn-warning table_action_father" title="' . __('systemsetting::blog_categories.edit') . '">';
                    $actions .= '<i class="uil uil-edit table_action_icon"></i>';
                    $actions .= '</a>';
                }
                if (auth()->user()->can('blog-categories.delete')) {
                    $actions .= '<a href="javascript:void(0);" class="remove btn btn-danger table_action_father" data-bs-toggle="modal" data-bs-target="#modal-delete-blog-category" data-item-id="' . $blogCategory->id . '" data-item-name="' . ($blogCategory->title ?? 'Blog Category') . '" title="' . __('systemsetting::blog_categories.delete') . '">';
                    $actions .= '<i class="uil uil-trash-alt table_action_icon"></i>';
                    $actions .= '</a>';
                }
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['title_display', 'image_preview', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        return view('systemsetting::blog-categories.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogCategoryRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('blog-categories', 'public');
                $data['image'] = $path;
            }

            $blogCategory = $this->blogCategoryService->createBlogCategory($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::blog_categories.created_successfully'),
                    'redirect' => route('admin.system-settings.blog-categories.index'),
                    'data' => $blogCategory
                ]);
            }

            return redirect()
                ->route('admin.system-settings.blog-categories.index')
                ->with('success', __('systemsetting::blog_categories.created_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::blog_categories.error_creating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::blog_categories.error_creating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $code, $id)
    {
        $blogCategory = $this->blogCategoryService->getBlogCategoryById($id);
        $languages = Language::all();
        return view('systemsetting::blog-categories.view', compact('blogCategory', 'languages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $code, $id)
    {
        $blogCategory = $this->blogCategoryService->getBlogCategoryById($id);
        $languages = Language::all();
        return view('systemsetting::blog-categories.form', compact('blogCategory', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogCategoryRequest $request, $lang, $code, $id)
    {
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('blog-categories', 'public');
                $data['image'] = $path;
            }

            $blogCategory = $this->blogCategoryService->updateBlogCategory($id, $data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::blog_categories.updated_successfully'),
                    'redirect' => route('admin.system-settings.blog-categories.index'),
                    'data' => $blogCategory
                ]);
            }

            return redirect()
                ->route('admin.system-settings.blog-categories.index')
                ->with('success', __('systemsetting::blog_categories.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::blog_categories.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::blog_categories.error_updating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $code, $id)
    {
        try {
            $this->blogCategoryService->deleteBlogCategory($id);

            return response()->json([
                'success' => true,
                'message' => __('systemsetting::blog_categories.deleted_successfully'),
                'redirect' => route('admin.system-settings.blog-categories.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('systemsetting::blog_categories.error_deleting') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle blog category status.
     */
    public function toggleStatus($lang, $code, $id)
    {
        try {
            $blogCategory = $this->blogCategoryService->getBlogCategoryById($id);
            $newStatus = !$blogCategory->active;
            
            $this->blogCategoryService->updateBlogCategory($id, [
                'active' => $newStatus
            ]);

            return response()->json([
                'status' => true,
                'message' => $newStatus 
                    ? __('systemsetting::blog_categories.activated_successfully')
                    : __('systemsetting::blog_categories.deactivated_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('systemsetting::blog_categories.error_changing_status') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
