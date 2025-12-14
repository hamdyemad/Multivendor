<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\PrivacyPolicyRepository;

class PrivacyPolicyService
{
    protected $repository;

    public function __construct(PrivacyPolicyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPrivacyPolicy()
    {
        return $this->repository->getPrivacyPolicy();
    }

    public function updatePrivacyPolicy($data)
    {
        return $this->repository->update($data);
    }
}
