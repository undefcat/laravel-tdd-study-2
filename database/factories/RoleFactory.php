<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }

    public function superAdmin()
    {
        return $this->state(fn (array $attribute) => [
            'name' => 'super_admin',
        ]);
    }

    public function articleAdmin()
    {
        return $this->state(fn (array $attribute) => [
            'name' => 'article_admin',
        ]);
    }
}
