<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\SystemSetting\app\Http\Requests\BlogRequest;
use Modules\SystemSetting\app\Models\Blog;
use Modules\SystemSetting\app\Services\BlogService;
use Modules\SystemSetting\app\Services\BlogCategoryService;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    protected $blogService;
    protected $blogCategoryService;

    public function __construct(BlogService $blogService, BlogCategoryService $blogCategoryService)
    {
        $this->blogService = $blogService;
        $this->blogCategoryService = $blogCategoryService;
        
        $this->middleware('can:blogs.index')->only(['index', 'datatable', 'show']);
        $this->middleware('can:blogs.create')->only(['create', 'store']);
        $this->middleware('can:blogs.edit')->only(['edit', 'update']);
        $this->middleware('can:blogs.delete')->only(['destroy']);
        $this->middleware('can:blogs.toggle-status')->only(['toggleStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogCategories = $this->blogCategoryService->getActiveBlogCategoriesForDropdown(); // Corrected service call
        return view('systemsetting::blogs.index', compact('blogCategories'));
    }

    /**
     * Get datatable data.
     */
    public function datatable(Request $request)
    {
        $query = $this->blogService->getAllBlogs();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query = $query->filter(function($item) use ($search) {
                return stripos($item->title, $search) !== false ||
                       stripos($item->blogCategory->title ?? '', $search) !== false;
            });
        }

        if ($request->filled('blog_category_id')) {
            $query = $query->where('blog_category_id', $request->blog_category_id);
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
            ->addColumn('blog_information', function ($blog) {
                $title = Str::limit($blog->title ?? '-', 50);
                $html = '<div class="userDatatable-content">';
                $html .= '<div class="mb-1"><strong>' . $title . '</strong></div>';
                $html .= '<div class="text-muted small">' . __('systemsetting::blogs.category') . ': ' . ($blog->blogCategory->title ?? '-') . '</div>';
                $html .= '</div>';
                return $html;
            })
            ->addColumn('image_preview', function ($blog) {
                if ($blog->mainImage && $blog->mainImage->path) {
                    $imagePath = asset('storage/' . $blog->mainImage->path);
                    return '<img src="' . $imagePath . '" alt="Blog Image" class="img-thumbnail" style="width: 60px; height: 40px; border-radius: 4px;" onerror="this.src=\'' . asset('images/no-image.png') . '\'">';
                }
                return '<div class="text-center"><i class="uil uil-image-slash text-muted" style="font-size: 24px;"></i></div>';
            })
            ->addColumn('status_badge', function ($blog) {
                if (auth()->user()->can('blogs.toggle-status')) {
                    $isChecked = $blog->active ? 'checked' : '';
                    $switchId = 'status-switch-' . $blog->id;
                    return '<div class="userDatatable-content">
                        <div class="form-switch">
                            <input class="form-check-input status-switcher"
                                   type="checkbox"
                                   id="' . $switchId . '"
                                   data-id="' . $blog->id . '"
                                   ' . $isChecked . '
                                   style="cursor: pointer; width: 40px; height: 20px;">
                            <label class="form-check-label" for="' . $switchId . '"></label>
                        </div>
                    </div>';
                } else {
                    if ($blog->active) {
                        return '<span class="badge badge-round badge-lg badge-success">' . __('systemsetting::blogs.active') . '</span>';
                    }
                    return '<span class="badge badge-round badge-lg badge-danger">' . __('systemsetting::blogs.inactive') . '</span>';
                }
            })
            ->addColumn('created_date', function ($blog) {
                return $blog->created_at;
            })
            ->addColumn('action', function ($blog) {
                $actions = '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';
                $actions .= '<a href="' . route('admin.system-settings.blogs.show', $blog->id) . '" class="view btn btn-primary table_action_father" title="' . __('systemsetting::blogs.view') . '">';
                $actions .= '<i class="uil uil-eye table_action_icon"></i>';
                $actions .= '</a>';
                if (auth()->user()->can('blogs.edit')) {
                    $actions .= '<a href="' . route('admin.system-settings.blogs.edit', $blog->id) . '" class="edit btn btn-warning table_action_father" title="' . __('systemsetting::blogs.edit') . '">';
                    $actions .= '<i class="uil uil-edit table_action_icon"></i>';
                    $actions .= '</a>';
                }
                if (auth()->user()->can('blogs.delete')) {
                    $actions .= '<a href="javascript:void(0);" class="remove btn btn-danger table_action_father" data-bs-toggle="modal" data-bs-target="#modal-delete-blog" data-item-id="' . $blog->id . '" data-item-name="' . ($blog->title ?? 'Blog') . '" title="' . __('systemsetting::blogs.delete') . '">';
                    $actions .= '<i class="uil uil-trash-alt table_action_icon"></i>';
                    $actions .= '</a>';
                }
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['blog_information', 'image_preview', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        $blogCategories = $this->blogCategoryService->getActiveBlogCategoriesForDropdown();
        return view('systemsetting::blogs.form', compact('languages', 'blogCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('blogs', 'public');
                $data['image'] = $path;
            }

            $blog = $this->blogService->createBlog($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::blogs.created_successfully'),
                    'redirect' => route('admin.system-settings.blogs.index'),
                    'data' => $blog
                ]);
            }

            return redirect()
                ->route('admin.system-settings.blogs.index')
                ->with('success', __('systemsetting::blogs.created_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::blogs.error_creating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::blogs.error_creating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $code, $id)
    {
        $blog = $this->blogService->getBlogById($id);
        $languages = Language::all();
        return view('systemsetting::blogs.view', compact('blog', 'languages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $code, $id)
    {
        $blog = $this->blogService->getBlogById($id);
        $languages = Language::all();
        $blogCategories = $this->blogCategoryService->getActiveBlogCategoriesForDropdown();
        return view('systemsetting::blogs.form', compact('blog', 'languages', 'blogCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogRequest $request, $lang, $code, $id)
    {
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('blogs', 'public');
                $data['image'] = $path;
            }

            $blog = $this->blogService->updateBlog($id, $data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::blogs.updated_successfully'),
                    'redirect' => route('admin.system-settings.blogs.index'),
                    'data' => $blog
                ]);
            }

            return redirect()
                ->route('admin.system-settings.blogs.index')
                ->with('success', __('systemsetting::blogs.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::blogs.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::blogs.error_updating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $code, $id)
    {
        try {
            $this->blogService->deleteBlog($id);

            return response()->json([
                'success' => true,
                'message' => __('systemsetting::blogs.deleted_successfully'),
                'redirect' => route('admin.system-settings.blogs.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('systemsetting::blogs.error_deleting') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle blog status.
     */
    public function toggleStatus($lang, $code, $id)
    {
        try {
            $blog = $this->blogService->getBlogById($id);
            $newStatus = !$blog->active;
            
            $this->blogService->updateBlog($id, [
                'active' => $newStatus
            ]);

            return response()->json([
                'status' => true,
                'message' => $newStatus 
                    ? __('systemsetting::blogs.activated_successfully')
                    : __('systemsetting::blogs.deactivated_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('systemsetting::blogs.error_changing_status') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
