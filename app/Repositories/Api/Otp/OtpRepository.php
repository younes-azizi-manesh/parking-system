<?php


namespace App\Repositories\Api\Otp;

use App\Models\Otp;

class OtpRepository
{
    public function __construct(private Otp $otp) {}

    public function create(array $otp)
    {
        return $this->otp->create($otp);
    }
    public function update(int $id, array $data)
    {
        return $this->otp->find($id)->update($data);
    }
    public function delete(int $id)
    {
        return $this->otp->find($id)->delete();
    }

    public function findWhere(array $conditions = [], array $with = [], array $columns = ['*'])
    {
        $query = $this->otp->with($with);

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                // ['view' => ['>', 100]].
                $query->where($field, $value[0], $value[1]);
            } else {
                // ['view', 100].
                $query->where($field, $value);
            }
        }
        return $query->first($columns);
    }

    public function findBy(string $field, string $value)
    {
        return $this->otp->where($field, $value)->first();
    }
}
