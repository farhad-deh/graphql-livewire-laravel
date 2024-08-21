<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Auth;

use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class Login extends Mutation
{
    protected $attributes = [
        'name' => 'auth/Login',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Token');
    }

    public function args(): array
    {
        return [
            'email' => ['name' => 'email', 'type' => Type::string()],
            'password' => ['name' => 'password', 'type' => Type::string()],
        ];
    }

    protected function rules(array $args = []): array
    {
        return [
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'min:6'],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (Auth::attempt(['email' => $args['email'], 'password' => $args['password']])) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->accessToken;
            return [
                'user' => $user,
                'token' => $token,
            ];
        }
        return new Error('Invalid credentials');
    }
}
