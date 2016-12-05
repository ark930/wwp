<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class ArticleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * 不使用这个路由
     */
    public function testCreate()
    {
        $this->actingAs($this->user)
            ->json('GET', '/articles/create')
            ->seeStatusCode(404);
    }

    public function testCreateWithoutAuth()
    {
        $this->json('GET', '/articles/create')
            ->seeStatusCode(401);
    }

    /**
     * 不使用这个路由
     */
    public function testEdit()
    {
        $this->actingAs($this->user)
            ->json('GET', '/articles/1/edit')
            ->seeStatusCode(404);
    }

    public function testEditWithoutAuth()
    {
        $this->json('GET', '/articles/1/edit')
            ->seeStatusCode(401);
    }

    public function testStore()
    {
        $this->storeApi()
            ->seeStatusCode(200)
            ->seeJson(['user_id' => $this->user['id']]);
    }

    public function testStoreWithoutAuth()
    {
        $this->storeWithoutAuth()
            ->seeStatusCode(401);
    }

    public function testIndex()
    {
        $this->storeApi()
            ->storeApi();

        $this->json('GET', '/articles')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                '*' => [
                    'id', 'user_id'
                ]
            ]);
    }

    public function testIndexWithoutAuth()
    {
        $this->storeApi()
            ->storeApi();

        $this->refreshApplication();
        $this->json('GET', '/articles')
            ->seeStatusCode(401);
    }

    /**
     * @depends testStore
     */
    public function testShow()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('GET', '/articles/1')
            ->seeStatusCode(200)
            ->seeJson(['id' => 1,]);
    }

    public function testShowWithoutAuth()
    {
        $this->storeApi();

        $this->refreshApplication();
        $this->json('GET', '/articles/1')
            ->seeStatusCode(401);
    }

    public function testShowNotFound()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('GET', '/articles/2')
            ->seeStatusCode(400)
            ->seeJsonStructure(['error']);
    }

    /**
     * @depends testStore
     */
    public function testUpdate()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('PUT', '/articles/1', [
                'title' => 'test'
            ])
            ->seeStatusCode(200)
            ->seeJson(['id' => 1,]);
    }

    public function testUpdateWithoutAuth()
    {
        $this->storeApi();

        $this->refreshApplication();
        $this->json('PUT', '/articles/1', [
                'title' => 'test'
            ])
            ->seeStatusCode(401);
    }

    public function testUpdateNotFound()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('PUT', '/articles/2', [
                'title' => 'test'
            ])
            ->seeStatusCode(400)
            ->seeJsonStructure(['error']);
    }

    /**
     * @depends testStore
     */
    public function testDelete()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('DELETE', '/articles/1')
            ->seeStatusCode(204);
    }

    public function testDeleteWithoutAuth()
    {
        $this->storeApi();

        $this->refreshApplication();
        $this->json('DELETE', '/articles/1')
            ->seeStatusCode(401);
    }

    public function testDeleteNotFound()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('DELETE', '/articles/1')
            ->seeStatusCode(204);

        $this->json('DELETE', '/articles/1')
            ->seeStatusCode(400)
            ->seeJsonStructure(['error']);

        $this->json('DELETE', '/articles/2')
            ->seeStatusCode(400)
            ->seeJsonStructure(['error']);
    }

    protected function storeWithAuth(array $data = [])
    {
        return $this->storeApi($data, true);
    }

    protected function storeWithoutAuth(array $data = [])
    {
        return $this->storeApi($data, false);
    }

    protected function storeApi(array $data = [], $auth = true)
    {
        if(empty($data)) {
            $data = [
                'title' => 'test'
            ];
        }

        if($auth) {
            return $this->actingAs($this->user)
                ->json('POST', '/articles', $data);
        }

        return $this->json('POST', '/articles', $data);
    }
}
