<?php

namespace Modules\CategoryManagment\app\DTOs;
use App\DTOs\FilterDTO;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\Department;

class CategoryFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?string $created_date_from = null,
        public ?string $created_date_to = null,
        public ?string $department_id = null,
        public ?string $main_category_id = null,
        public ?string $category_id = null,
        public ?int $per_page = null,
        public ?string $paginated = null,
        public ?string $sort = null,
        public ?string $sort_type = null,
    ) {}

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest($request): self
    {
        return new self(
            search: $request->input('search'),
            created_date_from: $request->input('created_date_from'),
            created_date_to: $request->input('created_date_to'),
            department_id: $request->input('department_id'),
            main_category_id: $request->input('main_category_id'),
            category_id: $request->input('category_id'),
            per_page: $request->integer('per_page', 15),
            paginated: $request->input('paginated', null),
            sort: $request->input('sort'),
            sort_type: $request->input('sort_type', 'desc'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'created_date_from' => $this->created_date_from,
            'created_date_to' => $this->created_date_to,
            'department_id' => $this->department_id,
            'main_category_id' => $this->main_category_id,
            'category_id' => $this->category_id,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
            'sort' => $this->sort,
            'sort_type' => $this->sort_type,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];

        if ($this->created_date_from && !$this->isValidDate($this->created_date_from)) {
            $this->errors['created_date_from'][] = __('validation.created_date_from_invalid');
        }

        if ($this->created_date_to && !$this->isValidDate($this->created_date_to)) {
            $this->errors['created_date_to'][] = __('validation.created_date_to_invalid');
        }

        if ($this->department_id && !$this->departmentExists($this->department_id)) {
            $this->errors['department_id'][] = __('validation.department_id_not_exist');
        }

        if ($this->main_category_id && !$this->categoryExists($this->main_category_id)) {
            $this->errors['main_category_id'][] = __('validation.category_id_not_exist');
        }

        if ($this->category_id && !$this->categoryExists($this->category_id)) {
            $this->errors['category_id'][] = __('validation.category_id_not_exist');
        }

        if ($this->sort && !in_array($this->sort, ['products', 'sort_number', 'created_at'])) {
            $this->errors['sort'][] = __('validation.invalid_sort_field');
        }

        if ($this->sort_type && !in_array(strtolower($this->sort_type), ['asc', 'desc'])) {
            $this->errors['sort_type'][] = __('validation.invalid_sort_type');
        }

        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function isValidDate(string $date): bool
    {
        return strtotime($date) !== false;
    }

    private function departmentExists(string $departmentId): bool
    {
        return Department::where('id', $departmentId)->orWhere('slug', $departmentId)->exists();
    }

    private function categoryExists(string $categoryId): bool
    {
       return Category::where('id', $categoryId)->orWhere('slug', $categoryId)->exists();
    }
}
