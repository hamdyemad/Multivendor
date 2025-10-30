<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Repositories\VariantsConfigurationRepository;

class VariantsConfigurationService
{
    protected $variantsConfigRepository;

    public function __construct(VariantsConfigurationRepository $variantsConfigRepository)
    {
        $this->variantsConfigRepository = $variantsConfigRepository;
    }

    public function getAll()
    {
        return $this->variantsConfigRepository->getAll();
    }

    public function getById($id)
    {
        return $this->variantsConfigRepository->findById($id);
    }

    public function create(array $data)
    {
        return $this->variantsConfigRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->variantsConfigRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->variantsConfigRepository->delete($id);
    }
}
