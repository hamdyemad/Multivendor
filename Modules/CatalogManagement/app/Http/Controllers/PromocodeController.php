<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserType;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\Actions\PromocodeAction;
use Modules\CatalogManagement\app\Services\PromocodeService;
use Modules\CatalogManagement\app\Http\Requests\PromocodeRequest;

class PromocodeController extends Controller
{
    public function __construct(
        protected PromocodeService $promocodeService,
        protected PromocodeAction $promocodeAction
    ) {
        $this->middleware('can:promocodes.index')->only(['index', 'datatable']);
        $this->middleware('can:promocodes.create')->only(['create', 'store']);
        $this->middleware('can:promocodes.edit')->only(['edit', 'update']);
        $this->middleware('can:promocodes.delete')->only(['destroy']);
        $this->middleware('can:promocodes.show')->only(['show']);
        $this->middleware('can:promocodes.change-status')->only(['changeStatus']);
    }

    public function index()
    {
        $data = [
            'title' => __('catalogmanagement::promocodes.title'),
        ];
        return view('catalogmanagement::promocodes.index', $data);
    }

    public function datatable(Request $request)
    {
        $result = $this->promocodeAction->getDatatableData($request->all());
        $dataPaginated = $result['dataPaginated'];

        return response()->json([
            'draw' => intval($request->input('draw', 1)), // Required for DataTables pagination
            'data' => $result['data'],
            'recordsTotal' => $result['totalRecords'],
            'recordsFiltered' => $result['filteredRecords'],
            'current_page' => $dataPaginated->currentPage(),
            'last_page' => $dataPaginated->lastPage(),
            'per_page' => $dataPaginated->perPage(),
            'total' => $dataPaginated->total(),
            'from' => $dataPaginated->firstItem(),
            'to' => $dataPaginated->lastItem()
        ]);
    }

    public function create($lang, $countryCode)
    {
        $data = [
            'title' => __('catalogmanagement::promocodes.create_promocode'),
        ];
        return view('catalogmanagement::promocodes.form', $data);
    }

    public function store($lang, $countryCode, PromocodeRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $this->promocodeService->createPromocode($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::promocodes.messages.created_successfully'),
                'redirect' => route('admin.promocodes.index')
            ]);
        }

        return redirect()->route('admin.promocodes.index')
            ->with('success', __('catalogmanagement::promocodes.messages.created_successfully'));
    }

    public function edit($lang, $countryCode, $id)
    {
        $promocode = $this->promocodeService->getPromocodeById($id);
        $data = [
            'promocode' => $promocode,
            'title' => __('catalogmanagement::promocodes.edit_promocode'),
        ];
        return view('catalogmanagement::promocodes.form', $data);
    }

    public function update($lang, $countryCode, PromocodeRequest $request, $id)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $this->promocodeService->updatePromocode($id, $validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::promocodes.messages.updated_successfully'),
                'redirect' => route('admin.promocodes.index')
            ]);
        }

        return redirect()->route('admin.promocodes.index')
            ->with('success', __('catalogmanagement::promocodes.messages.updated_successfully'));
    }

    public function destroy($lang, $countryCode, Request $request, $id)
    {
        $this->promocodeService->deletePromocode($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::promocodes.messages.deleted_successfully'),
                'redirect' => route('admin.promocodes.index')
            ]);
        }

        return redirect()->route('admin.promocodes.index')
            ->with('success', __('catalogmanagement::promocodes.messages.deleted_successfully'));
    }

    public function changeStatus($lang, $countryCode, Request $request, $id)
    {
        $request->validate(['status' => 'required|boolean']);

        $newStatus = $request->boolean('status');
        $this->promocodeService->updatePromocode($id, ['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => __('catalogmanagement::promocodes.messages.status_changed'),
        ]);
    }

    public function show($lang, $countryCode,$id)
    {
        $promocode = $this->promocodeService->getPromocodeById($id);
        $data = [
            'promocode' => $promocode,
            'title' => __('catalogmanagement::promocodes.view_promocode'),
        ];
        return view('catalogmanagement::promocodes.view', $data);
    }
}
