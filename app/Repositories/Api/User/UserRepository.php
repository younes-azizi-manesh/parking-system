<?php

namespace App\Repositories\Api\User;

use App\Models\User;

class UserRepository
{
    public function __construct(public User $user) {}

    public function all()
    {
        return $this->user->all();
    }
    public function find(int $id)
    {
        return $this->user->find($id);
    }
    public function findBy(string $field, string $value)
    {
        return $this->user->where($field, $value)->first();
    }
    public function create(array $user)
    {
        return $this->user->create($user);
    }
    public function update(int $id, array $data)
    {
        return $this->find($id)->update($data);
    }
    public function delete(int $id)
    {
        return $this->find($id)->delete();
    }

    public function findOrCreateByPhone(string $phone)
    {
        $user = $this->user->firstOrCreate(['phone' => $phone]);
        return $user;
    }
}
