<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\UserRepoInterface;
use App\Models\User;


class UserRepo implements UserRepoInterface{
    public function getAll(array $filters){
        $query = User::query();
        if(!empty($filters['email'])){
            $query->where('email', $filters['email']);
        }
        if(!empty($filters['name'])){
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }
        return $query->paginate(5);
    }
    public function findById(int $id, array $relations): ?User
    {
        $query = User::query();
        if(!empty($relations)){
            $query->with($relations);
        }
        return $query->withCount('likes')->find($id);
    }
    public function create(array $data): User{
        return User::create($data);
    }
    public function update(User $user, array $data): User{
        $user->update($data);
        return User::find($user->id);
    }
    public function delete(User $user): bool{
       return $user->delete();
    }
    
}