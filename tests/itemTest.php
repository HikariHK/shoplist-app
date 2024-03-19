<?php declare(strict_types=1);

namespace App;

require_once dirname(__DIR__) .'/src/models/item.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Depends;

final class ItemTest extends TestCase
{
    function testCreatesAnItem()
    {
        $result = Item::create((string) time(), 'arroz', 'cereais', 'quilo', 10);
        $this->assertInstanceOf(Item::class, $result);
        return $result;
    }

    #[Depends('testCreatesAnItem')]
    function testUpdatesAnItem(Item $item)
    {
        
        $expected = ["name" => "refrigerante", "type" => "bebida"];
        $result = Item::update(
            $item->_token, $item->_id, 
            $expected 
        );
        $this->assertNotFalse($result);
        
    }

    #[Depends('testCreatesAnItem')]
    function testDeletesAnItem(Item $item)
    {
        
        $result = Item::delete($item->_token, $item->_id);
        $this->assertNotFalse($result);
    }

    #[Depends('testCreatesAnItem')]
    function testCreatesAnotherItem(Item $result)
    {
        $result = Item::create($result->_token, 'arroz', 'cereais', 'quilo', 10);
        $this->assertInstanceOf(Item::class, $result);
        return $result;
    }

    #[Depends('testCreatesAnotherItem')]
    function testGetsAllItems(Item $item)
    {
        
        $items = Item::getAll($item->_token);
        $this->assertEquals(2, sizeof($items));
    }
}
