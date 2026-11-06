<?php

namespace App\Services;
use App\Repositories\Contracts\UserRepoInterface;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\Post;

class UserService {
    public function __construct(private UserRepoInterface $userRepo){}

    public function create(array $data) : User{
        return $this->userRepo->create($data);
    }
    public function findAll(array $filters = []) {
        return $this->userRepo->getAll($filters);
    }
    public function findById(int $id, array $relations = []) :?User{
        return $this->userRepo->findById($id,$relations);
    }
    public function update(User $user, array $data) :User{
        return $this->userRepo->update($user, $data);
    }
    public function delete(User $user) :bool{
        return $this->userRepo->delete($user);
    }
    public function resetPassword(array $data){
       return Password::reset($data,
            function (User $user, string $password){
                $user->forceFill(['password' => Hash::make($password)]);

                $user->save();

                event(new PasswordReset($user));

                $user->tokens()->delete();
            }
        );

    }
    
}