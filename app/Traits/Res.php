<?php

namespace App\Traits;

trait Res
{
    public function sendRes($message, $status = true,  $data = [], $errors = [], $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $code);
    }

    public function sendData($message, $status = true,  $data = [], $errors = [])
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * Extract pagination metadata from LengthAwarePaginator
     *
     * @param mixed $data Collection or LengthAwarePaginator
     * @return array|null
     */
    public function getPaginationMeta($data)
    {
        if (method_exists($data, 'lastPage')) {
            return [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ];
        }

        return null;
    }
}
