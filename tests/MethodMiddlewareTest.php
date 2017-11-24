<?php

namespace PermissionsHandler\Tests;


use PermissionsHandler;
use Illuminate\Support\Facades\Route;
use PermissionsHandler\Middleware\MethodMiddleware;
use PermissionsHandler\Tests\Controllers\TestController;


class MethodMiddlewareTest extends TestCase {


    protected $methodMiddleware;

    protected $request;

    public function setUp()
    {
        parent::setUp();

        Route::get('/index', 'PermissionsHandler\Tests\Controllers\TestController@index');
        Route::get('/checkAdminRole', 'PermissionsHandler\Tests\Controllers\TestController@checkAdminRole');
        Route::get('/mustHasAllRoles', 'PermissionsHandler\Tests\Controllers\TestController@mustHasAllRoles');
        Route::get('/checkAdminPermission', 'PermissionsHandler\Tests\Controllers\TestController@checkAdminPermission');
        Route::get('/mustHasAllPermissions', 'PermissionsHandler\Tests\Controllers\TestController@mustHasAllPermissions');

    }

    /** @test */
    public function a_guest_user_can_access_controller_method_that_doesnot_has_roles_or_permissions_assigned()
    {
        $response = $this->get('/index');
        $response->assertSee('accessed');
    }


    /** @test */
    public function a_user_with_user_role_can_access_a_controller_method_if_has_user_role()
    {
        $this->adminModel->assignRole($this->adminRoleModel);
        $this->actingAs($this->adminModel);
        $response = $this->get('/checkAdminRole');
        $response->assertSee('accessed');
    }

    /** @test */
    public function a_user_must_has_all_assigned_roles()
    {
        // the user has only admin role
        $this->adminModel->assignRole($this->adminRoleModel);
        $this->actingAs($this->adminModel);
        $response = $this->get('/mustHasAllRoles');
        $response->assertStatus(403);
    }


    /** @test */
    public function a_user_with_user_permission_adminPermissions_can_access_a_controller_method_if_has_this_permission()
    {
        $this->adminRoleModel->assignPermission($this->adminPermissionModel);
        $this->adminModel->assignRole($this->adminRoleModel);
        $this->actingAs($this->adminModel);
        $response = $this->get('/checkAdminPermission');
        $response->assertSee('accessed');
    }


    /** @test */
    public function a_user_must_has_all_assigned_permissions()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userModel->assignRole($this->userRoleModel);
        $this->actingAs($this->userModel);
        $response = $this->get('/mustHasAllPermissions');
        $response->assertStatus(403);
    }

}