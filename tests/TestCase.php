<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // RefreshDatabase har bir testda roles/permissions jadvalini tozalab
        // qayta seed qiladi (yangi ID bilan), lekin Spatie Permission
        // paketi nom->ID xaritasini butun test jarayoni davomida keshda
        // saqlab qoladi. Shu kesh har test oldidan tozalanmasa: testlar
        // ALOHIDA ishga tushirilganda (har biri yangi PHP jarayoni — toza
        // kesh) muammosiz o'tadi, lekin BUTUN to'plam birga ishga
        // tushirilganda 1-testdan keyingi barcha testlar eskirgan
        // (o'chirilgan) ID'larga tayanib, hasRole()/assignRole() noto'g'ri
        // ishlab, ommaviy va tushuntirib bo'lmaydigan xatolarga olib keladi.
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
