<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Repositories\CityRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use ApiResponse;

    protected CityRepositoryInterface $repository;

    public function __construct(CityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *      path="/api/cities",
     *      operationId="getCitiesList",
     *      tags={"Cities"},
     *      summary="Get list of cities",
     *      description="Returns list of cities with pagination, search, and sorting support",
     *      security={{"api_key":{}}},
     *
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number",
     *          required=false,
     *
     *          @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Items per page",
     *          required=false,
     *
     *          @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Search by name or country",
     *          required=false,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Sort field (name, population, founded_at)",
     *          required=false,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          description="Sort order (asc, desc)",
     *          required=false,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Cities retrieved successfully."),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/City")),
     *              @OA\Property(property="meta", type="object")
     *          )
     *      ),
     *
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 15);
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'founded_at');
        $sortOrder = $request->query('sort_order', 'desc');

        $paginated = $this->repository->paginate($perPage, $page, $search, $sortBy, $sortOrder);

        return $this->paginatedResponse($paginated, 'Cities retrieved successfully.', CityResource::class);
    }

    /**
     * @OA\Post(
     *      path="/api/cities",
     *      operationId="storeCity",
     *      tags={"Cities"},
     *      summary="Create new city",
     *      description="Returns city data",
     *      security={{"api_key":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name","country","population","founded_at"},
     *
     *              @OA\Property(property="name", type="string", example="Tokyo"),
     *              @OA\Property(property="country", type="string", example="Japan"),
     *              @OA\Property(property="population", type="integer", example=14000000),
     *              @OA\Property(property="founded_at", type="string", format="date", example="1457-01-01")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="data", ref="#/components/schemas/City")
     *          )
     *      ),
     *
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(StoreCityRequest $request): JsonResponse
    {
        $city = $this->repository->create($request->validated());

        return $this->successResponse(new CityResource($city), 'City created successfully.', 201);
    }

    /**
     * @OA\Get(
     *      path="/api/cities/{id}",
     *      operationId="getCityById",
     *      tags={"Cities"},
     *      summary="Get city information",
     *      description="Returns city data",
     *      security={{"api_key":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="City id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="data", ref="#/components/schemas/City")
     *          )
     *      ),
     *
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $city = $this->repository->find($id);

        if (! $city) {
            return $this->errorResponse('City not found.', 404);
        }

        return $this->successResponse(new CityResource($city), 'City retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/cities/{id}",
     *      operationId="updateCity",
     *      tags={"Cities"},
     *      summary="Update existing city",
     *      description="Returns updated city data",
     *      security={{"api_key":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="City id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="country", type="string"),
     *              @OA\Property(property="population", type="integer"),
     *              @OA\Property(property="founded_at", type="string", format="date")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *               @OA\Property(property="status", type="string", example="success"),
     *               @OA\Property(property="data", ref="#/components/schemas/City")
     *          )
     *      ),
     *
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(UpdateCityRequest $request, string $id): JsonResponse
    {
        $city = $this->repository->update($id, $request->validated());

        if (! $city) {
            return $this->errorResponse('City not found.', 404);
        }

        return $this->successResponse(new CityResource($city), 'City updated successfully.');
    }

    /**
     * @OA\Delete(
     *      path="/api/cities/{id}",
     *      operationId="deleteCity",
     *      tags={"Cities"},
     *      summary="Delete existing city",
     *      description="Deletes a record and returns no content",
     *      security={{"api_key":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="City id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="City deleted successfully.")
     *          )
     *      ),
     *
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);

        if (! $deleted) {
            return $this->errorResponse('City not found.', 404);
        }

        return $this->successResponse(null, 'City deleted successfully.', 200);
    }
}
