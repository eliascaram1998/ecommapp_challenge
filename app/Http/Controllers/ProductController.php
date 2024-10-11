<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\ListProductRequest;
use App\Models\Product;
use App\Services\ProductCreate;
use App\Services\ProductDelete;
use App\Services\ProductUpdate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends BaseController
{
    protected $productCreate;
    protected $productUpdate;
    protected $productDelete;

    public function __construct(
        ProductCreate $productCreate, 
        ProductUpdate $productUpdate, 
        ProductDelete $productDelete 
    )
    {
        $this->productCreate = $productCreate;
        $this->productUpdate = $productUpdate;
        $this->productDelete = $productDelete;
        $this->middleware(\App\Http\Middleware\SetAuthenticatedSession::class);
    }

    /**
     * Display the products index view.
     * 
     * @return View The view for the products index page.
     */
    public function index(): View
    {
        $isAuthenticated = session('isAuthenticated');

        return view('products.index')->with('is_authenticated', $isAuthenticated);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function listAjax(ListProductRequest $request): JsonResponse
    {
        $filters = $this->getFiltersFromRequest($request);
        $isAuthenticated = session('isAuthenticated');

        $response = $this->listAction(
            new Product(),
            $filters,
            'products.listAjax',
            $request->url(), 
            10,
            $isAuthenticated
        );
        return response()->json($response, 200);
    }

    /**
     * Store a newly created product in storage.
     * 
     * Validates the request data and creates a product if authenticated.
     * @param StoreProductRequest $request
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $this->productCreate->execute($request->validated());
            Log::info('Producto creado: ' . now());
            return response()->json('Producto creado con éxito', 200);
        } catch (\Exception $e) {
            return response()->json('Error al crear el producto.' . $e->getMessage(), 400);
        }
    }

    /**
     * Update a product in storage.
     * 
     * Validates the request data and update a product if authenticated.
     * @param EditProductRequest $request
     * @return JsonResponse
     */
    public function update(EditProductRequest $request): JsonResponse
    {
        try {
            $this->productUpdate->execute($request->validated());
            Log::info('Producto actualizado: ' . now());
            return response()->json('Producto actualizado con éxito', 200);
        } catch (\Exception $e) {
            return response()->json('Error al actualizar el producto. ' . $e->getMessage(), 400);
        }
    }

    /**
     * Delete a product in storage.
     * 
     * @param int $productId
     * @return JsonResponse
     */
    public function delete(int $productId): JsonResponse
    {
        if (!session('isAuthenticated')) {
            return response()->json(['Debes iniciar sesión para realizar esta acción.'], 403);
        }
        try {
            $this->productDelete->execute($productId);
            Log::info('Producto eliminado: ' . now());
            return response()->json('Producto eliminado con éxito', 200);
        } catch (\Exception $e) {
            return response()->json('Error al eliminar el producto.' . $e->getMessage(), 400);
        }
    }

    /**
     * Extract filters for list from request
     * 
     * @param ListProductRequest $request
     * @return array
     */
    protected function getFiltersFromRequest(ListProductRequest $request): array
    {
        $filters['title'] = $request->input('title');
        $filters['price'] = $request->input('price');
        $filters['created_at'] = $request->input('created_at');
        $filters['page'] = $request->input('page');
        return $filters;
    }
}
