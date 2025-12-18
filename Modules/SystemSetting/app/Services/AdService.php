<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Interfaces\AdRepositoryInterface;

class AdService
{
    protected $adRepository;

    public function __construct(AdRepositoryInterface $adRepository)
    {
        $this->adRepository = $adRepository;
    }

    public function getAllAds()
    {
        return $this->adRepository->all();
    }

    public function getAdById($id)
    {
        return $this->adRepository->find($id);
    }

    public function createAd(array $data)
    {
        return $this->adRepository->create($data);
    }

    public function updateAd($id, array $data)
    {
        return $this->adRepository->update($id, $data);
    }

    public function deleteAd($id)
    {
        return $this->adRepository->delete($id);
    }

    public function filterAds(array $filters)
    {
        return $this->adRepository->filter($filters);
    }
    public function toggleStatus($id, $status)
    {
        return $this->adRepository->toggleStatus($id, $status);
    }
}
