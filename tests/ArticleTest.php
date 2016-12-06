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
            ->seeJsonStructure($this->articleStructure());
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
                '*' => $this->articleStructure()
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

    public function testShow()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('GET', '/articles/1')
            ->seeStatusCode(200)
            ->seeJsonStructure($this->articleStructure());
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
            ->seeStatusCode(404)
            ->seeJsonStructure(['msg']);
    }

    public function testUpdate()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('PUT', '/articles/1', [
                'title' => 'test'
            ])
            ->seeStatusCode(200)
            ->seeJsonStructure($this->articleStructure());
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
            ->seeStatusCode(404)
            ->seeJsonStructure(['msg']);
    }

    public function testDelete()
    {
        $this->storeApi();

        $this->actingAs($this->user)
            ->json('DELETE', '/articles/1')
            ->seeStatusCode(200);
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
            ->seeStatusCode(200);

        $this->json('DELETE', '/articles/1')
            ->seeStatusCode(404)
            ->seeJsonStructure(['msg']);

        $this->json('DELETE', '/articles/2')
            ->seeStatusCode(404)
            ->seeJsonStructure(['msg']);
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

    private function articleStructure()
    {
        return [
            'id', 'cover_url', 'title', 'content', 'status', 'created_at', 'updated_at',
        ];
    }
}
