<?php

namespace App\Tests;

use App\Tests\ApiJWTTestCase;
use App\Entity\SaleOrder;
use App\Entity\Company;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class SaleOrdersTest extends ApiJWTTestCase
{
  use RefreshDatabaseTrait;

  public function testList(): void
  {
    $response = $this->userRequest('orange', '/api/orders');
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(4, $response->toArray()['hydra:totalItems']);

    $response = $this->userRequest('pekya', '/api/orders');
    $this->assertResponseStatusCodeSame(403);

    $response = $this->userRequest('vasea', '/api/orders');
    $this->assertResponseStatusCodeSame(403);

    $response = $this->userRequest('admin', '/api/orders');
    $this->assertResponseStatusCodeSame(200);
    $this->assertSame(12, $response->toArray()['hydra:totalItems']);
  }

  public function testCreateOrder(): void
  {
    $this->createOrder('orange');
    $this->assertResponseStatusCodeSame(201);
    $this->createOrder('admin');
    $this->assertResponseStatusCodeSame(201);
    $this->createOrder('vasea');
    $this->assertResponseStatusCodeSame(403);
    $this->createOrder('pekya');
    $this->assertResponseStatusCodeSame(403);
  }

  private function getCompanyIri() {
    $iri = static::findIriBy(User::class, ['username' => 'orange']);
    $parts = explode("/", $iri);
    return static::findIriBy(Company::class, ['owner' => $parts[3]]);
  }

  private function createOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $response = $client->request('POST', '/api/orders',['json' => [
      'date'=>date('Y-m-d'),
      'company' => $this->getCompanyIri()
      ]]);
  }

  private function updateOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $companyId = $this->iriToId($this->getCompanyIri());
    $iri = static::findIriBy(SaleOrder::class, ['company' => $companyId]);
    return $client->request('PUT', $iri, ['json' => [
      'date'=>date('Y-m-d'),
      'company' => $this->getCompanyIri()
      ]]);
  }

  public function testUpdateOrder(): void
  {
    $response = $this->updateOrder('admin');
    $this->assertResponseStatusCodeSame(200);
    $response = $this->updateOrder('orange');
    $this->assertResponseStatusCodeSame(200);
    $response = $this->updateOrder('vasea');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->updateOrder('pekya');
    $this->assertResponseStatusCodeSame(403);
  }

  public function deleteOrder($username) {
    $client = $this->getAuthenticatedClient($username);
    $companyId = $this->iriToId($this->getCompanyIri());
    $iri = static::findIriBy(SaleOrder::class, ['company' => $companyId]);
    return $client->request('DELETE', $iri);
  }

  public function testDeleteOrder(): void
  {
    $response = $this->deleteOrder('pekya');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('vasea');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('orange');
    $this->assertResponseStatusCodeSame(403);
    $response = $this->deleteOrder('admin');
    $this->assertResponseStatusCodeSame(204);

  }
}
