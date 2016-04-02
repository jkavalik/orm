<?php

/**
 * @testCase
 * @dataProvider ../../../sections.ini
 */

namespace NextrasTests\Orm\Integration\Relationships;

use Mockery;
use Nextras\Orm\Model\IModel;
use NextrasTests\Orm\DataTestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../../bootstrap.php';


class RelationshipOneHasManyCollectionTest extends DataTestCase
{
	public function testRemoveA()
	{
		$authorA = $this->orm->authors->getById(1);
		$authorB = $this->orm->authors->getById(2);
		$books = $authorA->books;

		$queries = $this->getQueries(function () use ($authorA, $authorB, $books) {
			Assert::count(0, $books->getEntitiesForPersistence());
			Assert::count(2, iterator_to_array($books));
			Assert::count(2, $books->getEntitiesForPersistence());

			$bookA = $this->orm->books->getById(1);
		});

		if ($queries) {
			Assert::count(1, $queries); // SELECT all books
		}
	}
}


$test = new RelationshipOneHasManyCollectionTest($dic);
$test->run();
